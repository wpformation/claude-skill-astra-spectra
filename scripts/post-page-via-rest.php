<?php
/**
 * post-page-via-rest.php
 *
 * Crée une page draft sur un site WordPress via la REST API.
 * Gère l'authentification Application Password (Basic Auth), l'upload du content,
 * la définition de Yoast SEO meta optionnel, le retour de l'URL d'édition Gutenberg.
 *
 * Usage CLI :
 *   php post-page-via-rest.php \
 *     --site-url=https://monsite.com \
 *     --user=admin \
 *     --app-password='xxxx xxxx xxxx xxxx xxxx xxxx' \
 *     --title="Ma page" \
 *     --content-file=/tmp/markup.html \
 *     [--status=draft|publish] \
 *     [--slug=mon-slug] \
 *     [--yoast-title="..."] \
 *     [--yoast-desc="..."]
 *
 *   echo "<!-- wp:paragraph --><p>...</p>" | php post-page-via-rest.php \
 *     --site-url=... --user=... --app-password=... --title="..."
 *
 * Output : JSON avec id, slug, link, edit_url. Exit code 0 si succès, 1 si erreur.
 */

function parse_args($argv) {
  $args = [];
  foreach ($argv as $arg) {
    if (preg_match('/^--([\w-]+)=(.*)$/', $arg, $m)) {
      $args[$m[1]] = $m[2];
    } elseif (preg_match('/^--([\w-]+)$/', $arg, $m)) {
      $args[$m[1]] = true;
    }
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
  if ($body !== null) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  }
  $response = curl_exec($ch);
  $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  $err = curl_error($ch);
  curl_close($ch);

  if ($response === false) {
    return ['error' => $err, 'code' => 0, 'body' => null];
  }
  return ['error' => null, 'code' => $http_code, 'body' => $response];
}

function main() {
  global $argv;
  $args = parse_args($argv);

  // Validation des args obligatoires
  $required = ['site-url', 'user', 'app-password', 'title'];
  foreach ($required as $r) {
    if (empty($args[$r])) {
      fail("Missing required arg --$r. See header for usage.");
    }
  }

  // Récupération du content
  $content = '';
  if (!empty($args['content-file'])) {
    if (!file_exists($args['content-file'])) {
      fail("Content file not found: {$args['content-file']}");
    }
    $content = file_get_contents($args['content-file']);
  } else {
    $content = stream_get_contents(STDIN);
  }
  if (empty($content)) {
    fail("Empty content. Provide --content-file or pipe markup via stdin.");
  }

  // Normaliser l'URL du site
  $site_url = rtrim($args['site-url'], '/');
  if (!preg_match('/^https?:\/\//', $site_url)) {
    $site_url = 'https://' . $site_url;
  }

  // Tester l'API REST accessible
  $check = http_request('GET', "$site_url/wp-json/wp/v2/types", [], null);
  if ($check['code'] !== 200 && $check['code'] !== 401) {
    fail("REST API not accessible. HTTP code: {$check['code']}", [
      'url' => "$site_url/wp-json/wp/v2/types",
      'curl_error' => $check['error'],
    ]);
  }

  // Build payload
  $payload = [
    'title' => $args['title'],
    'content' => $content,
    'status' => $args['status'] ?? 'draft',
  ];
  if (!empty($args['slug'])) $payload['slug'] = $args['slug'];
  if (!empty($args['excerpt'])) $payload['excerpt'] = $args['excerpt'];

  // Yoast meta (si plugin actif sur le site)
  $meta = [];
  if (!empty($args['yoast-title'])) $meta['_yoast_wpseo_title'] = $args['yoast-title'];
  if (!empty($args['yoast-desc'])) $meta['_yoast_wpseo_metadesc'] = $args['yoast-desc'];
  if (!empty($args['yoast-focuskw'])) $meta['_yoast_wpseo_focuskw'] = $args['yoast-focuskw'];
  if (!empty($meta)) $payload['meta'] = $meta;

  // Auth
  $auth = base64_encode($args['user'] . ':' . $args['app-password']);
  $headers = [
    'Authorization: Basic ' . $auth,
    'Content-Type: application/json; charset=utf-8',
    'Accept: application/json',
    'User-Agent: claude-skill-astra-spectra/0.8.1',
  ];

  // POST
  $endpoint = "$site_url/wp-json/wp/v2/pages";
  $body_json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  $response = http_request('POST', $endpoint, $headers, $body_json);

  if ($response['error']) {
    fail("cURL error: {$response['error']}");
  }

  if ($response['code'] === 401) {
    // Pré-test : vérifier si c'est un strip d'Authorization header (Apache mutu)
    $diagnostic = "Authentication failed (401).\n\n";
    $diagnostic .= "Diagnostic possibles, du plus fréquent au moins fréquent :\n\n";
    $diagnostic .= "1. HÉBERGEMENT MUTUALISÉ APACHE (o2switch, OVH mutu, 1&1, Hostinger, etc.)\n";
    $diagnostic .= "   Ton serveur strippe probablement l'header Authorization avant WordPress.\n";
    $diagnostic .= "   Fix : ajouter dans .htaccess (racine WP) après 'RewriteEngine On' :\n";
    $diagnostic .= "     RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]\n\n";
    $diagnostic .= "   Test : curl -u 'user:pass' $site_url/wp-json/wp/v2/users/me\n";
    $diagnostic .= "   Si réponse = {\"code\":\"rest_not_logged_in\"}, c'est ce strip qui pose problème.\n\n";
    $diagnostic .= "2. APPLICATION PASSWORD INVALIDE\n";
    $diagnostic .= "   Recopier exactement (espaces compris) depuis /wp-admin/profile.php.\n";
    $diagnostic .= "   Format attendu : 'xxxx xxxx xxxx xxxx xxxx xxxx' (24 chars + 5 espaces).\n\n";
    $diagnostic .= "3. USERNAME INCORRECT\n";
    $diagnostic .= "   Souvent ton login admin (pas ton email). Visible dans /wp-admin/users.php.\n\n";
    $diagnostic .= "4. APPLICATION PASSWORDS DÉSACTIVÉS\n";
    $diagnostic .= "   Certains plugins de sécurité (Solid Security, Wordfence) ou mu-plugins\n";
    $diagnostic .= "   désactivent Application Passwords. Vérifier le profile.php.\n";
    fail($diagnostic);
  }
  if ($response['code'] === 403) {
    fail("Permission denied (403). The user '{$args['user']}' must have edit_pages capability.");
  }
  if ($response['code'] === 404) {
    fail("Endpoint not found (404). Is the REST API enabled? Try $endpoint in browser.");
  }
  if ($response['code'] >= 400) {
    $data = json_decode($response['body'], true);
    fail("HTTP error {$response['code']}", [
      'endpoint' => $endpoint,
      'response' => $data ?: $response['body'],
    ]);
  }

  $data = json_decode($response['body'], true);
  if (!$data || !isset($data['id'])) {
    fail("Invalid response from server.", ['raw' => $response['body']]);
  }

  // Output succès
  $result = [
    'success' => true,
    'id' => $data['id'],
    'slug' => $data['slug'] ?? null,
    'link' => $data['link'] ?? null,
    'status' => $data['status'] ?? null,
    'edit_url' => "$site_url/wp-admin/post.php?post={$data['id']}&action=edit",
    'preview_url' => $data['link'] ?? null,
  ];
  echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit(0);
}

if (php_sapi_name() === 'cli') {
  main();
}
