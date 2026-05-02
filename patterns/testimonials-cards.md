# Pattern : Testimonials Cards (avec grands guillemets et avatars)

> **Use case** : section témoignages clients/étudiants avec 3 cards en row. 5 étoiles + grand guillemet display 120px orange + citation italique + avatar circulaire 64px + nom + sub-info. Pattern pour landings, page à propos, page formation.

> **Origine** : v0.9.0/0.9.1/0.9.2 produisaient des testimonials catastrophiques (guillemet 36px ridicule, sub-card boxée moche). Refonte v0.9.3 valide. Persistance via `_uag_custom_page_level_css` (cf piège #4 et #6).

## Structure

```
uagb/container#testimonials (root, alignfull, bg #fafafa, padding 140px)
  ├─ uagb/info-box#testi-title (eyebrow + H2 center)
  └─ uagb/container#testi-row (direction:row, wrap, equalHeight)
      ├─ uagb/container#testi-1 (white card, padding 56px, radius 24, shadow)
      │     ├─ uagb/info-box#testi-1-quote (« 120px + texte citation)
      │     └─ uagb/container#testi-1-author (row: avatar + nom/meta)
      │           ├─ uagb/image#testi-1-avatar (56×56, radius 50%)
      │           └─ uagb/info-box#testi-1-meta (nom-bold + sub-info)
      ├─ uagb/container#testi-2 (...)
      └─ uagb/container#testi-3 (...)
```

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{TESTI_EYEBROW}}` | Kicker uppercase | `Ils ont d&eacute;croch&eacute; leur BTS` |
| `{{TESTI_HEADING}}` | H2 contextuel | `3 anciens &eacute;tudiants qui ont utilis&eacute; le site...` |
| `{{TESTI_1_QUOTE}}` … `{{TESTI_3_QUOTE}}` | Citations (avec apostrophes typo) | `Les fiches synth&egrave;ses sur l&rsquo;E5A m&rsquo;ont sauv&eacute;e...` |
| `{{TESTI_1_NAME}}` … | Nom auteur | `L&eacute;a` |
| `{{TESTI_1_META}}` … | Sub-info (formation · date · lieu) | `BTS NDRC 2025 &middot; CFA Marseille` |
| `{{TESTI_1_AVATAR_URL}}` … | URL avatar | `https://site.com/wp-content/uploads/2026/05/lea.jpg` |
| `{{TESTI_1_AVATAR_ID}}` … | ID média WP | `49` |
| `{{ACCENT_COLOR}}` | Guillemet | `#FD9800` |

## Block markup (squelette card 1)

