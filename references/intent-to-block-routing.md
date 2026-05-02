# Intent → Block Routing

> **Rôle** : table de décision principale du skill. Pour chaque intention éditoriale exprimée en langage naturel, indique quel bloc utiliser (Spectra `uagb/*` ou Gutenberg core `core/*`) avec un fallback secondaire.
>
> **Pattern associé** : pour chaque bloc Spectra, le skill dispose d'un pattern documenté dans `patterns/<nom>.md` qui explique « comment construire » (variables, markup, CSS overrides, pièges, variantes). Liste complète : voir l'index `patterns/` dans le README.

## Règle de priorisation

1. **Spectra prioritaire** quand le bloc apporte un gain visuel/UX significatif (info-box, testimonial, FAQ, how-to, review, countdown, modal, tabs, slider) ou embarque du **schema SEO** (FAQ → FAQPage, how-to → HowTo, review → Review)
2. **Gutenberg core prioritaire** pour les blocs atomiques simples (paragraph, heading H3+, image isolée, embed YouTube/Twitter, quote, code, separator, spacer)
3. **Toujours wrapper** les compositions complexes dans un `uagb/container` pour la cohérence layout/responsive
4. Pour les couleurs : **toujours** utiliser `var(--ast-global-color-X)`, jamais de hex hardcoded (cf `references/design-system-tokens.md`)

## Table de routing complète (45 intentions)

### Texte courant et titres

| Intention éditoriale | Bloc recommandé | Alternative / fallback |
|----------------------|-----------------|------------------------|
| Paragraphe texte courant | `core/paragraph` | — |
| Titre H1 stylé (animation, soulignement, gradient) | `uagb/advanced-heading` | `core/heading` (level 1) |
| Titre H2 avec sous-titre | `uagb/advanced-heading` | `core/heading` + `core/paragraph` |
| Titre H3-H6 simple | `core/heading` | — |
| Citation simple | `core/quote` | `uagb/blockquote` (plus de design) |
| Citation visuelle / pull quote | `uagb/blockquote` | `core/pullquote` |
| Liste à puces simple | `core/list` | — |
| Liste avec icônes personnalisées | `uagb/icon-list` | `core/list` + emojis Unicode |
| Code source affiché | `core/code` | — |
| HTML custom | `core/html` | — |
| Shortcode legacy | `core/shortcode` | — |

### Images et médias

| Intention éditoriale | Bloc recommandé | Alternative / fallback |
|----------------------|-----------------|------------------------|
| Image décorative isolée | `core/image` | — |
| Galerie d'images | `uagb/image-gallery` | `core/gallery` |
| Vidéo uploadée (mp4) | `core/video` | — |
| Audio uploadé (mp3) | `core/audio` | — |
| Embed YouTube | `core/embed` (provider youtube) | — |
| Embed Twitter / X | `core/embed` (provider twitter) | — |
| Embed Spotify / SoundCloud | `core/embed` (provider spotify) | — |
| Embed Vimeo | `core/embed` (provider vimeo) | — |
| Lottie animation | `uagb/lottie` | — |
| Fichier téléchargeable | `core/file` | — |

### Sections de mise en page

| Intention éditoriale | Bloc recommandé | Alternative / fallback |
|----------------------|-----------------|------------------------|
| Hero section avec CTA | `uagb/container` + `uagb/advanced-heading` + `uagb/buttons` | `core/cover` + `core/heading` + `core/buttons` |
| Section CTA pleine largeur | `uagb/call-to-action` | container custom uagb/container + `uagb/buttons` |
| Section avec background image | `uagb/container` (backgroundType: image) | `core/cover` |
| Container avec design (bg color, shadow, padding) | `uagb/container` | `core/group` (plus limité) |
| Section divisée 2 colonnes (texte + image) | `uagb/container` (variation: 2-col) | `core/columns` ou `core/media-text` |
| 3 features (info boxes côte à côte) | `uagb/container` (3 cols) avec 3× `uagb/info-box` | `core/columns` + composition manuelle |
| Pricing 3 tiers | `uagb/container` (3 cols) avec 3× `uagb/info-box` (mode "pricing") | `uagb/price-list` |
| Bandeau de stats / chiffres | `uagb/container` + N× `uagb/counter` | `core/columns` + `core/heading` |

### Blocs SEO et schema

| Intention éditoriale | Bloc recommandé | Schema généré |
|----------------------|-----------------|---------------|
| FAQ accordéon | `uagb/faq` (avec `uagb/faq-child`) | FAQPage JSON-LD |
| How-to step-by-step | `uagb/how-to` | HowTo JSON-LD |
| Review d'un produit | `uagb/review` | Review + AggregateRating JSON-LD |
| Star rating standalone | `uagb/star-rating` | Rating |
| Table de matières (TOC) | `uagb/table-of-contents` | — |

