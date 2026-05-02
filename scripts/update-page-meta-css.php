<?php
/**
 * update-page-meta-css.php
 *
 * Update _uag_custom_page_level_css meta tag-aware : préserve le CSS user
 * existant en dehors des balises skill-managed.
 *
 * Pourquoi tag-aware : la session précédente a peut-être déjà injecté du CSS
 * dans ce meta. La session courante doit pouvoir REMPLACER son propre CSS
 * sans toucher au CSS user (custom modifications post-skill).
 *
 * Convention : encapsuler le CSS skill-generated entre 2 balises commentaires :
 *
 *   /* === skill-generated v1.0 START === * /
 *   ... CSS skill-managed ...
 *   /* === skill-generated v1.0 END === * /
 *
 * Le script remplace UNIQUEMENT cette section. Tout CSS hors balises = user, préservé.
 *
 * Usage CLI :
 *   php update-page-meta-css.php \
 *     --site-url=https://monsite.com \
 *     --user=admin \
 *     --app-password='xxxx xxxx xxxx xxxx xxxx xxxx' \
 *     --post-id=42 \
 *     --css-file=/path/to/overrides.css \
 *     [--skill-version=1.0]
 *
 * Output : JSON avec status, css_total_length, css_user_preserved_length
 */

function parse_args($argv) {
  $args = [];
  foreach ($argv as $arg) {
    if (preg_match('/^--([\w-]+)=(.*)$/', $arg, $m)) $args[$m[1]] = $m[2];
    elseif (preg_match('/^--([\w-]+)$/', $arg, $m)) $args[$m[1]] = true;
  }
  return $args;
}

function fail($msg, $details = null) {
  $out = ['success' => false, 'error' => $msg];
  if ($details !== null) $out['details'] = $details;
  echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit(1);
}

function http_request($method, $url, $headers, $body = null) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  if ($body !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err = curl_error($ch);
  curl_close($ch);
  return ['error' => $response === false ? $err : null, 'code' => $http_code, 'body' => $response];
}

/**
 * Fusionne le CSS skill-generated avec le CSS user existant.
 *
 * Stratégie :
 * 1. Si pas de balises START/END dans le CSS existant → append le skill CSS encapsulé
 * 2. Si balises présentes → remplacer SEULEMENT la section entre balises
 * 3. Préserver tout CSS hors balises (= user-managed)
 */
function wpf_skill_merge_css($existing_css, $new_skill_css, $version = '1.0') {
  $start_tag = "/* === skill-generated v{$version} START === */";
  $end_tag = "/* === skill-generated v{$version} END === */";

  // Pattern matching pour ANY version (pour migrer si version change)
  $any_version_pattern = '/\/\* === skill-generated v[0-9.]+ START === \*\/.*?\/\* === skill-generated v[0-9.]+ END === \*\//s';

  $skill_block = "{$start_tag}\n{$new_skill_css}\n{$end_tag}";

  if (preg_match($any_version_pattern, $existing_css)) {
    // Replace existing skill-managed section
    $merged = preg_replace($any_version_pattern, $skill_block, $existing_css);
  } else {
    // Append skill-managed section
    $sep = empty(trim($existing_css)) ? '' : "\n\n";
    $merged = $existing_css . $sep . $skill_block;
  }

  return $merged;
}

function main() {
  global $argv;
  $args = parse_args($argv);

  $required = ['site-url', 'user', 'app-password', 'post-id', 'css-file'];
  foreach ($required as $r) {
    if (empty($args[$r])) fail("Missing required arg --$r");
  }

  if (!file_exists($args['css-file'])) {
    fail("CSS file not found: {$args['css-file']}");
  }

  $new_skill_css = file_get_contents($args['css-file']);
  $skill_version = $args['skill-version'] ?? '1.0';

  $site_url = rtrim($args['site-url'], '/');
  if (!preg_match('/^https?:\/\//', $site_url)) $site_url = 'https://' . $site_url;
  $post_id = (int) $args['post-id'];

  $auth = base64_encode($args['user'] . ':' . $args['app-password']);
  $headers = [
    'Authorization: Basic ' . $auth,
    'Content-Type: application/json; charset=utf-8',
    'Accept: application/json',
    'User-Agent: claude-skill-astra-spectra/1.0',
  ];

  // 1. GET le meta existant
  $get_url = "$site_url/wp-json/wp/v2/pages/$post_id?context=edit&_fields=meta";
  $r = http_request('GET', $get_url, $headers);
  if ($r['code'] !== 200) {
    fail("Cannot read post meta (HTTP {$r['code']})", ['url' => $get_url, 'body' => $r['body']]);
  }
  $data = json_decode($r['body'], true);
  $existing_css = $data['meta']['_uag_custom_page_level_css'] ?? '';

  // 2. Merge tag-aware
  $merged_css = wpf_skill_merge_css($existing_css, $new_skill_css, $skill_version);

  // 3. POST update
  $upd_url = "$site_url/wp-json/wp/v2/pages/$post_id";
  $payload = json_encode([
    'meta' => ['_uag_custom_page_level_css' => $merged_css]
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  $r2 = http_request('POST', $upd_url, $headers, $payload);
  if ($r2['code'] !== 200) {
    fail("Failed to update meta (HTTP {$r2['code']})", ['response' => $r2['body']]);
  }
  $upd = json_decode($r2['body'], true);
  $final_css = $upd['meta']['_uag_custom_page_level_css'] ?? '';

  // Compute stats
  $user_preserved_length = strlen($existing_css) - (preg_match('/\/\* === skill-generated v[0-9.]+ START === \*\/.*?\/\* === skill-generated v[0-9.]+ END === \*\//s', $existing_css, $m) ? strlen($m[0]) : 0);

  echo json_encode([
    'success' => true,
    'post_id' => $post_id,
    'modified' => $upd['modified'],
    'css_total_length' => strlen($final_css),
    'skill_css_length' => strlen($new_skill_css),
    'user_css_preserved_length' => max(0, $user_preserved_length),
    'skill_version' => $skill_version,
  ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit(0);
}

if (
  php_sapi_name() === 'cli'
  && isset($GLOBALS['argv'])
  && is_array($GLOBALS['argv'])
  && !empty($GLOBALS['argv'][0])
  && basename($GLOBALS['argv'][0]) === basename(__FILE__)
) {
  main();
}
