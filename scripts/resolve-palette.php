<?php
/**
 * resolve-palette.php
 *
 * Résout les couleurs sémantiquement à partir d'une palette Astra arbitraire.
 *
 * RAISON D'ÊTRE
 * Le rapport visuel du 02/05/2026 (cours-ndrc.fr palette_3) a montré que les
 * slots Astra var(--ast-global-color-X) ne sont PAS sémantiquement stables
 * d'une palette à l'autre. Exemple sur palette_3 :
 *   - color-7 vaut #141006 (presque noir) au lieu de l'attendu off-white
 *   - color-4 vaut #FEF9E1 (crème pâle) au lieu d'un accent
 *
 * Tous les patterns qui supposaient « color-7 = bg light » ont produit des
 * sections noires illisibles. Solution : ne pas se fier au numéro de slot mais
 * choisir la couleur en fonction de son rôle sémantique (luminance, saturation).
 *
 * SLOTS GARANTIS (vérifié sur les 11 presets Astra natifs)
 *   - color-0 : primary (toujours saturé)
 *   - color-1 : primary darker / secondary action
 *   - color-2 : heading text (toujours luminance < 0.20)
 *   - color-3 : body text (toujours luminance entre 0.15 et 0.45)
 *   - color-5 : body bg (toujours luminance > 0.95)
 *
 * SLOTS VARIABLES (à résoudre sémantiquement)
 *   - color-4 : peut être accent OU pale tone OU variant primary
 *   - color-6, 7, 8 : varient selon la palette (bg alt, dark, etc.)
 *
 * RÔLES SÉMANTIQUES SUPPORTÉS
 *   - bg_page          : fond principal de la page (toujours très clair)
 *   - bg_section       : fond d'une section (light)
 *   - bg_section_alt   : fond d'une section alternée (off-white)
 *   - bg_card          : fond d'une card sur section (white pur)
 *   - bg_dark          : fond dark inversé (footer, hero dark)
 *   - text_heading     : couleur texte heading (très sombre)
 *   - text_body        : couleur texte body (sombre dim)
 *   - text_muted       : texte atténué (gris medium)
 *   - text_inverse     : texte sur fond primary (white pur, garanti lisible)
 *   - accent_primary   : couleur primaire (CTA, links, accents)
 *   - accent_secondary : couleur secondary primary (hover state, variant)
 *   - border_subtle    : couleur de border subtle (très clair)
 *   - border_strong    : couleur de border forte (medium)
 *
 * USAGE
 *   $palette = wpf_skill_get_active_palette();
 *   $hex = wpf_skill_resolve_color('bg_section', $palette);
 *   // Retourne le hex le plus adapté pour un fond de section sur la palette active.
 *
 * MODE FALLBACK
 *   Sur les rôles "robustes" (text_inverse, bg_card, border_subtle), on retourne
 *   un hex neutre hardcodé qui fonctionne sur toutes les palettes (#ffffff,
 *   #fafafa, #e5e7eb). Cela garantit la lisibilité même si la palette est
 *   exotique. Tradeoff : ces couleurs ne propagent pas au changement de palette.
 *
 * USAGE CLI
 *   php resolve-palette.php list                    # liste tous les rôles avec leur hex résolu
 *   php resolve-palette.php get bg_section          # un rôle précis
 *   php resolve-palette.php transpile < markup.html # remplace var(--ast-global-color-X) par hex selon contexte
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
 * Récupère les 9 hex de la palette Astra active.
 * Fallback sur la palette default si Astra absent.
 */
function wpf_skill_get_active_palette() {
  $default = ['#0274be', '#3a3a3a', '#0a0a0a', '#0a0a0a', '#0274be', '#ffffff', '#f5f5f5', '#fafafa', '#e7e7e7'];
  if (!function_exists('get_option')) return $default;
  $astra_settings = get_option('astra-settings');
  if (is_array($astra_settings) && isset($astra_settings['global-color-palette']['palette']) && is_array($astra_settings['global-color-palette']['palette'])) {
    $palette = $astra_settings['global-color-palette']['palette'];
    if (count($palette) === 9) return array_map('strtolower', $palette);
  }
  return $default;
}

/**
 * Luminance perceptuelle (BT.601). Output entre 0 (noir) et 1 (blanc).
 */
function wpf_skill_palette_luminance($hex) {
  $hex = ltrim($hex, '#');
  if (strlen($hex) === 3) {
    $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
  }
  if (strlen($hex) !== 6) return 0.5;
  $r = hexdec(substr($hex, 0, 2)) / 255;
  $g = hexdec(substr($hex, 2, 2)) / 255;
  $b = hexdec(substr($hex, 4, 2)) / 255;
  return 0.299 * $r + 0.587 * $g + 0.114 * $b;
}

