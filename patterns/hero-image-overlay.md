# Pattern : Hero Image Overlay

> **Use case** : hero pleine page avec image background + overlay couleur + heading H1 + sous-titre + 1-2 CTAs. Pattern clé du démo officiel Spectra Natures, présent sur Homepage, Services, Contact, About. Le must pour landing pages premium.

## Variables d'entrée

| Variable | Description | Exemple |
|----------|-------------|---------|
| `{{HERO_IMAGE_URL}}` | URL image background (recommandé 1920×1080+, optimisée < 300 KB) | `https://monsite.com/hero.jpg` |
| `{{HERO_IMAGE_ID}}` | ID média WordPress de l'image (uploadée préalablement) | `123` |
| `{{HEADLINE}}` | Titre principal H1 | `Transformez votre business avec WordPress` |
| `{{SUBLINE}}` | Sous-titre court (1-2 phrases) | `Une formation premium qui vous mène de zéro à expert en 8 semaines.` |
| `{{CTA_PRIMARY_LABEL}}` | Bouton principal | `Démarrer la formation` |
| `{{CTA_PRIMARY_LINK}}` | URL CTA principal | `/inscription/` |
| `{{CTA_SECONDARY_LABEL}}` | Bouton secondaire (optionnel) | `Voir le programme` |
| `{{CTA_SECONDARY_LINK}}` | URL CTA secondaire | `/programme/` |
| `{{OVERLAY_OPACITY}}` | Opacité overlay (0.5-0.8 typique) | `0.7` |

## Block markup

```html
<!-- wp:uagb/container {"block_id":"hero-overlay","alignItemsDesktop":"flex-start","backgroundType":"image","backgroundImageDesktop":{"id":{{HERO_IMAGE_ID}},"url":"{{HERO_IMAGE_URL}}"},"topPaddingDesktop":225,"bottomPaddingDesktop":225,"leftPaddingDesktop":40,"rightPaddingDesktop":40,"topPaddingTablet":100,"bottomPaddingTablet":80,"leftPaddingTablet":32,"rightPaddingTablet":32,"topPaddingMobile":80,"bottomPaddingMobile":64,"leftPaddingMobile":24,"rightPaddingMobile":24,"variationSelected":true,"rowGapDesktop":32,"rowGapTablet":32,"rowGapMobile":24,"columnGapDesktop":0,"isBlockRootParent":true,"overlayType":"color","overlayOpacity":{{OVERLAY_OPACITY}}} -->
<div class="wp-block-uagb-container uagb-block-hero-overlay alignfull uagb-is-root-container"><div class="uagb-container-inner-blocks-wrap"><!-- wp:uagb/info-box {"classMigrate":true,"headingAlign":"left","headingColor":"#ffffff","subHeadingColor":"#ffffff","headingTag":"h1","headSpace":20,"subHeadSpace":0,"block_id":"hero-overlay-text","showIcon":false,"headingFontSizeDesktop":56,"headingFontSizeTablet":42,"headingFontSizeMobile":32,"headingFontWeight":700,"headingLineHeightDesktop":1.15,"subHeadingFontSizeDesktop":18,"subHeadingFontSizeTablet":16,"subHeadingFontSizeMobile":15,"blockRightPadding":44,"blockRightPaddingTablet":45,"blockRightPaddingMobile":0,"blockPaddingUnit":"%","blockPaddingUnitTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-hero-overlay-text uagb-infobox__content-wrap  uagb-infobox-icon-above-title uagb-infobox-image-valign-top"><div class="uagb-ifb-content"><div class="uagb-ifb-title-wrap"><h1 class="uagb-ifb-title">{{HEADLINE}}</h1></div><p class="uagb-ifb-desc">{{SUBLINE}}</p></div></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/buttons {"block_id":"hero-overlay-buttons","classMigrate":true,"childMigrate":true,"align":"left","alignTablet":"left","alignMobile":"left","gap":12,"stack":"mobile"} -->
<div class="wp-block-uagb-buttons uagb-buttons__outer-wrap uagb-btn__default-btn uagb-btn-tablet__default-btn uagb-btn-mobile__default-btn uagb-block-hero-overlay-buttons"><div class="uagb-buttons__wrap uagb-buttons-layout-wrap "><!-- wp:uagb/buttons-child {"block_id":"hero-overlay-cta-1","label":"{{CTA_PRIMARY_LABEL}}","link":"{{CTA_PRIMARY_LINK}}","color":"#ffffff","background":"var(--ast-global-color-0)","hColor":"#ffffff","hBackground":"var(--ast-global-color-1)","btnBorderTopLeftRadius":4,"btnBorderTopRightRadius":4,"btnBorderBottomLeftRadius":4,"btnBorderBottomRightRadius":4,"btnBorderStyle":"none"} -->
<div class="wp-block-uagb-buttons-child uagb-buttons__outer-wrap uagb-block-hero-overlay-cta-1 wp-block-button"><div class="uagb-button__wrapper"><a class="uagb-buttons-repeater wp-block-button__link" aria-label="" href="{{CTA_PRIMARY_LINK}}" rel="follow noopener" target="_self" role="button"><div class="uagb-button__link">{{CTA_PRIMARY_LABEL}}</div></a></div></div>
<!-- /wp:uagb/buttons-child -->

<!-- wp:uagb/buttons-child {"block_id":"hero-overlay-cta-2","label":"{{CTA_SECONDARY_LABEL}}","link":"{{CTA_SECONDARY_LINK}}","color":"#ffffff","hColor":"#ffffff","hBackground":"rgba(255,255,255,0.15)","backgroundType":"transparent","btnBorderTopWidth":1,"btnBorderLeftWidth":1,"btnBorderRightWidth":1,"btnBorderBottomWidth":1,"btnBorderTopLeftRadius":4,"btnBorderTopRightRadius":4,"btnBorderBottomLeftRadius":4,"btnBorderBottomRightRadius":4,"btnBorderStyle":"solid","btnBorderColor":"#ffffff","btnBorderHColor":"#ffffff"} -->
<div class="wp-block-uagb-buttons-child uagb-buttons__outer-wrap uagb-block-hero-overlay-cta-2 wp-block-button"><div class="uagb-button__wrapper"><a class="uagb-buttons-repeater wp-block-button__link" aria-label="" href="{{CTA_SECONDARY_LINK}}" rel="follow noopener" target="_self" role="button"><div class="uagb-button__link">{{CTA_SECONDARY_LABEL}}</div></a></div></div>
<!-- /wp:uagb/buttons-child --></div></div>
<!-- /wp:uagb/buttons --></div></div>
<!-- /wp:uagb/container -->
```

