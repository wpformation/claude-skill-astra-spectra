# Pattern : Testimonials Grid

> **Use case** : Section témoignages client/utilisateur en grille 2-3 cards. Chaque témoignage : photo + citation + nom + poste/company. Renforce la preuve sociale, idéal entre features et pricing.

## Block markup (3 témoignages)

```html
<!-- wp:uagb/container {"block_id":"testimonials-section","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":1280,"directionDesktop":"column","alignItemsDesktop":"center","rowGapDesktop":48,"topPaddingDesktop":80,"bottomPaddingDesktop":80,"backgroundType":"color","backgroundColor":"var(--ast-global-color-4)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-testimonials-section"><!-- wp:uagb/advanced-heading {"block_id":"testimonials-heading","headingTag":"h2","headingTitle":"{{SECTION_HEADLINE}}","headingDesc":"{{SECTION_SUBLINE}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":36,"subHeadingTopMarginDesktop":12} -->
<div class="wp-block-uagb-advanced-heading uagb-block-testimonials-heading"><h2 class="uagb-heading-text">{{SECTION_HEADLINE}}</h2><p class="uagb-desc-text">{{SECTION_SUBLINE}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/testimonial {"block_id":"testimonials-grid","test_block_count":3,"test_item_count":3,"columns":3,"tcolumn":3,"test_items":[{"description":"{{T1_DESC}}","name":"{{T1_NAME}}","company":"{{T1_COMPANY}}","image":{"url":"{{T1_IMAGE}}","id":0}},{"description":"{{T2_DESC}}","name":"{{T2_NAME}}","company":"{{T2_COMPANY}}","image":{"url":"{{T2_IMAGE}}","id":0}},{"description":"{{T3_DESC}}","name":"{{T3_NAME}}","company":"{{T3_COMPANY}}","image":{"url":"{{T3_IMAGE}}","id":0}}],"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","descColor":"var(--ast-global-color-3)","iconColor":"var(--ast-global-color-0)","backgroundType":"color","backgroundColor":"var(--ast-global-color-5)","containerBorderRadius":16,"containerPaddingTop":32,"containerPaddingBottom":32,"containerPaddingLeft":32,"containerPaddingRight":32,"showImage":true,"imgPosition":"top","testItemsPerLine":3,"showRatings":true,"ratingsColor":"#FFC107"} -->
<div class="wp-block-uagb-testimonial uagb-block-testimonials-grid">[Spectra render testimonial cards]</div>
<!-- /wp:uagb/testimonial --></div>
<!-- /wp:uagb/container -->
```

## Variables

- `{{SECTION_HEADLINE}}` : « Ils nous font confiance », « Ce qu'en pensent nos clients »
- `{{T1_DESC}}` etc. : témoignage texte (60-200 chars idéal, max 300)
- `{{T1_NAME}}` etc. : nom de la personne
- `{{T1_COMPANY}}` etc. : poste + company (« CEO chez ACME ») ou seulement company
- `{{T1_IMAGE}}` etc. : URL photo (carrée, 200x200 min)

## Variantes

### Variante 1 — 6 témoignages en carousel

`columns: 3, layout: "carousel", autoplay: true, autoplaySpeed: 4000`. Plus de témoignages affichables sans scroll page.

### Variante 2 — Avec étoiles (rating)

`showRatings: true, ratings: 5` par item dans `test_items`.

### Variante 3 — Single testimonial featured (1 grand)

`columns: 1, testItemsPerLine: 1`, padding plus large, image plus grande (left positioned).

## Bonnes pratiques

- **Maximum 6 témoignages** par section (au-delà, lassitude)
- **Photos vraies** > photos stock (impact crédibilité x2)
- **Nom + poste + company** complets > juste prénom (autorité)
- **Témoignages spécifiques** > génériques (« j'ai gagné 3h/semaine » > « super outil »)
- **Logos clients** en complément si possible (séparé en grid below)
