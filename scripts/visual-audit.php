<?php
/**
 * visual-audit.php
 *
 * Audit interne d'une page draft sans dépendance à /impeccable.
 * Applique 9 checks visuels intégrés sur le markup d'une page, dont un check
 * WCAG AA contraste qui résout les var(--ast-global-color-X) vers les hex
 * réels de la palette active pour détecter les problèmes de lisibilité.
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

// Charge resolve-palette.php pour avoir wpf_skill_get_active_palette + wpf_skill_contrast_ratio
$_resolve_palette_path = __DIR__ . '/resolve-palette.php';
if (file_exists($_resolve_palette_path)) require_once $_resolve_palette_path;

/**
 * Résout une couleur (hex direct ou var(--ast-global-color-X)) vers son hex
 * réel selon la palette active. Utilisé par le check WCAG.
 */
function wpf_skill_resolve_to_hex($color_value, $palette = null) {
  if (!is_string($color_value)) return null;
  $val = trim($color_value);
  if ($val === '') return null;

  // Hex direct
  if (preg_match('/^#[0-9a-fA-F]{3}$/', $val) || preg_match('/^#[0-9a-fA-F]{6}$/', $val)) {
    return strtolower($val);
  }

  // var(--ast-global-color-X)
  if (preg_match('/var\(--ast-global-color-(\d)\)/', $val, $m)) {
    if ($palette === null && function_exists('wpf_skill_get_active_palette')) {
      $palette = wpf_skill_get_active_palette();
    }
    if (!is_array($palette)) return null;
    $idx = (int) $m[1];
    return strtolower($palette[$idx] ?? '');
  }

  // rgb / rgba — tente d'extraire les composantes
  if (preg_match('/rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i', $val, $m)) {
    return sprintf('#%02x%02x%02x', (int) $m[1], (int) $m[2], (int) $m[3]);
  }

  return null;
}

/**
 * Calcule un ratio de contraste WCAG si resolve-palette.php n'est pas chargé.
 */
