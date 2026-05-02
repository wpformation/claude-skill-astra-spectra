<?php
/**
 * post-render-check.php
 *
 * Validateur POST-render. Fetch l'URL frontend de la page POSTée et vérifie
 * que les éléments critiques sont bien dans le HTML rendu :
 *
 *   1. <style id="uagb-style-frontend-{post_id}"> est présent (Quirk #23)
 *   2. Le CSS skill-generated est dans le <head> (sinon overrides perdus)
 *   3. Pas de double H1 (Quirk #24 sur block themes FSE)
 *   4. Tous les block_id uagb-block-* sont rendus (sanity check parser)
 *
 * Usage CLI :
 *   php post-render-check.php --url=https://site.com/slug/ --post-id=42 \
 *     [--expected-block-ids=skill-hero,skill-stats,...] \
 *     [--user=admin --pass="app pass"]
 *
 * Output : JSON avec :
 *   - status : OK | WARNING | BLOCKED
 *   - p0[]   : violations bloquantes
 *   - p1[]   : violations sérieuses
 *   - p2[]   : violations cosmétiques
 *   - stats  : { html_size, h1_count, style_tags, ... }
 *
 * Codes :
 *   QUIRK-23  : <style id="uagb-style-frontend-X"> manquant → CSS perdu
 *   QUIRK-24  : double H1 (wp-block-post-title FSE + hero H1)
 *   POST-RENDER-BLOCKID : block_id attendu absent du rendu
 */

function post_render_check($html, $post_id = null, $expected_block_ids = []) {
    $report = [
        'status' => 'OK',
        'p0' => [],
        'p1' => [],
        'p2' => [],
        'stats' => [
            'html_size' => strlen($html),
            'h1_count' => 0,
            'style_tags' => 0,
            'uagb_blocks_rendered' => 0,
        ],
    ];

    if (empty($html)) {
        $report['status'] = 'BLOCKED';
        $report['p0'][] = ['code' => 'EMPTY-HTML', 'msg' => 'HTML rendu vide.'];
        return $report;
    }

    // === Quirk #23 : <style id="uagb-style-frontend-{post_id}"> doit être présent ===
    if ($post_id) {
        $needle = "uagb-style-frontend-{$post_id}";
        if (strpos($html, $needle) === false) {
            $report['p0'][] = [
                'code' => 'QUIRK-23',
                'msg' => "<style id=\"$needle\"> ABSENT du HTML rendu. Spectra n'a pas hook wp_head. Tous les overrides CSS sont perdus. Fix : déployer le mu-plugin compagnon (workaround Quirk #23 inclus depuis v1.0-rc4).",
            ];
        } else {
            $report['stats']['uagb_style_frontend_present'] = true;
        }
    }

    // === Vérifier que le skill-generated CSS est bien dans le HTML ===
    if (strpos($html, 'skill-generated') === false) {
        $report['p1'][] = [
            'code' => 'CSS-OVERRIDES-MISSING',
            'msg' => "Le CSS overrides skill-generated n'est pas dans le HTML. Soit le meta `_uag_custom_page_level_css` est vide, soit Spectra ne le concatène pas (vérifier `uag_enable_on_page_css_button=yes` — Quirk #20).",
        ];
    }

    // === Quirk #24 : Double H1 (block theme FSE) ===
    preg_match_all('/<h1[\s>]/i', $html, $h1_matches);
    $h1_count = count($h1_matches[0]);
    $report['stats']['h1_count'] = $h1_count;

    $has_post_title = (bool) preg_match('/<h1[^>]*class="[^"]*wp-block-post-title[^"]*"/', $html);
    if ($has_post_title && $h1_count >= 2) {
        $report['p1'][] = [
            'code' => 'QUIRK-24',
            'msg' => "Double H1 détecté ($h1_count <h1>) avec un wp-block-post-title (block theme FSE). SEO cassé. Fix : ajouter le post_meta `_skill_hide_post_title=1` (mu-plugin compagnon v1.0-rc4) OU CSS scope `body.page-id-{ID} .wp-block-post-title { display: none !important; }` dans `_uag_custom_page_level_css`.",
        ];
    } elseif ($h1_count > 1 && !$has_post_title) {
        $report['p2'][] = [
            'code' => 'MULTIPLE-H1',
            'msg' => "Plusieurs H1 détectés ($h1_count) sans wp-block-post-title. Probable double hero H1. Vérifier que les info-box hero ont `headingTag:h1` une seule fois sur la page.",
        ];
    }

    // === Sanity check : block_ids attendus présents ===
    if (!empty($expected_block_ids)) {
        foreach ($expected_block_ids as $bid) {
            if (strpos($html, "uagb-block-$bid") === false) {
                $report['p0'][] = [
                    'code' => 'POST-RENDER-BLOCKID',
                    'msg' => "block_id '$bid' attendu mais absent du HTML rendu. Le bloc est cassé ou supprimé par un parser/sanitizer.",
                ];
            }
        }
    }

    // === Compter les blocs uagb rendus (sanity general) ===
    preg_match_all('/uagb-block-[\w-]+/', $html, $uagb_matches);
    $report['stats']['uagb_blocks_rendered'] = count(array_unique($uagb_matches[0] ?? []));
    if ($report['stats']['uagb_blocks_rendered'] === 0) {
        $report['p0'][] = [
            'code' => 'NO-UAGB-BLOCKS',
            'msg' => "Aucun bloc `uagb-block-*` rendu dans le HTML. Le markup Spectra a été stripé par un parser ou Gutenberg n'a pas reconnu les blocs (vérifier que Spectra plugin est actif et que le post_content contient bien les block comments `<!-- wp:uagb/* -->`).",
        ];
    }

    // === Compter les style tags pour stats ===
    preg_match_all('/<style[^>]*>/', $html, $style_matches);
    $report['stats']['style_tags'] = count($style_matches[0] ?? []);

    // === Verdict global ===
    if (!empty($report['p0'])) {
        $report['status'] = 'BLOCKED';
    } elseif (!empty($report['p1'])) {
        $report['status'] = 'WARNING';
    }

    return $report;
}

// === Bloc CLI ===
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
        if (preg_match('/^--([\w-]+)=(.*)$/', $arg, $m)) $args[$m[1]] = $m[2];
    }

    $url = $args['url'] ?? '';
    $post_id = (int) ($args['post-id'] ?? 0);
    $expected = !empty($args['expected-block-ids']) ? explode(',', $args['expected-block-ids']) : [];
    $user = $args['user'] ?? '';
    $pass = $args['pass'] ?? '';

    if (empty($url)) {
        echo json_encode(['status' => 'BLOCKED', 'p0' => [['code' => 'NO-URL', 'msg' => 'Argument --url requis.']]]);
        exit(1);
    }

    // Fetch HTML via curl (si user/pass fournis, basic auth)
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 30,
    ]);
    if ($user && $pass) {
        curl_setopt($ch, CURLOPT_USERPWD, "$user:$pass");
    }
    $html = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200 || $html === false) {
        echo json_encode(['status' => 'BLOCKED', 'p0' => [['code' => 'FETCH-FAIL', 'msg' => "Fetch URL failed: HTTP $http_code"]]]);
        exit(1);
    }

    $report = post_render_check($html, $post_id, $expected);
    echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit($report['status'] === 'BLOCKED' ? 1 : 0);
}
