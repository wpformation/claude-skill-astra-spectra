<?php
/**
 * snapshot-page.php
 *
 * Récupère le snapshot complet d'une page existante (pour le workflow refonte) :
 *  - Block markup actuel
 *  - Inventaire des blocs présents
 *  - Métadonnées (title, slug, excerpt, featured image, parent, template)
 *  - Yoast SEO data si plugin actif
 *
 * Usage CLI :
 *   wp eval-file snapshot-page.php 123
 *
 * Usage REST API :
 *   GET /wp-json/astra-spectra/v1/snapshot/123 (si tu exposes ce endpoint via mu-plugin)
 *
 * Output :
 *   {
 *     "id": 123,
 *     "title": "...",
 *     "slug": "...",
 *     "permalink": "...",
 *     "status": "publish",
 *     "content_raw": "<!-- wp:... -->...",
 *     "content_length": 12345,
 *     "block_count": 18,
 *     "block_inventory": { "uagb/container": 2, "core/paragraph": 5, ... },
 *     "uses_spectra": true,
 *     "featured_image": { "id": 456, "url": "...", "alt": "..." },
 *     "parent_id": 0,
 *     "page_template": "default",
 *     "yoast": { "title": "...", "metadesc": "...", "focuskw": "..." },
 *     "menu_order": 0,
 *     "modified": "2026-05-02T10:00:00"
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

function wpf_skill_snapshot_page($post_id) {
  $post = get_post($post_id);
  if (!$post) {
    return ['error' => "Post $post_id not found."];
  }

  $blocks = parse_blocks($post->post_content);
  $inventory = [];
  $uses_spectra = false;

  $walker = function ($blocks) use (&$walker, &$inventory, &$uses_spectra) {
    foreach ($blocks as $b) {
      if (empty($b['blockName'])) continue;
      $inventory[$b['blockName']] = ($inventory[$b['blockName']] ?? 0) + 1;
      if (strpos($b['blockName'], 'uagb/') === 0) $uses_spectra = true;
      if (!empty($b['innerBlocks'])) $walker($b['innerBlocks']);
    }
  };
  $walker($blocks);

  $featured = [];
  $thumb_id = get_post_thumbnail_id($post_id);
  if ($thumb_id) {
    $featured = [
      'id' => $thumb_id,
      'url' => wp_get_attachment_url($thumb_id),
      'alt' => get_post_meta($thumb_id, '_wp_attachment_image_alt', true),
    ];
  }

  $yoast = [];
  if (defined('WPSEO_VERSION')) {
    $yoast = [
      'title' => get_post_meta($post_id, '_yoast_wpseo_title', true),
      'metadesc' => get_post_meta($post_id, '_yoast_wpseo_metadesc', true),
      'focuskw' => get_post_meta($post_id, '_yoast_wpseo_focuskw', true),
    ];
  }

  return [
    'id' => $post_id,
    'title' => $post->post_title,
    'slug' => $post->post_name,
    'permalink' => get_permalink($post_id),
    'status' => $post->post_status,
    'post_type' => $post->post_type,
    'content_raw' => $post->post_content,
    'content_length' => strlen($post->post_content),
    'block_count' => array_sum($inventory),
    'block_inventory' => $inventory,
    'uses_spectra' => $uses_spectra,
    'featured_image' => $featured,
    'parent_id' => $post->post_parent,
    'page_template' => get_page_template_slug($post_id) ?: 'default',
    'yoast' => $yoast,
    'menu_order' => $post->menu_order,
    'modified' => $post->post_modified_gmt,
    'excerpt' => $post->post_excerpt,
  ];
}

if (php_sapi_name() === 'cli' && isset($argv[1])) {
  $r = wpf_skill_snapshot_page((int)$argv[1]);
  echo json_encode($r, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
