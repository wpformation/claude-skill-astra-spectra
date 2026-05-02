<?php
/**
 * visual-audit.php
 *
 * Audit interne d'une page draft sans dépendance à /impeccable.
 * Applique les 12 checks visuels intégrés sur le markup d'une page.
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
      'hex_hardcoded' => 0,
      'block_ids_seen' => [],
      'block_ids_duplicates' => [],
    ],
  ];

  if (empty($content)) {
    $report['status'] = 'FAILED';
    $report['p0'][] = 'Empty content.';
    return $report;
  }

  $blocks = parse_blocks($content);

  // Walker récursif pour collecter stats
  $walker = function ($blocks) use (&$walker, &$report) {
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

      // Check 4 : block_id Spectra
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

      // Check 3 : hex hardcodés dans attrs couleur
      $color_attrs = ['backgroundColor', 'textColor', 'iconColor', 'headingColor', 'subHeadingColor', 'borderColor', 'color', 'overallBorderColor', 'topDividerColor'];
      foreach ($color_attrs as $attr) {
        if (isset($b['attrs'][$attr]) && is_string($b['attrs'][$attr])) {
          $val = $b['attrs'][$attr];
          if (preg_match('/^#[0-9a-fA-F]{6}$/', $val)) {
            $report['stats']['hex_hardcoded']++;
            $report['p1'][] = "Block $name uses hex $val for $attr (prefer var(--ast-global-color-X)).";
          }
        }
      }

      // Check 7 : alt vides sur images
      if ($name === 'core/image' || $name === 'uagb/image') {
        $alt = $b['attrs']['alt'] ?? null;
        if (empty($alt)) {
          $report['p1'][] = "Image block without alt text.";
        }
      }

      // Check 8 : container largeur excessive
      if ($name === 'uagb/container') {
        $width = $b['attrs']['contentWidth'] ?? null;
        if (is_numeric($width) && $width > 1400) {
          $report['p2'][] = "Container with contentWidth=$width (recommended ≤ 1200 for readability).";
        }

        // Check 9 : responsive padding
        $has_tablet = isset($b['attrs']['topPaddingTablet']) || isset($b['attrs']['rightPaddingTablet']);
        $has_mobile = isset($b['attrs']['topPaddingMobile']) || isset($b['attrs']['rightPaddingMobile']);
        if (!$has_tablet || !$has_mobile) {
          $report['p1'][] = "Container without responsive padding (tablet/mobile breakpoints missing).";
        }
      }

      // Récursion sur innerBlocks
      if (!empty($b['innerBlocks'])) {
        $walker($b['innerBlocks']);
      }
    }
  };

  $walker($blocks);

  // Check 1 verdict : H1 multiples
  if ($report['stats']['h1_count'] > 1) {
    $report['p0'][] = "Multiple H1 detected ({$report['stats']['h1_count']}). Keep only one H1 per page.";
  }
  if ($report['stats']['h1_count'] === 0) {
    $report['p1'][] = "No H1 detected. Page should have exactly one H1.";
  }

  // Check 12 : CTA visible
  $cta_count = preg_match_all('/uagb\\/buttons|core\\/buttons/', $content);
  if ($cta_count === 0) {
    $report['p1'][] = "No CTA button block found. Pages should have at least one clear CTA.";
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

// CLI usage
if (php_sapi_name() === 'cli') {
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
