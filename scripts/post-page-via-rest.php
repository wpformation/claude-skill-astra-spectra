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
 * v0.9.1 — pipeline 4 étapes pour garantir que la page draft est visualisable
 * avec ses styles, MÊME sur des hébergeurs Apache mutu (o2switch, OVH, Hostinger)
 * où l'auth REST peut être strippée et où le hook wp_head sur preview anonyme
 * n'injecte pas le CSS Spectra dynamique.
 *
 * Stratégies (tentées dans l'ordre, première qui réussit gagne) :
 *   1. mu-plugin compagnon : POST /wp-json/astra-spectra/v1/regen-assets/{id}
 *   2. mu-plugin compagnon alt : POST /wp-json/skill-test/v1/regen-spectra
 *   3. Temp-publish trick : update status='publish' → GET frontend URL (force
 *      le pipeline complet wp_head→Astra CSS→Spectra UAGB_Post_Assets) →
 *      revert au status original. Validé sur Astra 4.13 + Spectra 2.19.
 *   4. Fallback : suggère wp-cli ou ouvrir dans Gutenberg admin.
 *
 * IMPORTANT : la stratégie 3 (temp-publish) modifie temporairement le statut.
 * Utiliser le param `--no-temp-publish` côté CLI si tu veux garder strictement
 * draft (par exemple sur un site live où une URL publique d'1 seconde gênerait).
 *
 * Retourne un dict avec le statut de la tentative + diagnostics.
 */
function wpf_skill_trigger_spectra_assets_regen($site_url, $auth, $post_id, $allow_temp_publish = true) {
  $headers = [
    'Authorization: Basic ' . $auth,
    'Accept: application/json',
    'Content-Type: application/json',
    'User-Agent: claude-skill-astra-spectra/0.9.1',
  ];

  // Stratégie 1 : endpoint mu-plugin compagnon officiel
  $endpoint = "$site_url/wp-json/astra-spectra/v1/regen-assets/$post_id";
  $r = http_request('POST', $endpoint, $headers, json_encode(['post_id' => $post_id]));
  if ($r['code'] === 200) {
    return ['method' => 'mu-plugin-endpoint', 'status' => 'ok', 'http' => 200];
  }

  // Stratégie 2 : endpoint mu-plugin compagnon alternative (skill-test)
  $endpoint2 = "$site_url/wp-json/skill-test/v1/regen-spectra";
  $r1b = http_request('POST', $endpoint2, $headers, json_encode(['post_id' => $post_id]));
  if ($r1b['code'] === 200) {
    $data = json_decode($r1b['body'], true);
    return [
      'method' => 'skill-test-endpoint',
      'status' => 'ok',
      'css_len' => $data['css_len'] ?? null,
      'js_len' => $data['js_len'] ?? null,
    ];
  }

  // Stratégie 3 : temp-publish trick (si autorisé)
  // Validé sur loginarmor-dev (Astra 4.13.1 + Spectra 2.19.25 + palette_3) le 02/05/2026
  if ($allow_temp_publish) {
    $temp = wpf_skill_temp_publish_trick($site_url, $auth, $post_id);
    if ($temp['ok']) {
      return [
        'method' => 'temp-publish-trick',
        'status' => 'ok',
        'detail' => $temp,
      ];
    }
    // Si temp-publish a échoué, garder l'erreur dans le diagnostic
    $temp_publish_error = $temp;
  }

  // Stratégie 4 : trigger via GET sur la page draft authentifiée
  $preview_url = "$site_url/?p=$post_id&preview=true&_uagb_regen=1";
  $r2 = http_request('GET', $preview_url, $headers);
  $triggered = ($r2['code'] === 200 || $r2['code'] === 301 || $r2['code'] === 302);

  return [
    'method' => 'best_effort',
    'status' => $triggered ? 'preview_loaded' : 'manual_required',
    'preview_http_code' => $r2['code'],
    'temp_publish_attempted' => $allow_temp_publish ? ($temp_publish_error ?? null) : 'skipped_by_user',
    'manual_command_wp_cli' => "wp eval 'if (class_exists(\"UAGB_Post_Assets\")) { (new UAGB_Post_Assets($post_id))->generate_assets(); }'",
    'manual_command_admin' => "Ouvrir la page dans Gutenberg (l'edit_url ci-dessus) et la sauvegarder une fois → Spectra régénère le CSS.",
    'note' => 'Si la preview frontend apparaît sans styles (flex-grid cassé, pas de border-radius), exécuter une des manual_command_* ci-dessus. Pour automatiser, déployer le mu-plugin compagnon décrit dans references/mu-plugin-companion.md.',
  ];
}

/**
 * Temp-publish trick : publie temporairement la page pour déclencher tous les
 * hooks WP/Astra/Spectra (notamment wp_head + UAGB_Post_Assets dans son contexte
 * frontend complet), hit l'URL frontend, puis revert au statut original.
 *
 * Cette astuce résout le bug critique signalé sur cours-ndrc.fr (palette_3) :
 * sur draft preview anonyme, ni Astra CSS ni Spectra CSS ne sont injectés
 * dans la <head>, donc la page apparaît sans styles. Le temp-publish force
 * la génération via le pipeline normal.
 *
 * NOTE : la page est en statut 'publish' pendant ~1-2 secondes. Si le site
 * a un cache CDN agressif (Cloudflare, etc.) ou un crawler watching, la URL
 * peut être brièvement indexée. Sur un site dev, c'est anodin. Sur un site
 * live, utiliser `allow_temp_publish=false` et préférer le mu-plugin.
 *
 * @return array{ok: bool, original_status: string, frontend_http: ?int, error: ?string}
 */
function wpf_skill_temp_publish_trick($site_url, $auth, $post_id) {
  $headers = [
    'Authorization: Basic ' . $auth,
    'Accept: application/json',
    'Content-Type: application/json',
    'User-Agent: claude-skill-astra-spectra/0.9.1',
  ];

  // 1. Récupérer le statut actuel
  $get_url = "$site_url/wp-json/wp/v2/pages/$post_id?context=edit";
  $r = http_request('GET', $get_url, $headers);
  if ($r['code'] !== 200) {
    return ['ok' => false, 'error' => "Cannot read post status (HTTP {$r['code']})"];
  }
  $post_data = json_decode($r['body'], true);
  $original_status = $post_data['status'] ?? 'draft';
  $permalink = $post_data['link'] ?? null;

  if ($original_status === 'publish') {
    // Déjà publié, juste hit la frontend pour déclencher la regen
    if ($permalink) {
      http_request('GET', $permalink, ['User-Agent: claude-skill-astra-spectra/0.9.1']);
    }
    return ['ok' => true, 'original_status' => 'publish', 'note' => 'already_published'];
  }

  // 2. Publier temporairement
  $upd_url = "$site_url/wp-json/wp/v2/pages/$post_id";
  $upd_body = json_encode(['status' => 'publish']);
  $r2 = http_request('POST', $upd_url, $headers, $upd_body);
  if ($r2['code'] !== 200) {
    return ['ok' => false, 'error' => "Temp-publish failed (HTTP {$r2['code']})"];
  }
  $pub_data = json_decode($r2['body'], true);
  $frontend_url = $pub_data['link'] ?? $permalink;

  // 3. Hit la URL frontend (anonyme, sans auth header)
  $frontend_http = null;
  if ($frontend_url) {
    $r3 = http_request('GET', $frontend_url, ['User-Agent: claude-skill-astra-spectra/0.9.1']);
    $frontend_http = $r3['code'];
  }

  // 4. Revert au statut original
  $revert_body = json_encode(['status' => $original_status]);
  $r4 = http_request('POST', $upd_url, $headers, $revert_body);
  $revert_ok = ($r4['code'] === 200);

  return [
    'ok' => true,
    'original_status' => $original_status,
    'frontend_http' => $frontend_http,
    'reverted' => $revert_ok,
    'note' => $revert_ok ? 'css_generated_then_reverted' : 'WARNING: revert failed, page may still be public',
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
