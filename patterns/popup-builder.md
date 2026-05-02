# Pattern : Popup Builder (uagb/popup-builder)

> **Use case** : popup auto-déclenché par trigger défini (au scroll, au timer, à l'exit-intent, au load). Différent de `modal.md` qui demande un user click pour s'ouvrir. Cas typique : capture email newsletter, promo flash, message de bienvenue.

> **Bloc Spectra** : `uagb/popup-builder`. ⚠️ **Bloc disponible uniquement dans Spectra Pro** (pas la version free). Le skill propose un fallback core en variante 5 si Pro indispo.

> **⚠️ RGPD/UX warning** : les popups intrusifs ont un coût UX et SEO non négligeable. Google peut pénaliser les popups invasifs (Mobile Page Experience). Toujours offrir une fermeture claire + ne pas re-déclencher avant 7+ jours via cookie.

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{POPUP_TRIGGER}}` | `pageload` / `timer` / `scroll` / `exit-intent` / `click` | `exit-intent` |
| `{{POPUP_DELAY}}` | Si timer : délai en s | `15` |
| `{{POPUP_SCROLL_PCT}}` | Si scroll : % atteint | `50` |
| `{{POPUP_FREQUENCY}}` | `every-visit` / `once-per-day` / `once-per-7d` / `once-ever` | `once-per-7d` |
| `{{POPUP_HEADING}}` | Titre H2 | `Reste au courant` |
| `{{POPUP_BODY}}` | Contenu (formulaire, texte, image) | (newsletter form / promo) |
| `{{POPUP_WIDTH}}` | Largeur max desktop | `560` |
| `{{POPUP_POSITION}}` | `center` / `bottom-right` / `bottom-left` / `top` / `slide-in-right` | `center` |

## Block markup

```html
<!-- wp:uagb/popup-builder {"block_id":"{slug}-popup","trigger":"{{POPUP_TRIGGER}}","triggerDelay":{{POPUP_DELAY}},"triggerScrollPercent":{{POPUP_SCROLL_PCT}},"frequency":"{{POPUP_FREQUENCY}}","cookieDays":7,"position":"{{POPUP_POSITION}}","width":{{POPUP_WIDTH}},"widthUnit":"px","overlayBgColor":"rgba(15,23,42,0.85)","popupBgColor":"#ffffff","closeIcon":"times","closeIconColor":"#0F172A","closeIconSize":24,"animationType":"slide-up","animationDuration":400,"showOnceClosed":true,"escapeKeyClose":true,"clickOutsideClose":true} -->
<div class="wp-block-uagb-popup-builder uagb-block-{slug}-popup">
  <div class="uagb-popup__overlay" aria-hidden="true">
    <div class="uagb-popup__container" role="dialog" aria-modal="true" aria-labelledby="{slug}-popup-heading">
      <button type="button" class="uagb-popup__close" aria-label="Fermer">&times;</button>
      <div class="uagb-popup__inner">

        <!-- wp:uagb/advanced-heading {"block_id":"{slug}-popup-h","headingTag":"h2","headingAlign":"center"} -->
        <h2 id="{slug}-popup-heading" class="uagb-heading-text">{{POPUP_HEADING}}</h2>
        <!-- /wp:uagb/advanced-heading -->

        <!-- Contenu : formulaire newsletter, image promo, texte CTA -->
        {{POPUP_BODY}}

      </div>
    </div>
  </div>
</div>
<!-- /wp:uagb/popup-builder -->
```

## CSS overrides recommandés

```css
/* Overlay */
.uagb-block-{slug}-popup .uagb-popup__overlay {
  background: rgba(15,23,42,0.85) !important;
  backdrop-filter: blur(6px);
}

/* Container */
.uagb-block-{slug}-popup .uagb-popup__container {
  border-radius: 16px !important;
  padding: 56px 48px !important;
  max-width: {{POPUP_WIDTH}}px !important;
  box-shadow: 0 32px 80px rgba(0,0,0,0.40) !important;
  background: #ffffff !important;
}

/* Position bottom-right (slide-in-right) */
.uagb-block-{slug}-popup.uagb-popup__position-bottom-right .uagb-popup__container {
  position: fixed !important;
  bottom: 24px !important;
  right: 24px !important;
  max-width: 380px !important;
  padding: 32px 28px !important;
  border-radius: 12px !important;
  box-shadow: 0 16px 48px rgba(0,0,0,0.20) !important;
}

.uagb-block-{slug}-popup.uagb-popup__position-bottom-right .uagb-popup__overlay {
  background: transparent !important;
  pointer-events: none !important;
}

.uagb-block-{slug}-popup.uagb-popup__position-bottom-right .uagb-popup__container {
  pointer-events: all !important;
}

/* Close button */
.uagb-block-{slug}-popup .uagb-popup__close {
  position: absolute !important;
  top: 16px !important;
  right: 16px !important;
  background: none !important;
  border: none !important;
  font-size: 28px !important;
  color: #0F172A !important;
  cursor: pointer !important;
  width: 36px !important;
  height: 36px !important;
}

/* Heading */
.uagb-block-{slug}-popup h2 {
  font-size: 28px !important;
  font-weight: 800 !important;
  margin: 0 0 16px !important;
  color: #0F172A !important;
}

