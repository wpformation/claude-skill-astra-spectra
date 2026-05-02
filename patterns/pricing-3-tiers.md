# Pattern : Pricing 3 Tiers

> **Use case** : Section de tarification avec 3 plans (Starter, Pro, Enterprise) côte à côte. Plan central mis en avant (badge « Populaire » + bordure colorée + box-shadow plus marqué). Liste de features par plan avec icônes check.

## Variables d'entrée

| Variable | Description |
|----------|-------------|
| `{{SECTION_HEADLINE}}` | Titre section (ex: « Tarifs simples et transparents ») |
| `{{T1_NAME}}` / `{{T2_NAME}}` / `{{T3_NAME}}` | Nom du plan |
| `{{T1_PRICE}}` / `{{T2_PRICE}}` / `{{T3_PRICE}}` | Prix (ex: « 19 EUR ») |
| `{{T1_PERIOD}}` / `{{T2_PERIOD}}` / `{{T3_PERIOD}}` | Période (« /mois ») |
| `{{T1_DESC}}` / `{{T2_DESC}}` / `{{T3_DESC}}` | Description courte |
| `{{T1_FEATn}}` / `{{T2_FEATn}}` / `{{T3_FEATn}}` | Une feature par variable, n = 1 à 5 |
| `{{T1_CTA}}` / `{{T2_CTA}}` / `{{T3_CTA}}` | Texte CTA |
| `{{T1_LINK}}` / `{{T2_LINK}}` / `{{T3_LINK}}` | URL CTA |

## Règle critique pour les icônes (corrigée v0.8.2)

Comme pour `features-3-cols.md`, le pattern ne met PAS de `<svg>` dans le HTML rendu de `uagb/icon-list-child`. Spectra rend l'icône check via SVG inline généré dynamiquement à partir de `"icon":"check"` dans les attrs JSON. Le HTML écrit dans le pattern doit donc rester minimal (juste `<span class="uagb-icon-list-label">`), sans `<span class="uagb-icon-list-source"><svg></svg></span>`.

## Règle critique pour les block_id de features

Chaque `uagb/icon-list-child` DOIT avoir un `block_id` unique. Pour plusieurs features par tier, suffixer en `-1`, `-2`, ..., `-N` :

- Tier 1 : `t1-feat-1`, `t1-feat-2`, `t1-feat-3`
- Tier 2 : `t2-feat-1`, `t2-feat-2`, ...
- Tier 3 : `t3-feat-1`, `t3-feat-2`, ...

Le pattern par défaut ci-dessous fournit 3 features par tier. Pour en ajouter, dupliquer le bloc `uagb/icon-list-child` en incrémentant le suffixe.

## Block markup (3 features par tier)

