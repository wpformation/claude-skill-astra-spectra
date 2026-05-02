<?php
/**
 * run-evals.php
 *
 * Lance la suite d'évals automatisées définie dans evals.json.
 * Pour chaque eval, vérifie les assertions et logue les résultats.
 *
 * Usage CLI :
 *   php run-evals.php                    # toutes les évals
 *   php run-evals.php --category=build   # filtrer par catégorie
 *   php run-evals.php --id=build-01-page-formation
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

require_once __DIR__ . '/../scripts/validate-block-markup.php';
require_once __DIR__ . '/../scripts/visual-audit.php';
require_once __DIR__ . '/../scripts/auto-fix-markup.php';

function run_evals($args = []) {
  $evals_data = json_decode(file_get_contents(__DIR__ . '/evals.json'), true);
  $filter_category = $args['category'] ?? null;
  $filter_id = $args['id'] ?? null;

  $results = [
    'version' => $evals_data['version'],
    'started_at' => date('c'),
    'evals' => [],
    'summary' => [ 'total' => 0, 'passed' => 0, 'failed' => 0, 'skipped' => 0 ],
  ];

  foreach ($evals_data['evals'] as $eval) {
    if ($filter_id && $eval['id'] !== $filter_id) continue;
    if ($filter_category && $eval['category'] !== $filter_category) continue;

    $results['summary']['total']++;
    $r = run_single_eval($eval);
    $results['evals'][] = $r;

    if ($r['status'] === 'PASS') $results['summary']['passed']++;
    elseif ($r['status'] === 'FAIL') $results['summary']['failed']++;
    else $results['summary']['skipped']++;
  }

  $results['ended_at'] = date('c');
  return $results;
}

function run_single_eval($eval) {
  $start = microtime(true);
  $r = [
    'id' => $eval['id'],
    'category' => $eval['category'],
    'status' => 'SKIP',
    'duration_ms' => 0,
    'assertions_passed' => 0,
    'assertions_failed' => 0,
    'failures' => [],
    'note' => '',
  ];

  // Évals de validation et auto-fix sont les seules entièrement testables sans live LLM
  if ($eval['category'] === 'validation' && isset($eval['input_file'])) {
    $file = __DIR__ . '/' . $eval['input_file'];
    if (!file_exists($file)) {
      $r['note'] = "Fixture introuvable : $file";
      $r['duration_ms'] = round((microtime(true) - $start) * 1000);
      return $r;
    }

    $content = file_get_contents($file);
    $validation = wpf_skill_validate_markup($content);

    foreach ($eval['assertions'] as $a) {
      $passed = false;
      $detail = '';

      if ($a['type'] === 'validator_status') {
        $expected = $a['expected'];
        $actual = $validation['valid'] ? 'OK' : 'FAILED';
        $passed = ($actual === $expected);
        $detail = "validator_status expected=$expected actual=$actual";
      } elseif ($a['type'] === 'errors_contain') {
        $needle = $a['value'];
        $passed = false;
        foreach ($validation['errors'] as $err) {
          if (stripos($err, $needle) !== false) { $passed = true; break; }
        }
        $detail = "errors_contain '$needle' → " . ($passed ? 'YES' : 'NO');
      } elseif ($a['type'] === 'warnings_contain') {
        $needle = $a['value'];
        $passed = false;
        foreach ($validation['warnings'] as $w) {
          if (stripos($w, $needle) !== false) { $passed = true; break; }
        }
        $detail = "warnings_contain '$needle' → " . ($passed ? 'YES' : 'NO');
      } elseif ($a['type'] === 'fixes_count') {
        $fix = wpf_skill_apply_fixes($content);
        $passed = $fix['fixes_count'] >= ($a['min'] ?? 0);
        $detail = "fixes_count={$fix['fixes_count']} (expected min " . ($a['min'] ?? 0) . ")";
      } elseif ($a['type'] === 'post_fix_validator_status') {
        $fix = wpf_skill_apply_fixes($content);
        $post_validation = wpf_skill_validate_markup($fix['content']);
        $expected = $a['expected'];
        $actual = $post_validation['valid'] ? 'OK' : 'FAILED';
        $passed = ($actual === $expected);
        $detail = "post_fix_validator_status expected=$expected actual=$actual";
      }

      if ($passed) $r['assertions_passed']++;
      else { $r['assertions_failed']++; $r['failures'][] = $detail; }
    }

    $r['status'] = $r['assertions_failed'] === 0 ? 'PASS' : 'FAIL';
  }

  // Évals build/refonte/template/astra : nécessitent un appel LLM réel ou un setup live
  // → marquées SKIP avec note explicative pour exécution manuelle
  elseif (in_array($eval['category'], ['build', 'refonte', 'template', 'astra'], true)) {
    $r['note'] = 'Eval nécessite invocation skill réelle (LLM + WP live). À tester manuellement avec /astra-spectra.';
  }

  $r['duration_ms'] = round((microtime(true) - $start) * 1000);
  return $r;
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
  $args = [];
  foreach ($argv as $arg) {
    if (preg_match('/^--(\w+)=(.+)$/', $arg, $m)) $args[$m[1]] = $m[2];
  }

  $results = run_evals($args);
  echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

  exit($results['summary']['failed'] === 0 ? 0 : 1);
}
