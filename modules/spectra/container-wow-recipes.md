# `uagb/container` — Recettes WOW

> **Le bloc `uagb/container` est le bloc le plus puissant et le plus utilisé du skill.** Toutes les mises en forme « effet wow » sortent de ce bloc — pas de `core/group` ni de `core/columns` ni de `core/cover` quand on peut faire mieux. C'est le vrai différenciateur Spectra vs Gutenberg core.

## Pourquoi `uagb/container` est la fondation du skill

Le bloc `uagb/container` couvre TOUT ce que font `core/group`, `core/columns`, `core/cover`, `core/media-text` réunis, et **bien plus** :

- **Backgrounds avancés** : color, gradient, image (parallax/fixed/scale), video, overlay
- **Mise en page flex/grid** : 1 col, 2 cols, 3 cols, 4 cols, 5 cols, 6 cols, custom
- **Padding/margin responsive** : desktop / tablet / mobile (3 breakpoints)
- **Box shadow + hover** : customisation totale
- **Border radius** : par coin
- **Animations au scroll** : fade, slide, zoom (built-in)
- **Inner content width** : alignfull / alignwide / boxed avec largeur custom
- **Min height** : avec unités multiples (px, vh, %, em)
- **Direction** : row, column, row-reverse, column-reverse
- **Flex alignment** : items + justify content
- **Gap** : row + column gap indépendants

→ **Règle stricte du skill** : pour TOUTE section nouvelle, le wrapper est `uagb/container`. Jamais `core/group`. Jamais `core/cover`. Jamais `core/columns`.

## 12 recettes WOW prêtes à l'emploi

### Recette 1 — Hero pleine page avec background image + overlay sombre

```json
{
  "block_id": "hero-bg-image",
  "variationSelected": true,
  "contentWidth": "alignfull",
  "innerContentCustomWidthDesktop": 1140,
  "directionDesktop": "column",
  "alignItemsDesktop": "center",
  "justifyContentDesktop": "center",
  "minHeightDesktop": 100,
  "minHeightTypeDesktop": "vh",
  "topPaddingDesktop": 120,
  "bottomPaddingDesktop": 120,
  "backgroundType": "image",
  "backgroundImageDesktop": { "url": "https://exemple.com/hero-bg.jpg", "id": 0 },
  "backgroundSizeDesktop": "cover",
  "backgroundPositionDesktop": { "x": 0.5, "y": 0.5 },
  "backgroundRepeatDesktop": "no-repeat",
  "backgroundAttachmentDesktop": "fixed",
  "overlayType": "color",
  "backgroundImageColor": "rgba(0,0,0,0.55)"
}
```

**Effet** : Image full-screen avec parallax fixed, overlay sombre 55%, contenu centré verticalement et horizontalement, hauteur 100vh.

### Recette 2 — Section avec gradient diagonal animé

```json
{
  "block_id": "section-gradient-anim",
  "variationSelected": true,
  "contentWidth": "alignfull",
  "topPaddingDesktop": 100,
  "bottomPaddingDesktop": 100,
  "backgroundType": "gradient",
  "backgroundGradientType": "linear",
  "backgroundGradientColor1": "var(--ast-global-color-0)",
  "backgroundGradientLocation1": 0,
  "backgroundGradientColor2": "var(--ast-global-color-1)",
  "backgroundGradientLocation2": 100,
  "backgroundGradientAngle": 135,
  "backgroundCustomSizeDesktop": 200,
  "backgroundCustomSizeType": "%"
}
```

**Effet** : Section pleine largeur avec gradient diagonal entre couleur primaire et primaire darker. Si on fait varier `backgroundCustomSize` au scroll via CSS personnalisé, on obtient un effet de respiration.

### Recette 3 — Card avec glassmorphism (effet glass moderne)

