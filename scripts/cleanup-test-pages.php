<?php
/**
 * cleanup-test-pages.php
 *
 * Liste ou supprime les pages drafts dont le titre matche TEST/POC/DEMO/[skill]
 * (préfixes courants des pages générées pendant les tests du skill).
 *
 * Usage CLI :
 *   php cleanup-test-pages.php list
 *   php cleanup-test-pages.php delete --confirm
 *   php cleanup-test-pages.php delete --confirm --pattern='/^TEST /i'
 *
 * Sans --confirm, le mode 'delete' fait un dry-run et liste seulement.
 *
 * Pré-requis : run depuis un environnement WP avec wp-load.php accessible.
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

function wpf_skill_find_test_pages($pattern = '/^(TEST|POC|DEMO|\[skill\])/i') {
  $results = [];
  // Récupère les drafts (status: draft, future, pending) toutes pages confondues
  $args = [
    'post_type' => ['page', 'post'],
    'post_status' => ['draft', 'pending', 'future'],
    'posts_per_page' => -1,
    'orderby' => 'date',
    'order' => 'DESC',
  ];
  $query = new WP_Query($args);
  while ($query->have_posts()) {
    $query->the_post();
    $title = get_the_title();
    if (preg_match($pattern, $title)) {
      $results[] = [
        'id' => get_the_ID(),
        'title' => $title,
        'type' => get_post_type(),
        'status' => get_post_status(),
        'date' => get_the_date('c'),
        'edit_url' => admin_url("post.php?post=" . get_the_ID() . "&action=edit"),
      ];
    }
  }
  wp_reset_postdata();
  return $results;
}

function wpf_skill_delete_test_pages($ids, $force = true) {
  $deleted = [];
  $errors = [];
  foreach ($ids as $id) {
    $r = wp_delete_post($id, $force);
    if ($r === false) {
      $errors[] = ['id' => $id, 'error' => 'wp_delete_post returned false'];
    } else {
      $deleted[] = $id;
    }
  }
  return ['deleted' => $deleted, 'errors' => $errors];
}

if (php_sapi_name() === 'cli') {
  $cmd = $argv[1] ?? 'list';
  $args = [];
  for ($i = 2; $i < count($argv); $i++) {
    if (preg_match('/^--(\w+)(=(.+))?$/', $argv[$i], $m)) {
      $args[$m[1]] = $m[3] ?? true;
    }
  }

  $pattern = $args['pattern'] ?? '/^(TEST|POC|DEMO|\[skill\])/i';

  if ($cmd === 'list') {
    $pages = wpf_skill_find_test_pages($pattern);
    echo json_encode([
      'pattern' => $pattern,
      'count' => count($pages),
      'pages' => $pages,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  } elseif ($cmd === 'delete') {
    $pages = wpf_skill_find_test_pages($pattern);
    if (empty($pages)) {
      echo json_encode(['result' => 'no_pages_to_delete', 'pattern' => $pattern]);
      exit(0);
    }
    if (!isset($args['confirm'])) {
      echo json_encode([
        'result' => 'dry_run',
        'message' => 'Dry run — pass --confirm to actually delete.',
        'would_delete' => array_column($pages, 'id'),
        'pages' => $pages,
      ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
      exit(0);
    }
    $ids = array_column($pages, 'id');
    $r = wpf_skill_delete_test_pages($ids, true);
    echo json_encode([
      'result' => 'deleted',
      'count_deleted' => count($r['deleted']),
      'count_errors' => count($r['errors']),
      'deleted_ids' => $r['deleted'],
      'errors' => $r['errors'],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  } else {
    echo "Usage:\n  php cleanup-test-pages.php list\n  php cleanup-test-pages.php delete [--confirm] [--pattern='/regex/i']\n";
  }
}
