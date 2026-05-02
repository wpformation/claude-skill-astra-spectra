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

function wpf_skill_color_distance($hex1, $hex2) {
  $r1 = hexdec(substr($hex1, 1, 2)); $g1 = hexdec(substr($hex1, 3, 2)); $b1 = hexdec(substr($hex1, 5, 2));
  $r2 = hexdec(substr($hex2, 1, 2)); $g2 = hexdec(substr($hex2, 3, 2)); $b2 = hexdec(substr($hex2, 5, 2));
  return sqrt(pow($r1 - $r2, 2) + pow($g1 - $g2, 2) + pow($b1 - $b2, 2));
}

function wpf_skill_nearest_token($hex) {
  $hex = strtolower($hex);
  // Récupère la palette ACTIVE (pas une palette hardcodée)
  $palette = wpf_skill_get_active_palette_colors();

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

    // Fix : block_id manquant ou dupliqué
    if (strpos($name, 'uagb/') === 0) {
      $bid = $b['attrs']['block_id'] ?? null;
      if (empty($bid) || in_array($bid, $seen_ids, true)) {
        $new_bid = substr(wpf_skill_uuid_v4(), 0, 8);
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

if (php_sapi_name() === 'cli') {
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