```html
<!-- Section -->
<!-- wp:uagb/container {"block_id":"{slug}-testi","backgroundColor":"#fafafa","topPaddingDesktop":140,"bottomPaddingDesktop":140,"isBlockRootParent":true,"backgroundType":"color"} -->
<div class="wp-block-uagb-container uagb-block-{slug}-testi alignfull uagb-is-root-container">
  <div class="uagb-container-inner-blocks-wrap">

    <!-- Title -->
    <!-- wp:uagb/info-box {"block_id":"{slug}-testi-title","showPrefix":true,"prefixHeadingTag":"p","headingTag":"h2","headingAlign":"center", ... } -->
    ...

    <!-- Cards row -->
    <!-- wp:uagb/container {"block_id":"{slug}-testi-row","directionDesktop":"row","wrapDesktop":"wrap","equalHeight":true,"columnGapDesktop":28,"widthSetByUser":true} -->
    <div class="wp-block-uagb-container uagb-block-{slug}-testi-row">

      <!-- Card 1 -->
      <!-- wp:uagb/container {"block_id":"{slug}-testi-1","backgroundColor":"#ffffff","topPaddingDesktop":56,"bottomPaddingDesktop":56,"leftPaddingDesktop":48,"rightPaddingDesktop":48,"variationSelected":true,"rowGapDesktop":28,"containerBorderTopLeftRadius":24,"containerBorderTopRightRadius":24,"containerBorderBottomLeftRadius":24,"containerBorderBottomRightRadius":24,"boxShadowColor":"rgba(15,23,42,0.12)","boxShadowVOffset":12,"boxShadowBlur":48,"widthDesktop":31.5,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%","widthMobile":100,"widthTypeMobile":"%"} -->
      <div class="wp-block-uagb-container uagb-block-{slug}-testi-1">

        <!-- Quote « + texte -->
        <!-- wp:uagb/info-box {"block_id":"{slug}-testi-1-quote","headingTag":"p","headingAlign":"left", ... } -->
        <div class="wp-block-uagb-info-box uagb-block-{slug}-testi-1-quote ...">
          <div class="uagb-ifb-content">
            <div class="uagb-ifb-title-wrap"><p class="uagb-ifb-title">&ldquo;</p></div>
            <p class="uagb-ifb-desc">{{TESTI_1_QUOTE}}</p>
          </div>
        </div>
        <!-- /wp:uagb/info-box -->

        <!-- Author row : avatar + nom/meta -->
        <!-- wp:uagb/container {"block_id":"{slug}-testi-1-author","directionDesktop":"row","alignItemsDesktop":"center","topPaddingDesktop":0,"bottomPaddingDesktop":0,"leftPaddingDesktop":0,"rightPaddingDesktop":0,"variationSelected":true,"columnGapDesktop":16,"widthSetByUser":true} -->
        <div class="wp-block-uagb-container uagb-block-{slug}-testi-1-author">

          <!-- Avatar circulaire 56x56 -->
          <!-- wp:uagb/image {"block_id":"{slug}-testi-1-avatar","url":"{{TESTI_1_AVATAR_URL}}","id":{{TESTI_1_AVATAR_ID}},"width":64,"height":64,"sizeSlug":"custom","objectFit":"cover","customHeightSetDesktop":true,"imageBorderTopLeftRadius":50,"imageBorderTopRightRadius":50,"imageBorderBottomLeftRadius":50,"imageBorderBottomRightRadius":50,"imageBorderRadiusUnit":"%"} -->
          <div class="wp-block-uagb-image uagb-block-{slug}-testi-1-avatar wp-block-uagb-image--layout-default wp-block-uagb-image--effect-static">
            <figure class="wp-block-uagb-image__figure">
              <img src="{{TESTI_1_AVATAR_URL}}" alt="{{TESTI_1_NAME}}, {{TESTI_1_META}}" width="64" height="64" loading="eager" role="img"/>
            </figure>
          </div>
          <!-- /wp:uagb/image -->

          <!-- Nom + meta -->
          <!-- wp:uagb/info-box {"block_id":"{slug}-testi-1-meta","headingTag":"p","headingAlign":"left", ... } -->
          <div class="wp-block-uagb-info-box uagb-block-{slug}-testi-1-meta ...">
            <div class="uagb-ifb-content">
              <div class="uagb-ifb-title-wrap"><p class="uagb-ifb-title">{{TESTI_1_NAME}}</p></div>
              <p class="uagb-ifb-desc">{{TESTI_1_META}}</p>
            </div>
          </div>
          <!-- /wp:uagb/info-box -->
        </div>
        <!-- /wp:uagb/container -->

      </div>
      <!-- /wp:uagb/container -->

      <!-- Card 2 et 3 idem -->

    </div>
    <!-- /wp:uagb/container -->
  </div>
</div>
<!-- /wp:uagb/container -->
```

## CSS overrides obligatoires (`_uag_custom_page_level_css`)

