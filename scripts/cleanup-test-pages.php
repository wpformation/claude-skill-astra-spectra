<?php
/**
 * cleanup-test-pages.php
 *
 * Liste ou supprime les pages drafts dont le titre matche TEST/POC/DEMO/[skill]
 * (préfixes courants des pages générées pendant les tests du skill).
 *
 * 3 modes d'usage :
 *
 *   1. CLI direct depuis le dossier WP (auto-discovery wp-load.php) :
 *      cd /chemin/vers/wp && php /chemin/vers/skill/scripts/cleanup-test-pages.php list
 *
 *   2. CLI direct avec --wp-path explicite (depuis n'importe où) :
 *      php cleanup-test-pages.php list --wp-path=/var/www/monsite
 *      php cleanup-test-pages.php delete --confirm --wp-path=/var/www/monsite
 *
 *   3. Via WP-CLI eval-file (mode préféré, le plus robuste) :
 *      wp eval-file cleanup-test-pages.php list
 *
 *   4. require_once depuis un autre script (utiliser les fonctions seules) :
 *      // Pas d'auto-exécution du bloc CLI, juste les fonctions disponibles.
 *      require_once 'cleanup-test-pages.php';
 *      $pages = wpf_skill_find_test_pages();
 *
 * Options :
 *   --confirm                 (requis pour delete) confirme la suppression réelle
 *   --pattern='/^TEST /i'     regex personnalisée pour matcher les titres
 *   --wp-path=/path/to/wp     chemin explicite vers la racine WordPress
 *
 * Sans --confirm, le mode 'delete' fait un dry-run et liste seulement.
 */

// Helper : récupère les args CLI sous forme associative. Robuste à $argv null
// (cas wp eval-file qui ne pousse pas $argv dans le scope du script).
function wpf_skill_cleanup_parse_args() {
  if (!isset($GLOBALS['argv']) || !is_array($GLOBALS['argv'])) return [];
  $args = ['_positional' => []];
  foreach ($GLOBALS['argv'] as $i => $arg) {
    if ($i === 0) continue; // skip le nom du script
    if (preg_match('/^--(\w[\w-]*)(=(.*))?$/', $arg, $m)) {
      $args[$m[1]] = $m[3] ?? true;
    } else {
      $args['_positional'][] = $arg;
    }
  }
  return $args;
}

// Chargement WordPress avec --wp-path optionnel
if (!defined('ABSPATH')) {
  $cli_args = wpf_skill_cleanup_parse_args();
  $wp_path = $cli_args['wp-path'] ?? null;

  $wp_load_paths = [];
  if ($wp_path) $wp_load_paths[] = rtrim($wp_path, '/') . '/wp-load.php';
  $wp_load_paths = array_merge($wp_load_paths, [
    __DIR__ . '/../wp-load.php',
    __DIR__ . '/../../wp-load.php',
    __DIR__ . '/../../../wp-load.php',
    '/wordpress/wp-load.php',
  ]);
  foreach ($wp_load_paths as $p) {
    if (file_exists($p)) { require_once $p; break; }
  }
}

function wpf_skill_find_test_pages($pattern = '/^(TEST|POC|DEMO|\[skill\])/i') {
  if (!class_exists('WP_Query')) return [];
  $results = [];
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

// Bloc CLI : ne s'exécute QUE si le script est appelé directement en ligne de commande.
// Garde robuste : php_sapi_name() === 'cli' OU mode WP-CLI eval-file (qui peut faussement
// rapporter 'cli'), MAIS toujours vérifier que $argv existe avant de count() dessus,
// ET que le script appelé est bien le présent fichier (pas un require_once depuis ailleurs).
if (
  php_sapi_name() === 'cli'
  && isset($GLOBALS['argv'])
  && is_array($GLOBALS['argv'])
  && !empty($GLOBALS['argv'][0])
  && basename($GLOBALS['argv'][0]) === basename(__FILE__)
) {
  if (!defined('ABSPATH')) {
    echo json_encode(['error' => 'WordPress not loaded — provide --wp-path=/path/to/wp or run via wp eval-file from WP root.']);
    exit(1);
  }

  $args = wpf_skill_cleanup_parse_args();
  $cmd = $args['_positional'][0] ?? 'list';
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
    echo "Usage:\n";
    echo "  php cleanup-test-pages.php list [--pattern='/regex/i'] [--wp-path=/path/to/wp]\n";
    echo "  php cleanup-test-pages.php delete [--confirm] [--pattern='/regex/i'] [--wp-path=/path/to/wp]\n";
    echo "\nOu via WP-CLI : wp eval-file cleanup-test-pages.php list\n";
  }
}
