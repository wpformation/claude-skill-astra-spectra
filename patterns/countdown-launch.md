# Pattern : Countdown Launch (compte à rebours événement)

> **Use case** : compte à rebours avant un événement (lancement produit, fin de promo, séminaire, début d'inscription). Pression temporelle = conversion. Spectra `uagb/countdown` natif, JS auto-update.

## Bloc Spectra utilisé : `uagb/countdown`

## Structure

```
uagb/container#countdown-section (root, alignfull, image bg + overlay, padding 160px)
  ├─ uagb/info-box#countdown-title (eyebrow + H2 + desc center, white)
  ├─ uagb/countdown#countdown (4 cards : Days / Hours / Minutes / Seconds)
  └─ uagb/buttons#countdown-buttons (CTA primary + secondary)
```

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{COUNTDOWN_EYEBROW}}` | Kicker | `Inscriptions ferm&eacute;es bient&ocirc;t` |
| `{{COUNTDOWN_HEADING}}` | H2 | `Plus que quelques jours pour rejoindre la promo 2026.` |
| `{{COUNTDOWN_DESC}}` | Desc accroche | `Aucune inscription accept&eacute;e apr&egrave;s la date limite.` |
| `{{TARGET_DATE}}` | Date target ISO | `2026-09-01T23:59:59` |
| `{{LABEL_DAYS}}` | Label jour | `Jours` |
| `{{LABEL_HOURS}}` | Label heure | `Heures` |
| `{{LABEL_MINUTES}}` | Label minutes | `Minutes` |
| `{{LABEL_SECONDS}}` | Label secondes | `Secondes` |
| `{{CTA_LABEL}}` `{{CTA_LINK}}` | Bouton inscription | ... |
| `{{ACCENT_COLOR}}` | Chiffres + CTA | `#FD9800` |
| `{{BG_IMAGE_URL}}` | Image background ambiance | ... |

## Block markup (squelette)

```html
<!-- Section countdown avec image bg + overlay -->
<!-- wp:uagb/container {"block_id":"{slug}-countdown-section","backgroundType":"image","backgroundImageDesktop":{"id":XX,"url":"{{BG_IMAGE_URL}}"},"topPaddingDesktop":160,"bottomPaddingDesktop":160,"overlayType":"gradient","backgroundImageColor":"#0F172A","overlayBackgroundType":"gradient","overlayBackgroundGradientType":"linear","overlayBackgroundGradientColor1":"rgba(15,23,42,0.85)","overlayBackgroundGradientLocation1":0,"overlayBackgroundGradientColor2":"rgba(253,152,0,0.45)","overlayBackgroundGradientLocation2":100,"overlayBackgroundGradientAngle":135,"overlayOpacity":1,"alignItemsDesktop":"center","isBlockRootParent":true} -->
<div class="wp-block-uagb-container uagb-block-{slug}-countdown-section alignfull uagb-is-root-container">
  <div class="uagb-container-inner-blocks-wrap">

    <!-- Title -->
    <!-- wp:uagb/info-box {"block_id":"{slug}-countdown-title","showPrefix":true,"prefixHeadingTag":"p","headingTag":"h2","headingAlign":"center","headingColor":"#ffffff","subHeadingColor":"#ffffff","prefixColor":"{{ACCENT_COLOR}}", ... } -->
    ...

    <!-- Countdown bloc -->
    <!-- wp:uagb/countdown {"block_id":"{slug}-countdown","endDateTime":"{{TARGET_DATE}}","timezoneSetting":"site","showSeparator":false,"separator":":","timerType":"box","layout":"day-hour-minute-second","label_color":"#ffffff","digit_color":"{{ACCENT_COLOR}}","background":"#ffffff","boxBorderRadius":12,"boxBorderTopLeftRadius":12,"boxBorderTopRightRadius":12,"boxBorderBottomLeftRadius":12,"boxBorderBottomRightRadius":12,"boxBoxShadowColor":"rgba(15,23,42,0.15)","boxBoxShadowVOffset":8,"boxBoxShadowBlur":32,"digitFontSize":80,"digitFontSizeTablet":56,"digitFontSizeMobile":36,"digitFontWeight":800,"labelFontSize":13,"labelFontWeight":700,"timerLabelStyle":{"days":"Jours","hours":"Heures","minutes":"Minutes","seconds":"Secondes"}} -->
    <div class="wp-block-uagb-countdown uagb-block-{slug}-countdown">
      <!-- Spectra auto-render 4 cards -->
    </div>
    <!-- /wp:uagb/countdown -->

    <!-- CTA -->
    <!-- wp:uagb/buttons {"block_id":"{slug}-countdown-buttons","align":"center","gap":16} -->
    ...

  </div>
</div>
<!-- /wp:uagb/container -->
```

