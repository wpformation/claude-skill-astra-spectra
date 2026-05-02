# Pattern : Features 3 Colonnes

> **Use case** : Section présentant 3 features clés d'un produit/service avec icône + titre + description. Layout : 3 cards alignées desktop, 1 col mobile. Cards hoverables (ombre + scale subtle).

## Variables d'entrée

| Variable | Description | Exemple |
|----------|-------------|---------|
| `{{SECTION_HEADLINE}}` | Titre de la section | `Pourquoi choisir notre solution` |
| `{{SECTION_SUBLINE}}` | Sous-titre optionnel | `Tout ce qu'il faut pour réussir` |
| `{{F1_ICON}}` / `{{F2_ICON}}` / `{{F3_ICON}}` | **Nom court Spectra** (pas FontAwesome). Voir liste ci-dessous. | `rocket`, `lightbulb`, `chart-pie` |
| `{{F1_TITLE}}` / `{{F2_TITLE}}` / `{{F3_TITLE}}` | Titre H3 | `Rapide` |
| `{{F1_DESC}}` / `{{F2_DESC}}` / `{{F3_DESC}}` | Description | `Page draft en 2 minutes maximum.` |

## Règle critique pour les icônes

Spectra **n'utilise pas** FontAwesome via `<i class="fa-...">`. Il a sa propre lib d'icônes qui rend des SVG inline. Le pattern :

1. Met le **nom court** (ex `"icon":"rocket"`) dans l'attribut JSON
2. Laisse le HTML rendu **sans bloc `<i>` ni `<span class="uagb-icon">`**
3. À l'édition Gutenberg, Spectra re-génère l'icône SVG depuis l'attribut JSON

**Si tu mets un `<i class="fa-...">` dans le HTML, Gutenberg affichera un warning « invalid content »** parce que le HTML re-rendu par Spectra ne contient pas cette balise.

### Noms courts d'icônes Spectra utilisables

Catégories (extraits — voir `references/spectra-icons-list.md` pour la liste exhaustive) :
- **Tech** : `rocket`, `lightbulb`, `code`, `desktop`, `mobile`, `terminal`, `cog`, `database`
- **Business** : `chart-pie`, `chart-line`, `chart-bar`, `briefcase`, `building`, `dollar-sign`
- **Social** : `users`, `user`, `user-plus`, `comment`, `comments`, `heart`, `thumbs-up`
- **Content** : `book`, `book-open`, `file`, `file-alt`, `bookmark`, `image`, `video`
- **Action** : `check`, `check-circle`, `times`, `arrow-right`, `play`, `pause`, `bolt`, `magic`
- **Education** : `graduation-cap`, `chalkboard-teacher`, `university`, `pen`, `pen-fancy`

## Block markup

