# Pattern : Pricing 3 Tiers

> **Use case** : Section de tarification avec 3 plans (Starter, Pro, Enterprise) côte à côte. Plan central mis en avant (badge « Populaire » + bordure colorée + scale +5%). Liste de features par plan avec icônes check.

## Variables d'entrée

| Variable | Description |
|----------|-------------|
| `{{SECTION_HEADLINE}}` | Titre section (ex: « Tarifs simples et transparents ») |
| `{{T1_NAME}}` / `{{T2_NAME}}` / `{{T3_NAME}}` | Nom du plan |
| `{{T1_PRICE}}` / `{{T2_PRICE}}` / `{{T3_PRICE}}` | Prix (ex: « 19 € ») |
| `{{T1_PERIOD}}` / `{{T2_PERIOD}}` / `{{T3_PERIOD}}` | Période (« /mois ») |
| `{{T1_DESC}}` / `{{T2_DESC}}` / `{{T3_DESC}}` | Description courte |
| `{{T1_FEATURES}}` / `{{T2_FEATURES}}` / `{{T3_FEATURES}}` | Liste des features (\n separated) |
| `{{T1_CTA}}` / `{{T2_CTA}}` / `{{T3_CTA}}` | Texte CTA |
| `{{T1_LINK}}` / `{{T2_LINK}}` / `{{T3_LINK}}` | URL CTA |

## Block markup

