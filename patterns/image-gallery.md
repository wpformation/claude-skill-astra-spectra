# Pattern : Image Gallery (uagb/image-gallery)

> **Use case** : galerie photos avec lightbox click-to-zoom. Idéal pour portfolio, page projets, restaurant (photos plats), agence (réalisations), e-commerce (variations produit).

> **Bloc Spectra** : `uagb/image-gallery`. 4 layouts : `grid`, `masonry`, `carousel`, `tiled`. Lightbox native (zoom au clic, swipe mobile, ESC ferme).

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{GALLERY_LAYOUT}}` | `grid` / `masonry` / `carousel` / `tiled` | `masonry` |
| `{{GALLERY_COLS}}` | Colonnes desktop | `3` |
| `{{GALLERY_GAP}}` | Gap entre images en px | `16` |
| `{{GALLERY_IMAGES[]}}` | Array d'objets `{id, url, alt, caption}` | (cf ci-dessous) |
| `{{LIGHTBOX}}` | Activer lightbox au clic | `true` |
| `{{CAPTIONS}}` | Afficher captions sous chaque image | `false` |

### Structure d'une image

```json
{
  "id": 47,
  "url": "https://site.com/wp-content/uploads/2026/05/image-1.jpg",
  "alt": "Plat signature, gros plan",
  "caption": "Risotto safran, mai 2026",
  "width": 1200,
  "height": 800
}
```

## Block markup

```html
<!-- wp:uagb/image-gallery {"block_id":"{slug}-gallery","mediaGallery":[{"id":47,"url":"..."},{"id":48,"url":"..."},...],"layout":"{{GALLERY_LAYOUT}}","columnsDesktop":{{GALLERY_COLS}},"columnsTablet":2,"columnsMobile":1,"gutterDesktop":{{GALLERY_GAP}},"gutterTablet":12,"gutterMobile":8,"feed":"masonry","lightbox":{{LIGHTBOX}},"lightboxDisplayCaptions":false,"hoverEffect":"zoom","captionVisibility":"none","imageBorderRadius":12,"imageBorderRadiusUnit":"px"} -->
<div class="wp-block-uagb-image-gallery uagb-block-{slug}-gallery">
  <div class="uagb-image-gallery__wrap">
    <figure class="uagb-image-gallery__figure"><img src="..." alt="..." loading="lazy"></figure>
    <figure class="uagb-image-gallery__figure"><img src="..." alt="..." loading="lazy"></figure>
    <!-- ... -->
  </div>
</div>
<!-- /wp:uagb/image-gallery -->
```

## CSS overrides recommandés

```css
/* Image — radius + hover zoom subtil */
.uagb-block-{slug}-gallery .uagb-image-gallery__figure {
  border-radius: 12px !important;
  overflow: hidden !important;
  cursor: zoom-in !important;
}

.uagb-block-{slug}-gallery .uagb-image-gallery__figure img {
  transition: transform 0.3s ease, filter 0.3s ease !important;
}

.uagb-block-{slug}-gallery .uagb-image-gallery__figure:hover img {
  transform: scale(1.05) !important;
}

/* Lightbox overlay */
.uagb-image-gallery__lightbox-overlay {
  background: rgba(0,0,0,0.92) !important;
  backdrop-filter: blur(8px) !important;
}

/* Mobile : 1 col, gutter réduit */
@media (max-width: 600px) {
  .uagb-block-{slug}-gallery .uagb-image-gallery__wrap {
    grid-template-columns: 1fr !important;
    gap: 8px !important;
  }
}
```

## Pièges

| # | Quirk |
|---|---|
| **Format images** | WebP préféré (poids -30% vs JPEG, supporté partout depuis 2021). Fallback JPEG/PNG via plugin de conversion automatique (Imagify, ShortPixel) |
| **Dimensions** | Toutes les images doivent avoir des `width` et `height` HTML pour éviter le CLS (Cumulative Layout Shift). Spectra le fait par défaut si l'image est uploadée via media library |
| **Alt text** | OBLIGATOIRE pour SEO + a11y. Si l'utilisateur n'en fournit pas, le skill DOIT en générer un descriptif (ex : « Photo du restaurant intérieur ») et avertir de le préciser |
| **Lightbox lourd** | Le JS lightbox de Spectra ajoute ~30 KB. Pour des galeries < 6 images, désactiver lightbox et lier chaque image vers sa version full-size via `<a href>` natif |
| **Masonry CLS** | Layout masonry peut causer du reflow au chargement si les images ont des hauteurs très différentes. Mitiger avec `aspect-ratio` CSS ou imposer un ratio uniforme |
| **Lazy loading** | `loading="lazy"` natif sur toutes les images sauf la 1re visible (LCP). Spectra gère bien |

## Variantes

### Variante 1 — Grid uniform (3 cols, ratio 1:1)

Toutes les images en carré 600×600, parfait alignement. Pour Instagram-style ou témoignages photo + nom.

CSS spécifique :
```css
.uagb-block-{slug}-gallery .uagb-image-gallery__figure img {
  aspect-ratio: 1/1 !important;
  object-fit: cover !important;
}
```

### Variante 2 — Masonry varied (3 cols, hauteurs libres)

Pour portfolio créatif ou photos lifestyle. Hauteurs variables = effet Pinterest.

### Variante 3 — Carousel horizontal (slider 1-2 visible)

Pour landing avec 8-12 photos sans gâcher de hauteur de scroll. Arrows + dots.

### Variante 4 — Tiled (pattern Bento Box)

Layout asymétrique : 1 grosse image à gauche, 4 petites à droite en grille 2×2. Pour homepage hero photo + détails.

```css
/* Approximation tiled via CSS Grid manual */
.uagb-block-{slug}-gallery .uagb-image-gallery__wrap {
  display: grid !important;
  grid-template-columns: 2fr 1fr 1fr !important;
  grid-template-rows: 1fr 1fr !important;
  gap: 16px !important;
}
.uagb-block-{slug}-gallery .uagb-image-gallery__figure:first-child {
  grid-row: span 2 !important;
}
```

### Variante 5 — Galerie avec captions visibles (style musée)

Captions sous chaque image, italique. `captionVisibility: "always"` dans les attrs.

## Test post-génération

1. Vérifier que toutes les images chargent (pas de 404)
2. Cliquer sur une image → lightbox s'ouvre, ESC ferme
3. Mobile : swipe gauche/droite dans la lightbox pour naviguer
4. Vérifier les `loading="lazy"` sur toutes les images sauf la première (LCP)
5. a11y : alt texts présents et descriptifs (pas `alt="image-1"`)
6. Lighthouse : score images > 90, pas de CLS

## Pour aller plus loin

- Hero image overlay (1 seule grosse image) : `patterns/hero-image-overlay.md`
- Article éditorial avec images intercalées : `patterns/article-content-rich.md`
- Ratios attendus par pattern : `references/images-ratios.md`
