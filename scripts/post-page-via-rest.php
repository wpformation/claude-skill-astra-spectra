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

/**
 * Déclenche la régénération CSS Spectra pour un post donné.
 *
 * Spectra peut stocker son CSS dynamique en mode 'file' (dans
 * /wp-content/uploads/uag-plugin/assets/uag-css-{post_id}.css). Quand un post est
 * créé via l'API REST publique, le hook save_post peut ne pas exécuter la
 * génération. Sans CSS, la page apparaît sans styles (pas de flex-grid, pas
 * de box-shadow, layout cassé).
 *
 * Cette fonction tente 3 approches dans l'ordre :
 *   1. POST /wp-json/astra-spectra/v1/regen-assets/{id} (endpoint mu-plugin compagnon)
 *   2. GET sur la page draft avec ?_uagb-regen=1 query param (déclenche le hook
 *      sur certaines configs Spectra qui écoutent template_redirect)
 *   3. Fallback : informe l'utilisateur que la régénération est manuelle
 *
 * Retourne un dict avec le statut de la tentative.
 */
function wpf_skill_trigger_spectra_assets_regen($site_url, $auth, $post_id) {
  $headers = [
    'Authorization: Basic ' . $auth,
    'Accept: application/json',
    'User-Agent: claude-skill-astra-spectra/0.9.0',
  ];

  // Tentative 1 : endpoint mu-plugin compagnon (si installé)
  $endpoint = "$site_url/wp-json/astra-spectra/v1/regen-assets/$post_id";
  $r = http_request('POST', $endpoint, $headers, json_encode(['post_id' => $post_id]));
  if ($r['code'] === 200) {
    return ['method' => 'mu-plugin-endpoint', 'status' => 'ok', 'http' => 200];
  }

  // Tentative 2 : trigger via GET sur la page draft authentifiée
  // (sur certaines configs, Spectra régénère au premier load)
  $preview_url = "$site_url/?p=$post_id&preview=true&_uagb_regen=1";
  $r2 = http_request('GET', $preview_url, $headers);
  $triggered = ($r2['code'] === 200 || $r2['code'] === 301 || $r2['code'] === 302);

  // Tentative 3 : suggérer la régénération manuelle
  return [
    'method' => 'best_effort',
    'status' => $triggered ? 'preview_loaded' : 'manual_required',
    'preview_http_code' => $r2['code'],
    'manual_command_wp_cli' => "wp eval 'if (class_exists(\"UAGB_Post_Assets\")) { (new UAGB_Post_Assets($post_id))->generate_assets(); }'",
    'manual_command_admin' => "Ouvrir la page dans Gutenberg (l'edit_url ci-dessus) et la sauvegarder une fois → Spectra régénère le CSS.",
    'note' => 'Si la preview frontend apparaît sans styles (flex-grid cassé, pas de border-radius), exécuter une des manual_command_* ci-dessus. Pour automatiser, déployer le mu-plugin compagnon décrit dans references/mu-plugin-companion.md.',
  ];
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

  // === Régénération CSS Spectra ===
  // Spectra a 2 modes pour générer le CSS :
  //   - inline : CSS dans <head>
  //   - file   : CSS dans /uploads/uag-plugin/assets/uag-css-{post_id}.css
  // Sur certains sites (mode file actif), wp_insert_post via REST ne déclenche
  // PAS automatiquement la génération. Résultat : la preview frontend affiche
  // le HTML brut sans le CSS dynamique → pas de border-radius, pas de
  // box-shadow, pas de flex-grid. La page paraît cassée.
  //
  // On déclenche la régénération en POST sur un endpoint custom (à exposer
  // côté serveur via mu-plugin compagnon) OU on signale à l'utilisateur que
  // la régénération doit être déclenchée manuellement.
  $assets_regen = wpf_skill_trigger_spectra_assets_regen($site_url, $auth, $data['id']);

  // Output succès
  $result = [
    'success' => true,
    'id' => $data['id'],
    'slug' => $data['slug'] ?? null,
    'link' => $data['link'] ?? null,
    'status' => $data['status'] ?? null,
    'edit_url' => "$site_url/wp-admin/post.php?post={$data['id']}&action=edit",
    'preview_url' => $data['link'] ?? null,
    'spectra_assets_regen' => $assets_regen,
  ];
  echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit(0);
}

// Bloc CLI : ne s'exécute QUE si le script est lancé en ligne de commande directe.
if (
  php_sapi_name() === 'cli'
  && isset($GLOBALS['argv'])
  && is_array($GLOBALS['argv'])
  && !empty($GLOBALS['argv'][0])
  && basename($GLOBALS['argv'][0]) === basename(__FILE__)
) {
  main();
}