```html
<!-- wp:uagb/container {"block_id":"pricing-section","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":1280,"directionDesktop":"column","alignItemsDesktop":"center","rowGapDesktop":56,"topPaddingDesktop":100,"bottomPaddingDesktop":100,"topPaddingTablet":60,"bottomPaddingTablet":60,"topPaddingMobile":48,"bottomPaddingMobile":48,"leftPaddingTablet":24,"rightPaddingTablet":24,"leftPaddingMobile":16,"rightPaddingMobile":16,"backgroundType":"color","backgroundColor":"var(--ast-global-color-7)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-pricing-section"><!-- wp:uagb/advanced-heading {"block_id":"pricing-heading","headingTag":"h2","headingTitle":"{{SECTION_HEADLINE}}","headingColor":"var(--ast-global-color-2)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":40,"headingFontSizeTablet":32,"headingFontSizeMobile":28,"headingLineHeightDesktop":1.2,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-heading"><h2 class="uagb-heading-text">{{SECTION_HEADLINE}}</h2></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/container {"block_id":"pricing-row","variationSelected":true,"contentWidth":"boxed","directionDesktop":"row","alignItemsDesktop":"stretch","justifyContentDesktop":"space-between","columnGapDesktop":24,"rowGapDesktop":32,"directionTablet":"column","rowGapTablet":24} -->
<div class="wp-block-uagb-container uagb-block-pricing-row">

<!-- wp:uagb/container {"block_id":"pricing-tier-1","variationSelected":true,"contentWidth":"boxed","directionDesktop":"column","alignItemsDesktop":"flex-start","rowGapDesktop":24,"widthDesktop":32,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%","topPaddingDesktop":40,"bottomPaddingDesktop":40,"leftPaddingDesktop":32,"rightPaddingDesktop":32,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)","borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(0,0,0,0.08)","boxShadowVOffset":4,"boxShadowBlur":16} -->
<div class="wp-block-uagb-container uagb-block-pricing-tier-1"><!-- wp:uagb/advanced-heading {"block_id":"pricing-t1-name","headingTag":"h3","headingTitle":"{{T1_NAME}}","headingDesc":"{{T1_DESC}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":700,"headingFontSizeDesktop":24,"subHeadingFontSizeDesktop":15,"subHeadingTopMarginDesktop":8,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t1-name"><h3 class="uagb-heading-text">{{T1_NAME}}</h3><p class="uagb-desc-text">{{T1_DESC}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/advanced-heading {"block_id":"pricing-t1-price","headingTag":"h4","headingTitle":"{{T1_PRICE}}","headingDesc":"{{T1_PERIOD}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":800,"headingFontSizeDesktop":48,"subHeadingFontSizeDesktop":14,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t1-price"><h4 class="uagb-heading-text">{{T1_PRICE}}</h4><p class="uagb-desc-text">{{T1_PERIOD}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/icon-list {"block_id":"pricing-t1-features","gap":12,"icon_layout":"horizontal","label_color":"var(--ast-global-color-3)","icon_color":"var(--ast-global-color-0)"} -->
<div class="wp-block-uagb-icon-list uagb-block-pricing-t1-features"><!-- wp:uagb/icon-list-child {"block_id":"t1-feat-1","label":"{{T1_FEAT1}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t1-feat-1"><span class="uagb-icon-list-label">{{T1_FEAT1}}</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"t1-feat-2","label":"{{T1_FEAT2}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t1-feat-2"><span class="uagb-icon-list-label">{{T1_FEAT2}}</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"t1-feat-3","label":"{{T1_FEAT3}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t1-feat-3"><span class="uagb-icon-list-label">{{T1_FEAT3}}</span></div>
<!-- /wp:uagb/icon-list-child --></div>
<!-- /wp:uagb/icon-list -->

<!-- wp:uagb/buttons {"block_id":"pricing-t1-cta-wrap","align":"left"} -->
<div class="wp-block-uagb-buttons uagb-block-pricing-t1-cta-wrap"><!-- wp:uagb/buttons-child {"block_id":"pricing-t1-cta","label":"{{T1_CTA}}","link":"{{T1_LINK}}","backgroundColor":"transparent","color":"var(--ast-global-color-0)","borderColor":"var(--ast-global-color-0)","borderStyle":"solid","borderWidth":2,"borderRadius":8,"hoverBackgroundColor":"var(--ast-global-color-0)","hoverColor":"var(--ast-global-color-5)","sizeType":"px","size":16,"paddingTop":12,"paddingBottom":12,"paddingLeft":24,"paddingRight":24,"fontWeight":600} -->
<div class="wp-block-uagb-buttons-child uagb-block-pricing-t1-cta"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{T1_LINK}}"><span class="uagb-button-text">{{T1_CTA}}</span></a></div>
<!-- /wp:uagb/buttons-child --></div>
<!-- /wp:uagb/buttons --></div>
<!-- /wp:uagb/container -->

<!-- wp:uagb/container {"block_id":"pricing-tier-2","variationSelected":true,"contentWidth":"boxed","directionDesktop":"column","alignItemsDesktop":"flex-start","rowGapDesktop":24,"widthDesktop":34,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%","topPaddingDesktop":48,"bottomPaddingDesktop":48,"leftPaddingDesktop":32,"rightPaddingDesktop":32,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)","borderStyle":"solid","borderTopWidth":2,"borderRightWidth":2,"borderBottomWidth":2,"borderLeftWidth":2,"borderColor":"var(--ast-global-color-0)","borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(0,0,0,0.16)","boxShadowVOffset":12,"boxShadowBlur":40} -->
<div class="wp-block-uagb-container uagb-block-pricing-tier-2"><!-- wp:uagb/advanced-heading {"block_id":"pricing-t2-badge","headingTag":"h6","headingTitle":"Le plus populaire","headingColor":"var(--ast-global-color-0)","headingAlign":"left","headingFontWeight":700,"headingFontSizeDesktop":13,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t2-badge"><h6 class="uagb-heading-text">Le plus populaire</h6></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/advanced-heading {"block_id":"pricing-t2-name","headingTag":"h3","headingTitle":"{{T2_NAME}}","headingDesc":"{{T2_DESC}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":700,"headingFontSizeDesktop":24,"subHeadingFontSizeDesktop":15,"subHeadingTopMarginDesktop":8,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t2-name"><h3 class="uagb-heading-text">{{T2_NAME}}</h3><p class="uagb-desc-text">{{T2_DESC}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/advanced-heading {"block_id":"pricing-t2-price","headingTag":"h4","headingTitle":"{{T2_PRICE}}","headingDesc":"{{T2_PERIOD}}","headingColor":"var(--ast-global-color-0)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":800,"headingFontSizeDesktop":56,"subHeadingFontSizeDesktop":14,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t2-price"><h4 class="uagb-heading-text">{{T2_PRICE}}</h4><p class="uagb-desc-text">{{T2_PERIOD}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/icon-list {"block_id":"pricing-t2-features","gap":12,"icon_layout":"horizontal","label_color":"var(--ast-global-color-2)","icon_color":"var(--ast-global-color-0)"} -->
<div class="wp-block-uagb-icon-list uagb-block-pricing-t2-features"><!-- wp:uagb/icon-list-child {"block_id":"t2-feat-1","label":"{{T2_FEAT1}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t2-feat-1"><span class="uagb-icon-list-label">{{T2_FEAT1}}</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"t2-feat-2","label":"{{T2_FEAT2}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t2-feat-2"><span class="uagb-icon-list-label">{{T2_FEAT2}}</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"t2-feat-3","label":"{{T2_FEAT3}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t2-feat-3"><span class="uagb-icon-list-label">{{T2_FEAT3}}</span></div>
<!-- /wp:uagb/icon-list-child --></div>
<!-- /wp:uagb/icon-list -->

<!-- wp:uagb/buttons {"block_id":"pricing-t2-cta-wrap","align":"left"} -->
<div class="wp-block-uagb-buttons uagb-block-pricing-t2-cta-wrap"><!-- wp:uagb/buttons-child {"block_id":"pricing-t2-cta","label":"{{T2_CTA}}","link":"{{T2_LINK}}","backgroundColor":"var(--ast-global-color-0)","color":"var(--ast-global-color-5)","hoverBackgroundColor":"var(--ast-global-color-1)","hoverColor":"var(--ast-global-color-5)","borderRadius":8,"sizeType":"px","size":16,"paddingTop":14,"paddingBottom":14,"paddingLeft":28,"paddingRight":28,"fontWeight":600} -->
<div class="wp-block-uagb-buttons-child uagb-block-pricing-t2-cta"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{T2_LINK}}"><span class="uagb-button-text">{{T2_CTA}}</span></a></div>
<!-- /wp:uagb/buttons-child --></div>
<!-- /wp:uagb/buttons --></div>
<!-- /wp:uagb/container -->

<!-- wp:uagb/container {"block_id":"pricing-tier-3","variationSelected":true,"contentWidth":"boxed","directionDesktop":"column","alignItemsDesktop":"flex-start","rowGapDesktop":24,"widthDesktop":32,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%","topPaddingDesktop":40,"bottomPaddingDesktop":40,"leftPaddingDesktop":32,"rightPaddingDesktop":32,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)","borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(0,0,0,0.08)","boxShadowVOffset":4,"boxShadowBlur":16} -->
<div class="wp-block-uagb-container uagb-block-pricing-tier-3"><!-- wp:uagb/advanced-heading {"block_id":"pricing-t3-name","headingTag":"h3","headingTitle":"{{T3_NAME}}","headingDesc":"{{T3_DESC}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":700,"headingFontSizeDesktop":24,"subHeadingFontSizeDesktop":15,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t3-name"><h3 class="uagb-heading-text">{{T3_NAME}}</h3><p class="uagb-desc-text">{{T3_DESC}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/advanced-heading {"block_id":"pricing-t3-price","headingTag":"h4","headingTitle":"{{T3_PRICE}}","headingDesc":"{{T3_PERIOD}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":800,"headingFontSizeDesktop":48,"subHeadingFontSizeDesktop":14,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t3-price"><h4 class="uagb-heading-text">{{T3_PRICE}}</h4><p class="uagb-desc-text">{{T3_PERIOD}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/icon-list {"block_id":"pricing-t3-features","gap":12,"icon_layout":"horizontal","label_color":"var(--ast-global-color-3)","icon_color":"var(--ast-global-color-0)"} -->
<div class="wp-block-uagb-icon-list uagb-block-pricing-t3-features"><!-- wp:uagb/icon-list-child {"block_id":"t3-feat-1","label":"{{T3_FEAT1}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t3-feat-1"><span class="uagb-icon-list-label">{{T3_FEAT1}}</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"t3-feat-2","label":"{{T3_FEAT2}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t3-feat-2"><span class="uagb-icon-list-label">{{T3_FEAT2}}</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"t3-feat-3","label":"{{T3_FEAT3}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t3-feat-3"><span class="uagb-icon-list-label">{{T3_FEAT3}}</span></div>
<!-- /wp:uagb/icon-list-child --></div>
<!-- /wp:uagb/icon-list -->

<!-- wp:uagb/buttons {"block_id":"pricing-t3-cta-wrap","align":"left"} -->
<div class="wp-block-uagb-buttons uagb-block-pricing-t3-cta-wrap"><!-- wp:uagb/buttons-child {"block_id":"pricing-t3-cta","label":"{{T3_CTA}}","link":"{{T3_LINK}}","backgroundColor":"transparent","color":"var(--ast-global-color-0)","borderColor":"var(--ast-global-color-0)","borderStyle":"solid","borderWidth":2,"borderRadius":8,"hoverBackgroundColor":"var(--ast-global-color-0)","hoverColor":"var(--ast-global-color-5)","sizeType":"px","size":16,"paddingTop":12,"paddingBottom":12,"paddingLeft":24,"paddingRight":24,"fontWeight":600} -->
<div class="wp-block-uagb-buttons-child uagb-block-pricing-t3-cta"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{T3_LINK}}"><span class="uagb-button-text">{{T3_CTA}}</span></a></div>
<!-- /wp:uagb/buttons-child --></div>
<!-- /wp:uagb/buttons --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container -->
```