```json
{
  "block_id": "card-glass",
  "variationSelected": true,
  "contentWidth": "boxed",
  "innerContentCustomWidthDesktop": 800,
  "topPaddingDesktop": 60,
  "bottomPaddingDesktop": 60,
  "leftPaddingDesktop": 60,
  "rightPaddingDesktop": 60,
  "backgroundType": "color",
  "backgroundColor": "rgba(255,255,255,0.15)",
  "backdropFilterValue": 16,
  "backdropFilterType": "blur",
  "borderStyle": "solid",
  "borderTopWidth": 1,
  "borderRightWidth": 1,
  "borderBottomWidth": 1,
  "borderLeftWidth": 1,
  "borderColor": "rgba(255,255,255,0.3)",
  "borderRadiusTopLeft": 24,
  "borderRadiusTopRight": 24,
  "borderRadiusBottomLeft": 24,
  "borderRadiusBottomRight": 24,
  "boxShadowColor": "rgba(0,0,0,0.2)",
  "boxShadowHOffset": 0,
  "boxShadowVOffset": 12,
  "boxShadowBlur": 40,
  "boxShadowSpread": 0
}
```

**Effet** : Card translucide blanche avec backdrop-filter blur 16px, bordure subtile, border-radius 24px, ombre douce. Idéal posé sur un background coloré ou image.

### Recette 4 — Section diagonale (skew)

```json
{
  "block_id": "section-diagonal",
  "variationSelected": true,
  "contentWidth": "alignfull",
  "topPaddingDesktop": 140,
  "bottomPaddingDesktop": 140,
  "backgroundType": "color",
  "backgroundColor": "var(--ast-global-color-2)",
  "topDividerStyle": "tilt",
  "topDividerColor": "var(--ast-global-color-4)",
  "topDividerHeight": 80,
  "topDividerWidth": 100,
  "bottomDividerStyle": "tilt-opacity",
  "bottomDividerColor": "var(--ast-global-color-4)",
  "bottomDividerHeight": 80,
  "bottomDividerWidth": 100
}
```

**Effet** : Section pleine largeur avec dividers en haut et en bas (tilt), couleur de fond contrastante. Crée des transitions visuelles élégantes entre sections.

### Recette 5 — Container avec background video

```json
{
  "block_id": "section-bg-video",
  "variationSelected": true,
  "contentWidth": "alignfull",
  "minHeightDesktop": 100,
  "minHeightTypeDesktop": "vh",
  "directionDesktop": "column",
  "alignItemsDesktop": "center",
  "justifyContentDesktop": "center",
  "backgroundType": "video",
  "backgroundVideo": { "url": "https://exemple.com/bg-video.mp4", "id": 0 },
  "backgroundVideoFallbackImage": { "url": "https://exemple.com/fallback.jpg", "id": 0 },
  "overlayType": "color",
  "backgroundImageColor": "rgba(0,0,0,0.6)"
}
```

**Effet** : Vidéo en boucle full-screen en fond, fallback image, overlay sombre. Pour un hero immersif (formation, agence, lifestyle).

### Recette 6 — 3 colonnes alignées avec gap large

```json
{
  "block_id": "container-3-cols",
  "variationSelected": true,
  "contentWidth": "alignwide",
  "innerContentCustomWidthDesktop": 1280,
  "directionDesktop": "row",
  "alignItemsDesktop": "stretch",
  "justifyContentDesktop": "space-between",
  "rowGapDesktop": 40,
  "columnGapDesktop": 40,
  "rowGapTablet": 30,
  "columnGapTablet": 0,
  "directionTablet": "column",
  "topPaddingDesktop": 80,
  "bottomPaddingDesktop": 80,
  "backgroundType": "color",
  "backgroundColor": "var(--ast-global-color-5)"
}
```

**Effet** : Layout 3 colonnes desktop, 1 colonne tablet/mobile (responsive automatique), gap large, fond clair. Classique pour features ou pricing tiers.

### Recette 7 — Card hover avec scale + shadow

