<?php
/**
 * detect-environment.php
 *
 * Script à exécuter sur le site WordPress cible (via REST API ou direct PHP).
 * Retourne un JSON avec le profil environnement nécessaire au skill claude-skill-astra-spectra.
 *
 * Usage 1 — Appel HTTP REST (recommandé) :
 *   POST /wp-json/wpf-skill/v1/detect (si tu as un mu-plugin qui expose ce endpoint)
 *
 * Usage 2 — Exécution directe via WP-CLI :
 *   wp eval-file detect-environment.php
 *
 * Usage 3 — Exécution one-shot via Playground/local :
 *   require("/wordpress/wp-load.php");
 *   include __DIR__ . '/detect-environment.php';
 *
 * Output JSON :
 *   {
 *     "wp_version": "6.9.4",
 *     "php_version": "8.3.30",
 *     "rest_api_accessible": true,
 *     "spectra": { "active": true, "version": "2.19.25", "block_count_estimated": 49 },
 *     "astra": { "active": true, "version": "4.13.1", "current_palette": "palette_1" },
 *     "theme": { "slug": "astra", "name": "Astra", "version": "4.13.1", "is_block_theme": false },
 *     "permalinks": { "structure": "/%postname%/", "ok": true },
 *     "verdict": "GO" | "BLOCKED" | "DEGRADED",
 *     "blockers": [],
 *     "warnings": [],
 *     "recommendations": []
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

if (!defined('ABSPATH')) {
  echo json_encode(['error' => 'WordPress not loaded — provide path to wp-load.php']);
  exit(1);
}

$profile = [
  'wp_version' => get_bloginfo('version'),
  'php_version' => phpversion(),
  'rest_api_accessible' => true,
  'spectra' => ['active' => false, 'version' => null],
  'astra' => [
    'active' => false,
    'version' => null,
    'current_palette' => null,
    'pro_active' => false,
    'palette_colors' => [],
  ],
  'theme' => [],
  'permalinks' => [],
  'verdict' => 'UNKNOWN',
  'blockers' => [],
  'warnings' => [],
  'recommendations' => [],
];

// === 1. Active theme ===
$theme = wp_get_theme();
$profile['theme'] = [
  'slug' => $theme->get_stylesheet(),
  'name' => $theme->get('Name'),
  'version' => $theme->get('Version'),
  'is_block_theme' => function_exists('wp_is_block_theme') ? wp_is_block_theme() : false,
];

// === 2. Spectra plugin (BLOCKING si absent) ===
$active_plugins = get_option('active_plugins', []);
$spectra_slug = 'ultimate-addons-for-gutenberg/ultimate-addons-for-gutenberg.php';
if (in_array($spectra_slug, $active_plugins)) {
  $profile['spectra']['active'] = true;
  $plugin_data = get_file_data(WP_PLUGIN_DIR . '/' . $spectra_slug, ['Version' => 'Version']);
  $profile['spectra']['version'] = $plugin_data['Version'] ?? null;
} else {
  $profile['blockers'][] = "Spectra plugin (ultimate-addons-for-gutenberg) is NOT activated. Install it from https://wordpress.org/plugins/ultimate-addons-for-gutenberg/ before using this skill.";
}

// === 3. Astra theme (optional, enables Customizer module) ===
if ($profile['theme']['slug'] === 'astra') {
  $profile['astra']['active'] = true;
  $profile['astra']['version'] = $profile['theme']['version'];

  // Detect current palette name (depuis astra-color-palettes pour l'UI Customizer)
  $color_palettes = get_option('astra-color-palettes');
  if (is_array($color_palettes) && isset($color_palettes['currentPalette'])) {
    $profile['astra']['current_palette'] = $color_palettes['currentPalette'];
  }

  // Détecter les couleurs RÉELLES de la palette active (depuis astra-settings, pilote frontend)
  $astra_settings = get_option('astra-settings');
  if (is_array($astra_settings) && isset($astra_settings['global-color-palette']['palette'])) {
    $profile['astra']['palette_colors'] = $astra_settings['global-color-palette']['palette'];
  }

  // Check Astra Pro
  if (in_array('astra-addon/astra-addon.php', $active_plugins)) {
    $profile['astra']['pro_active'] = true;
  }
} else {
  $profile['warnings'][] = "Astra theme not active. The Astra Customizer module will be disabled. Spectra-only mode (still functional). Current theme: " . $profile['theme']['name'];
  $profile['recommendations'][] = "For premium experience (palette pilotage, header/footer builder), install Astra theme: https://wordpress.org/themes/astra/";
}

// === 4. WP version check ===
if (version_compare($profile['wp_version'], '6.0', '<')) {
  $profile['blockers'][] = "WordPress " . $profile['wp_version'] . " < 6.0 (block editor v2 not available). Upgrade WordPress before using this skill.";
}

// === 5. PHP version check ===
if (version_compare($profile['php_version'], '7.4', '<')) {
  $profile['blockers'][] = "PHP " . $profile['php_version'] . " < 7.4 not supported. Upgrade PHP before using this skill.";
}

// === 6. Permalinks ===
$permalink_structure = get_option('permalink_structure');
$profile['permalinks']['structure'] = $permalink_structure ?: '(default ?p=N)';
$profile['permalinks']['ok'] = !empty($permalink_structure);
if (empty($permalink_structure)) {
  $profile['warnings'][] = "Permalinks set to default (?p=ID). Recommended: /%postname%/ for SEO. Settings > Permalinks.";
}

// === 7. REST API accessibility ===
// On présume que oui car ce script tourne dans WP, mais on pourrait tester /wp-json/wp/v2/types
$profile['rest_api_accessible'] = function_exists('rest_url');

// === 8. Verdict ===
if (!empty($profile['blockers'])) {
  $profile['verdict'] = 'BLOCKED';
} elseif (!empty($profile['warnings'])) {
  $profile['verdict'] = 'DEGRADED';
} else {
  $profile['verdict'] = 'GO';
}

// === 9. Output ===
// Guard headers_sent() pour éviter le warning "Cannot modify header information"
// quand le script est exécuté via wp eval-file (mode CLI : pas de headers HTTP).
if (php_sapi_name() !== 'cli' && !headers_sent()) {
  header('Content-Type: application/json; charset=utf-8');
}
echo json_encode($profile, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
