<?php
/**
 * apply-design-tokens.php
 *
 * Applique une palette de design tokens (9 hex colors) au site cible :
 *  - Si Astra theme actif → modifie astra-settings.global-color-palette.palette + astra-color-palettes.palettes.palette_1
 *  - Sinon → injecte un wpf-design-tokens.css avec les variables --ast-global-color-X via wp_add_inline_style
 *
 * Format de palette : array de 9 hex colors
 *  index 0 : Primary
 *  index 1 : Primary darker (hover/active)
 *  index 2 : Heading dark
 *  index 3 : Body text
 *  index 4 : White / page bg
 *  index 5 : Light bg / cards
 *  index 6 : Dark variant
 *  index 7 : Border / divider
 *  index 8 : Black / emphasis
 *
 * Usage :
 *   wpf_skill_apply_palette([
 *     '#FF8C00', '#E67E00', '#0E0E14', '#334155', '#FFFFFF',
 *     '#F0F5FA', '#111111', '#D1D5DB', '#000000'
 *   ]);
 *
 * Ou via preset name (Astra natif) :
 *   wpf_skill_apply_preset('preset_8'); // Orange
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

/**
 * Appliquer une palette de 9 hex colors.
 *
 * @param array $palette 9 hex colors (with or without #)
 * @return array { 'success': bool, 'mode': 'astra' | 'fallback-css', 'message': string }
 */
function wpf_skill_apply_palette(array $palette) {
  if (count($palette) !== 9) {
    return ['success' => false, 'message' => 'Palette must have exactly 9 colors.'];
  }

  // Normaliser les hex (force # prefix, uppercase 6 chars)
  $normalized = array_map(function($c) {
    $c = ltrim(trim($c), '#');
    return '#' . strtoupper(substr($c, 0, 6));
  }, $palette);

  $is_astra = (wp_get_theme()->get_stylesheet() === 'astra');

  if ($is_astra) {
    // Mode Astra : update astra-settings.global-color-palette + astra-color-palettes.palettes.palette_1
    $settings = get_option('astra-settings', []);
    $settings['global-color-palette'] = ['palette' => $normalized];
    update_option('astra-settings', $settings);

    $color_palettes = get_option('astra-color-palettes', []);
    if (!isset($color_palettes['palettes'])) $color_palettes['palettes'] = [];
    $color_palettes['palettes']['palette_1'] = $normalized;
    $color_palettes['currentPalette'] = 'palette_1';
    update_option('astra-color-palettes', $color_palettes);

    // Vider les caches Astra
    if (function_exists('astra_clear_all_assets_cache')) astra_clear_all_assets_cache();
    delete_transient('astra_dynamic_css');
    wp_cache_flush();

    return [
      'success' => true,
      'mode' => 'astra',
      'message' => 'Palette appliquée via Astra. 9 variables CSS --ast-global-color-0..8 régénérées.',
      'palette' => $normalized,
    ];
  } else {
    // Fallback : injecter un CSS custom avec les variables --ast-global-color-X
    $css = ':root {';
    foreach ($normalized as $i => $hex) {
      $css .= "--ast-global-color-$i: $hex;";
    }
    $css .= '}';

    update_option('wpf_skill_design_tokens_css', $css);

    // Hook pour enqueue ce CSS sur le frontend
    if (!has_action('wp_enqueue_scripts', 'wpf_skill_enqueue_design_tokens')) {
      add_action('wp_enqueue_scripts', 'wpf_skill_enqueue_design_tokens');
    }

    return [
      'success' => true,
      'mode' => 'fallback-css',
      'message' => 'Palette appliquée via CSS inline (Astra non actif). Variables --ast-global-color-0..8 disponibles.',
      'palette' => $normalized,
    ];
  }
}

/**
 * Hook frontend pour enqueue le CSS fallback.
 */
function wpf_skill_enqueue_design_tokens() {
  $css = get_option('wpf_skill_design_tokens_css', '');
  if ($css) {
    wp_register_style('astra-spectra-skill-design-tokens', false);
    wp_enqueue_style('astra-spectra-skill-design-tokens');
    wp_add_inline_style('astra-spectra-skill-design-tokens', $css);
  }
}

/**
 * Appliquer un preset Astra natif (preset_1 à preset_11).
 * Listes des presets dans references/astra-customizer-map.md
 */
function wpf_skill_apply_preset(string $preset_name) {
  $presets = [
    'preset_1' => ['#0067FF', '#005EE9', '#0F172A', '#364151', '#E7F6FF', '#FFFFFF', '#D1DAE5', '#070614', '#222222'],
    'preset_2' => ['#6528F7', '#5511F8', '#0F172A', '#454F5E', '#F2F0FE', '#FFFFFF', '#D8D8F5', '#0D0614', '#222222'],
    'preset_3' => ['#DD183B', '#CC1939', '#0F172A', '#3A3A3A', '#FFEDE6', '#FFFFFF', '#FFD1BF', '#140609', '#222222'],
    'preset_4' => ['#54B435', '#379237', '#0F172A', '#2F3B40', '#EDFBE2', '#FFFFFF', '#D5EAD8', '#0C1406', '#222222'],
    'preset_5' => ['#DCA54A', '#D09A40', '#0F172A', '#4A4A4A', '#FAF5E5', '#FFFFFF', '#F0E6C5', '#141004', '#222222'],
    'preset_6' => ['#FB5FAB', '#EA559D', '#0F172A', '#454F5E', '#FCEEF5', '#FFFFFF', '#FAD8E9', '#140610', '#222222'],
    'preset_7' => ['#1B9C85', '#178E79', '#0F172A', '#454F5E', '#EDF6EE', '#FFFFFF', '#D4F3D7', '#06140C', '#222222'],
    'preset_8' => ['#FD9800', '#E98C00', '#0F172A', '#454F5E', '#FEF9E1', '#FFFFFF', '#F9F0C8', '#141006', '#222222'],
    'preset_9' => ['#FF6210', '#F15808', '#1C0D0A', '#353535', '#FEF1E4', '#FFFFFF', '#E5D7D1', '#140B06', '#222222'],
    'preset_10' => ['#737880', '#65696F', '#151616', '#393C40', '#F6F6F6', '#FFFFFF', '#F1F0F0', '#232529', '#222222'],
    'preset_11' => ['#0085FF', '#0177E3', '#FFFFFF', '#E7F6FF', '#212A37', '#0F172A', '#4F5B62', '#070614', '#222222'],
  ];

  if (!isset($presets[$preset_name])) {
    return ['success' => false, 'message' => "Preset '$preset_name' not found. Available: " . implode(', ', array_keys($presets))];
  }

  return wpf_skill_apply_palette($presets[$preset_name]);
}

// === CLI usage ===
if (php_sapi_name() === 'cli' && isset($argv[1])) {
  if (strpos($argv[1], 'preset_') === 0) {
    $r = wpf_skill_apply_preset($argv[1]);
  } else {
    // JSON array of 9 hex
    $palette = json_decode($argv[1], true);
    if (!is_array($palette)) {
      echo json_encode(['error' => 'Invalid palette JSON. Provide an array of 9 hex colors.']);
      exit(1);
    }
    $r = wpf_skill_apply_palette($palette);
  }
  echo json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
