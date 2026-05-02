# Pattern : Testimonials Grid

> **Use case** : Section témoignages en grille 3 cards. Chaque témoignage : citation + nom + poste/company. Renforce la preuve sociale, idéal entre features et pricing.

## Note d'implémentation (v0.8.2)

Le bloc Spectra `uagb/testimonial` natif a un rendu HTML complexe et fragile (carousel, item arrays imbriqués, dynamic JS layout) qui diverge facilement du HTML attendu et provoque des « invalid content » à l'ouverture dans Gutenberg. **Ce pattern utilise donc une composition `uagb/container` + 3× `uagb/info-box` qui produit le même résultat visuel sans le risque.** C'est une décision pragmatique : `uagb/info-box` rend de façon prévisible et a déjà été corrigé pour matcher le rendu réel Spectra.

## Variables d'entrée

| Variable | Description | Exemple |
|----------|-------------|---------|
| `{{SECTION_HEADLINE}}` | Titre de section | `Ils nous font confiance` |
| `{{SECTION_SUBLINE}}` | Sous-titre optionnel | `Plus de 200 entreprises automatisent avec nous` |
| `{{T1_DESC}}` etc. | Texte témoignage (60-300 chars idéal) | `Page de vente livrée en 90 secondes...` |
| `{{T1_NAME}}` etc. | Nom + poste + company | `Marie Dupont, CMO chez ACME` |

Pas de variable `{{T1_IMAGE}}` dans cette version : les photos d'avatar dans `uagb/info-box` nécessitent un media library upload, ce qui ne peut pas se faire depuis le markup. Si tu veux des photos, ajoute-les manuellement après création de la page (étape 2 du workflow).

## Block markup

```html
<!-- wp:uagb/container {"block_id":"testimonials-section","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":1280,"directionDesktop":"column","alignItemsDesktop":"center","rowGapDesktop":48,"topPaddingDesktop":100,"bottomPaddingDesktop":100,"topPaddingTablet":60,"bottomPaddingTablet":60,"topPaddingMobile":48,"bottomPaddingMobile":48,"leftPaddingTablet":24,"rightPaddingTablet":24,"leftPaddingMobile":16,"rightPaddingMobile":16,"backgroundType":"color","backgroundColor":"var(--ast-global-color-7)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-testimonials-section"><!-- wp:uagb/advanced-heading {"block_id":"testimonials-heading","headingTag":"h2","headingTitle":"{{SECTION_HEADLINE}}","headingDesc":"{{SECTION_SUBLINE}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":40,"headingFontSizeTablet":32,"headingFontSizeMobile":28,"subHeadingFontSizeDesktop":18,"separatorWidth":0,"subHeadingTopMarginDesktop":16} -->
<div class="wp-block-uagb-advanced-heading uagb-block-testimonials-heading"><h2 class="uagb-heading-text">{{SECTION_HEADLINE}}</h2><p class="uagb-desc-text">{{SECTION_SUBLINE}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/container {"block_id":"testimonials-row","variationSelected":true,"contentWidth":"boxed","directionDesktop":"row","alignItemsDesktop":"stretch","justifyContentDesktop":"space-between","columnGapDesktop":32,"rowGapDesktop":32,"directionTablet":"column","rowGapTablet":24} -->
<div class="wp-block-uagb-container uagb-block-testimonials-row"><!-- wp:uagb/info-box {"block_id":"testimonial-1","headingTag":"h3","headingTitle":"{{T1_NAME}}","headingDesc":"{{T1_DESC}}","showIcon":false,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontSizeDesktop":18,"subHeadingFontSizeDesktop":15,"subHeadingLineHeightDesktop":1.6,"ctaType":"none","backgroundColor":"var(--ast-global-color-5)","containerPaddingTopDesktop":32,"containerPaddingBottomDesktop":32,"containerPaddingLeftDesktop":28,"containerPaddingRightDesktop":28,"borderRadiusTopLeft":12,"borderRadiusTopRight":12,"borderRadiusBottomLeft":12,"borderRadiusBottomRight":12,"boxShadowColor":"rgba(0,0,0,0.06)","boxShadowVOffset":4,"boxShadowBlur":16,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-testimonial-1"><div class="uagb-ifb-content"><p class="uagb-ifb-desc">{{T1_DESC}}</p><div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{T1_NAME}}</h3></div></div></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/info-box {"block_id":"testimonial-2","headingTag":"h3","headingTitle":"{{T2_NAME}}","headingDesc":"{{T2_DESC}}","showIcon":false,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontSizeDesktop":18,"subHeadingFontSizeDesktop":15,"subHeadingLineHeightDesktop":1.6,"ctaType":"none","backgroundColor":"var(--ast-global-color-5)","containerPaddingTopDesktop":32,"containerPaddingBottomDesktop":32,"containerPaddingLeftDesktop":28,"containerPaddingRightDesktop":28,"borderRadiusTopLeft":12,"borderRadiusTopRight":12,"borderRadiusBottomLeft":12,"borderRadiusBottomRight":12,"boxShadowColor":"rgba(0,0,0,0.06)","boxShadowVOffset":4,"boxShadowBlur":16,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-testimonial-2"><div class="uagb-ifb-content"><p class="uagb-ifb-desc">{{T2_DESC}}</p><div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{T2_NAME}}</h3></div></div></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/info-box {"block_id":"testimonial-3","headingTag":"h3","headingTitle":"{{T3_NAME}}","headingDesc":"{{T3_DESC}}","showIcon":false,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","headingFontSizeDesktop":18,"subHeadingFontSizeDesktop":15,"subHeadingLineHeightDesktop":1.6,"ctaType":"none","backgroundColor":"var(--ast-global-color-5)","containerPaddingTopDesktop":32,"containerPaddingBottomDesktop":32,"containerPaddingLeftDesktop":28,"containerPaddingRightDesktop":28,"borderRadiusTopLeft":12,"borderRadiusTopRight":12,"borderRadiusBottomLeft":12,"borderRadiusBottomRight":12,"boxShadowColor":"rgba(0,0,0,0.06)","boxShadowVOffset":4,"boxShadowBlur":16,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-testimonial-3"><div class="uagb-ifb-content"><p class="uagb-ifb-desc">{{T3_DESC}}</p><div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{T3_NAME}}</h3></div></div></div>
<!-- /wp:uagb/info-box --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container -->
```

