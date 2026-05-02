<?php
/**
 * regen-spectra.php
 *
 * Force la régénération des assets CSS Spectra pour un post donné.
 *
 * Pourquoi : le CSS dynamique Spectra (`<style id="uagb-style-frontend-{post_id}">`)
 * peut ne pas être injecté dans `<head>` sur certaines configurations (cf
 * `references/spectra-attributes-quirks.md` piège #6). Ce script force la
 * régénération via l'endpoint mu-plugin compagnon, OU fallback sur des
 * stratégies alternatives.
 *
 * Stratégies (dans l'ordre, première qui réussit gagne) :
 * 1. Endpoint mu-plugin compagnon `/skill-test/v1/regen-spectra`
 * 2. Endpoint Spectra natif (si configuré) `/astra-spectra/v1/regen-assets/{id}`
 * 3. Temp-publish trick : publish→hit→draft (force pipeline complète)
 * 4. Fallback : suggérer wp-cli command à l'utilisateur
 *
 * Usage CLI :
 *   php regen-spectra.php \
 *     --site-url=https://monsite.com \
 *     --user=admin \
 *     --app-password='xxxx xxxx xxxx xxxx xxxx xxxx' \
 *     --post-id=42 \
 *     [--allow-temp-publish=true]
 *
 * Output : JSON avec method utilisée, css_len, js_len.
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
  echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
  curl_close($ch);
  return ['code' => $http_code, 'body' => $response];
}

function main() {
  global $argv;
  $args = parse_args($argv);

  $required = ['site-url', 'user', 'app-password', 'post-id'];
  foreach ($required as $r) {
    if (empty($args[$r])) fail("Missing required arg --$r");
  }

  $site_url = rtrim($args['site-url'], '/');
  if (!preg_match('/^https?:\/\//', $site_url)) $site_url = 'https://' . $site_url;
  $post_id = (int) $args['post-id'];
  $allow_temp_publish = !isset($args['allow-temp-publish']) || $args['allow-temp-publish'] === 'true';

  $auth = base64_encode($args['user'] . ':' . $args['app-password']);
  $headers = [
    'Authorization: Basic ' . $auth,
    'Content-Type: application/json',
    'Accept: application/json',
    'User-Agent: claude-skill-astra-spectra/1.0',
  ];

  // Strategy 1 : mu-plugin compagnon
  $r1 = http_request('POST', "$site_url/wp-json/skill-test/v1/regen-spectra", $headers, json_encode(['post_id' => $post_id]));
  if ($r1['code'] === 200) {
    $data = json_decode($r1['body'], true);
    echo json_encode([
      'success' => true,
      'method' => 'mu-plugin-compagnon',
      'css_len' => $data['css_len'] ?? null,
      'js_len' => $data['js_len'] ?? null,
    ], JSON_PRETTY_PRINT);
    exit(0);
  }

  // Strategy 2 : Spectra native endpoint (si déployé)
  $r2 = http_request('POST', "$site_url/wp-json/astra-spectra/v1/regen-assets/$post_id", $headers, json_encode(['post_id' => $post_id]));
  if ($r2['code'] === 200) {
    echo json_encode([
      'success' => true,
      'method' => 'astra-spectra-native',
    ], JSON_PRETTY_PRINT);
    exit(0);
  }

  // Strategy 3 : temp-publish trick
  if ($allow_temp_publish) {
    // Get current status
    $r3 = http_request('GET', "$site_url/wp-json/wp/v2/pages/$post_id?context=edit&_fields=status,link", $headers);
    if ($r3['code'] === 200) {
      $post_data = json_decode($r3['body'], true);
      $original_status = $post_data['status'] ?? 'draft';
      $permalink = $post_data['link'] ?? null;

      if ($original_status !== 'publish') {
        // Publish temporarily
        http_request('POST', "$site_url/wp-json/wp/v2/pages/$post_id", $headers, json_encode(['status' => 'publish']));

        // Hit frontend (force pipeline)
        if ($permalink) {
          http_request('GET', $permalink, ['User-Agent: claude-skill-astra-spectra/1.0']);
        }

        // Revert to original status
        http_request('POST', "$site_url/wp-json/wp/v2/pages/$post_id", $headers, json_encode(['status' => $original_status]));

        echo json_encode([
          'success' => true,
          'method' => 'temp-publish-trick',
          'original_status' => $original_status,
          'note' => 'Page was temporarily published to force Spectra CSS generation, then reverted.',
        ], JSON_PRETTY_PRINT);
        exit(0);
      } else {
        // Already published, just hit frontend
        if ($permalink) {
          http_request('GET', $permalink, ['User-Agent: claude-skill-astra-spectra/1.0']);
        }
        echo json_encode([
          'success' => true,
          'method' => 'frontend-hit',
          'note' => 'Page is already published, hit frontend to trigger Spectra render hooks.',
        ], JSON_PRETTY_PRINT);
        exit(0);
      }
    }
  }

  // Fallback : suggest wp-cli
  echo json_encode([
    'success' => false,
    'method' => 'manual',
    'manual_command_wp_cli' => "wp eval 'if (class_exists(\"UAGB_Post_Assets\")) { (new UAGB_Post_Assets($post_id))->generate_assets(); }'",
    'manual_command_admin' => "Open page in Gutenberg admin and click Update once.",
    'note' => 'No automated method worked. Deploy mu-plugin compagnon (scripts/mu-plugin-skill-test.php) for best results.',
  ], JSON_PRETTY_PRINT);
  exit(1);
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
