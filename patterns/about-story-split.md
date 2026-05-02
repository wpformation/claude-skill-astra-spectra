# Pattern : About Story Split (image + texte 50/50)

> **Use case** : section "Notre histoire" / "À propos" sur une page About. Image éditoriale large (paysage 1200×350) à gauche en pleine largeur, suivie d'un layout 2-colonnes "Heading | Description" en dessous. Pattern central du démo Spectra Natures About.

## Variables d'entrée

| Variable | Description | Exemple |
|----------|-------------|---------|
| `{{STORY_IMAGE_URL}}` | URL image hero éditoriale (paysage 1200×350+) | `https://monsite.com/about-hero.jpg` |
| `{{STORY_IMAGE_ID}}` | ID média WordPress | `456` |
| `{{STORY_HEADING}}` | Titre H2 (court, 2-3 mots) | `Notre histoire` |
| `{{STORY_DESC}}` | Description (1-3 phrases, 100-300 chars) | `Nous avons commencé avec un rêve : redéfinir la créativité. Depuis, nous avançons avec passion et innovation.` |

## Block markup

```html
<!-- wp:uagb/container {"block_id":"about-story","alignItemsTablet":"center","alignItemsMobile":"center","backgroundColor":"#fafafa","topPaddingDesktop":112,"bottomPaddingDesktop":112,"leftPaddingDesktop":40,"rightPaddingDesktop":40,"topPaddingTablet":80,"bottomPaddingTablet":80,"leftPaddingTablet":32,"rightPaddingTablet":32,"topPaddingMobile":64,"bottomPaddingMobile":64,"leftPaddingMobile":24,"rightPaddingMobile":24,"variationSelected":true,"rowGapDesktop":40,"rowGapMobile":36,"columnGapDesktop":0,"columnGapTablet":0,"isBlockRootParent":true,"backgroundType":"color"} -->
<div class="wp-block-uagb-container uagb-block-about-story alignfull uagb-is-root-container"><div class="uagb-container-inner-blocks-wrap"><!-- wp:uagb/image {"block_id":"about-story-image","url":"{{STORY_IMAGE_URL}}","align":"left","id":{{STORY_IMAGE_ID}},"linkDestination":"none","width":1200,"widthTablet":1200,"widthMobile":1200,"height":350,"heightTablet":350,"heightMobile":300,"sizeSlug":"custom","sizeSlugTablet":"custom","sizeSlugMobile":"custom","objectFit":"cover","objectFitTablet":"cover","objectFitMobile":"cover","customHeightSetDesktop":true,"customHeightSetTablet":true,"customHeightSetMobile":true,"imageBorderTopLeftRadius":6,"imageBorderTopRightRadius":6,"imageBorderBottomLeftRadius":6,"imageBorderBottomRightRadius":6,"className":"alignleft"} -->
<div class="wp-block-uagb-image alignleft uagb-block-about-story-image wp-block-uagb-image--layout-default wp-block-uagb-image--effect-static wp-block-uagb-image--align-left"><figure class="wp-block-uagb-image__figure"><img src="{{STORY_IMAGE_URL}}" alt="" width="1200" height="350" loading="lazy" role="img"/></figure></div>
<!-- /wp:uagb/image -->

<!-- wp:uagb/container {"block_id":"about-story-row","directionDesktop":"row","alignItemsDesktop":"flex-start","alignItemsTablet":"stretch","alignItemsMobile":"stretch","topPaddingDesktop":0,"bottomPaddingDesktop":0,"leftPaddingDesktop":0,"rightPaddingDesktop":0,"variationSelected":true,"rowGapDesktop":0,"rowGapTablet":0,"rowGapMobile":20,"columnGapDesktop":72,"columnGapTablet":40,"widthSetByUser":true} -->
<div class="wp-block-uagb-container uagb-block-about-story-row"><!-- wp:uagb/info-box {"classMigrate":true,"headingAlign":"left","headingColor":"var(--ast-global-color-2)","headingTag":"h2","headSpace":0,"block_id":"about-story-heading","showIcon":false,"showDesc":false,"headingFontSizeDesktop":36,"headingFontSizeTablet":30,"headingFontSizeMobile":26,"headingFontWeight":700,"headingLineHeightDesktop":1.2,"separatorWidth":0} -->
<div class="wp-block-uagb-info-box uagb-block-about-story-heading uagb-infobox__content-wrap  uagb-infobox-icon-above-title uagb-infobox-image-valign-top"><div class="uagb-ifb-content"><div class="uagb-ifb-title-wrap"><h2 class="uagb-ifb-title">{{STORY_HEADING}}</h2></div></div></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/info-box {"classMigrate":true,"headingAlign":"left","subHeadingColor":"var(--ast-global-color-3)","headingTag":"h2","headSpace":24,"subHeadSpace":0,"block_id":"about-story-desc","showTitle":false,"showIcon":false,"descFontSizeDesktop":17,"descFontSizeTablet":16,"descFontSizeMobile":15,"descLineHeightDesktop":1.65} -->
<div class="wp-block-uagb-info-box uagb-block-about-story-desc uagb-infobox__content-wrap  uagb-infobox-icon-above-title uagb-infobox-image-valign-top"><div class="uagb-ifb-content"><div class="uagb-ifb-title-wrap"></div><p class="uagb-ifb-desc">{{STORY_DESC}}</p></div></div>
<!-- /wp:uagb/info-box --></div>
<!-- /wp:uagb/container --></div></div>
<!-- /wp:uagb/container -->
```