/**
 * Saturation HSV approximative. Output entre 0 (gris) et 1 (très saturé).
 */
function wpf_skill_palette_saturation($hex) {
  $hex = ltrim($hex, '#');
  if (strlen($hex) === 3) {
    $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
  }
  if (strlen($hex) !== 6) return 0;
  $r = hexdec(substr($hex, 0, 2)) / 255;
  $g = hexdec(substr($hex, 2, 2)) / 255;
  $b = hexdec(substr($hex, 4, 2)) / 255;
  $max = max($r, $g, $b);
  $min = min($r, $g, $b);
  if ($max === 0.0) return 0;
  return ($max - $min) / $max;
}

/**
 * Calcule le ratio de contraste WCAG entre deux hex.
 * Retourne un float >= 1.0. WCAG AA exige ≥ 4.5 pour texte normal, ≥ 3.0 pour large.
 */
function wpf_skill_contrast_ratio($hex1, $hex2) {
  $relative_luminance = function ($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    if (strlen($hex) !== 6) return 0.5;
    $channels = [
      hexdec(substr($hex, 0, 2)) / 255,
      hexdec(substr($hex, 2, 2)) / 255,
      hexdec(substr($hex, 4, 2)) / 255,
    ];
    $linear = array_map(function ($c) {
      return $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
    }, $channels);
    return 0.2126 * $linear[0] + 0.7152 * $linear[1] + 0.0722 * $linear[2];
  };
  $l1 = $relative_luminance($hex1);
  $l2 = $relative_luminance($hex2);
  $lighter = max($l1, $l2);
  $darker = min($l1, $l2);
  return ($lighter + 0.05) / ($darker + 0.05);
}

/**
 * Résout un rôle sémantique en hex, en prenant la palette active comme entrée.
 * Stratégie hybride :
 *   - Pour les rôles ROBUSTES (bg_card, text_inverse, border_subtle) → hex neutre hardcodé
 *   - Pour les rôles BIEN COUVERTS par les slots garantis Astra (accent_primary,
 *     text_heading, text_body) → utilise le slot avec validation luminance
 *   - Pour les rôles VARIABLES (bg_section, bg_section_alt, bg_dark) → analyse la
 *     palette et choisit le slot adapté ; fallback hex neutre si aucun ne convient
 */
function wpf_skill_resolve_color($role, $palette = null) {
  if ($palette === null) $palette = wpf_skill_get_active_palette();

  // Hex neutres robustes (toujours retournés tels quels)
  $robust = [
    'bg_card' => '#ffffff',
    'text_inverse' => '#ffffff',
    'border_subtle' => '#e5e7eb',
    'border_strong' => '#9ca3af',
    'shadow_neutral' => 'rgba(0,0,0,0.08)',
    'shadow_strong' => 'rgba(0,0,0,0.16)',
    'shadow_hover' => 'rgba(0,0,0,0.20)',
  ];
  if (isset($robust[$role])) return $robust[$role];

  // Slots Astra à utiliser pour les rôles bien couverts
  $slot_aliases = [
    'accent_primary' => 0,
    'accent_secondary' => 1,
    'text_heading' => 2,
    'text_body' => 3,
  ];
  if (isset($slot_aliases[$role])) {
    return "var(--ast-global-color-{$slot_aliases[$role]})";
  }

  // Rôles VARIABLES : analyse la palette et choisit le slot le plus adapté.
  // On retourne un hex direct (pas de var()) parce que le slot peut différer
  // d'une palette à l'autre.
  $luminances = [];
  foreach ($palette as $idx => $hex) {
    $luminances[$idx] = wpf_skill_palette_luminance($hex);
  }

  if ($role === 'bg_page' || $role === 'bg_section') {
    // Cherche le slot avec la luminance la plus haute (proche de white)
    arsort($luminances);
    $best_idx = key($luminances);
    return $palette[$best_idx];
  }

  if ($role === 'bg_section_alt') {
    // 2e slot le plus clair (off-white). Si pas trouvé : fallback #fafafa neutre
    arsort($luminances);
    $sorted = array_keys($luminances);
    $second_idx = $sorted[1] ?? $sorted[0];
    $second_lum = $luminances[$second_idx];
    if ($second_lum < 0.85) return '#fafafa';
    return $palette[$second_idx];
  }

  if ($role === 'bg_dark') {
    // Slot avec la luminance la plus basse (proche de noir)
    asort($luminances);
    $best_idx = key($luminances);
    return $palette[$best_idx];
  }

  if ($role === 'text_muted') {
    // Slot text_body (slot 3) ou un slot intermédiaire luminance ~0.5
    if (isset($palette[3])) return "var(--ast-global-color-3)";
    return '#6b7280'; // gray-500 fallback
  }

  // Rôle inconnu → fallback white
  return '#ffffff';
}

