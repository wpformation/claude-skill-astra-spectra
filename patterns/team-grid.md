# Pattern : Team Grid

> **Use case** : Section présentant l'équipe sur une page « À propos ». 3 membres en grille avec nom + poste + bio courte. Photos et liens sociaux à ajouter manuellement après création.

## Note d'implémentation (v0.8.2)

Comme pour `testimonials-grid.md`, le bloc Spectra `uagb/team` natif a un rendu HTML complexe (slick carousel, item arrays imbriqués, social icons SVG dynamiques) qui diverge facilement du HTML attendu et provoque des « invalid content » à l'ouverture dans Gutenberg. **Ce pattern utilise donc une composition `uagb/container` + 3× `uagb/info-box` qui produit le même résultat visuel sans le risque.**

Pour les **photos d'équipe** (qui nécessitent un upload media library) et les **liens sociaux** (qui nécessitent un bloc `uagb/social-share`), passe en édition Gutenberg manuelle après création de la page (étape 2 du workflow).

## Variables d'entrée

| Variable | Description | Exemple |
|----------|-------------|---------|
| `{{SECTION_HEADLINE}}` | Titre de section | `L'équipe derrière le projet` |
| `{{SECTION_SUBLINE}}` | Sous-titre optionnel | `Trois personnes, dix ans d'expérience cumulée` |
| `{{M1_NAME}}` etc. | Nom complet | `Marie Dupont` |
| `{{M1_ROLE}}` etc. | Poste | `CEO & co-fondatrice` |
| `{{M1_BIO}}` etc. | Bio courte (60-200 chars) | `15 ans WordPress, ex-CTO Acme...` |

## Block markup