```html
<!-- wp:uagb/container {"block_id":"pricing-section","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":1280,"directionDesktop":"column","alignItemsDesktop":"center","rowGapDesktop":56,"topPaddingDesktop":100,"bottomPaddingDesktop":100,"backgroundType":"color","backgroundColor":"var(--ast-global-color-4)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-pricing-section"><!-- wp:uagb/advanced-heading {"block_id":"pricing-heading","headingTag":"h2","headingTitle":"{{SECTION_HEADLINE}}","headingColor":"var(--ast-global-color-2)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":40,"headingLineHeightDesktop":1.2} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-heading"><h2 class="uagb-heading-text">{{SECTION_HEADLINE}}</h2></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/container {"block_id":"pricing-row","variationSelected":true,"contentWidth":"boxed","directionDesktop":"row","alignItemsDesktop":"center","justifyContentDesktop":"space-between","columnGapDesktop":24,"rowGapDesktop":32,"directionTablet":"column","rowGapTablet":24} -->
<div class="wp-block-uagb-container uagb-block-pricing-row">

<!-- wp:uagb/container {"block_id":"pricing-tier-1","variationSelected":true,"contentWidth":"boxed","directionDesktop":"column","alignItemsDesktop":"flex-start","rowGapDesktop":24,"widthDesktop":32,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%","topPaddingDesktop":40,"bottomPaddingDesktop":40,"leftPaddingDesktop":32,"rightPaddingDesktop":32,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)","borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16} -->
<div class="wp-block-uagb-container uagb-block-pricing-tier-1"><!-- wp:uagb/advanced-heading {"block_id":"pricing-t1-name","headingTag":"h3","headingTitle":"{{T1_NAME}}","headingDesc":"{{T1_DESC}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":700,"headingFontSizeDesktop":24,"subHeadingFontSizeDesktop":15,"subHeadingTopMarginDesktop":8} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t1-name"><h3 class="uagb-heading-text">{{T1_NAME}}</h3><p class="uagb-desc-text">{{T1_DESC}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/advanced-heading {"block_id":"pricing-t1-price","headingTag":"div","headingTitle":"{{T1_PRICE}}","headingDesc":"{{T1_PERIOD}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":800,"headingFontSizeDesktop":48,"subHeadingFontSizeDesktop":14} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t1-price"><div class="uagb-heading-text">{{T1_PRICE}}</div><p class="uagb-desc-text">{{T1_PERIOD}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/icon-list {"block_id":"pricing-t1-features","gap":12,"icon_layout":"horizontal","label_color":"var(--ast-global-color-3)","icon_color":"var(--ast-global-color-0)"} -->
<div class="wp-block-uagb-icon-list uagb-block-pricing-t1-features"><!-- wp:uagb/icon-list-child {"block_id":"t1-feat","label":"{{T1_FEATURES}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t1-feat"><span class="uagb-icon-list-source"><svg></svg></span><span class="uagb-icon-list-label">{{T1_FEATURES}}</span></div>
<!-- /wp:uagb/icon-list-child --></div>
<!-- /wp:uagb/icon-list -->

<!-- wp:uagb/buttons {"block_id":"pricing-t1-cta-wrap","align":"left"} -->
<div class="wp-block-uagb-buttons uagb-block-pricing-t1-cta-wrap"><!-- wp:uagb/buttons-child {"block_id":"pricing-t1-cta","label":"{{T1_CTA}}","link":"{{T1_LINK}}","backgroundColor":"transparent","color":"var(--ast-global-color-0)","borderColor":"var(--ast-global-color-0)","borderStyle":"solid","borderWidth":2,"borderRadius":8,"hoverBackgroundColor":"var(--ast-global-color-0)","hoverColor":"var(--ast-global-color-4)","sizeType":"px","size":16,"paddingTop":12,"paddingBottom":12,"paddingLeft":24,"paddingRight":24,"fontWeight":600} -->
<div class="wp-block-uagb-buttons-child uagb-block-pricing-t1-cta"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{T1_LINK}}"><span class="uagb-button-text">{{T1_CTA}}</span></a></div>
<!-- /wp:uagb/buttons-child --></div>
<!-- /wp:uagb/buttons --></div>
<!-- /wp:uagb/container -->

<!-- wp:uagb/container {"block_id":"pricing-tier-2","variationSelected":true,"contentWidth":"boxed","directionDesktop":"column","alignItemsDesktop":"flex-start","rowGapDesktop":24,"widthDesktop":34,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%","topPaddingDesktop":48,"bottomPaddingDesktop":48,"leftPaddingDesktop":32,"rightPaddingDesktop":32,"backgroundType":"color","backgroundColor":"var(--ast-global-color-4)","borderStyle":"solid","borderTopWidth":2,"borderRightWidth":2,"borderBottomWidth":2,"borderLeftWidth":2,"borderColor":"var(--ast-global-color-0)","borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(255,140,0,0.18)","boxShadowVOffset":12,"boxShadowBlur":40} -->
<div class="wp-block-uagb-container uagb-block-pricing-tier-2"><!-- wp:uagb/advanced-heading {"block_id":"pricing-t2-badge","headingTag":"div","headingTitle":"⭐ Le plus populaire","headingColor":"var(--ast-global-color-0)","headingAlign":"left","headingFontWeight":700,"headingFontSizeDesktop":13} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t2-badge"><div class="uagb-heading-text">⭐ Le plus populaire</div></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/advanced-heading {"block_id":"pricing-t2-name","headingTag":"h3","headingTitle":"{{T2_NAME}}","headingDesc":"{{T2_DESC}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":700,"headingFontSizeDesktop":24,"subHeadingFontSizeDesktop":15,"subHeadingTopMarginDesktop":8} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t2-name"><h3 class="uagb-heading-text">{{T2_NAME}}</h3><p class="uagb-desc-text">{{T2_DESC}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/advanced-heading {"block_id":"pricing-t2-price","headingTag":"div","headingTitle":"{{T2_PRICE}}","headingDesc":"{{T2_PERIOD}}","headingColor":"var(--ast-global-color-0)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":800,"headingFontSizeDesktop":56,"subHeadingFontSizeDesktop":14} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t2-price"><div class="uagb-heading-text">{{T2_PRICE}}</div><p class="uagb-desc-text">{{T2_PERIOD}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/icon-list {"block_id":"pricing-t2-features","gap":12,"icon_layout":"horizontal","label_color":"var(--ast-global-color-2)","icon_color":"var(--ast-global-color-0)"} -->
<div class="wp-block-uagb-icon-list uagb-block-pricing-t2-features"><!-- wp:uagb/icon-list-child {"block_id":"t2-feat","label":"{{T2_FEATURES}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t2-feat"><span class="uagb-icon-list-source"><svg></svg></span><span class="uagb-icon-list-label">{{T2_FEATURES}}</span></div>
<!-- /wp:uagb/icon-list-child --></div>
<!-- /wp:uagb/icon-list -->

<!-- wp:uagb/buttons {"block_id":"pricing-t2-cta-wrap","align":"left"} -->
<div class="wp-block-uagb-buttons uagb-block-pricing-t2-cta-wrap"><!-- wp:uagb/buttons-child {"block_id":"pricing-t2-cta","label":"{{T2_CTA}}","link":"{{T2_LINK}}","backgroundColor":"var(--ast-global-color-0)","color":"var(--ast-global-color-4)","hoverBackgroundColor":"var(--ast-global-color-1)","hoverColor":"var(--ast-global-color-4)","borderRadius":8,"sizeType":"px","size":16,"paddingTop":14,"paddingBottom":14,"paddingLeft":28,"paddingRight":28,"fontWeight":600} -->
<div class="wp-block-uagb-buttons-child uagb-block-pricing-t2-cta"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{T2_LINK}}"><span class="uagb-button-text">{{T2_CTA}}</span></a></div>
<!-- /wp:uagb/buttons-child --></div>
<!-- /wp:uagb/buttons --></div>
<!-- /wp:uagb/container -->

<!-- wp:uagb/container {"block_id":"pricing-tier-3","variationSelected":true,"contentWidth":"boxed","directionDesktop":"column","alignItemsDesktop":"flex-start","rowGapDesktop":24,"widthDesktop":32,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%","topPaddingDesktop":40,"bottomPaddingDesktop":40,"leftPaddingDesktop":32,"rightPaddingDesktop":32,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)","borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16} -->
<div class="wp-block-uagb-container uagb-block-pricing-tier-3"><!-- wp:uagb/advanced-heading {"block_id":"pricing-t3-name","headingTag":"h3","headingTitle":"{{T3_NAME}}","headingDesc":"{{T3_DESC}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":700,"headingFontSizeDesktop":24,"subHeadingFontSizeDesktop":15} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t3-name"><h3 class="uagb-heading-text">{{T3_NAME}}</h3><p class="uagb-desc-text">{{T3_DESC}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/advanced-heading {"block_id":"pricing-t3-price","headingTag":"div","headingTitle":"{{T3_PRICE}}","headingDesc":"{{T3_PERIOD}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":800,"headingFontSizeDesktop":48,"subHeadingFontSizeDesktop":14} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-t3-price"><div class="uagb-heading-text">{{T3_PRICE}}</div><p class="uagb-desc-text">{{T3_PERIOD}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/icon-list {"block_id":"pricing-t3-features","gap":12,"icon_layout":"horizontal","label_color":"var(--ast-global-color-3)","icon_color":"var(--ast-global-color-0)"} -->
<div class="wp-block-uagb-icon-list uagb-block-pricing-t3-features"><!-- wp:uagb/icon-list-child {"block_id":"t3-feat","label":"{{T3_FEATURES}}","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-t3-feat"><span class="uagb-icon-list-source"><svg></svg></span><span class="uagb-icon-list-label">{{T3_FEATURES}}</span></div>
<!-- /wp:uagb/icon-list-child --></div>
<!-- /wp:uagb/icon-list -->

<!-- wp:uagb/buttons {"block_id":"pricing-t3-cta-wrap","align":"left"} -->
<div class="wp-block-uagb-buttons uagb-block-pricing-t3-cta-wrap"><!-- wp:uagb/buttons-child {"block_id":"pricing-t3-cta","label":"{{T3_CTA}}","link":"{{T3_LINK}}","backgroundColor":"transparent","color":"var(--ast-global-color-0)","borderColor":"var(--ast-global-color-0)","borderStyle":"solid","borderWidth":2,"borderRadius":8,"hoverBackgroundColor":"var(--ast-global-color-0)","hoverColor":"var(--ast-global-color-4)","sizeType":"px","size":16,"paddingTop":12,"paddingBottom":12,"paddingLeft":24,"paddingRight":24,"fontWeight":600} -->
<div class="wp-block-uagb-buttons-child uagb-block-pricing-t3-cta"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{T3_LINK}}"><span class="uagb-button-text">{{T3_CTA}}</span></a></div>
<!-- /wp:uagb/buttons-child --></div>
<!-- /wp:uagb/buttons --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container -->
```

