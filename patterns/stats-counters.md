# Pattern : Stats Counters

> **Use case** : Bandeau de chiffres clés / stats (clients, années, projets, satisfaction). Compteurs animés au scroll. Crédibilise rapidement (preuve sociale chiffrée).

## Block markup (4 stats)

```html
<!-- wp:uagb/container {"block_id":"stats-section","variationSelected":true,"contentWidth":"alignfull","innerContentCustomWidthDesktop":1280,"directionDesktop":"row","alignItemsDesktop":"center","justifyContentDesktop":"space-around","columnGapDesktop":40,"rowGapDesktop":24,"directionTablet":"column","topPaddingDesktop":60,"bottomPaddingDesktop":60,"topPaddingTablet":48,"bottomPaddingTablet":48,"topPaddingMobile":40,"bottomPaddingMobile":40,"leftPaddingTablet":24,"rightPaddingTablet":24,"leftPaddingMobile":16,"rightPaddingMobile":16,"backgroundType":"color","backgroundColor":"var(--ast-global-color-2)"} -->
<div class="wp-block-uagb-container alignfull uagb-block-stats-section"><!-- wp:uagb/counter {"block_id":"stat-1","layout":"number-and-heading","startNumber":0,"endNumber":{{S1_NUMBER}},"totalNumber":{{S1_NUMBER}},"numberSuffix":"{{S1_SUFFIX}}","headingTitle":"{{S1_LABEL}}","numberColor":"var(--ast-global-color-0)","headingColor":"#ffffff","numberFontSize":56,"headingFontSize":18,"animationDuration":2000,"thousandSeparator":true} -->
<div class="wp-block-uagb-counter uagb-block-stat-1">[Counter render]</div>
<!-- /wp:uagb/counter -->

<!-- wp:uagb/counter {"block_id":"stat-2","layout":"number-and-heading","startNumber":0,"endNumber":{{S2_NUMBER}},"totalNumber":{{S2_NUMBER}},"numberSuffix":"{{S2_SUFFIX}}","headingTitle":"{{S2_LABEL}}","numberColor":"var(--ast-global-color-0)","headingColor":"#ffffff","numberFontSize":56,"headingFontSize":18,"animationDuration":2000,"thousandSeparator":true} -->
<div class="wp-block-uagb-counter uagb-block-stat-2">[Counter render]</div>
<!-- /wp:uagb/counter -->

<!-- wp:uagb/counter {"block_id":"stat-3","layout":"number-and-heading","startNumber":0,"endNumber":{{S3_NUMBER}},"totalNumber":{{S3_NUMBER}},"numberSuffix":"{{S3_SUFFIX}}","headingTitle":"{{S3_LABEL}}","numberColor":"var(--ast-global-color-0)","headingColor":"#ffffff","numberFontSize":56,"headingFontSize":18,"animationDuration":2000,"thousandSeparator":true} -->
<div class="wp-block-uagb-counter uagb-block-stat-3">[Counter render]</div>
<!-- /wp:uagb/counter -->

<!-- wp:uagb/counter {"block_id":"stat-4","layout":"number-and-heading","startNumber":0,"endNumber":{{S4_NUMBER}},"totalNumber":{{S4_NUMBER}},"numberSuffix":"{{S4_SUFFIX}}","headingTitle":"{{S4_LABEL}}","numberColor":"var(--ast-global-color-0)","headingColor":"#ffffff","numberFontSize":56,"headingFontSize":18,"animationDuration":2000,"thousandSeparator":true} -->
<div class="wp-block-uagb-counter uagb-block-stat-4">[Counter render]</div>
<!-- /wp:uagb/counter --></div>
<!-- /wp:uagb/container -->
```

## Variables

- `{{S1_NUMBER}}` : chiffre cible (entier)
- `{{S1_SUFFIX}}` : « + », « % », « K », « M », « ans »
- `{{S1_LABEL}}` : libellé (« Clients satisfaits », « Années d'expérience »)

## Exemples remplis

```yaml
S1: { number: 1500, suffix: "+", label: "Clients satisfaits" }
S2: { number: 12, suffix: " ans", label: "D'expérience" }
S3: { number: 98, suffix: "%", label: "Taux de satisfaction" }
S4: { number: 50, suffix: "+", label: "Plugins créés" }
```

## Variantes

- **Background image** avec overlay : changer `backgroundType: "image"` + `backgroundImageColor: "rgba(0,0,0,0.7)"`
- **Icônes au-dessus des chiffres** : `layout: "number-and-icon-and-heading"`, `icon: "fa-users"`
- **Mode light** : `backgroundColor: var(--ast-global-color-5)`, `numberColor: var(--ast-global-color-0)`, `headingColor: var(--ast-global-color-2)`