### Blocs interactifs

| Intention éditoriale | Bloc recommandé | Notes |
|----------------------|-----------------|-------|
| Tabs / onglets | `uagb/tabs` (avec `uagb/tabs-child`) | Pas d'équivalent core. Stagger animations possibles |
| Carrousel slider | `uagb/slider` (avec `uagb/slider-child`) | Auto-play, navigation, pagination |
| Modal popup | `uagb/modal` | Trigger sur click ou load |
| Compte à rebours | `uagb/countdown` | Pour offres limitées, lancement produit |
| Compteurs animés | `uagb/counter` | Anime au scroll |
| Témoignages | `uagb/testimonial` | Layout grid ou carousel |

### Présentation équipe / personnes

| Intention éditoriale | Bloc recommandé | Alternative |
|----------------------|-----------------|-------------|
| Présentation équipe / membres | `uagb/team` | `uagb/container` + `uagb/info-box` |
| Présentation auteur | `uagb/info-box` | `uagb/container` custom |
| Speakers d'un événement | `uagb/team` (variation gallery) | — |

### Blog / Posts dynamiques

| Intention éditoriale | Bloc recommandé | Alternative |
|----------------------|-----------------|-------------|
| Grille d'articles récents | `uagb/post` (post grid) | `core/latest-posts` |
| Liste posts d'une catégorie | `uagb/post` (filtré par taxonomy) | `core/query-loop` |
| Article carousel | `uagb/post` (layout: carousel) | — |
| Timeline blog (chronologique) | `uagb/post` (layout: timeline) | — |
| Pagination de posts | `uagb/post` (avec pagination) | — |

### Forms

| Intention éditoriale | Bloc recommandé | Alternative |
|----------------------|-----------------|-------------|
| Formulaire de contact simple | `uagb/forms` (mode: contact) | Contact Form 7 + `uagb/cf7-designer` |
| Formulaire newsletter | `uagb/forms` (mode: subscribe) | Brevo plugin + shortcode `core/shortcode` |
| Formulaire CF7 stylisé | `uagb/cf7-designer` | — |
| Formulaire Gravity Forms stylisé | `uagb/gf-designer` | — |
| Search bar | `uagb/wp-search` | `core/search` |

### Maps et géolocalisation

| Intention éditoriale | Bloc recommandé | Alternative |
|----------------------|-----------------|-------------|
| Map intégrée Google | `uagb/google-map` | `core/embed` (Maps URL) |

### Notices et call-outs

| Intention éditoriale | Bloc recommandé | Alternative |
|----------------------|-----------------|-------------|
| Notice / alerte / callout | `uagb/inline-notice` | `core/group` + classe CSS |

### Espacement / structure

| Intention éditoriale | Bloc recommandé | Alternative |
|----------------------|-----------------|-------------|
| Séparateur visuel | `uagb/separator` | `core/separator` |
| Espace vertical | `core/spacer` | — |
| Boutons (1 ou +) | `uagb/buttons` (avec `uagb/buttons-child`) | `core/buttons` (plus simple, moins de design) |
| Bouton CTA standalone | `uagb/marketing-button` | `core/button` |

### Navigation

| Intention éditoriale | Bloc recommandé | Alternative |
|----------------------|-----------------|-------------|
| Menu de navigation | `core/navigation` | — |
| Liste de pages enfants | `core/page-list` | `uagb/post` (filtré) |
| Breadcrumbs | (utiliser le breadcrumb du thème) | — |

### Timeline

| Intention éditoriale | Bloc recommandé | Alternative |
|----------------------|-----------------|-------------|
| Timeline / chronologie verticale | `uagb/timeline` | `core/list` numérotée |
| Roadmap produit | `uagb/timeline` (variation roadmap) | — |

### Réseaux sociaux

| Intention éditoriale | Bloc recommandé | Alternative |
|----------------------|-----------------|-------------|
| Boutons partage social | `uagb/social-share` (avec `uagb/social-share-child`) | — |

### Cas avancés

| Intention éditoriale | Bloc recommandé | Notes |
|----------------------|-----------------|-------|
| Popup builder (full screen modal) | `uagb/popup-builder` | — |
| Liste taxonomies (catégories) | `uagb/taxonomy-list` | `core/categories` |
| Promote cross products | `uagb/promote-cross-products` | Use case agence/marketplace |

