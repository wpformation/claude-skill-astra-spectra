# Pattern : Hero CTA Split

> **Use case** : Hero principal de page d'accueil ou landing page. Contenu texte à gauche (titre + sous-titre + 2 CTAs), image ou animation à droite. Layout responsive : split 50/50 desktop, stacked mobile.

## Variables d'entrée

| Variable | Description | Défaut |
|----------|-------------|--------|
| `{{HEADLINE}}` | Titre H1 principal | « Mon titre principal » |
| `{{SUBLINE}}` | Sous-titre / description | « Description courte » |
| `{{CTA_PRIMARY_LABEL}}` | Texte du CTA primaire | « Commencer maintenant » |
| `{{CTA_PRIMARY_LINK}}` | URL du CTA primaire | `#` |
| `{{CTA_SECONDARY_LABEL}}` | Texte du CTA secondaire | « En savoir plus » |
| `{{CTA_SECONDARY_LINK}}` | URL du CTA secondaire | `#about` |
| `{{HERO_IMAGE_URL}}` | URL de l'image hero | placeholder |
| `{{HERO_IMAGE_ALT}}` | Alt text de l'image | « Illustration » |

## Block markup (à coller dans post_content)

```html
<!-- wp:uagb/container {"block_id":"hero-split","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":1280,"directionDesktop":"row","alignItemsDesktop":"center","justifyContentDesktop":"space-between","columnGapDesktop":80,"rowGapTablet":40,"directionTablet":"column","topPaddingDesktop":100,"bottomPaddingDesktop":100,"topPaddingTablet":60,"bottomPaddingTablet":60,"topPaddingMobile":40,"bottomPaddingMobile":40,"backgroundType":"color","backgroundColor":"var(--ast-global-color-4)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-hero-split"><!-- wp:uagb/container {"block_id":"hero-split-text","variationSelected":true,"contentWidth":"boxed","directionDesktop":"column","alignItemsDesktop":"flex-start","rowGapDesktop":24,"widthDesktop":50,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-container uagb-block-hero-split-text"><!-- wp:uagb/advanced-heading {"block_id":"hero-split-heading","headingTag":"h1","headingTitle":"{{HEADLINE}}","headingDesc":"{{SUBLINE}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontWeight":700,"headingFontSizeDesktop":56,"headingFontSizeTablet":42,"headingFontSizeMobile":36,"subHeadingFontSizeDesktop":20,"subHeadingFontSizeTablet":18,"subHeadingFontSizeMobile":16,"headingLineHeightDesktop":1.15,"subHeadingTopMarginDesktop":16} -->
<div class="wp-block-uagb-advanced-heading uagb-block-hero-split-heading"><h1 class="uagb-heading-text">{{HEADLINE}}</h1><p class="uagb-desc-text">{{SUBLINE}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/buttons {"block_id":"hero-split-buttons","align":"left","gap":16,"stack":"mobile"} -->
<div class="wp-block-uagb-buttons uagb-block-hero-split-buttons"><!-- wp:uagb/buttons-child {"block_id":"hero-cta-primary","label":"{{CTA_PRIMARY_LABEL}}","link":"{{CTA_PRIMARY_LINK}}","backgroundColor":"var(--ast-global-color-0)","color":"var(--ast-global-color-5)","hoverBackgroundColor":"var(--ast-global-color-1)","hoverColor":"var(--ast-global-color-5)","borderRadius":8,"sizeType":"px","sizeMobile":16,"sizeTablet":17,"size":18,"paddingTop":16,"paddingBottom":16,"paddingLeft":32,"paddingRight":32,"fontWeight":600} -->
<div class="wp-block-uagb-buttons-child uagb-block-hero-cta-primary"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{CTA_PRIMARY_LINK}}"><span class="uagb-button-text">{{CTA_PRIMARY_LABEL}}</span></a></div>
<!-- /wp:uagb/buttons-child -->

<!-- wp:uagb/buttons-child {"block_id":"hero-cta-secondary","label":"{{CTA_SECONDARY_LABEL}}","link":"{{CTA_SECONDARY_LINK}}","backgroundColor":"transparent","color":"var(--ast-global-color-0)","borderColor":"var(--ast-global-color-0)","borderStyle":"solid","borderWidth":2,"borderRadius":8,"hoverBackgroundColor":"var(--ast-global-color-0)","hoverColor":"var(--ast-global-color-5)","sizeType":"px","sizeMobile":16,"sizeTablet":17,"size":18,"paddingTop":14,"paddingBottom":14,"paddingLeft":32,"paddingRight":32,"fontWeight":600} -->
<div class="wp-block-uagb-buttons-child uagb-block-hero-cta-secondary"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{CTA_SECONDARY_LINK}}"><span class="uagb-button-text">{{CTA_SECONDARY_LABEL}}</span></a></div>
<!-- /wp:uagb/buttons-child --></div>
<!-- /wp:uagb/buttons --></div>
<!-- /wp:uagb/container -->

<!-- wp:uagb/container {"block_id":"hero-split-image","variationSelected":true,"contentWidth":"boxed","widthDesktop":45,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-container uagb-block-hero-split-image"><!-- wp:uagb/image {"block_id":"hero-image","url":"{{HERO_IMAGE_URL}}","alt":"{{HERO_IMAGE_ALT}}","borderRadiusTopLeft":24,"borderRadiusTopRight":24,"borderRadiusBottomLeft":24,"borderRadiusBottomRight":24,"boxShadowColor":"rgba(0,0,0,0.12)","boxShadowVOffset":24,"boxShadowBlur":48} -->
<figure class="wp-block-uagb-image uagb-block-hero-image"><img src="{{HERO_IMAGE_URL}}" alt="{{HERO_IMAGE_ALT}}"/></figure>
<!-- /wp:uagb/image --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container -->
```