```css
/* Quote — guillemet 120px orange display */
.uagb-block-{slug}-testi-1-quote .uagb-ifb-title,
.uagb-block-{slug}-testi-2-quote .uagb-ifb-title,
.uagb-block-{slug}-testi-3-quote .uagb-ifb-title {
  font-size: 120px !important;
  color: {{ACCENT_COLOR}} !important;
  font-weight: 800 !important;
  line-height: 0.4 !important;
  letter-spacing: -4px !important;
  margin: 0 0 -20px !important;
}

/* Citation desc */
.uagb-block-{slug}-testi-1-quote .uagb-ifb-desc,
.uagb-block-{slug}-testi-2-quote .uagb-ifb-desc,
.uagb-block-{slug}-testi-3-quote .uagb-ifb-desc {
  font-size: 18px !important;
  line-height: 1.7 !important;
  color: #0F172A !important;
}

/* Nom auteur — bold */
.uagb-block-{slug}-testi-1-meta .uagb-ifb-title,
.uagb-block-{slug}-testi-2-meta .uagb-ifb-title,
.uagb-block-{slug}-testi-3-meta .uagb-ifb-title {
  font-size: 16px !important;
  font-weight: 700 !important;
  color: #0F172A !important;
}

/* Meta sub-info — gris léger */
.uagb-block-{slug}-testi-1-meta .uagb-ifb-desc,
.uagb-block-{slug}-testi-2-meta .uagb-ifb-desc,
.uagb-block-{slug}-testi-3-meta .uagb-ifb-desc {
  font-size: 13px !important;
  font-weight: 500 !important;
  color: #454F5E !important;
}

/* Avatars circulaires — border subtle */
.uagb-block-{slug}-testi-1-avatar img,
.uagb-block-{slug}-testi-2-avatar img,
.uagb-block-{slug}-testi-3-avatar img {
  border: 2px solid #fafafa !important;
  box-shadow: 0 2px 8px rgba(15,23,42,0.08) !important;
}

/* H2 section */
.uagb-block-{slug}-testi-title .uagb-ifb-title {
  font-size: 52px !important;
  font-weight: 800 !important;
  line-height: 1.1 !important;
  letter-spacing: -1.5px !important;
}
@media (max-width: 1024px) { .uagb-block-{slug}-testi-title .uagb-ifb-title { font-size: 40px !important; } }
@media (max-width: 600px) { .uagb-block-{slug}-testi-title .uagb-ifb-title { font-size: 30px !important; } }
```

## Pièges

| # | Quirk |
|---|---|
| #1 | `headingFontSize:120` sur p → CSS override `.uagb-ifb-title` avec !important |
| #6 | CSS dynamique non injecté → meta `_uag_custom_page_level_css` |
| #4 | Pas de `style="..."` inline (sera strippé par Gutenberg save) |
| Image | Avatar **doit être ratio 1:1** (cf `images-ratios.md`) |

## Variantes

### Variante 1 — Avec note 5 étoiles (RECOMMANDÉE pour testimonials)

Renforce la crédibilité visuelle. Ajouter au-dessus du quote, un `uagb/icon-list` horizontal avec 5 icônes star jaunes (FA `star` est dans la whitelist `references/spectra-icons-list.md`) :

```html
<!-- wp:uagb/icon-list {"block_id":"{slug}-testi-1-stars","icon_color":"#FBBF24","size":18,"sizeUnit":"px","gap":3,"layout":"horizontal","label-display":"none"} -->
<div class="wp-block-uagb-icon-list uagb-block-{slug}-testi-1-stars uagb-icon-list__layout-horizontal">
  <ul class="uagb-icon-list__wrap">
    <li class="uagb-icon-list-repeater"><a class="uagb-icon-list__source-wrap"><span class="uagb-icon-list__source-icon"><i class="fas fa-star"></i></span></a></li>
    <li class="uagb-icon-list-repeater"><a class="uagb-icon-list__source-wrap"><span class="uagb-icon-list__source-icon"><i class="fas fa-star"></i></span></a></li>
    <li class="uagb-icon-list-repeater"><a class="uagb-icon-list__source-wrap"><span class="uagb-icon-list__source-icon"><i class="fas fa-star"></i></span></a></li>
    <li class="uagb-icon-list-repeater"><a class="uagb-icon-list__source-wrap"><span class="uagb-icon-list__source-icon"><i class="fas fa-star"></i></span></a></li>
    <li class="uagb-icon-list-repeater"><a class="uagb-icon-list__source-wrap"><span class="uagb-icon-list__source-icon"><i class="fas fa-star"></i></span></a></li>
  </ul>
</div>
<!-- /wp:uagb/icon-list -->
```

