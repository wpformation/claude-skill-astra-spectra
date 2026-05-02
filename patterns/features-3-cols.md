# Pattern : Features 3 Colonnes

> **Use case** : Section présentant 3 features clés d'un produit/service avec icône + titre + description. Layout : 3 cards alignées desktop, 1 col mobile. Cards hoverables (ombre + scale subtle).

## Variables d'entrée

| Variable | Description |
|----------|-------------|
| `{{SECTION_HEADLINE}}` | Titre de la section (ex: « Pourquoi choisir notre solution ») |
| `{{SECTION_SUBLINE}}` | Sous-titre optionnel |
| `{{F1_ICON}}` / `{{F2_ICON}}` / `{{F3_ICON}}` | Classe FontAwesome ou nom icône |
| `{{F1_TITLE}}` / `{{F2_TITLE}}` / `{{F3_TITLE}}` | Titre H3 |
| `{{F1_DESC}}` / `{{F2_DESC}}` / `{{F3_DESC}}` | Description |

## Block markup

```html
<!-- wp:uagb/container {"block_id":"features-section","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":1280,"directionDesktop":"column","alignItemsDesktop":"center","rowGapDesktop":48,"topPaddingDesktop":100,"bottomPaddingDesktop":100,"topPaddingTablet":60,"bottomPaddingTablet":60,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-features-section"><!-- wp:uagb/advanced-heading {"block_id":"features-heading","headingTag":"h2","headingTitle":"{{SECTION_HEADLINE}}","headingDesc":"{{SECTION_SUBLINE}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":40,"headingFontSizeTablet":32,"headingFontSizeMobile":28,"subHeadingFontSizeDesktop":18,"headingLineHeightDesktop":1.2,"subHeadingTopMarginDesktop":16,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-features-heading"><h2 class="uagb-heading-text">{{SECTION_HEADLINE}}</h2><p class="uagb-desc-text">{{SECTION_SUBLINE}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/container {"block_id":"features-row","variationSelected":true,"contentWidth":"boxed","directionDesktop":"row","alignItemsDesktop":"stretch","justifyContentDesktop":"space-between","columnGapDesktop":32,"rowGapDesktop":32,"directionTablet":"column","rowGapTablet":24} -->
<div class="wp-block-uagb-container uagb-block-features-row"><!-- wp:uagb/info-box {"block_id":"feature-1","headingTag":"h3","headingTitle":"{{F1_TITLE}}","headingDesc":"{{F1_DESC}}","showIcon":true,"icon":"{{F1_ICON}}","iconColor":"var(--ast-global-color-0)","iconBgColor":"var(--ast-global-color-5)","iconBgSizeDesktop":80,"iconSizeDesktop":36,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","ctaType":"none","backgroundColor":"var(--ast-global-color-4)","containerPaddingTopDesktop":40,"containerPaddingBottomDesktop":40,"containerPaddingLeftDesktop":32,"containerPaddingRightDesktop":32,"borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(0,0,0,0.08)","boxShadowVOffset":8,"boxShadowBlur":24,"boxShadowColorHover":"rgba(0,0,0,0.16)","boxShadowVOffsetHover":16,"boxShadowBlurHover":40,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-feature-1"><div class="uagb-ifb-icon-wrap"><span class="uagb-icon"><i class="{{F1_ICON}}"></i></span></div><h3 class="uagb-ifb-title">{{F1_TITLE}}</h3><p class="uagb-ifb-desc">{{F1_DESC}}</p></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/info-box {"block_id":"feature-2","headingTag":"h3","headingTitle":"{{F2_TITLE}}","headingDesc":"{{F2_DESC}}","showIcon":true,"icon":"{{F2_ICON}}","iconColor":"var(--ast-global-color-0)","iconBgColor":"var(--ast-global-color-5)","iconBgSizeDesktop":80,"iconSizeDesktop":36,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","ctaType":"none","backgroundColor":"var(--ast-global-color-4)","containerPaddingTopDesktop":40,"containerPaddingBottomDesktop":40,"containerPaddingLeftDesktop":32,"containerPaddingRightDesktop":32,"borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(0,0,0,0.08)","boxShadowVOffset":8,"boxShadowBlur":24,"boxShadowColorHover":"rgba(0,0,0,0.16)","boxShadowVOffsetHover":16,"boxShadowBlurHover":40,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-feature-2"><div class="uagb-ifb-icon-wrap"><span class="uagb-icon"><i class="{{F2_ICON}}"></i></span></div><h3 class="uagb-ifb-title">{{F2_TITLE}}</h3><p class="uagb-ifb-desc">{{F2_DESC}}</p></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/info-box {"block_id":"feature-3","headingTag":"h3","headingTitle":"{{F3_TITLE}}","headingDesc":"{{F3_DESC}}","showIcon":true,"icon":"{{F3_ICON}}","iconColor":"var(--ast-global-color-0)","iconBgColor":"var(--ast-global-color-5)","iconBgSizeDesktop":80,"iconSizeDesktop":36,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","ctaType":"none","backgroundColor":"var(--ast-global-color-4)","containerPaddingTopDesktop":40,"containerPaddingBottomDesktop":40,"containerPaddingLeftDesktop":32,"containerPaddingRightDesktop":32,"borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(0,0,0,0.08)","boxShadowVOffset":8,"boxShadowBlur":24,"boxShadowColorHover":"rgba(0,0,0,0.16)","boxShadowVOffsetHover":16,"boxShadowBlurHover":40,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-feature-3"><div class="uagb-ifb-icon-wrap"><span class="uagb-icon"><i class="{{F3_ICON}}"></i></span></div><h3 class="uagb-ifb-title">{{F3_TITLE}}</h3><p class="uagb-ifb-desc">{{F3_DESC}}</p></div>
<!-- /wp:uagb/info-box --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container -->
```

## Variantes

### Variante 1 — 4 colonnes

Remplacer `widthDesktop: 33.33` par `widthDesktop: 24` (ou `width: 23` avec gap réduit). Ajouter une 4e info-box.

### Variante 2 — Layout vertical (1 col)

`directionDesktop: "column"`, retirer `widthDesktop` des info-box. Pour use case article éditorial.

### Variante 3 — Sans icônes (mode minimaliste)

Remplacer chaque info-box par un `uagb/container` enfant avec juste un heading + paragraph dedans. Plus typographique, moins SaaS.

### Variante 4 — Background dark

Container parent : `backgroundColor: var(--ast-global-color-2)`. Cards : `backgroundColor: var(--ast-global-color-6)`. Heading des cards : `var(--ast-global-color-4)`.

## Block IDs

- `features-section`, `features-heading`, `features-row`
- `feature-1`, `feature-2`, `feature-3`

## Pour 3 features avec contexte spécifique

Renommer en : `features-formation-section`, `features-formation-1`, etc.