## Variantes

### Variante 1 — Sans image (centré)

Remplacer le `uagb/container` "hero-split-image" par rien et passer le container parent en `directionDesktop: "column"` + `alignItemsDesktop: "center"` + `headingAlign: "center"`.

### Variante 2 — Background image avec overlay

Remplacer le `backgroundColor` du container parent par :

```json
{
  "backgroundType": "image",
  "backgroundImageDesktop": { "url": "{{HERO_BG_URL}}" },
  "backgroundSizeDesktop": "cover",
  "backgroundAttachmentDesktop": "fixed",
  "overlayType": "color",
  "backgroundImageColor": "rgba(0,0,0,0.55)"
}
```

Et ajuster les couleurs heading/desc en `var(--ast-global-color-4)` (white).

### Variante 3 — Hero dark (mode sombre)

Container parent : `backgroundColor: var(--ast-global-color-2)`.
Heading : `headingColor: var(--ast-global-color-4)`.
SubHeading : `subHeadingColor: var(--ast-global-color-7)`.
CTA secondaire : `color: var(--ast-global-color-4)`, `borderColor: var(--ast-global-color-4)`.

## Block IDs utilisés

- `hero-split` (container parent)
- `hero-split-text` (container gauche)
- `hero-split-image` (container droite)
- `hero-split-heading`
- `hero-split-buttons`
- `hero-cta-primary`
- `hero-cta-secondary`
- `hero-image`

À adapter selon contexte (ex: `landing-formation-hero-split` pour éviter conflits si plusieurs heroes).

## Compatibilité

- **Spectra** ≥ 2.10
- **Astra** : optionnel (les variables CSS suivent la palette si Astra présent, sinon le fallback CSS du skill)
- **Responsive** : desktop (split 50/50), tablet (stacked), mobile (stacked + padding réduit)

## Test rapide

POST le markup ci-dessus sur `/wp-json/wp/v2/pages` avec `status: draft`, ouvrir Gutenberg → aucun warning. Frontend → split parfait.
