# Spectra Blocks Catalog (48 blocs Gutenberg)

> **Source** : repo officiel `brainstormforce/wp-spectra` (`/src/blocks/`), inspecté le 02/05/2026.
> **Namespace** : `uagb/*` (legacy Ultimate Addons for Gutenberg, stable depuis 2019).
> **Règle commune** : tous les blocs nécessitent un attr `block_id` unique (alphanumérique + tirets).

## 48 blocs Gutenberg disponibles

```
advanced-heading, blockquote, buttons, buttons-child, call-to-action,
cf7-designer, column, columns, container, countdown, counter,
faq, faq-child, forms, gf-designer, google-map, how-to,
icon, icon-list, icon-list-child, image, image-gallery, info-box,
inline-notice, lottie, marketing-button, modal, popup-builder, post,
price-list, price-list-child, promote-cross-products, review, section,
separator, slider, slider-child, social-share, social-share-child,
star-rating, table-of-contents, tabs, tabs-child, taxonomy-list,
team, testimonial, timeline, wp-search
```

> **Note sur `extensions`** : présent dans `/src/blocks/` du repo brainstormforce/wp-spectra, mais ce n'est PAS un bloc Gutenberg utilisable (pas d'item dans le block inserter). C'est un meta-bloc qui héberge les extensions (animations, motion effects, scroll reveal, etc.) appliquées aux autres blocs uagb. Il n'apparaît donc pas dans cette liste utilisable. Pour cette raison la documentation parle de **48 blocs**, pas 49.

## Catégories

### Layout / Structure (5 blocs)

| Bloc | Rôle | Block markup type |
|------|------|-------------------|
| `uagb/container` | Container principal flexible (1-6 cols, bg, padding, responsive) | Parent |
| `uagb/section` | Section legacy (préférer `container`) | Parent |
| `uagb/columns` | Layout colonnes legacy (préférer `container`) | Parent |
| `uagb/column` | Colonne enfant pour `uagb/columns` | Child of `columns` |
| `uagb/separator` | Séparateur visuel (line, dashed, dotted, image) | Standalone |

### Texte et titres (3 blocs)

| Bloc | Rôle | Notes |
|------|------|-------|
| `uagb/advanced-heading` | Titre + sous-titre stylisé (animations, gradient text, soulignement) | Préférer à `core/heading` pour H1/H2 design |
| `uagb/blockquote` | Citation visuelle avec design avancé | Préférer à `core/quote` quand design |
| `uagb/inline-notice` | Notice/alerte/callout avec icône (info/warning/success/error) | Pas d'équivalent core |

### Boutons (3 blocs)

| Bloc | Rôle | Hierarchy |
|------|------|-----------|
| `uagb/buttons` | Container parent pour groupe de boutons | Parent |
| `uagb/buttons-child` | Bouton individuel (CTA primaire/secondaire/etc.) | Child of `buttons` (OBLIGATOIRE) |
| `uagb/marketing-button` | Bouton CTA standalone avec design avancé (gradient, hover) | Standalone |

### Hero et CTA (2 blocs)

| Bloc | Rôle | Notes |
|------|------|-------|
| `uagb/call-to-action` | Section CTA pleine largeur avec headline + buttons | Composition pré-faite |
| `uagb/info-box` | Card avec icon + heading + desc + optional button | Très utilisé pour features 3 cols |

### Médias (5 blocs)

| Bloc | Rôle | Notes |
|------|------|-------|
| `uagb/image` | Image avec design avancé (overlay, hover, animations) | Préférer `core/image` pour image simple |
| `uagb/image-gallery` | Galerie avec grid/masonry/carousel | — |
| `uagb/icon` | Icon standalone avec lien optionnel | — |
| `uagb/icon-list` | Liste avec icônes personnalisées par item | Parent |
| `uagb/icon-list-child` | Item de liste avec icône | Child of `icon-list` |
| `uagb/lottie` | Animation Lottie JSON | Nécessite l'URL du fichier .json |

### Blocs SEO + schema (4 blocs)

| Bloc | Schema généré | Notes |
|------|---------------|-------|
| `uagb/faq` | FAQPage JSON-LD | Parent — contient `faq-child` |
| `uagb/faq-child` | item FAQPage | Child of `faq` (attrs : `question`, `answer`) |
| `uagb/how-to` | HowTo JSON-LD | Étapes avec image/desc/duration |
| `uagb/review` | Review + AggregateRating JSON-LD | Item avec rating, pros/cons |
| `uagb/star-rating` | Rating | Standalone, pas de schema complet |
| `uagb/table-of-contents` | — | TOC auto depuis les H2/H3/H4 de la page |

### Interactif (8 blocs)

| Bloc | Rôle | Hierarchy |
|------|------|-----------|
| `uagb/tabs` | Onglets horizontaux/verticaux | Parent |
| `uagb/tabs-child` | Contenu d'un onglet | Child of `tabs` |
| `uagb/slider` | Carousel avec auto-play, navigation | Parent |
| `uagb/slider-child` | Slide individuel | Child of `slider` |
| `uagb/modal` | Modal popup (trigger click ou load) | — |
| `uagb/popup-builder` | Popup full-screen avec contenu custom | — |
| `uagb/countdown` | Compte à rebours (offre limitée, lancement) | — |
| `uagb/counter` | Compteur animé (stats, chiffres clés) | Anime au scroll |