Note structurelle : `<p class="uagb-ifb-desc">` est avant `<h3 class="uagb-ifb-title">` dans le HTML, parce que pour un témoignage on lit d'abord la citation puis on regarde de qui elle vient — ordre visuel naturel.

## Block IDs

- `testimonials-section`, `testimonials-heading`, `testimonials-row`
- `testimonial-1`, `testimonial-2`, `testimonial-3`

Renommer si plusieurs sections testimonials sur la même page : `testimonial-saas-1`, `testimonial-formation-1`, etc.

## Variantes

### Variante 1 — 4 témoignages

Remplacer `widthDesktop: 33.33` par `widthDesktop: 24` (4 cards en ligne avec gap 24px) ou `widthDesktop: 48.5` (2 cards par ligne sur 2 lignes).

### Variante 2 — Avec photo (action manuelle)

Après création de la page, ouvrir chaque info-box, activer `Show Image`, uploader la photo (carrée 200×200 min), positionner `Image Position: Above title`. Laisser le skill pour le markup, ajouter les photos depuis le media library Gutenberg.

### Variante 3 — Single featured (1 grand testimonial)

Garder un seul `uagb/info-box` avec `widthDesktop: 80`, `headingFontSizeDesktop: 28`, padding plus large, citation en plus gros corps.

### Variante 4 — Background dark

Section : `backgroundColor: var(--ast-global-color-2)`. Cards : `backgroundColor: var(--ast-global-color-3)`. Heading des cards : `var(--ast-global-color-7)`. Desc : `var(--ast-global-color-7)` aussi avec opacity 0.85 visuelle.

## Bonnes pratiques

- **Maximum 6 témoignages** par section (au-delà, lassitude)
- **Photos vraies** > photos stock (impact crédibilité × 2)
- **Nom + poste + company** complets > juste prénom (autorité)
- **Témoignages spécifiques** > génériques (« j'ai gagné 3h/semaine » > « super outil »)
- **Logos clients** en complément si possible (séparés en grid below)

## Test post-génération

Ouvrir la page créée dans Gutenberg authentifié. Aucun warning « invalid content » ne doit apparaître sur les info-box. Si warning : vérifier la structure `uagb-ifb-content > uagb-ifb-title-wrap > h3` + `uagb-ifb-desc`.
