# Pattern : Post Display (uagb/post)

> **Use case** : afficher dynamiquement les derniers articles / un type de post personnalisé / une catégorie filtrée. 4 layouts disponibles : `grid`, `masonry`, `carousel`, `timeline`. Idéal pour blog homepage, page agence (case studies), portfolio, témoignages dynamiques.

> **Bloc Spectra** : `uagb/post`. Génère côté serveur une query WP (`WP_Query`) à chaque render. Pas de pagination native (pour cela, utiliser `core/query-loop` + ajouts custom).

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{POST_LAYOUT}}` | `grid` / `masonry` / `carousel` / `timeline` | `grid` |
| `{{POST_TYPE}}` | Slug du post type | `post` (blog) ou `produit` (CPT) |
| `{{POST_COUNT}}` | Nombre de posts à afficher | `6` |
| `{{POST_CATEGORY}}` | Slug catégorie (filtrage) | `actualites` (vide = toutes) |
| `{{POST_COLS_DESKTOP}}` | Colonnes desktop | `3` |
| `{{POST_COLS_MOBILE}}` | Colonnes mobile | `1` |
| `{{POST_SHOW_IMAGE}}` | Afficher la featured image | `true` |
| `{{POST_SHOW_DATE}}` | Afficher la date | `true` |
| `{{POST_SHOW_AUTHOR}}` | Afficher l'auteur | `true` |
| `{{POST_SHOW_EXCERPT}}` | Afficher l'extrait | `true` |
| `{{POST_SHOW_CTA}}` | Afficher un bouton « Lire » | `true` |

## Block markup — Variante A : Grid (3 cols)

```html
<!-- wp:uagb/post {"block_id":"{slug}-posts","postType":"{{POST_TYPE}}","postsToShow":{{POST_COUNT}},"taxonomyType":"category","categories":"{{POST_CATEGORY}}","layout":"grid","columns":{{POST_COLS_DESKTOP}},"tcolumns":2,"mcolumns":{{POST_COLS_MOBILE}},"orderBy":"date","order":"desc","displayPostImage":{{POST_SHOW_IMAGE}},"imgSize":"medium","displayPostTitle":true,"displayPostExcerpt":{{POST_SHOW_EXCERPT}},"excerptLength":20,"displayPostDate":{{POST_SHOW_DATE}},"displayPostAuthor":{{POST_SHOW_AUTHOR}},"displayPostComment":false,"displayPostTaxonomy":false,"displayPostContinueReading":{{POST_SHOW_CTA}},"ctaText":"Lire l'article","columnGap":24,"rowGap":48,"contentPadding":24,"borderStyle":"solid","borderWidth":1,"borderColor":"#e5e7eb","borderRadius":12,"borderRadiusUnit":"px","bgType":"color","bgColor":"#ffffff","linkColor":"#0F172A","linkHColor":"var(--ast-global-color-0)","titleColor":"#0F172A","titleFontSizeDesktop":22,"titleFontWeight":"800","titleLineHeightDesktop":1.3,"metaColor":"#454F5E","metaFontSizeDesktop":13,"excerptColor":"#454F5E","excerptFontSizeDesktop":15,"excerptLineHeightDesktop":1.6,"ctaColor":"#FFFFFF","ctaBgColor":"var(--ast-global-color-0)","ctaHColor":"#FFFFFF","ctaBgHColor":"var(--ast-global-color-1)","ctaPaddingTopBottom":12,"ctaPaddingLeftRight":24,"ctaBorderRadius":6} -->
<div class="wp-block-uagb-post-grid uagb-block-{slug}-posts">
  <!-- Auto-généré au render via WP_Query -->
</div>
<!-- /wp:uagb/post -->
```

## Block markup — Variante B : Masonry (Pinterest-style)

```html
<!-- wp:uagb/post-masonry {"block_id":"{slug}-posts","postType":"{{POST_TYPE}}","postsToShow":9,"layout":"masonry","columns":3,"orderBy":"date","order":"desc",...} -->
<div class="wp-block-uagb-post-masonry uagb-block-{slug}-posts">
  <!-- Auto-généré -->