```html
<!-- wp:uagb/container {"block_id":"team-section","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":1280,"directionDesktop":"column","alignItemsDesktop":"center","rowGapDesktop":48,"topPaddingDesktop":100,"bottomPaddingDesktop":100,"topPaddingTablet":60,"bottomPaddingTablet":60,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-team-section"><!-- wp:uagb/advanced-heading {"block_id":"team-heading","headingTag":"h2","headingTitle":"{{SECTION_HEADLINE}}","headingDesc":"{{SECTION_SUBLINE}}","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":40,"headingFontSizeTablet":32,"headingFontSizeMobile":28,"subHeadingFontSizeDesktop":18,"separatorWidth":0,"subHeadingTopMarginDesktop":16} -->
<div class="wp-block-uagb-advanced-heading uagb-block-team-heading"><h2 class="uagb-heading-text">{{SECTION_HEADLINE}}</h2><p class="uagb-desc-text">{{SECTION_SUBLINE}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/container {"block_id":"team-row","variationSelected":true,"contentWidth":"boxed","directionDesktop":"row","alignItemsDesktop":"stretch","justifyContentDesktop":"space-between","columnGapDesktop":32,"rowGapDesktop":32,"directionTablet":"column","rowGapTablet":24} -->
<div class="wp-block-uagb-container uagb-block-team-row"><!-- wp:uagb/info-box {"block_id":"team-member-1","headingTag":"h3","headingTitle":"{{M1_NAME}}","subHeadingText":"{{M1_ROLE}}","headingDesc":"{{M1_BIO}}","showIcon":false,"showSubHeading":true,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-0)","subHeadingFontWeight":600,"descColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontSizeDesktop":20,"subHeadingFontSizeDesktop":14,"subHeadingFontSizeType":"px","subHeadingLetterSpacing":0.5,"subHeadingTopSpacing":4,"subHeadingBottomSpacing":12,"descFontSizeDesktop":14,"ctaType":"none","backgroundColor":"var(--ast-global-color-7)","containerPaddingTopDesktop":32,"containerPaddingBottomDesktop":32,"containerPaddingLeftDesktop":24,"containerPaddingRightDesktop":24,"borderRadiusTopLeft":12,"borderRadiusTopRight":12,"borderRadiusBottomLeft":12,"borderRadiusBottomRight":12,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-team-member-1"><div class="uagb-ifb-content"><div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{M1_NAME}}</h3><div class="uagb-ifb-sub-heading">{{M1_ROLE}}</div></div><p class="uagb-ifb-desc">{{M1_BIO}}</p></div></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/info-box {"block_id":"team-member-2","headingTag":"h3","headingTitle":"{{M2_NAME}}","subHeadingText":"{{M2_ROLE}}","headingDesc":"{{M2_BIO}}","showIcon":false,"showSubHeading":true,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-0)","subHeadingFontWeight":600,"descColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontSizeDesktop":20,"subHeadingFontSizeDesktop":14,"subHeadingFontSizeType":"px","subHeadingLetterSpacing":0.5,"subHeadingTopSpacing":4,"subHeadingBottomSpacing":12,"descFontSizeDesktop":14,"ctaType":"none","backgroundColor":"var(--ast-global-color-7)","containerPaddingTopDesktop":32,"containerPaddingBottomDesktop":32,"containerPaddingLeftDesktop":24,"containerPaddingRightDesktop":24,"borderRadiusTopLeft":12,"borderRadiusTopRight":12,"borderRadiusBottomLeft":12,"borderRadiusBottomRight":12,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-team-member-2"><div class="uagb-ifb-content"><div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{M2_NAME}}</h3><div class="uagb-ifb-sub-heading">{{M2_ROLE}}</div></div><p class="uagb-ifb-desc">{{M2_BIO}}</p></div></div>
<!-- /wp:uagb/info-box -->

<!-- wp:uagb/info-box {"block_id":"team-member-3","headingTag":"h3","headingTitle":"{{M3_NAME}}","subHeadingText":"{{M3_ROLE}}","headingDesc":"{{M3_BIO}}","showIcon":false,"showSubHeading":true,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-0)","subHeadingFontWeight":600,"descColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontSizeDesktop":20,"subHeadingFontSizeDesktop":14,"subHeadingFontSizeType":"px","subHeadingLetterSpacing":0.5,"subHeadingTopSpacing":4,"subHeadingBottomSpacing":12,"descFontSizeDesktop":14,"ctaType":"none","backgroundColor":"var(--ast-global-color-7)","containerPaddingTopDesktop":32,"containerPaddingBottomDesktop":32,"containerPaddingLeftDesktop":24,"containerPaddingRightDesktop":24,"borderRadiusTopLeft":12,"borderRadiusTopRight":12,"borderRadiusBottomLeft":12,"borderRadiusBottomRight":12,"widthDesktop":33.33,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%"} -->
<div class="wp-block-uagb-info-box uagb-block-team-member-3"><div class="uagb-ifb-content"><div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{M3_NAME}}</h3><div class="uagb-ifb-sub-heading">{{M3_ROLE}}</div></div><p class="uagb-ifb-desc">{{M3_BIO}}</p></div></div>
<!-- /wp:uagb/info-box --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container -->
```

## Block IDs

- `team-section`, `team-heading`, `team-row`
- `team-member-1`, `team-member-2`, `team-member-3`

## Variantes

### 4 membres

Réduire `widthDesktop: 33.33` → `widthDesktop: 24` et ajouter un 4e info-box.

### Avec photos (action manuelle après création)

1. Ouvrir la page dans Gutenberg
2. Cliquer sur chaque info-box
3. Activer `Show Image`, choisir `Image Position: Above title`, uploader la photo
4. Ajuster `Image Size: 160` et `Image Border Radius: 50%` pour photos rondes

### Avec liens sociaux (action manuelle)

Ajouter un `uagb/social-share` enfant dans chaque info-box ou en-dessous. Le bloc `uagb/social-share` gère LinkedIn, Twitter, GitHub, etc.

## Test post-génération

Ouvrir la page créée dans Gutenberg authentifié. Aucun warning « invalid content » ne doit apparaître. Si warning : vérifier que `subHeadingText` est bien rempli (le sous-titre `<div class="uagb-ifb-sub-heading">` doit contenir le rôle).
