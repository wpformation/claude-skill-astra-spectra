# Module Astra — Customizer Map exhaustif

> Cartographie complète des options `astra-settings` pilotables par le skill quand le thème Astra est actif. Sans Astra MCP officiel, on passe par `update_option('astra-settings', $merged)` après lecture-modification-écriture.

## Règle d'or

```
1. $current = get_option('astra-settings');         // 1942 keys, 242 KB
2. $merged = array_merge_recursive($current, $patch); // sur-ensemble
3. update_option('astra-settings', $merged);        // écriture
4. astra_clear_all_assets_cache();                   // invalide cache CSS Astra
5. delete_transient('astra_dynamic_css');            // invalide transient
6. wp_cache_flush();                                 // flush global
```

**JAMAIS** un `update_option('astra-settings', $patch)` brut : ça écrase les 1942 autres keys.

## 1. Identité du site (logo, favicon)

| Key Astra | Type | Usage |
|-----------|------|-------|
| `display-site-tagline` | bool | Afficher la tagline |
| `display-site-title` | bool | Afficher le nom du site |
| `ast-header-logo-width` | int | Largeur logo desktop (px) |
| `ast-header-logo-width-tablet` | int | Largeur logo tablette |
| `ast-header-logo-width-mobile` | int | Largeur logo mobile |
| `ast-header-retina-logo` | string (URL) | Logo retina @2x |
| `transparent-header-logo` | string (URL) | Logo header transparent |
| `inherit-sticky-logo` | bool | Sticky header utilise logo standard |
| `different-mobile-logo` | bool | Logo différent sur mobile |

## 2. Palette globale (9 couleurs)

```php
'global-color-palette' => [
  'palette' => [
    '#0274be',  // 0 — primary (CTA, links)
    '#3a3a3a',  // 1 — secondary (text body)
    '#0a0a0a',  // 2 — heading text
    '#0a0a0a',  // 3 — heading dark
    '#0274be',  // 4 — accent (hover)
    '#ffffff',  // 5 — body bg
    '#f5f5f5',  // 6 — alt bg (sections grises)
    '#fafafa',  // 7 — off-white (cards)
    '#e7e7e7',  // 8 — border / dividers
  ],
]
```

Variables CSS générées automatiquement par Astra : `--ast-global-color-0` à `--ast-global-color-8`.

**Presets natifs Astra (11)** : `default`, `palette-1`, `palette-2`, ..., `palette-10`. Activables via `'global-color-palette' => ['currentPalette' => 'palette-3']`.

## 3. Typographie globale

| Key | Valeur typique |
|-----|----------------|
| `body-font-family` | `'Inter', sans-serif` |
| `body-font-weight` | `400` |
| `body-font-size` | `[ 'desktop' => 16, 'tablet' => 15, 'mobile' => 14 ]` |
| `body-line-height` | `1.65` |
| `body-text-transform` | `none` |
| `headings-font-family` | `'Inter Tight', sans-serif` |
| `headings-font-weight` | `700` |
| `headings-line-height` | `1.2` |
| `headings-letter-spacing` | `-0.02em` |
| `font-size-h1` | `[ 'desktop' => 48, 'tablet' => 38, 'mobile' => 32 ]` |
| `font-size-h2` | `[ 'desktop' => 36, 'tablet' => 28, 'mobile' => 24 ]` |
| `font-size-h3` | `[ 'desktop' => 28, 'tablet' => 22, 'mobile' => 20 ]` |
| `font-size-h4` | `[ 'desktop' => 22, 'tablet' => 18, 'mobile' => 18 ]` |
| `font-size-h5` | `[ 'desktop' => 18, 'tablet' => 16, 'mobile' => 16 ]` |
| `font-size-h6` | `[ 'desktop' => 16, 'tablet' => 14, 'mobile' => 14 ]` |

Astra accepte tous les Google Fonts via `body-font-family` et `headings-font-family` (pas besoin de `wp_enqueue_style` séparé, géré par le thème).

## 4. Layout container

| Key | Valeurs possibles |
|-----|-------------------|
| `site-content-width` | int (px), défaut 1200 |
| `narrow-container-max-width` | int (px), défaut 750 |
| `site-layout` | `ast-full-width-layout` \| `ast-box-layout` \| `ast-padded-layout` |
| `site-content-layout` | `boxed-container` \| `content-boxed-container` \| `plain-container` \| `page-builder` \| `narrow-container` |
| `single-page-content-layout` | id (override pour pages uniquement) |
| `single-post-content-layout` | id (override pour articles) |
| `archive-post-content-layout` | id (override pour archives) |

## 5. Header builder (Astra Pro feature)

```php
'header-desktop-items' => [
  'above'    => [ 'above_left' => [], 'above_left_center' => [], 'above_center' => [], 'above_right_center' => [], 'above_right' => [] ],
  'primary'  => [ 'primary_left' => ['logo'], 'primary_center' => ['menu-1'], 'primary_right' => ['button-1'] ],
  'below'    => [ 'below_left' => [], 'below_center' => [], 'below_right' => [] ],
],
'header-mobile-items' => [
  'popup' => [ 'popup_content' => ['mobile-menu', 'button-1'] ],
  'primary' => [ 'primary_left' => ['logo'], 'primary_right' => ['mobile-trigger'] ],
],
```