```html
<!-- wp:uagb/container {"block_id":"features-section","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":1280,"directionDesktop":"column","alignItemsDesktop":"center","rowGapDesktop":48,"topPaddingDesktop":100,"bottomPaddingDesktop":100,"topPaddingTablet":60,"bottomPaddingTablet":60,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-features-section"><!-- wp:uagb/advanced-heading {"block_id":"features-heading","headingTag":"h2","headingTitle":"{{SECTION_HEADLINE}}","headingDesc":"{{SECTION_SUBLINE}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":40,"headingFontSizeTablet":32,"headingFontSizeMobile":28,"subHeadingFontSizeDesktop":18,"headingLineHeightDesktop":1.2,"subHeadingTopMarginDesktop":16,"separatorWidth":0} -->
<div class="wp-block-uagb-advanced-heading uagb-block-features-heading"><h2 class="uagb-heading-text">{{SECTION_HEADLINE}}</h2><p class="uagb-desc-text">{{SECTION_SUBLINE}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/container {"block_id":"features-row","variationSelected":true,"contentWidth":"boxed","directionDesktop":"row","alignItemsDesktop":"stretch","justifyContentDesktop":"space-between","columnGapDesktop":32,"rowGapDesktop":32,"directionTablet":"column","rowGapTablet":24} -->
<div class="wp-block-uagb-container uagb-block-features-row"><!-- wp:uagb/info-box {"block_id":"feature-1","headingTag":"h3","headingTitle":"{{F1_TITLE}}","headingDesc":"{{F1_DESC}}","source_type":"icon","icon":"{{F1_ICON}}","iconColor":"var(--ast-global-color-0)","iconBgColor":"var(--ast-global-color-7)","iconBgSizeDesktop":80,"iconSizeDesktop":36,"iconimgPosition":"above-title","iconimgBorderRadius":50,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","ctaType":"none","backgroundColor":"var(--ast-global-color-5)","containerPaddingTopDesktop":40,"containerPaddingBottomDesktop":40,"containerPaddingLeftDesktop":32,"containerPaddingRightDesktop":32,"borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(0,0,0,0.08)","boxShadowVOffset":8,"boxShadowBlur":24,"boxShadowColorHover":"rgba(0,0,0,0.16)","boxShadowVOffsetHover":16,"boxShadowBlurHover":40,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-feature-1"><div class="uagb-ifb-content"><div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{F1_TITLE}}</h3></div><p class="uagb-ifb-desc">{{F1_DESC}}</p></div></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/info-box {"block_id":"feature-2","headingTag":"h3","headingTitle":"{{F2_TITLE}}","headingDesc":"{{F2_DESC}}","source_type":"icon","icon":"{{F2_ICON}}","iconColor":"var(--ast-global-color-0)","iconBgColor":"var(--ast-global-color-7)","iconBgSizeDesktop":80,"iconSizeDesktop":36,"iconimgPosition":"above-title","iconimgBorderRadius":50,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","ctaType":"none","backgroundColor":"var(--ast-global-color-5)","containerPaddingTopDesktop":40,"containerPaddingBottomDesktop":40,"containerPaddingLeftDesktop":32,"containerPaddingRightDesktop":32,"borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(0,0,0,0.08)","boxShadowVOffset":8,"boxShadowBlur":24,"boxShadowColorHover":"rgba(0,0,0,0.16)","boxShadowVOffsetHover":16,"boxShadowBlurHover":40,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-feature-2"><div class="uagb-ifb-content"><div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{F2_TITLE}}</h3></div><p class="uagb-ifb-desc">{{F2_DESC}}</p></div></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/info-box {"block_id":"feature-3","headingTag":"h3","headingTitle":"{{F3_TITLE}}","headingDesc":"{{F3_DESC}}","source_type":"icon","icon":"{{F3_ICON}}","iconColor":"var(--ast-global-color-0)","iconBgColor":"var(--ast-global-color-7)","iconBgSizeDesktop":80,"iconSizeDesktop":36,"iconimgPosition":"above-title","iconimgBorderRadius":50,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","ctaType":"none","backgroundColor":"var(--ast-global-color-5)","containerPaddingTopDesktop":40,"containerPaddingBottomDesktop":40,"containerPaddingLeftDesktop":32,"containerPaddingRightDesktop":32,"borderRadiusTopLeft":16,"borderRadiusTopRight":16,"borderRadiusBottomLeft":16,"borderRadiusBottomRight":16,"boxShadowColor":"rgba(0,0,0,0.08)","boxShadowVOffset":8,"boxShadowBlur":24,"boxShadowColorHover":"rgba(0,0,0,0.16)","boxShadowVOffsetHover":16,"boxShadowBlurHover":40,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-feature-3"><div class="uagb-ifb-content"><div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{F3_TITLE}}</h3></div><p class="uagb-ifb-desc">{{F3_DESC}}</p></div></div>
<!-- /wp:uagb/info-box --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container -->
```

## Changements vs version précédente (correctifs 02/05/2026)

- **Retiré** : `<div class="uagb-ifb-icon-wrap"><span class="uagb-icon"><i class="{{F1_ICON}}"></i></span></div>` (incompatible avec rendu Spectra)
- **Ajouté** : `"source_type":"icon"`, `"iconimgPosition":"above-title"`, `"iconimgBorderRadius":50` dans les attrs JSON
- **Ajouté** : structure `uagb-ifb-content` + `uagb-ifb-title-wrap` qui correspond au rendu Spectra réel
- **Changé** : `iconBgColor` de `var(--ast-global-color-5)` → `var(--ast-global-color-7)` (off-white au lieu de body bg) pour pastille visible
- **Changé** : `backgroundColor` cards de `var(--ast-global-color-4)` → `var(--ast-global-color-5)` (body bg) pour fond cohérent palette par défaut

## Variantes

### Variante 1 — 4 colonnes

Remplacer `widthDesktop: 33.33` par `widthDesktop: 24`. Ajouter une 4e info-box.

### Variante 2 — Layout vertical (1 col)

`directionDesktop: "column"`, retirer `widthDesktop` des info-box.

### Variante 3 — Sans icônes (mode minimaliste)

Remplacer chaque info-box par un `uagb/container` enfant avec juste heading + paragraph. Plus typographique.

### Variante 4 — Background dark

Container parent : `backgroundColor: var(--ast-global-color-3)`. Cards : `backgroundColor: var(--ast-global-color-2)`. Heading des cards : `var(--ast-global-color-7)`.

## Block IDs

- `features-section`, `features-heading`, `features-row`
- `feature-1`, `feature-2`, `feature-3`

Renommer si plusieurs sections features sur la même page : `features-saas-1`, `features-pro-1`, etc.

## Test post-génération

Ouvrir la page créée dans Gutenberg authentifié. Aucun warning « invalid content » ne doit apparaître sur les info-box. Si warning : vérifier que le `icon` est bien un nom court (`rocket`, `lightbulb`...) et pas une classe FontAwesome (`fa-rocket`).