### Posts dynamiques (1 bloc)

| Bloc | Rôle | Variations |
|------|------|------------|
| `uagb/post` | Affichage dynamique posts WP | grid, masonry, carousel, timeline, list |

### People (3 blocs)

| Bloc | Rôle | Notes |
|------|------|-------|
| `uagb/team` | Présentation équipe (grid de membres) | — |
| `uagb/testimonial` | Témoignages (grid ou carousel) | — |
| `uagb/timeline` | Timeline verticale (chronologie) | — |

### Pricing (3 blocs)

| Bloc | Rôle | Hierarchy |
|------|------|-----------|
| `uagb/price-list` | Liste de prix (menu restaurant, services) | Parent |
| `uagb/price-list-child` | Item prix individuel | Child of `price-list` |
| Pricing 3 tiers complet | (composition) | `uagb/container` (3 cols) + 3× `uagb/info-box` (mode pricing) |

### Forms (3 blocs)

| Bloc | Rôle | Notes |
|------|------|-------|
| `uagb/forms` | Formulaire natif Spectra (contact, subscribe, etc.) | Pas de plugin externe |
| `uagb/cf7-designer` | Stylise un formulaire Contact Form 7 existant | Nécessite CF7 plugin |
| `uagb/gf-designer` | Stylise un formulaire Gravity Forms existant | Nécessite Gravity Forms plugin |

### Maps (1 bloc)

| Bloc | Rôle | Notes |
|------|------|-------|
| `uagb/google-map` | Carte Google Maps intégrée | Address ou lat/lng |

### Recherche / navigation (2 blocs)

| Bloc | Rôle | Notes |
|------|------|-------|
| `uagb/wp-search` | Search bar stylisée | Préférer à `core/search` |
| `uagb/taxonomy-list` | Liste des catégories/tags | — |

### Réseaux sociaux (2 blocs)

| Bloc | Rôle | Hierarchy |
|------|------|-----------|
| `uagb/social-share` | Boutons partage social | Parent |
| `uagb/social-share-child` | Bouton réseau individuel | Child of `social-share` |

### Cas avancés (3 blocs)

| Bloc | Rôle | Use case |
|------|------|----------|
| `uagb/extensions` | Extensions custom (utility) | Avancé |
| `uagb/promote-cross-products` | Affichage produits liés (e-commerce, agence) | Marketplace |
| `uagb/section` | Section legacy (préférer `container`) | Compat ancien |

## Attributs critiques par bloc (raccourci)

> **Note** : chaque bloc a 30-100 attributs possibles. Ce tableau liste UNIQUEMENT les attrs critiques utilisés dans 80% des cas. Pour la liste complète, voir le code source `brainstormforce/wp-spectra/src/blocks/<block-name>/attributes.js`.

### `uagb/container`

```json
{
  "block_id": "REQUIRED-unique-id",
  "variationSelected": true,
  "contentWidth": "alignwide" | "alignfull" | "boxed",
  "innerContentCustomWidthDesktop": 1140,
  "directionDesktop": "row" | "column",
  "alignItemsDesktop": "flex-start" | "center" | "flex-end" | "stretch",
  "justifyContentDesktop": "flex-start" | "center" | "flex-end" | "space-between" | "space-around",
  "rowGapDesktop": 20,
  "columnGapDesktop": 20,
  "topPaddingDesktop": 80,
  "bottomPaddingDesktop": 80,
  "backgroundType": "color" | "image" | "gradient" | "none",
  "backgroundColor": "var(--ast-global-color-X)",
  "backgroundImageDesktop": { "url": "...", "id": 123 },
  "boxShadowColor": "rgba(0,0,0,0.1)",
  "borderRadiusDesktop": 0
}
```

### `uagb/advanced-heading`

```json
{
  "block_id": "REQUIRED",
  "headingTag": "h1" | "h2" | "h3" | "h4" | "h5" | "h6",
  "headingTitle": "Mon titre",
  "headingDesc": "Sous-titre optionnel",
  "headingColor": "var(--ast-global-color-2)",
  "subHeadingColor": "var(--ast-global-color-3)",
  "headingAlign": "left" | "center" | "right",
  "headingFontFamily": "inherit",
  "headingFontWeight": 700,
  "headingFontSizeDesktop": 48,
  "subHeadingFontSizeDesktop": 18,
  "showDecoration": true | false,
  "decorationStyle": "underline" | "highlight" | "image"
}
```

### `uagb/buttons` + `uagb/buttons-child`

```json
// uagb/buttons (parent)
{
  "block_id": "REQUIRED",
  "align": "left" | "center" | "right",
  "gap": 16,
  "stack": "none" | "tablet" | "mobile"
}

// uagb/buttons-child
{
  "block_id": "REQUIRED",
  "label": "Cliquez ici",
  "link": "https://...",
  "target": "_self" | "_blank",
  "backgroundColor": "var(--ast-global-color-0)",
  "color": "var(--ast-global-color-4)",
  "hoverBackgroundColor": "var(--ast-global-color-1)",
  "borderColor": "var(--ast-global-color-0)",
  "borderWidth": 0,
  "borderRadius": 4,
  "icon": "fa fa-arrow-right",
  "iconPosition": "before" | "after"
}
```