if (!function_exists('wpf_skill_contrast_ratio')) {
  function wpf_skill_contrast_ratio($hex1, $hex2) {
    $rl = function ($hex) {
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
    $l1 = $rl($hex1); $l2 = $rl($hex2);
    $lighter = max($l1, $l2); $darker = min($l1, $l2);
    return ($lighter + 0.05) / ($darker + 0.05);
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
      '9_wcag_aa_contrast',
      '10_section_bg_alternation',
    ],
    'wcag_violations' => [],
    'section_bgs' => [],
  ];

  if (empty($content)) {
    $report['status'] = 'FAILED';
    $report['p0'][] = 'Empty content.';
    return $report;
  }

  $blocks = parse_blocks($content);

  // Walker récursif AVEC PROPAGATION DE CURRENT_BG (v0.9.1).
  // $depth = 0 pour root containers, >0 pour nested.
  // $current_bg = hex du parent courant (ou null si pas de bg défini en amont).
  // $is_dark_context = true si le parent a un bg sombre, overlay sombre, image+overlay, etc.
  //                    Permet de whitelister #ffffff text_inverse legitime.
  $walker = function ($blocks, $depth = 0, $current_bg = null, $is_dark_context = false) use (&$walker, &$report) {
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

      // Détection bg contextuel pour le bloc courant (avant le check 3)
      // - Si le bloc lui-même a un bg → c'est son contexte ET celui de ses enfants
      // - Sinon, le contexte est hérité du parent (current_bg + is_dark_context)
      $own_bg = $b['attrs']['backgroundColor'] ?? null;
      $own_bg_type = $b['attrs']['backgroundType'] ?? null;
      $own_overlay_type = $b['attrs']['overlayType'] ?? null;
      $own_bg_image_color = $b['attrs']['backgroundImageColor'] ?? null;
      $own_gradient_c1 = $b['attrs']['backgroundGradientColor1'] ?? null;
      $own_gradient_c2 = $b['attrs']['backgroundGradientColor2'] ?? null;
      $has_image_bg = ($own_bg_type === 'image');
      $has_overlay = (!empty($own_overlay_type));
      $has_gradient_bg = ($own_bg_type === 'gradient');

      // Résout le bg du bloc courant (utilisé pour le check WCAG)
      $own_bg_resolved = $own_bg ? wpf_skill_resolve_to_hex($own_bg) : null;
      // Pour les bgs gradient/image+overlay, on prend la couleur dominante (color1 ou backgroundImageColor)
      if (!$own_bg_resolved && $has_gradient_bg && $own_gradient_c1) {
        $own_bg_resolved = wpf_skill_resolve_to_hex($own_gradient_c1);
      }
      if (!$own_bg_resolved && $has_image_bg && $own_bg_image_color) {
        $own_bg_resolved = wpf_skill_resolve_to_hex($own_bg_image_color);
      }

      // Le bg "effectif" pour les enfants = own_bg si défini, sinon current_bg hérité
      $effective_bg = $own_bg_resolved ?: $current_bg;
      // Le contexte dark se propage si :
      // - on est sur un bg sombre (luminance < 0.4)
      // - OU on a une image+overlay (assumé dark par défaut)
      // - OU on a un gradient avec color1 sombre
      $own_is_dark = false;
      if ($own_bg_resolved && function_exists('wpf_skill_palette_luminance')) {
        $lum = wpf_skill_palette_luminance($own_bg_resolved);
        $own_is_dark = ($lum < 0.4);
      } elseif ($own_bg_resolved) {
        // Heuristique fallback : hex sombre = somme des canaux faible
        $hex = ltrim($own_bg_resolved, '#');
        if (strlen($hex) === 6) {
          $r = hexdec(substr($hex, 0, 2));
          $g = hexdec(substr($hex, 2, 2));
          $b_ch = hexdec(substr($hex, 4, 2));
          $own_is_dark = (($r + $g + $b_ch) / 3 < 100);
        }
      }
      $effective_dark = $own_is_dark || $has_image_bg || $has_overlay || $is_dark_context;

      // Check 3 : couleurs hardcodées — AVEC WHITELIST CONTEXTUELLE (v0.9.1)
      // #ffffff sur context dark = legitime text_inverse, ne PAS flagger
      // Idem pour les colors text qui sont en #ffffff sur un container avec image+overlay parent
      $color_attrs = [
        'backgroundColor', 'textColor', 'iconColor', 'headingColor',
        'subHeadingColor', 'borderColor', 'color', 'overallBorderColor',
        'topDividerColor', 'bottomDividerColor',
        'boxShadowColor', 'boxShadowColorHover',
        'iconBgColor', 'iconHoverColor',
        'hoverBackgroundColor', 'hoverColor',
        'label_color', 'icon_color', 'icon_bg_color',
      ];
      $text_color_attrs = ['headingColor', 'subHeadingColor', 'descColor', 'color', 'textColor', 'label_color', 'iconColor', 'iconHoverColor'];

      foreach ($color_attrs as $attr) {
        if (isset($b['attrs'][$attr]) && wpf_skill_is_hardcoded_color($b['attrs'][$attr])) {
          $val = $b['attrs'][$attr];
          $val_lower = strtolower($val);
          $report['stats']['hardcoded_color_count']++;

          $is_box_shadow = (strpos($attr, 'boxShadow') !== false);
          $is_neutral_rgba = (strpos($val, 'rgba(0,0,0') === 0 || strpos($val, 'rgba(0, 0, 0') === 0);
          $is_neutral_hex = in_array($val_lower, ['#fff', '#ffffff', '#000', '#000000'], true);
          $is_text_attr = in_array($attr, $text_color_attrs, true);
          $is_white = in_array($val_lower, ['#fff', '#ffffff'], true);

          // WHITELIST v0.9.1 : #ffffff sur text attr dans un dark context = text_inverse legit
          if ($is_text_attr && $is_white && $effective_dark) {
            $report['p3'][] = "Block $name $attr=#ffffff (text_inverse legit on dark context — bg=$effective_bg).";
            continue;
          }
          // WHITELIST : #fafafa, #f5f5f5, #f9f9f9 sur backgroundColor sont des grays neutres acceptables
          $neutral_grays = ['#fafafa', '#f5f5f5', '#f9f9f9', '#f7f7f7', '#fcfcfc', '#e5e7eb', '#e5e5e5', '#eeeeee'];
          if ($attr === 'backgroundColor' && in_array($val_lower, $neutral_grays, true)) {
            $report['p3'][] = "Block $name backgroundColor=$val (neutral gray bg, acceptable per section-rhythm.md).";
            continue;
          }

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

      // Check 9 : WCAG AA contraste — AVEC PROPAGATION CURRENT_BG (v0.9.1)
      // Auparavant le check ne regardait que (text_color, $b['attrs']['backgroundColor']) sur le MÊME bloc.
      // Bug : un info-box enfant avec headingColor='#0F172A' dans un container parent backgroundColor='#0F172A'
      // n'était pas détecté car l'info-box lui-même n'a pas de backgroundColor.
      // Fix : utiliser $effective_bg (own_bg ou current_bg propagé).
      $bid = $b['attrs']['block_id'] ?? '(no block_id)';
      $check_bg = $effective_bg;
      // Si le bloc est sur un parent avec image+overlay, on prend la couleur d'overlay
      // (assumée sombre par défaut) pour le check
      if (!$check_bg && ($has_image_bg || $has_overlay) && $own_bg_image_color) {
        $check_bg = wpf_skill_resolve_to_hex($own_bg_image_color);
      }
      // Pour les blocs enfants d'un parent overlay/image, on hérite du current_bg parent
      // qui aura été calculé à partir du bg image color du root parent
      $text_attrs = ['headingColor', 'subHeadingColor', 'descColor', 'color', 'label_color'];
      foreach ($text_attrs as $tattr) {
        $text_color = $b['attrs'][$tattr] ?? null;
        if (!$check_bg || !$text_color) continue;
        $text_hex = wpf_skill_resolve_to_hex($text_color);
        if (!$text_hex) continue;

        $ratio = wpf_skill_contrast_ratio($check_bg, $text_hex);
        $is_heading = ($tattr === 'headingColor');
        $wcag_threshold = $is_heading ? 3.0 : 4.5;
        if ($ratio < $wcag_threshold) {
          $msg = sprintf(
            "Block %s (%s) : contraste %s (%s) sur bg hérité (%s) = %.2f:1 < %.1f:1 (WCAG AA %s).",
            $name, $bid, $tattr, $text_hex, $check_bg, $ratio, $wcag_threshold, $is_heading ? 'large text' : 'normal text'
          );
          if ($ratio < 1.5) {
            $report['p0'][] = "[ILLISIBLE] $msg";
          } else {
            $report['p1'][] = $msg;
          }
          $report['wcag_violations'][] = [
            'block' => $name,
            'block_id' => $bid,
            'text_attr' => $tattr,
            'text_color_resolved' => $text_hex,
            'background_resolved' => $check_bg,
            'background_inherited' => ($check_bg !== $own_bg_resolved),
            'ratio' => round($ratio, 2),
            'wcag_threshold' => $wcag_threshold,
          ];
        }
      }

      // Check 5 : container largeur excessive
      if ($name === 'uagb/container') {
        $width = $b['attrs']['contentWidth'] ?? null;
        if (is_numeric($width) && $width > 1400) {
          $report['p2'][] = "Container with contentWidth=$width (recommended ≤ 1200 for readability).";
        }

        // Check 10 : collecter les backgroundColor des root containers
        if ($depth === 0) {
          $bg_color = $b['attrs']['backgroundColor'] ?? null;
          $bg_type = $b['attrs']['backgroundType'] ?? 'none';
          $report['section_bgs'][] = [
            'block_id' => $b['attrs']['block_id'] ?? '(no block_id)',
            'background_type' => $bg_type,
            'background_color' => $bg_color,
            'background_resolved' => $bg_color ? wpf_skill_resolve_to_hex($bg_color) : null,
          ];
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

      // Récursion sur innerBlocks AVEC PROPAGATION du bg + dark context (v0.9.1)
      if (!empty($b['innerBlocks'])) {
        $walker($b['innerBlocks'], $depth + 1, $effective_bg, $effective_dark);
      }
    }
  };

  $walker($blocks, 0, null, false);

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

  // Check 10 : alternance bg entre root containers consécutifs
  $sections = $report['section_bgs'];
  for ($i = 1; $i < count($sections); $i++) {
    $prev = $sections[$i - 1];
    $curr = $sections[$i];
    $prev_resolved = $prev['background_resolved'];
    $curr_resolved = $curr['background_resolved'];
    // Si les deux sections ont un bg color et qu'il est identique → mur de blocs visuel
    if ($prev_resolved && $curr_resolved && strtolower($prev_resolved) === strtolower($curr_resolved)) {
      $report['p2'][] = sprintf(
        "Sections '%s' and '%s' both have backgroundColor=%s — they will visually merge. Alternate bgs between consecutive sections (e.g. #ffffff ↔ #fafafa) for clear hierarchy. See references/section-rhythm.md.",
        $prev['block_id'], $curr['block_id'], $prev_resolved
      );
    }
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