## Notes importantes

- **Tier 2 (central) est mis en avant** : bordure couleur primaire 2px, box shadow plus marquée, prix en couleur primaire, badge « Le plus populaire » en haut, CTA primaire (vs CTAs ghost sur tier 1 et 3)
- **Layout responsive** : 3 colonnes desktop avec gap, 1 colonne mobile (stack)
- **Multiple features par tier** : pour ajouter plusieurs features, dupliquer le `uagb/icon-list-child` autant de fois que nécessaire avec un `block_id` unique pour chacun (ex: `t1-feat-1`, `t1-feat-2`, ...)

## Variantes

### Variante 1 — 4 plans (Free + 3 paid)

Réduire `widthDesktop` à 24% et ajouter un 4e tier identique au tier 1.

### Variante 2 — Pricing avec toggle Mensuel/Annuel

Ajouter un container parent au-dessus de `pricing-row` avec un `uagb/buttons` (deux boutons toggle). Le pricing doit être dupliqué côté JS — pour le markup statique, livrer 2 sections (mensuel + annuel) avec un attribut `style="display:none"` géré ensuite par CSS/JS custom.

### Variante 3 — Mode dark

Container parent : `backgroundColor: var(--ast-global-color-2)`. Cards 1 et 3 : `backgroundColor: var(--ast-global-color-6)`. Card 2 (mise en avant) : `backgroundColor: var(--ast-global-color-4)` (white sur fond dark = très contraste).

## Block IDs

- `pricing-section`, `pricing-heading`, `pricing-row`
- `pricing-tier-1`, `pricing-tier-2`, `pricing-tier-3`
- `pricing-t1-name`, `pricing-t1-price`, `pricing-t1-features`, `pricing-t1-cta`, `pricing-t1-cta-wrap`, `t1-feat`
- (idem pour t2 et t3)
- `pricing-t2-badge` (badge populaire, uniquement tier 2)