### `uagb/info-box`

```json
{
  "block_id": "REQUIRED",
  "headingTag": "h3",
  "headingTitle": "Feature title",
  "headingDesc": "Description",
  "icon": "fa fa-rocket",
  "iconColor": "var(--ast-global-color-0)",
  "iconBgColor": "var(--ast-global-color-5)",
  "headingColor": "var(--ast-global-color-2)",
  "subHeadingColor": "var(--ast-global-color-3)",
  "headingAlign": "left" | "center",
  "ctaType": "none" | "button" | "text" | "all",
  "ctaText": "En savoir plus",
  "ctaLink": "...",
  "imagePosition": "above-title" | "above-content" | "left" | "right"
}
```

### `uagb/faq` + `uagb/faq-child`

```json
// uagb/faq (parent)
{
  "block_id": "REQUIRED",
  "headingAlign": "left",
  "equalHeight": false,
  "layout": "accordion" | "grid",
  "inactiveOtherItems": false,
  "expandFirstItem": true,
  "iconAlign": "left" | "right",
  "iconActiveColor": "var(--ast-global-color-0)",
  "iconColor": "var(--ast-global-color-3)"
}

// uagb/faq-child
{
  "block_id": "REQUIRED",
  "question": "Comment installer le plugin ?",
  "answer": "Allez dans Extensions > Ajouter, recherchez Spectra, installez et activez."
}
```

### `uagb/testimonial`

```json
{
  "block_id": "REQUIRED",
  "test_block_count": 3,
  "test_item_count": 3,
  "columns": 3,
  "test_items": [
    {
      "description": "Témoignage texte",
      "name": "Jean Dupont",
      "company": "ACME Corp",
      "image": { "url": "...", "id": 123 }
    }
  ],
  "headingColor": "var(--ast-global-color-2)",
  "subHeadingColor": "var(--ast-global-color-3)",
  "iconColor": "var(--ast-global-color-0)"
}
```

### `uagb/team`

```json
{
  "block_id": "REQUIRED",
  "teamItems": [
    {
      "name": "Jean Dupont",
      "designation": "CEO",
      "description": "Bio courte",
      "image": { "url": "...", "id": 123 },
      "socialLinks": [
        { "icon": "fa fa-linkedin", "link": "https://linkedin.com/..." }
      ]
    }
  ],
  "imgPosition": "above" | "left" | "right",
  "tcolumn": 3
}
```

### `uagb/post` (post grid)

```json
{
  "block_id": "REQUIRED",
  "postType": "post" | "page" | "{custom_post_type}",
  "postsToShow": 6,
  "categories": "",
  "taxonomyType": "category",
  "orderBy": "date" | "title" | "menu_order" | "modified",
  "order": "desc" | "asc",
  "layout": "grid" | "masonry" | "carousel" | "list",
  "columns": 3,
  "displayPostImage": true,
  "displayPostTitle": true,
  "displayPostExcerpt": true,
  "displayPostDate": true,
  "displayPostAuthor": true,
  "displayPostComment": false,
  "displayPostTaxonomy": false,
  "excerptLength": 25,
  "ctaText": "Lire la suite"
}
```

### `uagb/counter`

```json
{
  "block_id": "REQUIRED",
  "startNumber": 0,
  "endNumber": 1000,
  "totalNumber": 1000,
  "numberPrefix": "",
  "numberSuffix": "+",
  "headingTitle": "Clients satisfaits",
  "animationDuration": 2000,
  "numberColor": "var(--ast-global-color-0)",
  "headingColor": "var(--ast-global-color-2)",
  "layout": "number-and-heading"
}
```

### `uagb/timeline`

```json
{
  "block_id": "REQUIRED",
  "timelineItem": 5,
  "items": [
    { "title": "Étape 1", "content": "Description", "date_label": "Janvier 2026" }
  ],
  "displayPostDate": true,
  "headingAlignment": "center",
  "tm_orientation": "vertical",
  "iconColor": "var(--ast-global-color-0)",
  "lineColor": "var(--ast-global-color-7)"
}
```

### `uagb/how-to`

```json
{
  "block_id": "REQUIRED",
  "headingTitle": "Comment installer le plugin ?",
  "headingDesc": "Tutoriel pas-à-pas",
  "totalTime": "PT5M",
  "totalTimeText": "5 minutes",
  "tools": ["WordPress admin"],
  "supplies": [],
  "steps": [
    { "name": "Étape 1", "text": "Description", "imageURL": "..." }
  ],
  "schema_enabled": true
}
```

## Pour aller plus loin

- Recettes markup éprouvées : `../modules/spectra/markup-recipes.md`
- Génération `block_id` unique : `../modules/spectra/block-id-generator.md`
- Validation pré-POST : `../scripts/validate-block-markup.php`
