<?php
/**
 * validate-block-markup.php
 *
 * Valide le markup Gutenberg avant POST sur le site.
 * Roundtrip critique : parse_blocks($content) puis serialize_blocks($blocks).
 * Si le résultat diffère du source, le markup contient des erreurs (bloc inconnu, attribut JSON cassé, balise mal fermée, etc.).
 *
 * Usage :
 *   $result = wpf_skill_validate_markup($block_markup);
 *   if (!$result['valid']) { echo "ERRORS:\n"; print_r($result['errors']); exit; }
 *
 * Output :
 *   {
 *     "valid": true | false,
 *     "block_count": 15,
 *     "block_inventory": { "uagb/container": 2, "uagb/info-box": 3, "core/paragraph": 1, ... },
 *     "errors": [],   // si valid=false : liste des problèmes
 *     "warnings": [], // attributs manquants, block_id non unique, hex hardcoded, etc.
 *     "diff_size": 0  // chars de différence entre original et reserialized
 *   }
 */

if (!defined('ABSPATH')) {
  $wp_load_paths = [
    __DIR__ . '/../wp-load.php',
    __DIR__ . '/../../wp-load.php',
    __DIR__ . '/../../../wp-load.php',
    '/wordpress/wp-load.php',
  ];
  foreach ($wp_load_paths as $p) {
    if (file_exists($p)) { require_once $p; break; }
  }
}

function wpf_skill_validate_markup($content) {
  $result = [
    'valid' => false,
    'block_count' => 0,
    'block_inventory' => [],
    'errors' => [],
    'warnings' => [],
    'diff_size' => 0,
  ];

  if (empty($content)) {
    $result['errors'][] = 'Empty content provided.';
    return $result;
  }

  // === Roundtrip parse → serialize ===
  $blocks = parse_blocks($content);
  $reserialized = serialize_blocks($blocks);
  $result['diff_size'] = abs(strlen($content) - strlen($reserialized));

  // Diff > 0 n'est pas forcément une erreur : serialize_blocks() peut ajouter
  // ou normaliser des whitespaces sans casser le markup. Pour distinguer les vraies
  // erreurs des diffs cosmétiques, on compare ligne à ligne (en ignorant les whitespaces).
  if ($result['diff_size'] > 0) {
    $orig_lines = preg_split('/\r?\n/', $content);
    $reser_lines = preg_split('/\r?\n/', $reserialized);
    $line_count_orig = count($orig_lines);
    $line_count_reser = count($reser_lines);

    // Compare les lignes en ignorant les whitespaces de bout
    $real_diff_found = false;
    $min = min($line_count_orig, $line_count_reser);
    for ($i = 0; $i < $min; $i++) {
      if (rtrim($orig_lines[$i]) !== rtrim($reser_lines[$i])) {
        $real_diff_found = true;
        $result['errors'][] = "Line " . ($i + 1) . " content diff (real, not whitespace): \"" . substr($orig_lines[$i], 0, 80) . "\" vs \"" . substr($reser_lines[$i], 0, 80) . "\"";
        break;
      }
    }

    if (!$real_diff_found) {
      // Diff cosmétique uniquement (whitespace) : on note un warning, pas une erreur
      $result['warnings'][] = "Roundtrip cosmetic diff: " . $result['diff_size'] . " chars (whitespace only, markup is valid).";
    } else {
      $result['errors'][] = "Roundtrip real diff: " . $result['diff_size'] . " chars. Markup contains a malformed block, broken JSON attrs, or unclosed comment.";
    }

    // Si counts de lignes différents = vraie problème
    if ($line_count_orig !== $line_count_reser) {
      $result['errors'][] = "Line count mismatch: original $line_count_orig vs reserialized $line_count_reser. Likely a missing or extra block opening/closing comment.";
    }
  }

  // === Inventaire des blocs ===
  $seen_block_ids = [];

  $walker = function ($blocks) use (&$walker, &$result, &$seen_block_ids) {
    foreach ($blocks as $b) {
      if (empty($b['blockName'])) continue;

      $name = $b['blockName'];
      $result['block_inventory'][$name] = ($result['block_inventory'][$name] ?? 0) + 1;
      $result['block_count']++;

      // Spectra blocks must have a unique block_id
      if (strpos($name, 'uagb/') === 0) {
        $block_id = $b['attrs']['block_id'] ?? null;
        if (empty($block_id)) {
          $result['warnings'][] = "Spectra block $name without block_id attribute. Gutenberg may recompute and break rendering.";
        } else {
          if (in_array($block_id, $seen_block_ids)) {
            $result['errors'][] = "Duplicate block_id '$block_id' on block $name. Each Spectra block must have a UNIQUE block_id.";
          }
          $seen_block_ids[] = $block_id;
        }

        // Check pour hex hardcoded dans les attrs couleur (anti-pattern)
        $color_attrs = ['backgroundColor', 'textColor', 'iconColor', 'headingColor', 'subHeadingColor', 'borderColor', 'color'];
        foreach ($color_attrs as $attr) {
          if (isset($b['attrs'][$attr]) && is_string($b['attrs'][$attr]) && preg_match('/^#[0-9a-fA-F]{6}$/', $b['attrs'][$attr])) {
            $result['warnings'][] = "Block $name uses hardcoded hex {$b['attrs'][$attr]} for $attr. Prefer var(--ast-global-color-X) for design system coherence.";
          }
        }
      }

      if (!empty($b['innerBlocks'])) {
        $walker($b['innerBlocks']);
      }
    }
  };

  $walker($blocks);

  // === Verdict ===
  $result['valid'] = empty($result['errors']);
  return $result;
}

// === CLI usage : pipe markup as stdin or pass as argument ===
if (php_sapi_name() === 'cli' && isset($argv[1])) {
  $input = file_exists($argv[1]) ? file_get_contents($argv[1]) : $argv[1];
  $r = wpf_skill_validate_markup($input);
  echo json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
