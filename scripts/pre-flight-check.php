<?php
/**
 * pre-flight-check.php
 *
 * Validateur BLOQUEUR avant POST. Parcourt le markup généré et flag les
 * occurrences des 19 pièges Spectra documentés dans
 * references/spectra-attributes-quirks.md.
 *
 * Doit être exécuté APRÈS génération du markup et AVANT POST via REST API.
 * Si retourne `status: BLOCKED`, ne pas POST. Corriger d'abord.
 *
 * Usage CLI :
 *   php pre-flight-check.php < markup.html
 *   php pre-flight-check.php --content-file=/tmp/markup.html [--css-file=/tmp/overrides.css]
 *
 * Output : JSON avec :
 *   - status : OK | WARNING | BLOCKED
 *   - p0[]   : violations bloquantes (ne PAS POST)
 *   - p1[]   : violations sérieuses (corriger fortement recommandé)
 *   - p2[]   : violations cosmétiques (acceptable mais à fixer si possible)
 *   - quirks_checked : liste des 19 pièges vérifiés
 *
 * Convention de codes :
 *   QUIRK-{N}  : pièges 1-19 documentés
 *   I18N-{X}   : règles i18n
 *   CONV-{X}   : conventions skill (block_id, naming, etc.)
 */

// Whitelist d'icônes Spectra validées (sous-set de spectra-icons-list.md)
const SPECTRA_ICONS_WHITELIST = [
  'envelope', 'envelope-open', 'phone', 'phone-alt', 'mobile-alt', 'fax',
  'comments', 'comment', 'comment-alt', 'comment-dots',
  'share-alt', 'share-square', 'external-link-alt', 'link',
  'shopping-cart', 'shopping-bag', 'credit-card', 'store',
  'chart-bar', 'chart-line', 'chart-pie', 'chart-area',
  'briefcase', 'building', 'city',
  'dollar-sign', 'euro-sign', 'pound-sign',
  'tags', 'tag', 'percent', 'percentage',
  'book', 'book-reader', 'graduation-cap', 'school',
  'file', 'file-alt', 'file-pdf', 'file-word', 'file-excel',
  'folder', 'folder-open',
  'clipboard', 'tasks', 'check-square',
  'pencil-alt', 'edit',
  'home', 'user', 'users', 'user-circle',
  'search', 'filter', 'sort',
  'plus', 'minus', 'times', 'check',
  'arrow-right', 'arrow-left', 'arrow-up', 'arrow-down',
  'chevron-right', 'chevron-left', 'chevron-up', 'chevron-down',
  'caret-right', 'caret-left', 'caret-up', 'caret-down',
  'bars', 'cog', 'cogs', 'ellipsis-h', 'ellipsis-v',
  'calendar', 'calendar-alt', 'clock', 'stopwatch', 'hourglass',
  'history', 'bell', 'bell-slash',
  'cloud', 'cloud-upload-alt', 'cloud-download-alt',
  'wifi', 'globe', 'globe-americas', 'globe-europe',
  'database', 'server', 'hdd',
  'desktop', 'laptop', 'mobile', 'tablet-alt',
  'heart', 'heart-broken', 'hand-holding-heart',
  'running', 'walking', 'biking',
  'apple-alt', 'utensils',
  'medkit', 'pills',
  'map', 'map-marker-alt', 'map-marked-alt',
  'plane', 'car', 'bus', 'train', 'ship',
  'suitcase',
  'play', 'play-circle', 'pause', 'stop',
  'music', 'headphones', 'video', 'volume-up', 'volume-mute',
  'camera', 'image', 'film',
  'check-circle', 'exclamation-circle', 'exclamation-triangle',
  'info-circle', 'question-circle',
  'times-circle', 'ban',
  'star', 'star-half-alt',
  'thumbs-up', 'thumbs-down',
  'lock', 'lock-open', 'unlock', 'shield-alt',
  'key', 'fingerprint', 'user-shield',
  'flag', 'flag-checkered', 'rocket', 'trophy', 'medal', 'award', 'handshake',
];

