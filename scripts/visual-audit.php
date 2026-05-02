<?php
/**
 * visual-audit.php
 *
 * Audit interne d'une page draft sans dépendance à /impeccable.
 * Applique 8 checks visuels intégrés sur le markup d'une page.
 *
 * Usage CLI :
 *   php visual-audit.php <page_id>
 *   php visual-audit.php < markup.html
 *
 * Output : JSON avec verdict + liste de problèmes priorisés (P0/P1/P2/P3).
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
 * Détecte une couleur hardcodée (hex, rgb, rgba, hsl, hsla) qui n'utilise PAS
 * une variable CSS Astra (--ast-global-color-X).
 */
function wpf_skill_is_hardcoded_color($val) {
  if (!is_string($val) || $val === '') return false;
  // Si la valeur contient déjà une CSS var Astra → OK
  if (strpos($val, 'var(--ast-global-color') !== false) return false;
  // Hex 3 ou 6 chars : #fff ou #ffffff
  if (preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $val)) return true;
  // rgb(...) ou rgba(...)
  if (preg_match('/^rgba?\s*\(/i', $val)) return true;
  // hsl(...) ou hsla(...)
  if (preg_match('/^hsla?\s*\(/i', $val)) return true;
  return false;
}

function wpf_skill_visual_audit($content) {
  $report = [
    'status' => 'OK',
    'p0' => [],
    'p1' => [],
    'p2' => [],
    'p3' => [],
    'stats' => [
      'block_count' => 0,
      'h1_count' => 0,
      'h2_count' => 0,
      'h3_count' => 0,
      'hardcoded_color_count' => 0,
      'block_ids_seen' => [],
      'block_ids_duplicates' => [],
    ],
    'checks_run' => [
      '1_heading_hierarchy',
      '2_block_id_unique',
      '3_no_hardcoded_color',
      '4_image_alt',
      '5_container_max_width',
      '6_root_container_responsive_padding',
      '7_cta_present',
      '8_h1_present',
    ],
  ];

  if (empty($content)) {
    $report['status'] = 'FAILED';
    $report['p0'][] = 'Empty content.';
    return $report;
  }

  $blocks = parse_blocks($content);

  // Walker récursif. $depth=0 pour les containers racine, >0 pour les nested.
  $walker = function ($blocks, $depth = 0) use (&$walker, &$report) {
    foreach ($blocks as $b) {
      if (empty($b['blockName'])) continue;
      $report['stats']['block_count']++;

      $name = $b['blockName'];

      // Check 1 : hiérarchie titres
      if ($name === 'core/heading') {
        $level = $b['attrs']['level'] ?? 2;
        if ($level === 1) $report['stats']['h1_count']++;
        elseif ($level === 2) $report['stats']['h2_count']++;
        elseif ($level === 3) $report['stats']['h3_count']++;
      }
      if ($name === 'uagb/advanced-heading') {
        $tag = $b['attrs']['headingTag'] ?? 'h2';
        if ($tag === 'h1') $report['stats']['h1_count']++;
        elseif ($tag === 'h2') $report['stats']['h2_count']++;
        elseif ($tag === 'h3') $report['stats']['h3_count']++;
      }

      // Check 2 : block_id Spectra
      if (strpos($name, 'uagb/') === 0) {
        $bid = $b['attrs']['block_id'] ?? null;
        if (empty($bid)) {
          $report['p0'][] = "Block $name without block_id (Gutenberg may recompute and break rendering).";
        } else {
          if (in_array($bid, $report['stats']['block_ids_seen'], true)) {
            $report['stats']['block_ids_duplicates'][] = $bid;
            $report['p0'][] = "Duplicate block_id '$bid' on $name.";
          }
          $report['stats']['block_ids_seen'][] = $bid;
        }
      }

      // Check 3 : couleurs hardcodées (hex, rgb, rgba, hsl, hsla)
      // Étendu aux box-shadow et aux dividers couleur.
      $color_attrs = [
        'backgroundColor', 'textColor', 'iconColor', 'headingColor',
        'subHeadingColor', 'borderColor', 'color', 'overallBorderColor',
        'topDividerColor', 'bottomDividerColor',
        'boxShadowColor', 'boxShadowColorHover',
        'iconBgColor', 'iconHoverColor',
        'hoverBackgroundColor', 'hoverColor',
        'label_color', 'icon_color', 'icon_bg_color',
      ];
      foreach ($color_attrs as $attr) {
        if (isset($b['attrs'][$attr]) && wpf_skill_is_hardcoded_color($b['attrs'][$attr])) {
          $val = $b['attrs'][$attr];
          $report['stats']['hardcoded_color_count']++;
          // box-shadow rgba(0,0,0,...) très courant et acceptable (drop shadow neutre) → P3, pas P1
          $is_box_shadow = (strpos($attr, 'boxShadow') !== false);
          $is_neutral_rgba = (strpos($val, 'rgba(0,0,0') === 0 || strpos($val, 'rgba(0, 0, 0') === 0);
          $is_neutral_hex = in_array(strtolower($val), ['#fff', '#ffffff', '#000', '#000000'], true);

          if ($is_box_shadow && ($is_neutral_rgba || $is_neutral_hex)) {
            $report['p3'][] = "Block $name $attr=$val (neutral shadow, acceptable but consider opacity-only on token).";
          } elseif ($is_box_shadow) {
            $report['p1'][] = "Block $name uses colored shadow $val for $attr (prefer rgba(0,0,0,X) neutral or var(--ast-global-color-X)).";
          } else {
            $report['p1'][] = "Block $name uses hardcoded color $val for $attr (prefer var(--ast-global-color-X)).";
          }
        }
      }

      // Check 4 : alt vides sur images
      if ($name === 'core/image' || $name === 'uagb/image') {
        $alt = $b['attrs']['alt'] ?? null;
        if (empty($alt)) {
          $report['p1'][] = "Image block without alt text.";
        }
      }

      // Check 5 : container largeur excessive
      if ($name === 'uagb/container') {
        $width = $b['attrs']['contentWidth'] ?? null;
        if (is_numeric($width) && $width > 1400) {
          $report['p2'][] = "Container with contentWidth=$width (recommended ≤ 1200 for readability).";
        }

        // Check 6 : responsive padding — UNIQUEMENT sur containers racine (depth 0).
        // Les containers internes héritent souvent du padding parent en flex/grid layout
        // et n'ont pas besoin de leurs propres breakpoints responsive. Avant v0.8.2 ce
        // check était déclenché sur tous les containers → spam de faux positifs.
        if ($depth === 0) {
          $has_top_padding = isset($b['attrs']['topPaddingDesktop']) || isset($b['attrs']['topPaddingTablet']) || isset($b['attrs']['topPaddingMobile']);
          $has_tablet = isset($b['attrs']['topPaddingTablet']) || isset($b['attrs']['rightPaddingTablet']) || isset($b['attrs']['bottomPaddingTablet']);
          $has_mobile = isset($b['attrs']['topPaddingMobile']) || isset($b['attrs']['rightPaddingMobile']) || isset($b['attrs']['bottomPaddingMobile']);
          // On n'avertit que si le container racine a un padding desktop défini mais pas ses équivalents tablet/mobile
          if ($has_top_padding && (!$has_tablet || !$has_mobile)) {
            $bid = $b['attrs']['block_id'] ?? '(no block_id)';
            $report['p1'][] = "Root container '$bid' has desktop padding but missing tablet/mobile breakpoints.";
          }
        }
      }

      // Récursion sur innerBlocks (depth + 1 pour distinguer racine vs nested)
      if (!empty($b['innerBlocks'])) {
        $walker($b['innerBlocks'], $depth + 1);
      }
    }
  };

  $walker($blocks, 0);

  // Check 8 verdict : H1 multiples / absent
  if ($report['stats']['h1_count'] > 1) {
    $report['p0'][] = "Multiple H1 detected ({$report['stats']['h1_count']}). Keep only one H1 per page.";
  }
  if ($report['stats']['h1_count'] === 0) {
    $report['p1'][] = "No H1 detected. Page should have exactly one H1.";
  }

  // Check 7 : CTA visible (compté au niveau de la page entière, pas par section)
  $cta_count = preg_match_all('/uagb\\/buttons|core\\/buttons/', $content);
  if ($cta_count === 0) {
    $report['p1'][] = "No CTA button block found in the entire page. A landing page should have at least one clear CTA (uagb/buttons or core/buttons).";
  }

  // Verdict global
  if (!empty($report['p0'])) {
    $report['status'] = 'FAILED';
  } elseif (!empty($report['p1'])) {
    $report['status'] = 'WARNING';
  } else {
    $report['status'] = 'OK';
  }

  return $report;
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
  $input = '';
  if (isset($argv[1])) {
    if (is_numeric($argv[1])) {
      $post = get_post((int)$argv[1]);
      if (!$post) { echo json_encode(['status' => 'FAILED', 'error' => "Post {$argv[1]} not found."]); exit(1); }
      $input = $post->post_content;
    } elseif (file_exists($argv[1])) {
      $input = file_get_contents($argv[1]);
    } else {
      $input = $argv[1];
    }
  } else {
    $input = stream_get_contents(STDIN);
  }

  $r = wpf_skill_visual_audit($input);
  echo json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