## Variantes

### Variante 1 — Image à droite, texte à gauche

Inverser l'ordre des blocs : `image` en deuxième, `container row` en premier.

### Variante 2 — Avec eyebrow kicker au-dessus du heading

Modifier `about-story-heading` :

```json
{
  "showPrefix": true,
  "prefixHeadingTag": "p",
  "prefixColor": "var(--ast-global-color-2)",
  "prefixFontSize": 14,
  "prefixFontWeight": 600,
  "prefixTransform": "uppercase",
  "prefixLetterSpacing": 2,
  "prefixSpace": 16
}
```

HTML :

```html
<div class="uagb-ifb-title-wrap">
  <p class="uagb-ifb-title-prefix">DEPUIS 2012</p>
  <h2 class="uagb-ifb-title">Notre histoire</h2>
</div>
```

### Variante 3 — Avec CTA bouton

Ajouter un `uagb/buttons` après l'info-box `about-story-desc`. Bouton « En savoir plus → » ghost, color-2 → hover color-0.

### Variante 4 — Background dark mode

Remplacer `backgroundColor: "#fafafa"` par `backgroundColor: "var(--ast-global-color-2)"` (slot heading dark, garanti sombre). Inverser headingColor → `#ffffff`, descColor → `rgba(255,255,255,0.85)`.

## Block IDs

- `about-story`, `about-story-image`, `about-story-row`
- `about-story-heading`, `about-story-desc`

## Conseils

- **Image** : paysage 1200×350+, optimisée < 200 KB. Photo nature, lieu, équipe au travail
- **Heading court** : 2-3 mots max (« Notre histoire », « Notre mission », « Nos valeurs »). Le punch est dans le visuel + la description
- **Description** : 100-300 chars. Plus long = perd de l'impact. Utiliser un sub-heading court + des bullet points si besoin de plus
- **Padding 112 desktop** : niveau premium, calé sur le démo Natures

## Test post-génération

Ouvrir dans Gutenberg authentifié. Vérifier :

1. Pas de warning « invalid content »
2. Image visible en pleine largeur, ratio respecté
3. Heading + desc en 2 colonnes desktop, stack mobile
4. Sur mobile : image en haut + heading + desc en stack vertical

## Inspiration

Inspiré du démo officiel **Spectra Natures** (websitedemos.net/natures-01) — section « Our Story » de la page About. Voir [`references/spectra-demo-reference.md`](../references/spectra-demo-reference.md).