</div>
<!-- /wp:uagb/post-masonry -->
```

Différence vs grid : hauteurs variables selon le contenu/image, pas d'alignement equalHeight. Effet « collage Pinterest ». Utile pour portfolio créatif.

## Block markup — Variante C : Carousel (slider horizontal)

```html
<!-- wp:uagb/post-carousel {"block_id":"{slug}-posts","postType":"{{POST_TYPE}}","postsToShow":12,"layout":"carousel","columns":3,"tcolumns":2,"mcolumns":1,"autoplay":false,"infiniteLoop":true,"transitionSpeed":600,"arrowDots":"arrows_dots","arrowColor":"var(--ast-global-color-0)","arrowSize":24,"pauseOnHover":true,...} -->
<div class="wp-block-uagb-post-carousel uagb-block-{slug}-posts">
  <!-- Auto-généré -->
</div>
<!-- /wp:uagb/post-carousel -->
```

Idéal pour homepage blog : 12 derniers articles en slider avec arrows + dots.

## Block markup — Variante D : Timeline (chronologique)

```html
<!-- wp:uagb/post-timeline {"block_id":"{slug}-posts","postType":"{{POST_TYPE}}","postsToShow":8,"layout":"timeline","timelinAlignment":"left","verticalSpace":48,"horizontalSpace":24,"connectorBg":"var(--ast-global-color-0)","connectorBgFocus":"var(--ast-global-color-0)","dateFormat":"d M Y","tm_content":"date_excerpt",...} -->
<div class="wp-block-uagb-post-timeline uagb-block-{slug}-posts">
  <!-- Auto-généré -->
</div>
<!-- /wp:uagb/post-timeline -->
```

Effet « ligne du temps » : chaque post sur un nœud de timeline avec date à gauche, contenu à droite. Pour newsroom, releases produit, journal d'agence.

## CSS overrides recommandés (commun aux 4 variantes)

```css
/* Card — radius + shadow + lift hover */
.uagb-block-{slug}-posts .uagb-post__inner-wrap {
  border-radius: 12px !important;
  border: 1px solid #e5e7eb !important;
  background: #ffffff !important;
  overflow: hidden !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease !important;
}

.uagb-block-{slug}-posts .uagb-post__inner-wrap:hover {
  transform: translateY(-4px) !important;
  box-shadow: 0 12px 32px rgba(15,23,42,0.12) !important;
}

/* Image — ratio 16:9 strict (évite le wobble Pinterest sauf en masonry) */
.uagb-block-{slug}-posts:not(.uagb-post-masonry) .uagb-post__image img {
  aspect-ratio: 16/9 !important;
  object-fit: cover !important;
  width: 100% !important;
  height: auto !important;
}

/* Title — bold éditorial */
.uagb-block-{slug}-posts .uagb-post__title {
  font-size: 22px !important;
  font-weight: 800 !important;
  line-height: 1.3 !important;
  margin: 0 0 12px !important;
}

.uagb-block-{slug}-posts .uagb-post__title a {
  color: #0F172A !important;
  text-decoration: none !important;
}

.uagb-block-{slug}-posts .uagb-post__title a:hover {
  color: var(--ast-global-color-0) !important;
}

/* Meta (date + author) — uppercase tracking */
.uagb-block-{slug}-posts .uagb-post-grid-byline span,
.uagb-block-{slug}-posts .uagb-post__date {
  font-size: 12px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 1.5px !important;
  color: #454F5E !important;
}

/* Excerpt */
.uagb-block-{slug}-posts .uagb-post__excerpt {
  font-size: 15px !important;
  line-height: 1.65 !important;
  color: #454F5E !important;
  margin: 12px 0 20px !important;
}

/* CTA « Lire l'article » — bouton rond compact */
.uagb-block-{slug}-posts .uagb-post__cta {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  padding: 10px 20px !important;
  border-radius: 6px !important;
  font-weight: 700 !important;
  font-size: 14px !important;
}

