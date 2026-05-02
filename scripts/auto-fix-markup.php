<?php
/**
 * auto-fix-markup.php
 *
 * Applique les corrections automatiques détectées par visual-audit.php.
 * Réécrit le markup Gutenberg avec les fixes (block_id régénérés, hex → tokens, H1 dégradés, etc.).
 *
 * Usage CLI :
 *   php auto-fix-markup.php <page_id>
 *   php auto-fix-markup.php < markup.html > markup-fixed.html
 *
 * Output stdout : markup corrigé (et stderr : journal des fixes appliqués).
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

function wpf_skill_uuid_v4() {
  $data = random_bytes(16);
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function wpf_skill_get_active_palette_colors() {
  // Lire dynamiquement les couleurs RÉELLES de la palette Astra active
  // (depuis astra-settings.global-color-palette.palette qui pilote le frontend).
  // Fallback sur la palette default Astra si l'option n'est pas dispo.
  $default = ['#0274be', '#3a3a3a', '#0a0a0a', '#0a0a0a', '#0274be', '#ffffff', '#f5f5f5', '#fafafa', '#e7e7e7'];

  if (!function_exists('get_option')) return $default;

  $astra_settings = get_option('astra-settings');
  if (is_array($astra_settings) && isset($astra_settings['global-color-palette']['palette']) && is_array($astra_settings['global-color-palette']['palette'])) {
    $palette = $astra_settings['global-color-palette']['palette'];
    if (count($palette) === 9) return $palette;
  }
  return $default;
}

function wpf_skill_color_luminance($hex) {
  $r = hexdec(substr($hex, 1, 2)) / 255;
  $g = hexdec(substr($hex, 3, 2)) / 255;
  $b = hexdec(substr($hex, 5, 2)) / 255;
  // Coefficients ITU-R BT.601 (perceptual)
  return 0.299 * $r + 0.587 * $g + 0.114 * $b;
}

function wpf_skill_color_distance($hex1, $hex2) {
  // Distance euclidienne pondérée par perception : composante luminance amplifiée
  // pour que noir vs gris foncé soit bien distingué de noir vs blanc cassé.
  $r1 = hexdec(substr($hex1, 1, 2)); $g1 = hexdec(substr($hex1, 3, 2)); $b1 = hexdec(substr($hex1, 5, 2));
  $r2 = hexdec(substr($hex2, 1, 2)); $g2 = hexdec(substr($hex2, 3, 2)); $b2 = hexdec(substr($hex2, 5, 2));
  $dr = $r1 - $r2; $dg = $g1 - $g2; $db = $b1 - $b2;
  // Pondération « redmean » (approximation Lab légère, sans dépendance)
  $r_avg = ($r1 + $r2) / 2;
  $weight_r = 2 + $r_avg / 256;
  $weight_g = 4;
  $weight_b = 2 + (255 - $r_avg) / 256;
  return sqrt($weight_r * $dr * $dr + $weight_g * $dg * $dg + $weight_b * $db * $db);
}

function wpf_skill_nearest_token($hex) {
  $hex = strtolower($hex);
  $palette = wpf_skill_get_active_palette_colors();
  $hex_lum = wpf_skill_color_luminance($hex);

  // Step 1 : préférence sémantique pour les noirs très foncés (luminance < 0.15)
  // Les slots Astra par convention : 2 = text body, 3 = heading dark.
  // Si l'un de ces deux slots a une couleur très foncée aussi, on le préfère
  // au slot le plus proche en distance pure (qui peut tomber sur color-7 par hasard).
  if ($hex_lum < 0.15) {
    foreach ([2, 3, 1, 8] as $semantic_idx) {
      if (isset($palette[$semantic_idx])) {
        $slot_lum = wpf_skill_color_luminance(strtolower($palette[$semantic_idx]));
        if ($slot_lum < 0.25) {
          return "var(--ast-global-color-$semantic_idx)";
        }
      }
    }
  }

  // Step 2 : préférence sémantique pour les blancs très clairs (luminance > 0.9)
  // Les slots Astra par convention : 5 = body bg, 7 = off-white.
  if ($hex_lum > 0.9) {
    foreach ([5, 7, 6] as $semantic_idx) {
      if (isset($palette[$semantic_idx])) {
        $slot_lum = wpf_skill_color_luminance(strtolower($palette[$semantic_idx]));
        if ($slot_lum > 0.85) {
          return "var(--ast-global-color-$semantic_idx)";
        }
      }
    }
  }

  // Step 3 : fallback sur distance pondérée perceptuelle
  $best_idx = 0;
  $best_dist = PHP_INT_MAX;
  foreach ($palette as $idx => $palette_hex) {
    $dist = wpf_skill_color_distance($hex, strtolower($palette_hex));
    if ($dist < $best_dist) {
      $best_dist = $dist;
      $best_idx = $idx;
    }
  }
  return "var(--ast-global-color-$best_idx)";
}

function wpf_skill_auto_fix(&$blocks, &$fixes_log, &$seen_ids, &$h1_seen, $context = 'page') {
  $color_attrs = ['backgroundColor', 'textColor', 'iconColor', 'headingColor', 'subHeadingColor', 'borderColor', 'color'];

  foreach ($blocks as $idx => &$b) {
    if (empty($b['blockName'])) continue;
    $name = $b['blockName'];

    // Fix : block_id manquant ou dupliqué.
    // Génère un block_id parlant `<short-block-name>-<hash6>` au lieu d'un UUID anonyme.
    // Plus facile à debug visuellement dans Gutenberg (« uagb-block-info-box-c293b1 »
    // au lieu de « uagb-block-c293b1ce »).
    if (strpos($name, 'uagb/') === 0) {
      $bid = $b['attrs']['block_id'] ?? null;
      if (empty($bid) || in_array($bid, $seen_ids, true)) {
        $short_name = substr($name, 5); // retire "uagb/"
        // garder seulement les chars valides (alpha-num + tirets)
        $short_name = preg_replace('/[^a-z0-9-]/', '', strtolower($short_name));
        // tronquer à 16 chars max pour éviter des classes CSS trop longues
        if (strlen($short_name) > 16) $short_name = substr($short_name, 0, 16);
        $hash = substr(wpf_skill_uuid_v4(), 0, 6);
        $new_bid = "$short_name-$hash";
        // sécurité : si encore dupliqué (très improbable) → ajout suffixe
        $attempt = 0;
        while (in_array($new_bid, $seen_ids, true) && $attempt < 5) {
          $hash = substr(wpf_skill_uuid_v4(), 0, 6);
          $new_bid = "$short_name-$hash";
          $attempt++;
        }
        $b['attrs']['block_id'] = $new_bid;
        $fixes_log[] = "[$name] block_id → $new_bid";
        $bid = $new_bid;
      }
      $seen_ids[] = $bid;
    }

    // Fix : hex hardcodé → token
    foreach ($color_attrs as $attr) {
      if (isset($b['attrs'][$attr]) && is_string($b['attrs'][$attr]) && preg_match('/^#[0-9a-fA-F]{6}$/', $b['attrs'][$attr])) {
        $old = $b['attrs'][$attr];
        $new = wpf_skill_nearest_token($old);
        $b['attrs'][$attr] = $new;
        $fixes_log[] = "[$name] $attr: $old → $new";
      }
    }

    // Fix : H1 multiples (garder le 1er, dégrader les suivants en H2)
    if ($name === 'core/heading') {
      $level = $b['attrs']['level'] ?? 2;
      if ($level === 1) {
        if ($h1_seen) {
          $b['attrs']['level'] = 2;
          $fixes_log[] = "[core/heading] H1 (duplicate) → H2";
        } else {
          $h1_seen = true;
        }
      }
    }
    if ($name === 'uagb/advanced-heading') {
      $tag = $b['attrs']['headingTag'] ?? 'h2';
      if ($tag === 'h1') {
        if ($h1_seen) {
          $b['attrs']['headingTag'] = 'h2';
          $fixes_log[] = "[uagb/advanced-heading] H1 (duplicate) → H2";
        } else {
          $h1_seen = true;
        }
      }
    }

    // Récursion sur innerBlocks
    if (!empty($b['innerBlocks'])) {
      wpf_skill_auto_fix($b['innerBlocks'], $fixes_log, $seen_ids, $h1_seen, $name);
    }
  }
}

function wpf_skill_apply_fixes($content) {
  $blocks = parse_blocks($content);
  $fixes_log = [];
  $seen_ids = [];
  $h1_seen = false;

  wpf_skill_auto_fix($blocks, $fixes_log, $seen_ids, $h1_seen);

  $fixed = serialize_blocks($blocks);

  return [
    'content' => $fixed,
    'fixes_count' => count($fixes_log),
    'fixes_log' => $fixes_log,
  ];
}

// Bloc CLI : ne s'exécute QUE si le script est lancé en ligne de commande directe
// (pas en require_once depuis un autre script, pas via wp eval-file).
if (
  php_sapi_name() === 'cli'
  && isset($GLOBALS['argv'])
  && is_array($GLOBALS['argv'])
  && !empty($GLOBALS['argv'][0])
  && basename($GLOBALS['argv'][0]) === basename(__FILE__)
) {
  $argv = $GLOBALS['argv'];
  $input = '';
  if (isset($argv[1])) {
    if (is_numeric($argv[1])) {
      $post = get_post((int)$argv[1]);
      if (!$post) { fwrite(STDERR, "Post {$argv[1]} not found.\n"); exit(1); }
      $input = $post->post_content;
    } elseif (file_exists($argv[1])) {
      $input = file_get_contents($argv[1]);
    } else {
      $input = $argv[1];
    }
  } else {
    $input = stream_get_contents(STDIN);
  }

  $r = wpf_skill_apply_fixes($input);
  fwrite(STDERR, "FIXES APPLIED: {$r['fixes_count']}\n");
  foreach ($r['fixes_log'] as $line) {
    fwrite(STDERR, "  - $line\n");
  }
  echo $r['content'];
}