## Changements vs version précédente (correctifs v0.8.2)

- **Retiré** : `<span class="uagb-icon-list-source"><svg></svg></span>` du HTML rendu (le SVG vide divergeait du SVG check réel généré par Spectra → warning Gutenberg)
- **Retiré** : `boxShadowColor:"rgba(255,140,0,0.18)"` (orange WPF hardcodé) sur le tier 2. Remplacé par `rgba(0,0,0,0.16)` neutre qui marche sur n'importe quelle palette
- **Changé** : `headingTag:"div"` du prix → `headingTag:"h4"` (sémantique correcte, et Spectra accepte h1-h6 standard, pas `div` qui peut déclencher un fallback)
- **Changé** : `headingTag:"div"` du badge populaire → `headingTag:"h6"` (sémantique faible, OK pour un label)
- **Changé** : badge « ⭐ Le plus populaire » → « Le plus populaire » (l'emoji dans `headingTitle` JSON peut causer des problèmes d'encodage selon les configs MySQL)
- **Ajouté** : `t1-feat-1, t1-feat-2, t1-feat-3` au lieu d'un seul `t1-feat` (block_id unique pour chaque feature ; même pattern pour t2 et t3)
- **Ajouté** : variables `{{T1_FEAT1}}`, `{{T1_FEAT2}}`, `{{T1_FEAT3}}` (une feature par variable, plus simple à remplir que `{{T1_FEATURES}}` séparées par `\n`)
- **Changé** : `backgroundColor` section parent de `--ast-global-color-4` (accent) → `--ast-global-color-7` (off-white). Plus standard pour pricing
- **Changé** : `hoverColor` boutons de `--ast-global-color-4` → `--ast-global-color-5` (white sur fond orange = lisible)
- **Ajouté** : `separatorWidth: 0` sur les `uagb/advanced-heading` (évite le séparateur fin par défaut sous le titre, plus propre pour un pricing)
- **Ajouté** : box-shadow léger sur tiers 1 et 3 pour cohérence visuelle avec tier 2 (just less marqué)

