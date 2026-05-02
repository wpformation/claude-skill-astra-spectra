# Pattern : Slider / Carousel

> **Use case** : carrousel d'images/contenus avec navigation flèches + dots. Galerie produits, gallery photo équipe, témoignages > 6, slides marketing rotatives.

## Bloc Spectra utilisé : `uagb/slider` + `uagb/slider-child`

## Structure

```
uagb/container#slider-section (root, alignfull, padding 120px)
  ├─ uagb/info-box#slider-title (eyebrow + H2 center)
  └─ uagb/slider#slider (autoplay, 1 slide visible desktop, navigation arrows + dots)
      ├─ uagb/slider-child#slide-1 (innerBlocks : image + heading + texte)
      ├─ uagb/slider-child#slide-2 (...)
      └─ uagb/slider-child#slide-3 (...)
```

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{SLIDER_EYEBROW}}` | Kicker | `Notre &eacute;quipe` |
| `{{SLIDER_HEADING}}` | H2 | `Une &eacute;quipe au service de ta r&eacute;ussite.` |
| `{{SLIDE_N_IMAGE_URL}}` | URL image | ... |
| `{{SLIDE_N_IMAGE_ID}}` | ID média | ... |
| `{{SLIDE_N_TITLE}}` | Titre slide | ... |
| `{{SLIDE_N_DESC}}` | Desc slide | ... |
| `{{ACCENT_COLOR}}` | Dots actifs + arrows | `#FD9800` |

## Block markup (squelette)

```html
<!-- wp:uagb/slider {"block_id":"{slug}-slider","autoplay":true,"autoplaySpeed":5000,"infinite":true,"arrowDots":"arrows_dots","arrowSize":24,"arrowColor":"{{ACCENT_COLOR}}","arrowBorderColor":"{{ACCENT_COLOR}}","arrowBorderWidth":2,"arrowBorderRadius":50,"arrowPadding":12,"dotsColor":"#e5e7eb","dotsActiveColor":"{{ACCENT_COLOR}}","slidesToShow":1,"slidesToShowTablet":1,"slidesToShowMobile":1,"transitionDuration":600,"pauseOnHover":true} -->
<div class="wp-block-uagb-slider uagb-block-{slug}-slider">

  <!-- wp:uagb/slider-child {"block_id":"{slug}-slide-1"} -->
  <div class="wp-block-uagb-slider-child uagb-block-{slug}-slide-1">
    <!-- Inner blocks : image, heading, desc -->
    <!-- wp:uagb/image {...} -->
    <!-- wp:core/heading {...} -->
    <!-- wp:core/paragraph {...} -->
  </div>
  <!-- /wp:uagb/slider-child -->

  <!-- Slides 2, 3, ... -->

</div>
<!-- /wp:uagb/slider -->
```

## CSS overrides (`_uag_custom_page_level_css`)

```css
/* Slider container max-width pour readability */
.uagb-block-{slug}-slider {
  max-width: 1100px !important;
  margin-left: auto !important;
  margin-right: auto !important;
}

/* Dots position bottom centered */
.uagb-block-{slug}-slider .slick-dots {
  bottom: -40px !important;
}

/* Slide content padding */
.uagb-block-{slug}-slide-1,
.uagb-block-{slug}-slide-2,
.uagb-block-{slug}-slide-3 {
  padding: 40px !important;
}
```

## Variantes

### Variante 1 — Carousel multi-slides desktop (3 visible)

`slidesToShow: 3` desktop, `slidesToShowTablet: 2`, `slidesToShowMobile: 1`. Utile pour gallery produits.

### Variante 2 — Hero slider full-screen

Slide-content full-width + image bg + overlay. Padding 0. Slides à 100vh height. Utile pour homepage.

### Variante 3 — Logos clients en boucle infinie

Variante simplifiée : slides = juste un logo. `slidesToShow: 5`. `autoplay: true`. `arrowDots: "none"`. Effect ribbon de logos.

## Pièges

- **Lazy loading + slider** : les images des slides 2+ sont en `loading="lazy"` par défaut, peuvent ne pas charger correctement quand le slide devient actif. Forcer `loading="eager"` sur tous les slides pour éviter.
- **Slick.js conflict** : Spectra utilise slick.js. Si le thème charge une autre version, conflits possibles. Tester soigneusement.
- **Hauteur slides** : les slides peuvent avoir des hauteurs différentes selon contenu. Activer `adaptiveHeight: true` pour que le slider se redimensionne automatiquement.
- **Mobile swipe** : par défaut activé, OK. Mais désactiver si conflict avec scroll vertical (rare).

## Test post-génération

1. Screenshot slide 1 (auto-display)
2. Wait 5s → slide 2 doit apparaître automatiquement
3. Click arrow droite → slide 3
4. Click dot 1 → retour slide 1
5. Test responsive 768 → mêmes interactions
6. Vérifier que les images de tous les slides chargent (pas que slide 1)
