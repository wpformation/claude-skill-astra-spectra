# Pattern : CTA Banner Full-Width

> **Use case** : Section CTA pleine largeur en fin de page ou entre sections importantes. Background gradient ou couleur primaire, headline accrocheuse, 1-2 boutons CTA. Utilisé pour conversion (newsletter, achat, RDV, téléchargement).

## Variables d'entrée

| Variable | Description |
|----------|-------------|
| `{{HEADLINE}}` | Titre accrocheur (ex: « Prêt à démarrer ? ») |
| `{{SUBLINE}}` | Sous-titre / argument complémentaire |
| `{{CTA_PRIMARY_LABEL}}` | Texte CTA primaire |
| `{{CTA_PRIMARY_LINK}}` | URL CTA primaire |
| `{{CTA_SECONDARY_LABEL}}` | (optionnel) CTA secondaire |
| `{{CTA_SECONDARY_LINK}}` | (optionnel) URL CTA secondaire |

## Block markup

```html
<!-- wp:uagb/container {"block_id":"cta-banner","variationSelected":true,"contentWidth":"alignfull","innerContentCustomWidthDesktop":900,"directionDesktop":"column","alignItemsDesktop":"center","justifyContentDesktop":"center","rowGapDesktop":24,"topPaddingDesktop":80,"bottomPaddingDesktop":80,"topPaddingTablet":60,"bottomPaddingTablet":60,"topPaddingMobile":40,"bottomPaddingMobile":40,"backgroundType":"gradient","backgroundGradientType":"linear","backgroundGradientColor1":"var(--ast-global-color-0)","backgroundGradientLocation1":0,"backgroundGradientColor2":"var(--ast-global-color-1)","backgroundGradientLocation2":100,"backgroundGradientAngle":135,"selectGradient":"basic"} -->
<div class="wp-block-uagb-container alignfull uagb-block-cta-banner"><!-- wp:uagb/advanced-heading {"block_id":"cta-headline","headingTag":"h2","headingTitle":"{{HEADLINE}}","headingDesc":"{{SUBLINE}}","headingColor":"var(--ast-global-color-4)","subHeadingColor":"var(--ast-global-color-4)","headingAlign":"center","headingFontWeight":800,"headingFontSizeDesktop":44,"headingFontSizeTablet":36,"headingFontSizeMobile":28,"subHeadingFontSizeDesktop":18,"subHeadingFontSizeTablet":16,"subHeadingFontSizeMobile":15,"headingLineHeightDesktop":1.15,"subHeadingTopMarginDesktop":12,"subHeadingTopMarginTablet":10,"subHeadingTopMarginMobile":8} -->
<div class="wp-block-uagb-advanced-heading uagb-block-cta-headline"><h2 class="uagb-heading-text">{{HEADLINE}}</h2><p class="uagb-desc-text">{{SUBLINE}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/buttons {"block_id":"cta-buttons","align":"center","gap":16,"stack":"mobile"} -->
<div class="wp-block-uagb-buttons uagb-block-cta-buttons"><!-- wp:uagb/buttons-child {"block_id":"cta-primary","label":"{{CTA_PRIMARY_LABEL}}","link":"{{CTA_PRIMARY_LINK}}","backgroundColor":"var(--ast-global-color-4)","color":"var(--ast-global-color-0)","hoverBackgroundColor":"var(--ast-global-color-2)","hoverColor":"var(--ast-global-color-4)","borderRadius":8,"sizeType":"px","size":18,"paddingTop":18,"paddingBottom":18,"paddingLeft":40,"paddingRight":40,"fontWeight":700,"boxShadowColor":"rgba(0,0,0,0.15)","boxShadowVOffset":8,"boxShadowBlur":24} -->
<div class="wp-block-uagb-buttons-child uagb-block-cta-primary"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{CTA_PRIMARY_LINK}}"><span class="uagb-button-text">{{CTA_PRIMARY_LABEL}}</span></a></div>
<!-- /wp:uagb/buttons-child -->

<!-- wp:uagb/buttons-child {"block_id":"cta-secondary","label":"{{CTA_SECONDARY_LABEL}}","link":"{{CTA_SECONDARY_LINK}}","backgroundColor":"transparent","color":"var(--ast-global-color-4)","borderColor":"var(--ast-global-color-4)","borderStyle":"solid","borderWidth":2,"borderRadius":8,"hoverBackgroundColor":"var(--ast-global-color-4)","hoverColor":"var(--ast-global-color-0)","sizeType":"px","size":18,"paddingTop":16,"paddingBottom":16,"paddingLeft":36,"paddingRight":36,"fontWeight":600} -->
<div class="wp-block-uagb-buttons-child uagb-block-cta-secondary"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{CTA_SECONDARY_LINK}}"><span class="uagb-button-text">{{CTA_SECONDARY_LABEL}}</span></a></div>
<!-- /wp:uagb/buttons-child --></div>
<!-- /wp:uagb/buttons --></div>
<!-- /wp:uagb/container -->
```

## Variantes

### Variante 1 — CTA seul (sans secondaire)

Supprimer le second `uagb/buttons-child`.

### Variante 2 — Background image avec overlay (effet wow)

Remplacer le `backgroundType: gradient` par :

```json
{
  "backgroundType": "image",
  "backgroundImageDesktop": { "url": "{{BG_IMAGE_URL}}" },
  "backgroundSizeDesktop": "cover",
  "backgroundAttachmentDesktop": "fixed",
  "overlayType": "color",
  "backgroundImageColor": "rgba(0,0,0,0.65)"
}
```

### Variante 3 — Avec dividers (transitions diagonales)

Ajouter au container parent :

```json
{
  "topDividerStyle": "tilt",
  "topDividerColor": "var(--ast-global-color-4)",
  "topDividerHeight": 60,
  "topDividerWidth": 100,
  "bottomDividerStyle": "tilt-opacity",
  "bottomDividerColor": "var(--ast-global-color-4)",
  "bottomDividerHeight": 60,
  "bottomDividerWidth": 100
}
```

Donne une section qui « flotte » entre 2 sections claires, avec transitions élégantes.

### Variante 4 — Mode minimaliste (couleur unie + headline + 1 CTA)

Remplacer le gradient par `backgroundColor: var(--ast-global-color-2)` (dark) ou `var(--ast-global-color-5)` (light). Plus sobre, idéal pour les CTAs « secondaires » entre sections.

## Block IDs

- `cta-banner` (container parent)
- `cta-headline`
- `cta-buttons`
- `cta-primary`, `cta-secondary`

## Bonnes pratiques copywriting

- **Headline** : 6-8 mots max, 1 verbe d'action (« Démarre », « Découvrez », « Réservez »), 1 promesse
- **Subline** : 12-20 mots, lever un doute ou ajouter un argument (« Sans engagement », « 30 jours satisfait ou remboursé », « Réponse en 24h »)
- **CTA primaire** : verbe d'action 1re personne (« Je m'inscris », « Je télécharge », « Je réserve ») cf rules-process WPF
- **CTA secondaire** : action « low commitment » (« En savoir plus », « Voir un exemple », « Voir la démo »)

## Compatibilité

- **Spectra** ≥ 2.10
- **Astra** : optionnel
- **Responsive** : padding réduit en tablet/mobile, boutons stack en mobile

## Quand utiliser ce pattern

- Fin de landing page (avant footer) : « Prêt à démarrer ? »
- Mid-section (entre features et témoignages) : briser le rythme, relancer l'attention
- Page « À propos » : transition vers contact ou réservation
- Page de tarif : juste avant le pricing, pour engager
