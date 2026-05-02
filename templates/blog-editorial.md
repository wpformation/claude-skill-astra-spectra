# Template : Blog Editorial

> **Use case** : page d'archive blog éditorial avec hero + filter catégories + grid d'articles + newsletter + featured posts. Pour sites magazine, news, content marketing.

## Composition de patterns

```
1. patterns/hero-image-overlay.md           (variante hero court 120px padding, headline éditorial, sans CTAs ou avec 1 seul)
2. categories-filter (custom, voir markup ci-dessous)
3. uagb/post (grille blog 3 colonnes auto)
4. patterns/cta-banner-fullwidth.md         (variante "subscribe newsletter" avec uagb/forms inline)
5. uagb/post (variante featured posts: 1 large + 4 small)
```

## Variables d'entrée

| Variable | Description |
|---|---|
| `{{HERO_HEADLINE}}` | Titre blog (ex: « Le blog cours-ndrc.fr ») |
| `{{HERO_SUBHEADLINE}}` | Tagline éditoriale |
| `{{CATEGORIES_LIST[]}}` | Slugs catégories à filtrer |
| `{{POSTS_PER_PAGE}}` | Nombre articles affichés (12 default) |
| `{{NEWSLETTER_TITLE}}` | Titre CTA newsletter |
| `{{NEWSLETTER_FORM_ID}}` | ID Mailchimp/Brevo/etc |
| `{{FEATURED_TAG}}` | Tag pour featured posts (e.g. `populaire`) |

## Sections clés

### 1. Hero éditorial court

Pattern `hero-image-overlay.md` avec :
- `topPaddingDesktop: 120` (court, pas le drama landing)
- Image background ambiance bibliothèque/lecture
- Headline + tagline
- Pas de CTA principal (le contenu = le CTA)

### 2. Filter catégories (custom)

Boutons inline pour filtrer par catégorie. Markup :

```html
<!-- wp:uagb/container {"block_id":"{slug}-cat-filter","backgroundColor":"#ffffff","topPaddingDesktop":40,"bottomPaddingDesktop":40,"directionDesktop":"row","alignItemsDesktop":"center","justifyContentDesktop":"center","wrapDesktop":"wrap","columnGapDesktop":12,"rowGapDesktop":12,"isBlockRootParent":true} -->
<div class="wp-block-uagb-container uagb-block-{slug}-cat-filter alignfull">
  <!-- 1 button par catégorie via uagb/buttons-child -->
  <!-- ex: <a href="?cat=cours-e5a">Cours E5A</a> -->
</div>
<!-- /wp:uagb/container -->
```

### 3. Grid blog posts

Bloc `uagb/post` (post grid Spectra) avec :

```html
<!-- wp:uagb/post {"block_id":"{slug}-posts","postsToShow":12,"columns":3,"tcolumns":2,"mcolumns":1,"order":"DESC","orderBy":"date","categories":"","postType":"post","layoutConfig":["image","title","author","date","excerpt","cta"],"imageRatio":"16/9","ctaText":"Lire l'article","ctaTextColor":"#FD9800","showLoadMore":true,"loadMoreText":"Charger plus","equalHeight":true} -->
<div class="wp-block-uagb-post uagb-block-{slug}-posts">
  <!-- Spectra auto-render -->
</div>
<!-- /wp:uagb/post -->
```

### 4. CTA Newsletter (au milieu de la grid)

Pattern `cta-banner-fullwidth.md` variante "newsletter" :
- Heading « Reçois 1 article par semaine »
- Form inline (uagb/forms ou shortcode Brevo/Mailchimp)
- Bouton « Je m'inscris »

### 5. Featured posts grid (en bas)

Variante `uagb/post` avec `featured: true` :
- 1 post large (col 1, hauteur 100%)
- 4 posts small (col 2, en grille 2x2)

## CSS overrides minimum

```css
/* Posts grid avec hover effect */
.uagb-block-{slug}-posts .uagb-post__inner-wrap {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 16px;
  overflow: hidden;
}
.uagb-block-{slug}-posts .uagb-post__inner-wrap:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 40px rgba(15,23,42,0.12);
}

/* Cat filter buttons hover */
.uagb-block-{slug}-cat-filter .wp-block-button__link:hover {
  background-color: var(--ast-global-color-0) !important;
  color: #ffffff !important;
}
```

## Configuration Astra

```json
{
  "site-content-layout": "page-builder",
  "site-sidebar-layout": "no-sidebar",
  "ast-title-bar-display": "disabled",
  "ast-banner-title-visibility": "disabled"
}
```

## Workflow

1. Brief utilisateur : `« crée la page d'accueil de mon blog avec hero + filtre catégories + grid articles + newsletter »`
2. Lire patterns : `hero-image-overlay`, `cta-banner-fullwidth`
3. Lire references : `i18n-rules`, `astra-page-template-rules`, `images-ratios`
4. Composer markup en assemblant les sections
5. Générer CSS overrides ciblant les block_id du slug
6. POST + meta + regen Spectra
7. Screenshot validation

## Variantes par secteur

- **Blog formation** : catégories par épreuve (E5A, E5B, CEJM)
- **Blog tech** : catégories par stack (React, WordPress, AI)
- **Blog cuisine** : catégories par type de plat
- **Blog news** : catégories par rubrique (politique, éco, sport)