## Pour ajouter plus de 3 features par tier

Dupliquer le bloc `uagb/icon-list-child` en incrémentant le block_id et le numéro de variable :

```html
<!-- wp:uagb/icon-list-child {"block_id":"t1-feat-4","label":"{{T1_FEAT4}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t1-feat-4"><span class="uagb-icon-list-label">{{T1_FEAT4}}</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"t1-feat-5","label":"{{T1_FEAT5}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t1-feat-5"><span class="uagb-icon-list-label">{{T1_FEAT5}}</span></div>
<!-- /wp:uagb/icon-list-child -->
```

`block_id` doit RESTER UNIQUE dans toute la page. Pas seulement dans le tier — globalement.

## Variantes

### Variante 1 — 4 plans (Free + 3 paid)

Réduire `widthDesktop` à 24% et ajouter un 4e tier identique au tier 1.

### Variante 2 — Pricing avec toggle Mensuel/Annuel

Livrer 2 sections (mensuelle + annuelle) avec un `uagb/buttons` toggle au-dessus. La logique d'affichage requiert du JS custom non couvert par le markup statique.

### Variante 3 — Mode dark

Section parent : `backgroundColor: var(--ast-global-color-3)`. Cards 1 et 3 : `backgroundColor: var(--ast-global-color-2)`. Card 2 (mise en avant) : `backgroundColor: var(--ast-global-color-7)` (off-white sur fond dark = très contraste).

## Block IDs (référence)

- `pricing-section`, `pricing-heading`, `pricing-row`
- `pricing-tier-1`, `pricing-tier-2`, `pricing-tier-3`
- `pricing-t1-name`, `pricing-t1-price`, `pricing-t1-features`, `pricing-t1-cta-wrap`, `pricing-t1-cta`
- `t1-feat-1`, `t1-feat-2`, `t1-feat-3` (et `-4`, `-5`... si plus de 3 features)
- (idem pour t2 et t3)
- `pricing-t2-badge` (badge populaire, uniquement tier 2)

## Test post-génération

Ouvrir la page créée dans Gutenberg authentifié. Vérifier :

1. Aucun warning « invalid content »
2. Les icônes check apparaissent (rendues par Spectra au mount du bloc)
3. Le tier 2 est visuellement distingué (border + shadow plus marqué + badge en haut)
4. Les 3 features par tier s'affichent en liste avec icône check à gauche
5. Les CTAs ont la bonne couleur (ghost sur tiers 1/3, plein sur tier 2)