/* Variante carousel : arrows + dots */
.uagb-post-carousel .slick-arrow {
  width: 48px !important;
  height: 48px !important;
  border-radius: 50% !important;
  background: var(--ast-global-color-0) !important;
  color: #FFFFFF !important;
}

/* Variante timeline : dots de la timeline */
.uagb-post-timeline .uagb-timeline__marker {
  background: var(--ast-global-color-0) !important;
  border: 4px solid #FFFFFF !important;
  width: 18px !important;
  height: 18px !important;
}
```

## Pièges

| # | Quirk |
|---|---|
| **`postType` custom** | Si CPT non standard (ex: `produit`, `temoignage`), vérifier que le slug correspond exactement à celui défini dans `register_post_type()`. Sinon → liste vide silencieuse |
| **`taxonomyType` + `categories`** | `taxonomyType` doit matcher la taxonomie associée au CPT. Pour post standard : `taxonomyType: "category"`. Pour CPT custom : `taxonomyType: "{taxo_slug}"` (ex: `secteur` pour CPT `temoignage`) |
| **Excerpt vide** | Si les posts n'ont pas d'excerpt manuel, WP génère automatiquement les premiers 55 mots. Pour plus de contrôle : configurer `excerptLength` ou ajouter un excerpt manuel sur chaque post |
| **Featured image absente** | Si certains posts n'ont pas d'image, le bloc affiche un placeholder gris. Pour fallback joli : CSS `.uagb-post__image:empty { background: linear-gradient(...); }` ou imposer une image obligatoire sur tous les posts |
| **Cache Spectra** | Les posts affichés sont cachés par Spectra (1h par défaut). Pour test, force regen via `UAGB_Helper::delete_uag_asset_dir()` puis recharge la page |
| **Pagination** | `uagb/post` ne supporte pas la pagination native. Pour archive complète paginated, préférer `core/query-loop` ou un plugin de listing |
| **Mobile carousel** | Sur mobile, le carousel passe à 1 col par défaut. Vérifier que les arrows sont assez grandes (44×44 min pour tap) |

## Variantes par cas d'usage

### Variante 1 — Blog homepage : 6 derniers articles (grid 3 cols)

Layout `grid`, count `6`, cols `3` desktop / `1` mobile, show image + date + excerpt + CTA. Le pattern par défaut.

### Variante 2 — Portfolio agence : masonry

Layout `masonry`, count `9`, cols `3` desktop. Hauteurs variables = effet créatif. Pour CPT `case-study` ou `realisation`.

### Variante 3 — Newsroom : carousel

Layout `carousel`, count `12`, cols `3` desktop, autoplay `false`, dots + arrows. Idéal pour homepage corporate.

### Variante 4 — Releases produit : timeline

Layout `timeline`, count `8`, alignement `center` (zigzag left/right). Pour journal de release ou agenda d'événements.

### Variante 5 — Témoignages dynamiques (depuis CPT)

Si tu as un CPT `temoignage` avec une taxonomie `secteur`, tu peux afficher les témoignages d'un secteur spécifique :

```json
{
  "postType": "temoignage",
  "taxonomyType": "secteur",
  "categories": "agence-creative",
  "postsToShow": 6,
  "layout": "grid",
  "columns": 3
}
```

## Test post-génération

1. Vérifier que les posts s'affichent (pas de message « no posts found »)
2. Vérifier l'ordre (par défaut : date desc → les plus récents en premier)
3. Vérifier les featured images (toutes présentes ou fallback joli)
4. Cliquer sur un titre → navigation vers le post correspondant
5. Mobile : layout 1 col, carrousel arrows utilisables
6. SEO : view-source → vérifier que les `<a href="/post-slug/">` sont rendus côté serveur (Google indexe)

## Pour aller plus loin

- Article complet : `patterns/article-content-rich.md`
- Pagination custom : voir `core/query-loop` + plugins
- Filtres dynamiques par taxonomie (Ajax) : nécessite plugin tiers (Facetwp, Searchwp)
- Template blog : `templates/blog-editorial.md`