/* Mobile : popup fullscreen sauf bottom-right */
@media (max-width: 600px) {
  .uagb-block-{slug}-popup:not(.uagb-popup__position-bottom-right) .uagb-popup__container {
    max-width: 92vw !important;
    padding: 40px 24px !important;
  }
  .uagb-block-{slug}-popup.uagb-popup__position-bottom-right .uagb-popup__container {
    bottom: 16px !important;
    right: 16px !important;
    left: 16px !important;
    max-width: calc(100vw - 32px) !important;
  }
}
```

## Pièges

| # | Quirk |
|---|---|
| **Spectra Pro requis** | `uagb/popup-builder` est dans Spectra Pro. Si site sur Spectra free → bloc absent → fallback core (cf variante 5) |
| **Cookie freq.** | Toujours `frequency: once-per-7d` minimum (sinon popup pénible à chaque page). `cookieDays: 7` ou `30` |
| **Exit-intent desktop only** | `exit-intent` détecte le mouvement souris vers la barre d'URL. Sur mobile, pas de souris → trigger non actif. Combiner avec `scroll: 80%` pour mobile |
| **Loading impact** | Le popup ajoute du JS au load. Vérifier que ça ne casse pas le LCP. Préférer `defer` ou `async` sur le script |
| **a11y focus trap** | Spectra Pro gère normalement. Vérifier en mode keyboard-only que TAB cycle dans le popup et que ESC ferme |
| **Newsletter capture** | Si le popup contient un formulaire newsletter, le mettre minimaliste : 1 champ email + 1 bouton. Pas de nom + société + RGPD checkbox 5 lignes |
| **Google penalty** | Mobile : popup ne doit pas couvrir > 30% de l'écran sauf si déclenché par interaction user. `pageload` direct intrusif est risqué |

## Variantes

### Variante 1 — Newsletter capture (exit-intent desktop)

```json
{
  "trigger": "exit-intent",
  "frequency": "once-per-7d",
  "position": "center",
  "width": 560
}
```

Body : H2 « Reste au courant » + paragraphe « 1 article par mois, anti-spam, désinscription en 1 clic » + formulaire newsletter inline + bouton.

### Variante 2 — Promo flash bottom-right (slide-in)

```json
{
  "trigger": "scroll",
  "triggerScrollPercent": 50,
  "frequency": "once-per-7d",
  "position": "bottom-right",
  "width": 380
}
```

Body : badge « -30% », heading « Promo Black Friday », CTA « En profiter ». Slide-in discret depuis bas-droit.

### Variante 3 — Cookie consent banner (RGPD)

⚠️ Pour cookie consent, préférer un plugin dédié (Complianz, CookieYes, Real Cookie Banner) qui gère mieux les opt-in granulaires + multi-langue + intégrations GTM. Pas un cas d'usage popup-builder.

### Variante 4 — Welcome popup (page d'accueil first visit)

```json
{
  "trigger": "pageload",
  "triggerDelay": 3,
  "frequency": "once-ever",
  "position": "center"
}
```

Body : H2 « Bienvenue sur {{BRAND_NAME}} », image lifestyle, paragraphe brand story, CTA « Découvrir ».

⚠️ Risque UX : popup au load = friction immédiate. Préférer trigger `scroll: 30%` pour laisser l'utilisateur découvrir d'abord.

### Variante 5 — Fallback core (sans Spectra Pro)

Si Spectra free, le popup-builder n'existe pas. Fallback : `core/group` + JS minimal :

```html
<div id="popup-fallback" class="popup-overlay" hidden>
  <div class="popup-content">
    <button class="popup-close" aria-label="Fermer">×</button>
    <h2>{{POPUP_HEADING}}</h2>
    {{POPUP_BODY}}
  </div>
</div>
<script>
(function(){
  const popup = document.getElementById('popup-fallback');
  const closeBtn = popup.querySelector('.popup-close');
  if (localStorage.getItem('popup-closed-{{slug}}')) return;
  // Trigger : scroll 50%
  let triggered = false;
  window.addEventListener('scroll', () => {
    if (triggered) return;
    const pct = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
    if (pct > 50) {
      popup.hidden = false;
      triggered = true;
    }
  });
  closeBtn.addEventListener('click', () => {
    popup.hidden = true;
    localStorage.setItem('popup-closed-{{slug}}', '1');
  });
})();
</script>
```

CSS minimal :

```css
.popup-overlay { position: fixed; inset: 0; background: rgba(15,23,42,0.85); z-index: 9999; display: flex; align-items: center; justify-content: center; }
.popup-content { background: #fff; border-radius: 16px; padding: 48px; max-width: 520px; position: relative; }
.popup-close { position: absolute; top: 12px; right: 12px; background: none; border: none; font-size: 28px; cursor: pointer; }
```

## Test post-génération

1. Charger la page → trigger se déclenche (scroll 50% / timer 15s / exit-intent)
2. Cliquer sur close → popup disparaît
3. Recharger la page → popup ne réapparaît pas (cookie OK)
4. Vider cookie → popup réapparaît au prochain trigger
5. Mobile : popup fullscreen ou slide-in correctement positionné
6. a11y : ESC ferme, focus trap actif, screen reader annonce « Dialog »

## Pour aller plus loin

- Modal user-triggered (clic bouton) : `patterns/modal.md`
- Forms newsletter dans popup : `patterns/forms.md`
- Plugins cookie consent recommandés : Complianz, CookieYes, Real Cookie Banner