## Pourquoi ce pattern marche sur toutes les palettes

- **Texte heading + subline en `#ffffff` direct** : white pur garanti lisible sur image overlay (pas dépendant de la palette)
- **CTA primary** : `var(--ast-global-color-0)` (slot garanti primary saturé) avec texte `#ffffff` (white pur)
- **CTA secondary** : transparent + border `#ffffff` + hover overlay 15 % opacity → look ghost classique
- **Pas de dépendance aux slots variables** (4, 6, 7, 8) → marche sur palette default, palette_3 Natures, palette_5 rouge, etc.

## Variantes

### Variante 1 — overlay gradient au lieu de color flat

```json
{
  "overlayType": "gradient",
  "overlayBackgroundGradientType": "linear",
  "overlayBackgroundGradientColor1": "rgba(0,0,0,0.85)",
  "overlayBackgroundGradientLocation1": 0,
  "overlayBackgroundGradientColor2": "rgba(0,0,0,0.30)",
  "overlayBackgroundGradientLocation2": 100,
  "overlayBackgroundGradientAngle": 180
}
```

Dégradé top-down sombre→clair → améliore lisibilité du texte (en haut) tout en montrant l'image (en bas).

### Variante 2 — Hero court (page interne)

Réduire `topPaddingDesktop`/`bottomPaddingDesktop` à 112 (au lieu de 225). Pour une page Services, About, Contact où le hero ne doit pas occuper toute la page.

### Variante 3 — Hero avec eyebrow kicker

Ajouter un kicker au-dessus du H1 :

```json
{
  "showPrefix": true,
  "prefixHeadingTag": "p",
  "prefixColor": "#ffffff",
  "prefixFontSize": 13,
  "prefixFontWeight": 700,
  "prefixTransform": "uppercase",
  "prefixLetterSpacing": 3
}
```

Et dans le HTML :

```html
<div class="uagb-ifb-title-wrap">
  <p class="uagb-ifb-title-prefix">PROGRAMME 2026</p>
  <h1 class="uagb-ifb-title">{{HEADLINE}}</h1>
</div>
```

### Variante 4 — Single CTA (sans secondary)

Retirer simplement le 2e `uagb/buttons-child`. Garder juste le primary.

### Variante 5 — Hero sans image (fallback minimal)

Si pas d'image disponible, remplacer `backgroundType: "image"` par `backgroundType: "gradient"` :

```json
{
  "backgroundType": "gradient",
  "backgroundGradientType": "linear",
  "backgroundGradientColor1": "var(--ast-global-color-0)",
  "backgroundGradientLocation1": 0,
  "backgroundGradientColor2": "var(--ast-global-color-1)",
  "backgroundGradientLocation2": 100,
  "backgroundGradientAngle": 135
}
```

Texte reste en `#ffffff`. Recette WOW « gradient mesh » dans `modules/spectra/container-wow-recipes.md`.

## Block IDs

- `hero-overlay`, `hero-overlay-text`, `hero-overlay-buttons`
- `hero-overlay-cta-1`, `hero-overlay-cta-2`

## Conseils

- **Image** : préférer 1920×1080+ pour ne pas pixeliser sur retina, optimisée < 300 KB (TinyPNG/Squoosh)
- **Choix d'image** : abstrait/nature/dégradé > photo de personne (gros format → souvent un visage trop net qui distrait du heading)
- **Overlay opacity 0.7** : sweet spot lisibilité ÷ visibilité de l'image. Pour images très contrastées, monter à 0.8. Pour images douces, descendre à 0.55.
- **Padding 225 desktop** : drama level pro. Si la page est secondaire (About, Services, Contact), descendre à 112-150.
- **Single H1 par page** : ce pattern fournit le H1 de la page. S'assurer qu'aucun autre pattern de la page n'en ajoute un autre.

## Test post-génération

Ouvrir la page dans Gutenberg authentifié. Vérifier :

1. Pas de warning « invalid content » sur l'info-box ni sur les buttons-child
2. Image background visible avec overlay (≈30 % opacity = 70 % visible)
3. Heading lisible (contraste WCAG AA via `visual-audit.php`)
4. Boutons primary + secondary alignés gauche, stack mobile

## Inspiration

Inspiré du démo officiel **Spectra Natures** (websitedemos.net/natures-01) — Hero des pages Homepage, Services, Contact, About. Voir [`references/spectra-demo-reference.md`](../references/spectra-demo-reference.md) pour l'analyse complète.