```json
{
  "block_id": "card-hover",
  "variationSelected": true,
  "contentWidth": "boxed",
  "topPaddingDesktop": 40,
  "bottomPaddingDesktop": 40,
  "leftPaddingDesktop": 40,
  "rightPaddingDesktop": 40,
  "backgroundType": "color",
  "backgroundColor": "var(--ast-global-color-4)",
  "borderRadiusTopLeft": 16,
  "borderRadiusTopRight": 16,
  "borderRadiusBottomLeft": 16,
  "borderRadiusBottomRight": 16,
  "boxShadowColor": "rgba(0,0,0,0.08)",
  "boxShadowHOffset": 0,
  "boxShadowVOffset": 8,
  "boxShadowBlur": 24,
  "boxShadowSpread": 0,
  "boxShadowColorHover": "rgba(0,0,0,0.18)",
  "boxShadowHOffsetHover": 0,
  "boxShadowVOffsetHover": 16,
  "boxShadowBlurHover": 40,
  "boxShadowSpreadHover": 0
}
```

**Effet** : Card blanche avec ombre douce. Au hover, l'ombre s'intensifie. Combinable avec un transform: scale(1.02) via CSS custom pour effet 3D.

### Recette 8 — Section split (50/50) image + texte

```json
{
  "block_id": "split-50-50",
  "variationSelected": true,
  "contentWidth": "alignfull",
  "directionDesktop": "row",
  "alignItemsDesktop": "stretch",
  "justifyContentDesktop": "space-between",
  "rowGapDesktop": 0,
  "columnGapDesktop": 0,
  "directionTablet": "column"
}
```

**Inner blocks** : 2× `uagb/container` enfants, chacun à 50% de width. Le premier avec `backgroundType: image`, le second avec contenu texte. Mieux que `core/media-text` qui est plus rigide.

### Recette 9 — Hero avec décoration géométrique

```json
{
  "block_id": "hero-geo-decor",
  "variationSelected": true,
  "contentWidth": "alignfull",
  "minHeightDesktop": 90,
  "minHeightTypeDesktop": "vh",
  "topPaddingDesktop": 120,
  "bottomPaddingDesktop": 120,
  "backgroundType": "color",
  "backgroundColor": "var(--ast-global-color-2)",
  "topDividerStyle": "curve",
  "topDividerColor": "var(--ast-global-color-0)",
  "topDividerHeight": 100,
  "topDividerWidth": 200,
  "topDividerInvert": true,
  "bottomDividerStyle": "wave",
  "bottomDividerColor": "var(--ast-global-color-4)",
  "bottomDividerHeight": 60,
  "bottomDividerWidth": 100
}
```

**Effet** : Hero sombre avec divider curve coloré en haut et wave clair en bas. Effet visuel marqué pour un site moderne.

### Recette 10 — Container animé au scroll (fade + slide)

```json
{
  "block_id": "container-animated",
  "variationSelected": true,
  "contentWidth": "alignwide",
  "topPaddingDesktop": 80,
  "bottomPaddingDesktop": 80,
  "backgroundType": "color",
  "backgroundColor": "var(--ast-global-color-4)",
  "containerAnimation": {
    "type": "uag-fade-in-up",
    "duration": 800,
    "delay": 100,
    "scrollOffset": 80
  }
}
```

**Effet** : La section apparaît avec un fade + glissement vers le haut quand elle entre dans le viewport. Ajouté un sentiment de fluidité au scroll.

### Recette 11 — Section avec mesh gradient (Web 3 / SaaS)

```json
{
  "block_id": "section-mesh",
  "variationSelected": true,
  "contentWidth": "alignfull",
  "minHeightDesktop": 80,
  "minHeightTypeDesktop": "vh",
  "topPaddingDesktop": 100,
  "bottomPaddingDesktop": 100,
  "backgroundType": "gradient",
  "backgroundGradientType": "radial",
  "backgroundGradientColor1": "var(--ast-global-color-0)",
  "backgroundGradientLocation1": 0,
  "backgroundGradientColor2": "var(--ast-global-color-1)",
  "backgroundGradientLocation2": 50,
  "selectGradient": "complex"
}
```