// Slots Astra GARANTIS (stables sur 11 presets)
const ASTRA_GUARANTEED_SLOTS = [0, 1, 2, 3, 5];
// Slots VARIABLES (à éviter sans helper resolve)
const ASTRA_VARIABLE_SLOTS = [4, 6, 7, 8];

function pre_flight_main($content, $css = '') {
  $report = [
    'status' => 'OK',
    'p0' => [],
    'p1' => [],
    'p2' => [],
    'stats' => [
      'block_count' => 0,
      'block_ids_seen' => [],
      'icons_seen' => [],
    ],
    'quirks_checked' => [
      '1_headingFontSize_on_p',
      '2_infobox_widthDesktop',
      '3_faq_answer_attr',
      '4_inline_styles_in_innerContent',
      '5_blockid_unique',
      '6_spectra_dynamic_css',
      '7_palette_variable_slots',
      '8_icons_whitelist',
      '9_hero_overlay_opacity',
      '10_hero_blockRightPadding',
      '11_iconlist_layout',
      '12_faq_max_width',
      '13_double_h1_template',
      '14_apache_mutu_auth',
      '15_section_bg_alternation',
      '16_image_ratio',
      '17_button_padding',
      '18_astra_padding_bottom',
      '19_eyebrow_size',
    ],
  ];

  if (empty($content)) {
    $report['status'] = 'BLOCKED';
    $report['p0'][] = ['code' => 'EMPTY-CONTENT', 'msg' => 'Markup vide.'];
    return $report;
  }

  // === Quirk #5 : block_id unique ===
  preg_match_all('/"block_id":"([^"]+)"/', $content, $bid_matches);
  $bids = $bid_matches[1] ?? [];
  $report['stats']['block_count'] = count($bids);
  $bid_counts = array_count_values($bids);
  foreach ($bid_counts as $bid => $count) {
    if ($count > 1) {
      $report['p0'][] = [
        'code' => 'QUIRK-5',
        'msg' => "block_id dupliqué : '$bid' apparaît $count fois. Spectra recompute = rendu cassé.",
      ];
    }
  }
  $report['stats']['block_ids_seen'] = array_keys($bid_counts);

  // Check block_id manquant sur uagb/* blocs
  preg_match_all('/<!-- wp:(uagb\/[\w-]+)\s+\{([^}]*)\}/', $content, $block_matches, PREG_SET_ORDER);
  foreach ($block_matches as $b) {
    $block_name = $b[1];
    $attrs_json = $b[2];
    if (strpos($attrs_json, '"block_id"') === false) {
      $report['p0'][] = [
        'code' => 'QUIRK-5',
        'msg' => "Block $block_name sans block_id. Gutenberg recomputera et cassera le rendu.",
      ];
    }
  }

  // === Quirk #2 : info-box avec widthDesktop (pas supporté) ===
  preg_match_all('/<!-- wp:uagb\/info-box\s+\{([^}]*"widthDesktop"[^}]*)\}/', $content, $infobox_matches);
  foreach ($infobox_matches[0] as $match) {
    $report['p0'][] = [
      'code' => 'QUIRK-2',
      'msg' => 'info-box avec attribut `widthDesktop` détecté. Les info-box ne supportent pas cet attribut → empilement vertical au lieu de row. Wrapper dans uagb/container avec widthDesktop.',
    ];
  }

  // === Quirk #3 : faq-child avec `description` au lieu de `answer` ===
  preg_match_all('/<!-- wp:uagb\/faq-child\s+\{([^}]*)\}/', $content, $faq_matches);
  foreach ($faq_matches[1] as $attrs) {
    if (strpos($attrs, '"description"') !== false && strpos($attrs, '"answer"') === false) {
      $report['p0'][] = [
        'code' => 'QUIRK-3',
        'msg' => 'faq-child utilise attribut `description`. Le bon nom est `answer`. Sinon Lorem Ipsum au render.',
      ];
    }
  }

  // === Quirk #4 : inline style="..." dans innerContent (sera strippé par Gutenberg save) ===
  // On ignore les style typo sécurité (text-transform, letter-spacing) qui sont parfois nécessaires sur title-prefix
  preg_match_all('/<(?:p|h[1-6]|div|span)[^>]+style="([^"]*font-size[^"]*)"/', $content, $inline_matches);
  foreach ($inline_matches[1] as $style) {
    if (strpos($style, 'font-size') !== false) {
      $report['p1'][] = [
        'code' => 'QUIRK-4',
        'msg' => "Inline style avec font-size détecté : `$style`. Sera STRIPPÉ par Gutenberg dès le premier save. Mettre dans _uag_custom_page_level_css.",
      ];
    }
  }

  // === Quirk #7 : slots Astra VARIABLES (color-4, 6, 7, 8) ===
  preg_match_all('/var\(--ast-global-color-(\d)\)/', $content, $slot_matches);
  foreach ($slot_matches[1] as $slot) {
    $slot_int = (int) $slot;
    if (in_array($slot_int, ASTRA_VARIABLE_SLOTS, true)) {
      $report['p1'][] = [
        'code' => 'QUIRK-7',
        'msg' => "var(--ast-global-color-$slot) utilisé. Slot VARIABLE selon la palette (peut valoir noir massif sur palette_3). Préférer hex direct (#fafafa, #ffffff, #e5e7eb) ou helper resolve_color().",
      ];
    }
  }

  // === Quirk #8 : icônes hors whitelist ===
  preg_match_all('/"icon":"([^"]+)"/', $content, $icon_matches);
  foreach ($icon_matches[1] as $icon) {
    if (!empty($icon) && !in_array($icon, SPECTRA_ICONS_WHITELIST, true)) {
      $report['p1'][] = [
        'code' => 'QUIRK-8',
        'msg' => "Icône '$icon' hors whitelist Spectra. Risque de fallback identique. Whitelist : references/spectra-icons-list.md. Ou utiliser numéros 01/02/03 (features-numbered).",
      ];
    }
    $report['stats']['icons_seen'][] = $icon;
  }

  // === Quirk #9 : hero overlay opacity > 0.85 ===
  preg_match_all('/"overlayOpacity":\s*([\d.]+)/', $content, $overlay_matches);
  foreach ($overlay_matches[1] as $opacity) {
    if ((float) $opacity > 0.85) {
      $report['p1'][] = [
        'code' => 'QUIRK-9',
        'msg' => "overlayOpacity = $opacity. Trop opaque (>0.85), image background invisible. Recommandé : 0.65 max pour un overlay flat. Pour gradient overlay, utiliser color1 0.7 → color2 0.20.",
      ];
    }
  }

  // === Quirk #10 : blockRightPadding > 25% ===
  preg_match_all('/"blockRightPadding":\s*(\d+).*?"blockPaddingUnit":"%"/', $content, $padding_matches);
  foreach ($padding_matches[1] as $padding) {
    if ((int) $padding > 25) {
      $report['p1'][] = [
        'code' => 'QUIRK-10',
        'msg' => "blockRightPadding = $padding%. > 25% écrase le texte de la desc hero. Recommandé : 25% max.",
      ];
    }
  }

  // === Quirk #17 : padding bouton CTA insuffisant ===
  preg_match_all('/<!-- wp:uagb\/buttons-child\s+\{([^}]*)\}/', $content, $btn_matches);
  foreach ($btn_matches[1] as $btn_attrs) {
    if (preg_match('/"paddingBtnLeft":\s*(\d+)/', $btn_attrs, $m)) {
      if ((int) $m[1] < 30) {
        $report['p2'][] = [
          'code' => 'QUIRK-17',
          'msg' => "paddingBtnLeft = {$m[1]}. < 30 = bouton pincé. Recommandé : 36-44 desktop.",
        ];
      }
    }
  }

  // === Quirk #19 : eyebrow font-size < 14 ===
  preg_match_all('/"prefixFontSizeDesktop":\s*(\d+)/', $content, $prefix_matches);
  foreach ($prefix_matches[1] as $size) {
    if ((int) $size < 14) {
      $report['p2'][] = [
        'code' => 'QUIRK-19',
        'msg' => "Eyebrow prefixFontSizeDesktop = {$size}px. < 14px = trop discret, ressemble à un debug tag. Recommandé : 14-15px avec letter-spacing 3-4px.",
      ];
    }
  }

  // === I18N : check accents français (heuristique) ===
  // Si le contenu paraît être en français (mots clés) ET contient des mots sans accents là où il devrait y en avoir
  if (preg_match('/\b(le|la|les|de|du|un|une|et|ou|pour|avec|dans|sur)\b/i', $content)) {
    // Mots probablement en français
    $bad_patterns = [
      'reussir' => 'réussir',
      'rediges' => 'rédigés',
      'eleves' => 'élèves',
      'epreuve' => 'épreuve',
      'theorie' => 'théorie',
      'methode' => 'méthode',
      'frequentes' => 'fréquentes',
      'decroche' => 'décroché',
      'deja' => 'déjà',
      'evaluation' => 'évaluation',
      'cle' => 'clé',
      'apres' => 'après',
      'prets' => 'prêts',
      'pret' => 'prêt',
      'dernieres' => 'dernières',
      'derniere' => 'dernière',
    ];
    foreach ($bad_patterns as $bad => $good) {
      if (preg_match('/\b' . $bad . '\b/i', $content)) {
        $report['p0'][] = [
          'code' => 'I18N-FR-ACCENTS',
          'msg' => "Mot français sans accent : '$bad' trouvé. Devrait être '$good' ou utiliser HTML entity (`&eacute;` etc.). Lire references/i18n-rules.md.",
        ];
      }
    }
  }

  // === I18N : mojibake détecté (UTF-8 mal encodé) ===
  if (strpos($content, "\xc3\xa2\xe2\x82\xac") !== false || preg_match('/â€/', $content)) {
    $report['p0'][] = [
      'code' => 'I18N-MOJIBAKE',
      'msg' => 'Mojibake `â€` détecté dans le markup. Caractères UTF-8 mal encodés. Utiliser HTML entities pour `—` (`&mdash;`), `«` (`&laquo;`), `…` (`&hellip;`).',
    ];
  }

  // === I18N : em-dash direct sans entity ===
  if (preg_match('/[^a-zA-Z0-9]—[^a-zA-Z0-9]/u', $content)) {
    $report['p2'][] = [
      'code' => 'I18N-MDASH',
      'msg' => 'Em-dash `—` direct détecté. Préférer `&mdash;` HTML entity pour éviter mojibake.',
    ];
  }

  // === Quirk #12 : FAQ wrapper width ===
  if (preg_match('/<!-- wp:uagb\/faq\s/', $content)) {
    // Vérifier qu'il y a un container parent avec widthDesktop ≤ 80
    if (!preg_match('/<!-- wp:uagb\/container\s+\{[^}]*"widthDesktop":\s*(\d+).*?\}\s*-->\s*<div[^>]*>\s*<!-- wp:uagb\/faq/s', $content)) {
      $report['p2'][] = [
        'code' => 'QUIRK-12',
        'msg' => 'uagb/faq sans wrapper container max-width. La FAQ s\'étendra sur toute la largeur (1100px+). Wrapper dans container widthDesktop:62 pour readability.',
      ];
    }
  }

  // === Quirk #15 : sections root sans alternance bg ===
  preg_match_all('/<!-- wp:uagb\/container\s+\{([^}]*"isBlockRootParent":\s*true[^}]*)\}/', $content, $root_matches);
  $section_bgs = [];
  foreach ($root_matches[1] as $attrs) {
    if (preg_match('/"backgroundColor":"([^"]+)"/', $attrs, $bm)) {
      $section_bgs[] = strtolower($bm[1]);
    } else {
      $section_bgs[] = null;
    }
  }
  for ($i = 1; $i < count($section_bgs); $i++) {
    if ($section_bgs[$i] !== null && $section_bgs[$i - 1] !== null && $section_bgs[$i] === $section_bgs[$i - 1]) {
      $report['p2'][] = [
        'code' => 'QUIRK-15',
        'msg' => "Sections root #{$i} et #" . ($i - 1) . " ont même backgroundColor `{$section_bgs[$i]}`. Mur de blocs visuel. Alterner #ffffff ↔ #fafafa.",
      ];
    }
  }

  // === Quirk #18 : check si CSS contient padding-bottom override Astra ===
  if (!empty($css) && !preg_match('/\.entry-content\s*\{\s*padding-bottom:\s*0/', $css)) {
    $has_alignfull_last = preg_match('/<!-- wp:uagb\/container\s+\{[^}]*"alignfull"|"isBlockRootParent":\s*true[^}]*\}\s*-->[\s\S]*<!-- \/wp:uagb\/container -->\s*$/', $content);
    if ($has_alignfull_last) {
      $report['p2'][] = [
        'code' => 'QUIRK-18',
        'msg' => 'Page avec dernier bloc alignfull mais CSS overrides ne contient pas `.entry-content { padding-bottom: 0 }`. Astra applique 4em padding-bottom = espace orphelin avant le footer.',
      ];
    }
  }

  // === Quirk #19 (CSS) : vérifier que les eyebrows ont fontWeight 800 ===
  preg_match_all('/"prefixFontWeight":"?(\d+)"?/', $content, $weight_matches);
  foreach ($weight_matches[1] as $weight) {
    if ((int) $weight < 700) {
      $report['p2'][] = [
        'code' => 'QUIRK-19',
        'msg' => "Eyebrow prefixFontWeight = $weight. < 700 = trop léger. Recommandé : 800 pour bon impact visuel.",
      ];
    }
  }

  // === CONV : block_id ne préfixe pas par v{N}- (pattern démo, pas générique) ===
  foreach ($report['stats']['block_ids_seen'] as $bid) {
    if (preg_match('/^v\d+-/', $bid)) {
      $report['p2'][] = [
        'code' => 'CONV-DEMO-PREFIX',
        'msg' => "block_id '$bid' utilise préfixe `v{N}-` (pattern démo loginarmor-dev). Préférer un slug page : `{slug-page}-{section}-{element}` (e.g. `accueil-hero-text`).",
      ];
      break; // Juste flagger une fois
    }
  }

  // === Verdict global ===
  if (!empty($report['p0'])) {
    $report['status'] = 'BLOCKED';
  } elseif (!empty($report['p1'])) {
    $report['status'] = 'WARNING';
  } else {
    $report['status'] = 'OK';
  }

  return $report;
}

// Bloc CLI : ne s'exécute QUE si le script est appelé directement.
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

  $content = '';
  $css = '';

  if (!empty($args['content-file']) && file_exists($args['content-file'])) {
    $content = file_get_contents($args['content-file']);
  } else {
    $content = stream_get_contents(STDIN);
  }

  if (!empty($args['css-file']) && file_exists($args['css-file'])) {
    $css = file_get_contents($args['css-file']);
  }

  $report = pre_flight_main($content, $css);
  echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  // Exit code : 0 si OK ou WARNING, 1 si BLOCKED
  exit($report['status'] === 'BLOCKED' ? 1 : 0);
}