**Composants header disponibles** :
- `logo`, `mobile-trigger`, `mobile-menu`
- `menu-1`, `menu-2`, `menu-3` (jusqu'à 5 menus)
- `button-1`, `button-2` (jusqu'à 2 CTAs configurables avec text/url/style)
- `html-1`, `html-2` (HTML libre)
- `widget-1`, `widget-2` (widgets sidebar)
- `social-icons-1`, `search`, `account`, `cart`

Pour configurer un bouton header :
```php
'header-button1-text' => 'S\'inscrire',
'header-button1-link-option' => [ 'url' => '/inscription/', 'new_tab' => false ],
'header-button1-button-style' => 'fill',
'header-button1-bg-color' => 'var(--ast-global-color-0)',
'header-button1-color' => '#ffffff',
'header-button1-padding' => [ 'top' => 12, 'right' => 24, 'bottom' => 12, 'left' => 24 ],
'header-button1-border-radius' => [ 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8 ],
```

## 6. Footer builder (Astra Pro feature)

```php
'footer-desktop-items' => [
  'above' => [
    'above_1' => ['html-1'],
    'above_2' => ['menu-1'],
    'above_3' => ['social-icons-1'],
  ],
  'primary' => [
    'primary_1' => ['logo'],
    'primary_2' => ['copyright-1'],
  ],
  'below' => [
    'below_1' => ['copyright-2'],
  ],
],
```

**Composants footer** : `copyright-1`, `copyright-2`, `html-1`, `html-2`, `logo`, `menu-1`, `social-icons-1`, `widget-1` à `widget-4`.

## 7. Boutons globaux

| Key | Description |
|-----|-------------|
| `button-bg-color` | Couleur fond bouton primaire (souvent `var(--ast-global-color-0)`) |
| `button-color` | Couleur texte bouton primaire |
| `button-bg-h-color` | Couleur fond hover |
| `button-h-color` | Couleur texte hover |
| `button-radius-fields` | Border radius `[ top, right, bottom, left ]` |
| `button-padding` | Padding `[ desktop, tablet, mobile ]` × `[ top, right, bottom, left ]` |
| `theme-button-padding` | Padding bouton thème (différent du Customizer Astra) |
| `font-family-button` | Famille typo bouton |
| `font-weight-button` | Poids typo bouton |
| `font-size-button` | Taille typo bouton |

## 8. Sidebar

| Key | Valeur |
|-----|--------|
| `site-sidebar-layout` | `default` \| `left-sidebar` \| `right-sidebar` \| `no-sidebar` |
| `single-page-sidebar-layout` | override pour pages |
| `single-post-sidebar-layout` | override pour articles |
| `archive-post-sidebar-layout` | override pour archives |
| `site-sidebar-width` | int (%) défaut 30 |

## 9. Blog & archives

| Key | Description |
|-----|-------------|
| `blog-post-content` | `excerpt` \| `full-content` |
| `blog-post-structure` | array (ordre des éléments : `image`, `title-meta`) |
| `blog-meta` | array meta affichés (`comments`, `category`, `author`, `date`, `tag`, `read-time`) |
| `archive-content-image` | bool |
| `blog-grid` | int (nombre de colonnes : 1, 2, 3, 4) |
| `single-post-meta` | array (idem blog-meta pour single) |

## 10. Performance

| Key | Description |
|-----|-------------|
| `astra-settings-block-editor-styles` | Charger styles thème dans Gutenberg |
| `disable-astra-fonts` | Désactiver Google Fonts Astra (si géré ailleurs) |
| `dynamic-typography` | Charger typo dynamique conditionnellement |
| `load-google-fonts-locally` | Self-host Google Fonts |
| `preload-local-fonts` | `<link rel=preload>` sur fonts |

## 11. Integration WooCommerce (si actif)

Section dédiée `astra-settings.shop-*`, voir `references/astra-woocommerce-map.md` (à créer si besoin).

## 12. Custom CSS (fallback)

```php
'custom-css' => "
:root {
  --my-custom-spacing: 80px;
}
.uagb-container__inner {
  scroll-behavior: smooth;
}
"
```

Astra ajoute automatiquement ce CSS dans `<head>` via `wp_add_inline_style`.

## Workflow type — Modifier la palette

```php
$current = get_option('astra-settings');
$current['global-color-palette']['palette'] = [
  '#FF8C00', '#0a0a0a', '#0a0a0a', '#0a0a0a', '#FF8C00',
  '#ffffff', '#f5f5f5', '#fafafa', '#e7e7e7',
];
$current['global-color-palette']['currentPalette'] = 'default';
update_option('astra-settings', $current);
astra_clear_all_assets_cache();
delete_transient('astra_dynamic_css');
wp_cache_flush();
```

## Workflow type — Configurer un header complet

```php
$current = get_option('astra-settings');

// Layout : logo gauche, menu centré, CTA à droite
$current['header-desktop-items']['primary'] = [
  'primary_left' => ['logo'],
  'primary_center' => ['menu-1'],
  'primary_right' => ['button-1'],
];

// Configurer le CTA
$current['header-button1-text'] = 'Demander un devis';
$current['header-button1-link-option'] = [ 'url' => '/devis/', 'new_tab' => false ];
$current['header-button1-button-style'] = 'fill';

// Sticky header
$current['header-main-stick'] = true;
$current['header-main-stick-meta'] = 'shrink';

update_option('astra-settings', $current);
astra_clear_all_assets_cache();
```

## Anti-patterns

- ❌ `update_option('astra-settings', ['global-color-palette' => [...]])` — écrase les 1942 autres keys
- ❌ Modifier `astra-color-palettes` au lieu de `astra-settings.global-color-palette` — la 1re option pilote l'UI Customizer, la 2e pilote le **frontend**
- ❌ Oublier d'invalider les caches → CSS périmé pendant 12h
- ❌ Toucher à `astra_addons_options` (Astra Pro) sans connaître la clé exacte
- ❌ Modifier le footer-builder sans Astra Pro actif → fallback widgetisé legacy s'affiche