**Effet** : Gradient radial complexe (mesh-like). Très utilisé sur les landing pages SaaS et Web 3 modernes.

### Recette 12 — Section avec sticky element

```json
{
  "block_id": "section-sticky",
  "variationSelected": true,
  "contentWidth": "alignwide",
  "directionDesktop": "row",
  "alignItemsDesktop": "flex-start",
  "topPaddingDesktop": 80,
  "bottomPaddingDesktop": 80
}
```

**Inner blocks** : 2× `uagb/container`. Le premier (sidebar) a `position: sticky; top: 100px;` ajouté via `customCss` pour rester collé au scroll. Idéal pour articles éditoriaux avec TOC sticky à gauche.

## Combos puissants à mémoriser

### Combo 1 — Hero immersif full-impact

```
uagb/container (hero-bg-image, recette 1)
  ├── uagb/advanced-heading (h1 + sous-titre, gradient text optionnel)
  ├── uagb/buttons
  │   ├── uagb/buttons-child (CTA primaire, couleur 0)
  │   └── uagb/buttons-child (CTA secondaire, transparent + border)
  └── uagb/separator (style: image, décoratif)
```

### Combo 2 — Section features cards hoverables

```
uagb/container (3-cols, recette 6, fond clair)
  ├── uagb/container (card-hover, recette 7)
  │   ├── uagb/icon (couleur 0, taille 48)
  │   ├── uagb/advanced-heading (h3)
  │   └── uagb/info-box (mode contenu)
  ├── uagb/container (card-hover, ...)
  └── uagb/container (card-hover, ...)
```

### Combo 3 — Section split CTA premium

```
uagb/container (alignfull, gradient mesh, recette 11)
  ├── uagb/container (50% gauche, padding 80px)
  │   ├── uagb/advanced-heading (h2 blanc)
  │   ├── core/paragraph (couleur claire)
  │   └── uagb/buttons (CTA blanc transparent + border)
  └── uagb/container (50% droite, image full bleed)
```

### Combo 4 — Article avec sidebar sticky

```
uagb/container (sticky parent, recette 12)
  ├── uagb/container (30% gauche, sticky top:100px)
  │   └── uagb/table-of-contents
  └── uagb/container (70% droite, contenu article)
      ├── core/paragraph
      ├── core/heading
      ├── uagb/info-box (call-out important)
      ├── core/embed (vidéo)
      └── uagb/faq
```

## Règles d'utilisation strictes (recap)

1. **TOUTE section** = `uagb/container`. Jamais `core/group` ou `core/cover` ou `core/columns` quand on veut un effet design.
2. **Couleurs** : `var(--ast-global-color-X)`, jamais de hex hardcoded (sauf cas overlay rgba).
3. **Padding responsive** : desktop / tablet / mobile DOIVENT être définis si la section a du padding (sinon mobile cassé).
4. **Direction tablet/mobile** : si row sur desktop avec 2+ inner containers, **toujours** mettre `directionTablet: "column"` ou `directionMobile: "column"` (sinon écrasement).
5. **block_id unique** sur chaque container, même nested.
6. **Min height** en `vh` pour les hero, `auto` pour les sections normales.
7. **Animations** au scroll : à utiliser avec parcimonie (1-2 par page max), sinon ça devient chargé.
8. **Box shadows** : préférer rgba avec opacity 0.08-0.18 pour les ombres douces, 0.20-0.40 pour les ombres marquées.

## Pour aller plus loin

- Catalogue complet des attributs `uagb/container` : `../../references/spectra-blocks-catalog.md` section Layout
- Patterns hybrides utilisant ces recettes : `../../patterns/`
- Templates de pages complètes : `../../templates/`