CSS overrides associé :

```css
.uagb-block-{slug}-testi-1-stars,
.uagb-block-{slug}-testi-2-stars,
.uagb-block-{slug}-testi-3-stars {
  margin-bottom: 16px !important;
}
.uagb-block-{slug}-testi-1-stars i,
.uagb-block-{slug}-testi-2-stars i,
.uagb-block-{slug}-testi-3-stars i {
  color: #FBBF24 !important;
  font-size: 18px !important;
}
```

Pour 4.5 ⭐, remplacer la dernière icône par `fa-star-half-alt`.

### Variante 1.b — Watermark guillemet en background

Au lieu d'un guillemet display 120px en haut de card, mettre un guillemet ÉNORME (180px) en watermark transparent en arrière-plan de la card. Plus subtil, plus éditorial.

CSS overrides :

```css
.uagb-block-{slug}-testi-1,
.uagb-block-{slug}-testi-2,
.uagb-block-{slug}-testi-3 {
  position: relative;
  overflow: hidden;
}
.uagb-block-{slug}-testi-1::before,
.uagb-block-{slug}-testi-2::before,
.uagb-block-{slug}-testi-3::before {
  content: "“"; /* U+201C littéral, file CSS DOIT être UTF-8 sans BOM. NE PAS utiliser "\201C" — strippé par sanitize_inline_css() de Spectra (cf quirk #21). */
  position: absolute;
  top: -40px;
  right: -10px;
  font-size: 240px;
  font-weight: 800;
  line-height: 1;
  color: #f1f5f9; /* slate-100, très clair */
  font-family: Georgia, serif;
  pointer-events: none;
  z-index: 0;
}
.uagb-block-{slug}-testi-1 > *,
.uagb-block-{slug}-testi-2 > *,
.uagb-block-{slug}-testi-3 > * {
  position: relative;
  z-index: 1;
}
```

Permet de retirer le `uagb/info-box quote` titre `&ldquo;` et garder seulement la citation desc + auteur. Plus minimaliste.

### Variante 2 — Avec logo de l'entreprise (B2B)

Remplacer l'avatar circulaire par un logo client (ratio rectangle, e.g. 120×40). Utile pour testimonials B2B où la marque > la personne.

### Variante 3 — Slider au lieu de grid

Si > 6 témoignages, remplacer le row container par un `uagb/slider` qui paginé.

### Variante 4 — Citation longue + photo cover

Pour 1 seul témoignage hero (au lieu de 3 cards), pleine largeur :
- Photo couverture 16:9 à gauche
- Citation longue + auteur à droite
- Padding section 200px

## Ratio image
**Avatar 1:1** strict. Si l'utilisateur fournit du portrait recadré (e.g. selfie LinkedIn), upload tel quel mais s'assurer du ratio carré 400×400 minimum.

## Test post-génération

1. Screenshot 1440 → 3 cards alignées en row, **même hauteur** (`equalHeight`)
2. Vérifier guillemets `&ldquo;` visibles ÉNORMES en orange (sinon piège #1)
3. Vérifier avatars circulaires (ratio 50% radius appliqué)
4. Vérifier nom + meta clairement lisibles
5. Test responsive 768 → 1 col stack
6. Test responsive 375 → idem

## Inspiration

Pattern testimonials classique adapté avec grands guillemets éditoriaux (référence à Apple's "Privacy. That's iPhone." landing). Évolution post-feedback v0.9.0-0.9.2.
