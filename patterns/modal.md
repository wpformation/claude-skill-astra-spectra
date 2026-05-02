# Pattern : Modal (uagb/modal)

> **Use case** : afficher un contenu en surcouche modale au-dessus de la page (vidéo de démo, formulaire de devis rapide, conditions légales, image plein écran). Différent de `popup-builder` (qui est un popup auto-déclenché à l'arrivée / au scroll / au timer).

> **Bloc Spectra** : `uagb/modal`. Conteneur qui s'ouvre via un trigger button et se ferme via close button + ESC + click outside. Animation d'apparition fade ou slide.

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{MODAL_TRIGGER_LABEL}}` | Texte du bouton qui ouvre la modal | `Voir la démo en vidéo` |
| `{{MODAL_HEADING}}` | Titre H2 dans la modal | `Comment ça marche` |
| `{{MODAL_CONTENT}}` | Contenu HTML/blocs imbriqués | (vidéo, formulaire, image, texte) |
| `{{MODAL_WIDTH}}` | Largeur max desktop en px | `720` |
| `{{TRIGGER_VARIANT}}` | `button` / `text-link` / `image` | `button` |

## Block markup (squelette)

```html
<!-- wp:uagb/modal {"block_id":"{slug}-modal","trigger":"button","triggerText":"{{MODAL_TRIGGER_LABEL}}","modalWidth":{{MODAL_WIDTH}},"modalWidthUnit":"px","modalHeightDesktop":"auto","overlayBgColor":"rgba(15,23,42,0.85)","modalBgColor":"#ffffff","closeIconColor":"#0F172A","closeIconSize":24,"animationType":"fade","animationDuration":300,"openOnLoad":false} -->
<div class="wp-block-uagb-modal uagb-block-{slug}-modal">
  <button type="button" class="uagb-modal__trigger uagb-buttons-repeater wp-block-button__link">{{MODAL_TRIGGER_LABEL}}</button>
  <div class="uagb-modal__overlay" aria-hidden="true">
    <div class="uagb-modal__container" role="dialog" aria-modal="true" aria-labelledby="{slug}-modal-heading">
      <button type="button" class="uagb-modal__close" aria-label="Fermer">&times;</button>
      <div class="uagb-modal__inner">
        <!-- wp:uagb/advanced-heading {"block_id":"{slug}-modal-h","headingTag":"h2"} -->
        <h2 id="{slug}-modal-heading" class="uagb-heading-text">{{MODAL_HEADING}}</h2>
        <!-- /wp:uagb/advanced-heading -->

        <!-- Contenu : vidéo embed / formulaire / texte / image -->
        {{MODAL_CONTENT}}
      </div>
    </div>
  </div>
</div>
<!-- /wp:uagb/modal -->
```

## CSS overrides recommandés

```css
/* Bouton trigger — style par défaut harmonisé */
.uagb-block-{slug}-modal .uagb-modal__trigger {
  background-color: var(--ast-global-color-0) !important;
  color: var(--ast-global-color-5) !important;
  padding: 16px 32px !important;
  border-radius: 8px !important;
  font-weight: 700 !important;
  border: none !important;
  cursor: pointer !important;
}

/* Overlay sombre */
.uagb-block-{slug}-modal .uagb-modal__overlay {
  background-color: rgba(15,23,42,0.85) !important;
  backdrop-filter: blur(4px);
}

/* Container modal */
.uagb-block-{slug}-modal .uagb-modal__container {
  border-radius: 18px !important;
  padding: 56px 48px !important;
  max-width: {{MODAL_WIDTH}}px !important;
  box-shadow: 0 24px 64px rgba(0,0,0,0.30) !important;
}

/* Close button — top-right */
.uagb-block-{slug}-modal .uagb-modal__close {
  position: absolute !important;
  top: 16px !important;
  right: 16px !important;
  background: none !important;
  border: none !important;
  font-size: 32px !important;
  color: #0F172A !important;
  cursor: pointer !important;
  width: 40px !important;
  height: 40px !important;
}
.uagb-block-{slug}-modal .uagb-modal__close:hover {
  color: var(--ast-global-color-0) !important;
}

/* Mobile : modal fullscreen */
@media (max-width: 600px) {
  .uagb-block-{slug}-modal .uagb-modal__container {
    max-width: 100vw !important;
    max-height: 100vh !important;
    border-radius: 0 !important;
    padding: 56px 24px !important;
  }
}
```

## Pièges

| # | Quirk |
|---|---|
| **`openOnLoad`** | Si tu mets `true`, la modal s'ouvre au chargement de page (déclenche un popup intrusif). Pour un déclencheur user, garder `false`. Pour un popup auto, utiliser `uagb/popup-builder` à la place |
| **Scroll lock** | Spectra gère normalement le `body { overflow: hidden }` à l'ouverture. Si pas le cas, ajouter dans CSS overrides : `body.uagb-modal-open { overflow: hidden !important; }` |
| **Vidéo dans modal** | Si tu embarques un `core/embed` YouTube, ajouter `?enablejsapi=1` à l'URL pour pouvoir pause la vidéo à la fermeture (sinon elle continue de jouer en arrière-plan) |
| **a11y** | Toujours `role="dialog"`, `aria-modal="true"`, `aria-labelledby` pointant vers le h2 du titre. Le trigger button DOIT être un `<button>`, pas un `<a>` (qui suggère une navigation) |
| **Focus trap** | Spectra gère le focus trap (TAB cycle dans la modal). Vérifier en mobile/tablette que ESC ferme bien la modal |

## Variantes

### Variante 1 — Modal vidéo (démo produit)

Trigger = bouton « Voir la démo en vidéo » avec icône play. Modal contient un embed YouTube/Vimeo plein largeur.

```html
{{MODAL_CONTENT}} =
<!-- wp:core/embed {"url":"https://www.youtube.com/watch?v=...","type":"video","providerNameSlug":"youtube"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube">
  <div class="wp-block-embed__wrapper">https://www.youtube.com/watch?v=...</div>
</figure>
<!-- /wp:core/embed -->
```

### Variante 2 — Modal formulaire devis rapide

Trigger = bouton « Demander un devis ». Modal contient un `uagb/forms` avec 4-5 champs (nom, email, téléphone, projet, budget).

### Variante 3 — Modal CGV / Politique de confidentialité (legal)

Trigger = lien texte « Lire les CGV » dans un footer. Modal contient un long texte scrollable. CSS spécifique : `max-height: 80vh; overflow-y: auto;` sur `.uagb-modal__inner`.

### Variante 4 — Modal image plein écran (lightbox simple)

Trigger = clic sur image. Modal contient l'image en grand. Préférer `uagb/image-gallery` qui a sa propre lightbox pour des galleries entières.

## Test post-génération

1. Cliquer sur le trigger → modal s'ouvre avec animation fade 300ms
2. Cliquer sur close (✕) → modal se ferme
3. Cliquer sur l'overlay (zone sombre autour) → modal se ferme
4. ESC → modal se ferme
5. Mobile : scroll du contenu OK, pas de scroll de la page derrière
6. a11y : focus reste dans la modal pendant qu'elle est ouverte (pas d'échappement TAB)

## Pour aller plus loin

- Pour popup auto-déclenché (au scroll, au timer, à l'exit-intent) : voir `patterns/popup-builder.md`
- Pour formulaires dans la modal : voir `patterns/forms.md`
