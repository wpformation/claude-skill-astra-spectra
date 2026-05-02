# Pattern : Team Grid

> **Use case** : Section présentant l'équipe sur une page « À propos ». 3-4 membres en grille avec photo + nom + poste + bio courte + liens sociaux.

## Block markup (3 membres)

```html
<!-- wp:uagb/container {"block_id":"team-section","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":1280,"directionDesktop":"column","alignItemsDesktop":"center","rowGapDesktop":48,"topPaddingDesktop":80,"bottomPaddingDesktop":80,"backgroundType":"color","backgroundColor":"var(--ast-global-color-4)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-team-section"><!-- wp:uagb/advanced-heading {"block_id":"team-heading","headingTag":"h2","headingTitle":"{{SECTION_HEADLINE}}","headingDesc":"{{SECTION_SUBLINE}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":36} -->
<div class="wp-block-uagb-advanced-heading uagb-block-team-heading"><h2 class="uagb-heading-text">{{SECTION_HEADLINE}}</h2><p class="uagb-desc-text">{{SECTION_SUBLINE}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/team {"block_id":"team-grid","tcolumn":3,"imgPosition":"above","imgStyle":"circle","imgSize":160,"teamItems":[{"name":"{{M1_NAME}}","designation":"{{M1_ROLE}}","description":"{{M1_BIO}}","image":{"url":"{{M1_PHOTO}}","id":0},"socialLinks":[{"icon":"linkedin","link":"{{M1_LINKEDIN}}"},{"icon":"twitter","link":"{{M1_TWITTER}}"}]},{"name":"{{M2_NAME}}","designation":"{{M2_ROLE}}","description":"{{M2_BIO}}","image":{"url":"{{M2_PHOTO}}","id":0},"socialLinks":[{"icon":"linkedin","link":"{{M2_LINKEDIN}}"}]},{"name":"{{M3_NAME}}","designation":"{{M3_ROLE}}","description":"{{M3_BIO}}","image":{"url":"{{M3_PHOTO}}","id":0},"socialLinks":[{"icon":"linkedin","link":"{{M3_LINKEDIN}}"}]}],"nameColor":"var(--ast-global-color-2)","designationColor":"var(--ast-global-color-0)","descColor":"var(--ast-global-color-3)","socialIconColor":"var(--ast-global-color-3)","socialIconHoverColor":"var(--ast-global-color-0)","backgroundType":"color","backgroundColor":"var(--ast-global-color-5)","borderRadius":16,"containerPaddingTop":32,"containerPaddingBottom":32,"containerPaddingLeft":24,"containerPaddingRight":24} -->
<div class="wp-block-uagb-team uagb-block-team-grid">[Spectra render team cards]</div>
<!-- /wp:uagb/team --></div>
<!-- /wp:uagb/container -->
```

## Variables

- `{{M1_NAME}}` etc. : nom complet
- `{{M1_ROLE}}` etc. : « CEO », « Directeur Technique », « Formatrice WordPress »
- `{{M1_BIO}}` etc. : 60-150 chars bio courte
- `{{M1_PHOTO}}` etc. : URL photo carrée 400x400 min
- `{{M1_LINKEDIN}}` / `{{M1_TWITTER}}` etc. : URLs profils

## Variantes

- **4 colonnes** : `tcolumn: 4`
- **Layout horizontal** (image left) : `imgPosition: "left"`
- **Image carrée** au lieu de circle : `imgStyle: "square"`, `borderRadius: 8`