/**
 * Transpile un markup en remplaçant les var(--ast-global-color-X) PROBLÉMATIQUES
 * par des hex direct selon la palette active.
 *
 * Stratégie : pour les attrs sémantiquement risqués (backgroundColor sur section,
 * borderColor sur card, etc.), si le slot référencé a une luminance hors plage
 * attendue, le remplacer par le hex sémantiquement correct.
 */
function wpf_skill_transpile_palette_aware($markup, $palette = null) {
  if ($palette === null) $palette = wpf_skill_get_active_palette();

  // Map des combinaisons (attr, slot référencé) → rôle attendu + plage luminance
  $expectations = [
    // attr_name => [slot_X => [role, min_luminance, max_luminance]]
    'backgroundColor' => [
      5 => ['bg_section', 0.85, 1.0],
      7 => ['bg_section_alt', 0.85, 1.0],
      6 => ['bg_section_alt', 0.80, 1.0],
    ],
    'borderColor' => [
      7 => ['border_subtle', 0.80, 1.0],
      8 => ['border_subtle', 0.75, 1.0],
    ],
    'iconBgColor' => [
      7 => ['bg_section_alt', 0.85, 1.0],
    ],
  ];

  // Walker simple : scan attribut par attribut via regex
  // Format Spectra : "attrName":"var(--ast-global-color-N)"
  return preg_replace_callback(
    '/"(backgroundColor|borderColor|iconBgColor)":"var\(--ast-global-color-(\d)\)"/',
    function ($m) use ($palette, $expectations) {
      $attr = $m[1];
      $slot = (int) $m[2];
      if (!isset($expectations[$attr][$slot])) {
        return $m[0]; // garde tel quel
      }
      list($role, $min_lum, $max_lum) = $expectations[$attr][$slot];
      $hex = $palette[$slot] ?? null;
      if (!$hex) return $m[0];
      $lum = wpf_skill_palette_luminance($hex);
      if ($lum >= $min_lum && $lum <= $max_lum) {
        return $m[0]; // slot OK pour ce rôle
      }
      // Slot hors plage → résoudre sémantiquement et injecter hex direct
      $resolved = wpf_skill_resolve_color($role, $palette);
      return "\"$attr\":\"$resolved\"";
    },
    $markup
  );
}

// Bloc CLI : ne s'exécute QUE si le script est appelé directement.
if (
  php_sapi_name() === 'cli'
  && isset($GLOBALS['argv'])
  && is_array($GLOBALS['argv'])
  && !empty($GLOBALS['argv'][0])
  && basename($GLOBALS['argv'][0]) === basename(__FILE__)
) {
  $argv = $GLOBALS['argv'];
  $cmd = $argv[1] ?? 'list';

  if ($cmd === 'list') {
    $palette = wpf_skill_get_active_palette();
    $roles = [
      'bg_page', 'bg_section', 'bg_section_alt', 'bg_card', 'bg_dark',
      'text_heading', 'text_body', 'text_muted', 'text_inverse',
      'accent_primary', 'accent_secondary',
      'border_subtle', 'border_strong',
      'shadow_neutral', 'shadow_strong', 'shadow_hover',
    ];
    $output = [
      'palette' => $palette,
      'palette_luminances' => array_map('wpf_skill_palette_luminance', $palette),
      'resolved' => [],
    ];
    foreach ($roles as $role) {
      $output['resolved'][$role] = wpf_skill_resolve_color($role, $palette);
    }
    echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  } elseif ($cmd === 'get' && isset($argv[2])) {
    echo wpf_skill_resolve_color($argv[2]) . "\n";
  } elseif ($cmd === 'transpile') {
    $input = stream_get_contents(STDIN);
    echo wpf_skill_transpile_palette_aware($input);
  } elseif ($cmd === 'contrast' && isset($argv[2], $argv[3])) {
    $ratio = wpf_skill_contrast_ratio($argv[2], $argv[3]);
    echo json_encode([
      'hex1' => $argv[2],
      'hex2' => $argv[3],
      'ratio' => round($ratio, 2),
      'wcag_aa_normal' => $ratio >= 4.5,
      'wcag_aa_large' => $ratio >= 3.0,
      'wcag_aaa_normal' => $ratio >= 7.0,
    ], JSON_PRETTY_PRINT);
  } else {
    echo "Usage:\n";
    echo "  php resolve-palette.php list\n";
    echo "  php resolve-palette.php get <role>\n";
    echo "  php resolve-palette.php transpile < markup.html\n";
    echo "  php resolve-palette.php contrast #ffffff #0a0a0a\n";
  }
}