## CSS overrides (`_uag_custom_page_level_css`)

```css
/* Countdown digits dramatic */
.uagb-block-{slug}-countdown .uagb-countdown__digit {
  font-size: 80px !important;
  font-weight: 800 !important;
  color: {{ACCENT_COLOR}} !important;
  letter-spacing: -3px !important;
}

/* Countdown labels */
.uagb-block-{slug}-countdown .uagb-countdown__label {
  text-transform: uppercase !important;
  letter-spacing: 2px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #454F5E !important;
}

/* Countdown box cards */
.uagb-block-{slug}-countdown .uagb-countdown__digit-box {
  background-color: #ffffff !important;
  padding: 36px 24px !important;
  min-width: 140px !important;
}

@media (max-width: 1024px) {
  .uagb-block-{slug}-countdown .uagb-countdown__digit { font-size: 56px !important; }
  .uagb-block-{slug}-countdown .uagb-countdown__digit-box { min-width: 110px !important; padding: 28px 16px !important; }
}
@media (max-width: 600px) {
  .uagb-block-{slug}-countdown .uagb-countdown__digit { font-size: 36px !important; }
  .uagb-block-{slug}-countdown .uagb-countdown__digit-box { min-width: 80px !important; padding: 20px 8px !important; }
}
```

## Variantes

### Variante 1 — Countdown sans secondes

`layout: "day-hour-minute"`. Plus calme, pas de second qui s'incrémente. Utile si le countdown est long (>1 mois).

### Variante 2 — Compteur inline (sans cards)

`timerType: "label"` → digits inline avec separator `:`. Plus discret, OK pour ribbon top de page.

### Variante 3 — Avec image side au lieu d'image bg

Utiliser un container row 50/50 image | countdown+CTA. Plus éditorial.

### Variante 4 — Auto-hide après expiration

Spectra a `messageOnExpiration: "Inscriptions cl&ocirc;tur&eacute;es"`. À la fin du countdown, affiche ce message au lieu des 0 0 0 0.

## Pièges

- **`endDateTime`** : doit être en format ISO (`2026-09-01T23:59:59`). Pas de format custom.
- **timezoneSetting** : `"site"` utilise le timezone WordPress (Settings > General). Sinon `"user"` utilise le timezone du navigateur visiteur. Choisir selon le contexte (`"user"` pour event international).
- **JS dépendance** : Spectra charge le JS countdown sur la page. Si JS désactivé (rare), les digits restent statiques.
- **Décalage horaire** : tester sur 2-3 navigateurs / timezones différents. Le countdown doit se calculer côté JS au load (pas au server-side).

## Test post-génération

1. Screenshot → 4 cards (Jours / Heures / Minutes / Secondes) avec digits ÉNORMES orange
2. Wait 60s → secondes s'incrémentent à la baisse
3. Vérifier que la date target est bien interprétée (pas un nombre négatif)
4. Test responsive 768 → cards plus petites mais lisibles
5. Test responsive 375 → digits 36px, OK
