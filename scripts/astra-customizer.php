<?php
/**
 * astra-customizer.php
 *
 * Pilote intégral du Customizer Astra : palette, typographie, header builder, footer builder, layout.
 * Lit + modifie + écrit `astra-settings` sans écraser les centaines d'autres keys
 * (200+ top-level, 800-2000+ leaves selon la config Astra Pro).
 *
 * Usage CLI :
 *   php astra-customizer.php apply config.json
 *   php astra-customizer.php export > current-config.json
 *
 * Patches JSON acceptés (extraits) :
 *   { "palette": { "currentPalette": "default", "colors": ["#FF8C00", ...] } }
 *   { "typography": { "body": { "family": "Inter", "size": 16 }, "headings": { "family": "Inter Tight", "weight": 700 } } }
 *   { "header": { "primary": { "left": ["logo"], "center": ["menu-1"], "right": ["button-1"] }, "button1": { "text": "...", "url": "..." } } }
 *   { "footer": { "primary": { "1": ["logo"], "2": ["copyright-1"] } } }
 *   { "layout": { "container_width": 1200, "narrow_width": 750, "site_layout": "ast-full-width-layout" } }
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

// Compte récursif de toutes les leaves (valeurs scalaires) d'un array imbriqué.
// Astra stocke beaucoup de configs en arrays imbriqués → un count() top-level
// sous-évalue massivement (ex: 216 top-level vs 851 leaves réelles sur Astra Pro 4.13 mesuré sur prod).
function wpf_skill_count_leaves($arr) {
  if (!is_array($arr)) return 1;
  $n = 0;
  foreach ($arr as $v) {
    $n += is_array($v) ? wpf_skill_count_leaves($v) : 1;
  }
  return $n;
}

function wpf_skill_astra_export() {
  $current = get_option('astra-settings');
  if (!$current) return ['error' => 'astra-settings option not found. Astra theme inactive?'];

  // currentPalette est dans une option séparée 'astra-color-palettes' (pilote l'UI Customizer),
  // PAS dans 'astra-settings.global-color-palette.currentPalette'.
  $current_palette_name = null;
  $color_palettes = get_option('astra-color-palettes');
  if (is_array($color_palettes) && isset($color_palettes['currentPalette'])) {
    $current_palette_name = $color_palettes['currentPalette'];
  }

  // Extraire les sections clés
  return [
    'palette' => [
      'currentPalette' => $current_palette_name,
      'colors' => $current['global-color-palette']['palette'] ?? [],
    ],
    'typography' => [
      'body_family' => $current['body-font-family'] ?? null,
      'body_size' => $current['body-font-size'] ?? null,
      'headings_family' => $current['headings-font-family'] ?? null,
      'headings_weight' => $current['headings-font-weight'] ?? null,
      'h1_size' => $current['font-size-h1'] ?? null,
      'h2_size' => $current['font-size-h2'] ?? null,
    ],
    'layout' => [
      'site_layout' => $current['site-layout'] ?? null,
      'site_content_layout' => $current['site-content-layout'] ?? null,
      'site_content_width' => $current['site-content-width'] ?? null,
      'narrow_container_max_width' => $current['narrow-container-max-width'] ?? null,
    ],
    'header' => [
      'desktop_items' => $current['header-desktop-items'] ?? null,
      'mobile_items' => $current['header-mobile-items'] ?? null,
      'logo_width' => $current['ast-header-logo-width'] ?? null,
      'main_stick' => $current['header-main-stick'] ?? false,
      'button1_text' => $current['header-button1-text'] ?? null,
      'button1_url' => $current['header-button1-link-option']['url'] ?? null,
    ],
    'footer' => [
      'desktop_items' => $current['footer-desktop-items'] ?? null,
    ],
    'sidebar' => [
      'site_sidebar_layout' => $current['site-sidebar-layout'] ?? null,
      'single_page_sidebar' => $current['single-page-sidebar-layout'] ?? null,
      'single_post_sidebar' => $current['single-post-sidebar-layout'] ?? null,
    ],
    '_meta' => [
      'top_level_keys' => count($current),
      'total_leaves' => wpf_skill_count_leaves($current),
      'option_size_kb' => round(strlen(serialize($current)) / 1024, 1),
    ],
  ];
}

function wpf_skill_astra_apply($patch) {
  $current = get_option('astra-settings');
  if (!$current) return ['error' => 'astra-settings option not found. Astra theme inactive?'];

  $applied = [];

  // Palette
  if (isset($patch['palette'])) {
    if (isset($patch['palette']['colors']) && is_array($patch['palette']['colors'])) {
      // Valider 9 hex
      $colors = $patch['palette']['colors'];
      if (count($colors) === 9) {
        foreach ($colors as $hex) {
          if (!preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) {
            return ['error' => "Invalid hex in palette: $hex"];
          }
        }
        $current['global-color-palette']['palette'] = $colors;
        $applied[] = 'palette.colors (9 hex)';
      } else {
        return ['error' => 'Palette must be exactly 9 hex colors.'];
      }
    }
    if (isset($patch['palette']['currentPalette'])) {
      $current['global-color-palette']['currentPalette'] = $patch['palette']['currentPalette'];
      $applied[] = 'palette.currentPalette = ' . $patch['palette']['currentPalette'];
    }
  }

  // Typographie
  if (isset($patch['typography'])) {
    $typo = $patch['typography'];
    $map = [
      'body_family' => 'body-font-family',
      'body_weight' => 'body-font-weight',
      'body_size' => 'body-font-size',
      'body_line_height' => 'body-line-height',
      'headings_family' => 'headings-font-family',
      'headings_weight' => 'headings-font-weight',
      'headings_line_height' => 'headings-line-height',
      'h1_size' => 'font-size-h1',
      'h2_size' => 'font-size-h2',
      'h3_size' => 'font-size-h3',
      'h4_size' => 'font-size-h4',
      'h5_size' => 'font-size-h5',
      'h6_size' => 'font-size-h6',
    ];
    foreach ($map as $key => $astra_key) {
      if (isset($typo[$key])) {
        $current[$astra_key] = $typo[$key];
        $applied[] = "typography.$key";
      }
    }
  }

  // Layout
  if (isset($patch['layout'])) {
    $l = $patch['layout'];
    if (isset($l['container_width'])) { $current['site-content-width'] = (int)$l['container_width']; $applied[] = 'layout.container_width'; }
    if (isset($l['narrow_width'])) { $current['narrow-container-max-width'] = (int)$l['narrow_width']; $applied[] = 'layout.narrow_width'; }
    if (isset($l['site_layout'])) { $current['site-layout'] = $l['site_layout']; $applied[] = 'layout.site_layout'; }
    if (isset($l['site_content_layout'])) { $current['site-content-layout'] = $l['site_content_layout']; $applied[] = 'layout.site_content_layout'; }
  }

  // Header
  if (isset($patch['header'])) {
    $h = $patch['header'];
    if (isset($h['primary'])) {
      $current['header-desktop-items']['primary'] = [
        'primary_left' => $h['primary']['left'] ?? [],
        'primary_center' => $h['primary']['center'] ?? [],
        'primary_right' => $h['primary']['right'] ?? [],
      ];
      $applied[] = 'header.primary';
    }
    if (isset($h['main_stick'])) { $current['header-main-stick'] = (bool)$h['main_stick']; $applied[] = 'header.main_stick'; }
    if (isset($h['logo_width'])) { $current['ast-header-logo-width'] = (int)$h['logo_width']; $applied[] = 'header.logo_width'; }
    if (isset($h['button1'])) {
      $b = $h['button1'];
      if (isset($b['text'])) { $current['header-button1-text'] = $b['text']; $applied[] = 'header.button1.text'; }
      if (isset($b['url'])) {
        $current['header-button1-link-option'] = [
          'url' => $b['url'],
          'new_tab' => $b['new_tab'] ?? false,
        ];
        $applied[] = 'header.button1.url';
      }
      if (isset($b['style'])) { $current['header-button1-button-style'] = $b['style']; $applied[] = 'header.button1.style'; }
    }
  }

  // Footer
  if (isset($patch['footer'])) {
    $f = $patch['footer'];
    if (isset($f['primary'])) {
      $current['footer-desktop-items']['primary'] = $f['primary'];
      $applied[] = 'footer.primary';
    }
    if (isset($f['below'])) {
      $current['footer-desktop-items']['below'] = $f['below'];
      $applied[] = 'footer.below';
    }
  }

  // Sidebar
  if (isset($patch['sidebar'])) {
    $s = $patch['sidebar'];
    if (isset($s['site_sidebar_layout'])) { $current['site-sidebar-layout'] = $s['site_sidebar_layout']; $applied[] = 'sidebar.site_sidebar_layout'; }
    if (isset($s['single_page_sidebar'])) { $current['single-page-sidebar-layout'] = $s['single_page_sidebar']; $applied[] = 'sidebar.single_page_sidebar'; }
    if (isset($s['single_post_sidebar'])) { $current['single-post-sidebar-layout'] = $s['single_post_sidebar']; $applied[] = 'sidebar.single_post_sidebar'; }
  }

  // Custom CSS
  if (isset($patch['custom_css'])) {
    $current['custom-css'] = $patch['custom_css'];
    $applied[] = 'custom_css (' . strlen($patch['custom_css']) . ' chars)';
  }

  // Écriture
  $ok = update_option('astra-settings', $current);

  // Invalidation caches
  if (function_exists('astra_clear_all_assets_cache')) astra_clear_all_assets_cache();
  delete_transient('astra_dynamic_css');
  wp_cache_flush();

  return [
    'ok' => $ok,
    'applied' => $applied,
    'count' => count($applied),
  ];
}

// Bloc CLI : ne s'exécute QUE si le script est lancé en ligne de commande directe.
if (
  php_sapi_name() === 'cli'
  && isset($GLOBALS['argv'])
  && is_array($GLOBALS['argv'])
  && !empty($GLOBALS['argv'][0])
  && basename($GLOBALS['argv'][0]) === basename(__FILE__)
) {
  $argv = $GLOBALS['argv'];
  $cmd = $argv[1] ?? 'help';

  if ($cmd === 'export') {
    echo json_encode(wpf_skill_astra_export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  } elseif ($cmd === 'apply' && isset($argv[2])) {
    $file = $argv[2];
    if (!file_exists($file)) { echo json_encode(['error' => "File not found: $file"]); exit(1); }
    $patch = json_decode(file_get_contents($file), true);
    if (!$patch) { echo json_encode(['error' => 'Invalid JSON in patch file.']); exit(1); }
    echo json_encode(wpf_skill_astra_apply($patch), JSON_PRETTY_PRINT);
  } else {
    echo "Usage:\n  php astra-customizer.php export > config.json\n  php astra-customizer.php apply patch.json\n";
  }
}