## Logique de sélection automatique (pour le skill)

Quand l'utilisateur exprime une intention en langage naturel, suivre ces étapes :

> **Heuristique pour Claude Code (pas de script automatique)** : la sélection est faite par le LLM en s'appuyant sur ces règles. Il n'y a pas de scripts/route-intent.php : Claude Code lit la table ci-dessus et la sélection ci-dessous, et choisit.

1. **Tokenize** la phrase pour identifier les mots-clés (« hero », « pricing », « FAQ », « grille », « timeline », « contact », etc.)
2. **Match** chaque mot-clé contre la table de routing ci-dessus
3. **Priorise** dans cet ordre :
   - **Spectra** si le bloc apporte un gain visuel/UX significatif (`uagb/info-box`, `uagb/testimonial`, `uagb/team`, `uagb/timeline`)
   - **Spectra** si le bloc embarque du schema SEO automatique (`uagb/faq` → FAQPage, `uagb/how-to` → HowTo, `uagb/review` → Review)
   - **Spectra** s'il n'y a pas d'équivalent core (`uagb/countdown`, `uagb/modal`, `uagb/tabs`, `uagb/slider`)
   - **Core** pour les blocs atomiques simples (`core/paragraph`, `core/heading` H3+, `core/list`, `core/image` isolée, `core/embed`)
4. **Wrappe** systématiquement les compositions multi-blocs dans un `uagb/container` (jamais `core/group`, `core/columns` ou `core/cover` quand on veut un effet design)
5. **Génère** un `block_id` unique pour chaque bloc Spectra (pattern : `<context>-<type>-<index>`, ex `hero-cta-1`, `pricing-tier-3`, `faq-q5`)
7. **Applique** les couleurs via `var(--ast-global-color-X)` depuis la palette active

## Exemples concrets

### Exemple 1 : « Crée une landing page formation avec hero, 3 features, pricing, FAQ et CTA YouTube »

Intentions tokenisées :
- « hero » → `uagb/container` + `uagb/advanced-heading` + `uagb/buttons`
- « 3 features » → `uagb/container` (3 cols) + 3× `uagb/info-box`
- « pricing » → `uagb/container` (3 cols) + 3× `uagb/info-box` (mode pricing) ou `uagb/price-list`
- « FAQ » → `uagb/faq` (avec FAQPage schema)
- « CTA YouTube » → `core/heading` + `core/embed` (provider youtube)

Composition finale : 5 sections principales, ~12-15 blocs au total.

### Exemple 2 : « Article éditorial sur les 5 plugins SEO WordPress avec sommaire et vidéo »

Intentions tokenisées :
- « sommaire » → `uagb/table-of-contents`
- « article éditorial » → `core/heading` + `core/paragraph` (récurrents)
- « 5 plugins » → 5× `uagb/info-box` ou 5× section répétée
- « vidéo » → `core/embed` (YouTube)

### Exemple 3 : « Refonds /a-propos/ en mode moderne avec timeline et équipe »

Intentions tokenisées :
- « timeline » → `uagb/timeline`
- « équipe » → `uagb/team`
- « moderne » → utiliser les patterns avec gradients, glassmorphism, animations

## Anti-patterns à éviter

- ❌ **Hex hardcoded** dans les attrs couleur des blocs uagb. Toujours `var(--ast-global-color-X)`.
- ❌ **`uagb/buttons-child` standalone** sans parent `uagb/buttons` → le block parser cassera.
- ❌ **`block_id` répété** entre blocs → comportements imprévisibles.
- ❌ **Markdown brut** dans le content. Le content WP doit être du **block markup** (commentaires HTML `<!-- wp:* -->`).
- ❌ **Nested `uagb/container` à profondeur > 3** → impact perf + complexité d'édition.
- ❌ **`core/cover` sur l'ensemble d'une page** alors qu'`uagb/container` est plus flexible.
- ❌ **`core/columns` pour des features** alors qu'`uagb/container` (variation grid) gère mieux le responsive.

## Pour aller plus loin

- Catalogue exhaustif des 49 blocs Spectra avec attributs : [`spectra-blocks-catalog.md`](spectra-blocks-catalog.md)
- Catalogue des blocs core curé : [`gutenberg-core-blocks.md`](gutenberg-core-blocks.md)
- Syntaxe block markup et pièges parsing : [`block-markup-syntax.md`](block-markup-syntax.md)
- Mapping design system : [`design-system-tokens.md`](design-system-tokens.md)
- Patterns prêts à l'emploi : `../patterns/`
- Templates de pages complètes : `../templates/`
