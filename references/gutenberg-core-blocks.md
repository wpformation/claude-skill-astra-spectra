# Référence : blocs Gutenberg core/* — quand les utiliser, leurs limites

> **Spectra ne couvre PAS tous les besoins éditoriaux.** Pour les blocs atomiques simples (paragraphe, heading H3+, list, image isolée, embed YouTube), utiliser les blocs `core/*` est plus léger et plus prévisible que de bricoler avec `uagb/*`.

> Ce fichier liste les 30+ blocs core indispensables, leurs cas d'usage validés, et leurs limites face à Spectra.

## Règle de routing core vs uagb

| Cas | Préférer |
|---|---|
| Bloc atomique simple (paragraph, heading H3+, list, image isolée) | `core/*` |
| Embed natif (YouTube, Twitter, Vimeo, Spotify) | `core/embed` |
| Composition complexe avec design (hero, features, testimonials, cards) | `uagb/*` |
| Schema SEO (FAQ, How-to, Review) | `uagb/*` (FAQPage/HowTo/Review schema auto) |
| Maintien compatibilité si Spectra désactivé | `core/*` |

## Catalogue core/* validé pour le skill

### Texte

#### `core/paragraph`
- **Usage** : tout paragraphe courant
- **Attributs critiques** : `align`, `dropCap`, `fontSize`, `lineHeight`, `textColor`
- **Limite** : pas de styling avancé. Pour texte stylé, wrapper dans `uagb/container`

```html
<!-- wp:paragraph -->
<p>Texte du paragraphe avec accents corrects.</p>
<!-- /wp:paragraph -->
```

#### `core/heading`
- **Usage** : titres H3-H6 simples (les H1-H2 stylés vont dans `uagb/info-box` avec eyebrow)
- **Attributs critiques** : `level` (1-6), `align`, `textColor`, `fontSize`
- **Limite** : pas d'eyebrow prefix, pas de letter-spacing négatif

```html
<!-- wp:heading {"level":3} -->
<h3>Sous-titre simple</h3>
<!-- /wp:heading -->
```

#### `core/list`
- **Usage** : liste à puces ou numérotée simple
- **Attributs critiques** : `ordered`, `values`
- **Limite** : pas d'icônes custom, pas de couleurs par item. Pour bullets stylées préférer `uagb/icon-list` (avec quirks #11) OU 3 mini-cards container

```html
<!-- wp:list -->
<ul><li>Premier item</li><li>Deuxi&egrave;me item</li></ul>
<!-- /wp:list -->
```

#### `core/quote`
- **Usage** : citation simple en flux d'article
- **Attributs critiques** : `value`, `citation`, `align`
- **Alternative** : `uagb/blockquote` pour design avancé

#### `core/pullquote`
- **Usage** : pull quote magazine-style
- **Attributs critiques** : `value`, `citation`, `mainColor`, `textColor`
- **Limite** : design opinioné, peu customisable

#### `core/preformatted`
- **Usage** : texte pré-formaté (code source, ASCII art, poésie)
- **Attributs critiques** : aucun, juste innerHTML pre

#### `core/code`
- **Usage** : extrait de code dans un article tutoriel
- **Attributs critiques** : aucun, juste innerHTML code
- **Note** : pas de syntax highlighting natif. Pour highlight, ajouter Prism/highlight.js via custom CSS/JS

```html
<!-- wp:code -->
<pre class="wp-block-code"><code>function exemple() { return 42; }</code></pre>
<!-- /wp:code -->
```

#### `core/verse`
- **Usage** : poésie / texte avec retours à la ligne préservés
- **Rare** : skip pour la plupart des sites

### Listes & navigation

#### `core/separator`
- **Usage** : ligne horizontale séparatrice entre sections
- **Attributs critiques** : `style` (default, dots, wide)
- **Note** : très basique. Pour décoratif préférer `uagb/separator`

#### `core/spacer`
- **Usage** : espacement vertical custom entre blocs
- **Attributs critiques** : `height` (px)
- **Note** : utile pour fine-tune sans toucher aux paddings de container

```html
<!-- wp:spacer {"height":48} -->
<div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->
```

#### `core/page-list`
- **Usage** : afficher la liste des pages enfants (sitemap inline)
- **Attributs critiques** : `parentPageID`
- **Note** : auto-rendered par WP, pas de contenu statique

### Médias

#### `core/image`
- **Usage** : image isolée simple (pas dans une composition)
- **Attributs critiques** : `id`, `url`, `alt`, `caption`, `align`, `sizeSlug`, `linkDestination`
- **Limite** : pas d'objectFit, pas de border-radius custom (sauf via theme.json), pas d'overlay. Pour ces cas, utiliser `uagb/image`

```html
<!-- wp:image {"id":47,"sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img src="..." alt="..."/></figure>
<!-- /wp:image -->
```

#### `core/gallery`
- **Usage** : galerie d'images simple (3-12 images)
- **Attributs critiques** : `ids[]`, `columns`, `imageCrop`, `linkTo`
- **Alternative** : `uagb/image-gallery` pour design avancé (lightbox, masonry)

#### `core/cover`
- **Usage** : section avec image background + overlay + texte (alternative simple à `uagb/container` image bg)
- **Attributs critiques** : `id`, `url`, `dimRatio` (0-100), `overlayColor`, `align`
- **Limite vs uagb/container** : pas de gradient overlay, pas de padding responsive granulaire

```html
<!-- wp:cover {"url":"...","dimRatio":70,"align":"full"} -->
<div class="wp-block-cover alignfull">...</div>
<!-- /wp:cover -->
```

#### `core/media-text`
- **Usage** : layout 50/50 média + texte
- **Attributs critiques** : `mediaPosition` (left/right), `mediaWidth`, `verticalAlignment`
- **Alternative** : `uagb/container` direction:row pour design custom (cf `patterns/about-story-split.md`)

#### `core/video`
- **Usage** : vidéo locale uploadée (MP4/WebM)
- **Attributs critiques** : `id`, `src`, `controls`, `loop`, `muted`, `autoplay`
- **Limite** : pas optimal pour vidéo lourde, préférer YouTube/Vimeo via `core/embed`

#### `core/audio`
- **Usage** : audio uploadé (podcast, voiceover)
- **Attributs critiques** : `id`, `src`, `loop`, `autoplay`

#### `core/file`
- **Usage** : fichier téléchargeable (PDF, ZIP, DOCX)
- **Attributs critiques** : `id`, `href`, `displayPreview`, `previewHeight`

### Embeds

#### `core/embed` — la famille embed
- **Usage** : YouTube, Vimeo, Twitter, Spotify, SoundCloud, Instagram, TikTok, Facebook, Reddit, etc.
- **Variantes nommées** : `core-embed/youtube`, `core-embed/vimeo`, `core-embed/twitter`, etc.
- **Attributs critiques** : `url` (oEmbed compatible), `type`, `providerNameSlug`

```html
<!-- wp:embed {"url":"https://youtube.com/watch?v=XXX","type":"video","providerNameSlug":"youtube"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube">
  <div class="wp-block-embed__wrapper">https://youtube.com/watch?v=XXX</div>
</figure>
<!-- /wp:embed -->
```

**Provider slugs courants** : `youtube`, `vimeo`, `twitter`, `spotify`, `soundcloud`, `instagram`, `tiktok`, `facebook`, `dailymotion`, `flickr`, `imgur`, `meetup-com`, `mixcloud`, `pinterest`, `reddit`, `reverbnation`, `screencast`, `slideshare`, `smugmug`, `someecards`, `speaker-deck`, `ted`, `tumblr`, `videopress`, `wordpress-tv`.

### Layout

#### `core/group`
- **Usage** : wrapper basique pour grouper plusieurs blocs
- **Attributs critiques** : `tagName`, `align`, `backgroundColor`, `textColor`
- **Limite vs uagb/container** : pas de direction:row natif, pas de padding responsive granulaire, pas de gradient bg, pas d'image bg avec overlay
- **Quand l'utiliser** : groupement simple, e.g. wrapper un texte + image dans une carte basique

#### `core/columns`
- **Usage** : grille colonnes simple (2-6 cols)
- **Attributs critiques** : `columns` (2-6), `verticalAlignment`
- **Limite vs uagb/container row** : pas de wrap responsive granulaire, pas d'equalHeight, pas de columnGap responsive
- **Quand l'utiliser** : grille simple sans design system Spectra

#### `core/column`
- **Enfant de `core/columns`**
- **Attributs critiques** : `width` (e.g. "33.33%")

#### `core/buttons`
- **Usage** : groupe de boutons simples
- **Attributs critiques** : aucun, contient `core/button` enfants
- **Limite vs uagb/buttons** : pas de hover states custom, pas de border-radius granulaire, pas de padding responsive

#### `core/button`
- **Enfant de `core/buttons`**
- **Attributs critiques** : `text`, `url`, `style`, `borderRadius`

### Articles & taxonomies

#### `core/latest-posts`
- **Usage** : afficher les N derniers articles
- **Attributs critiques** : `postsToShow`, `displayPostContent`, `displayPostDate`, `categories`, `order`
- **Alternative** : `uagb/post` pour design grid avancé

#### `core/latest-comments`
- **Rare** : skip généralement

#### `core/categories`
- **Usage** : liste/dropdown des catégories
- **Attributs critiques** : `displayAsDropdown`, `showHierarchy`, `showPostCounts`

#### `core/tag-cloud`
- **Usage** : nuage de tags
- **Attributs critiques** : `taxonomy`, `numberOfTags`, `smallestFontSize`

#### `core/archives`
- **Usage** : liste archives par mois
- **Attributs critiques** : `displayAsDropdown`, `showPostCounts`

### Forms & search

#### `core/search`
- **Usage** : barre de recherche simple
- **Attributs critiques** : `label`, `buttonText`, `placeholder`, `buttonPosition`
- **Alternative** : `uagb/wp-search` pour design avancé

#### `core/rss`
- **Usage** : feed RSS embedded (afficher les posts d'un autre site)
- **Attributs critiques** : `feedURL`, `itemsToShow`, `displayAuthor`

#### `core/calendar`
- **Usage** : calendrier des posts par mois
- **Attributs critiques** : `month`, `year`

### Navigation

#### `core/navigation`
- **Usage** : menu de navigation principal
- **Note** : géré par le theme habituellement, rarement dans le content

### HTML / Shortcodes

#### `core/html`
- **Usage** : HTML custom inline (SVG, embed iframe non-oEmbed, snippet HTML)
- **Attributs critiques** : aucun, juste innerHTML
- **Quand l'utiliser** : SVG inline custom, iframe Calendly, snippet de tracking

```html
<!-- wp:html -->
<svg width="48" height="48" viewBox="0 0 24 24"><path d="..."/></svg>
<!-- /wp:html -->
```

> ⚠️ **Piège kses (découvert POC `claude-skill-gutenberg-core` 02/05/2026)** : si tu insères un `<style>` ou `<script>` à l'intérieur d'un `core/html` via `wp_insert_post` / `wp_update_post` / REST API et que l'auteur du POST n'a PAS la capability `unfiltered_html`, WordPress strip silencieusement les balises `<style>`/`<script>` via `wp_filter_post_kses`. Le marker de bloc `<!-- wp:html -->` est conservé mais son contenu est sanitizé. Vérifier en lisant `post_content` après update : si tes balises ont disparu, c'est ce piège. Sur **single-site**, seuls les administrators ont `unfiltered_html`. Sur **multisite**, **personne** ne l'a par défaut (même les super-admins). Solutions : (a) POST en tant qu'admin via Application Password, (b) bypass temporaire via `kses_remove_filters()` autour de `wp_update_post` puis `kses_init_filters()`, (c) utiliser `_uag_custom_page_level_css` (meta natif Spectra) pour le CSS — c'est ce que fait le skill par défaut, donc ce piège ne te concerne que si tu sors du flux standard pour insérer du `<style>` custom.

#### `core/shortcode`
- **Usage** : exécuter un shortcode WordPress legacy (`[contact-form-7 id="123"]`, `[woocommerce_cart]`)
- **Attributs critiques** : `text` (le shortcode)
- **Note** : utile pour intégrer plugins legacy

```html
<!-- wp:shortcode -->
[contact-form-7 id="123" title="Contact"]
<!-- /wp:shortcode -->
```

## Quand utiliser core vs uagb — table de décision rapide

| Intention | Bloc recommandé |
|---|---|
| Texte courant | `core/paragraph` |
| H1/H2 stylé (eyebrow + drama) | `uagb/info-box` |
| H3-H6 simple | `core/heading` |
| Liste simple | `core/list` |
| Liste avec icônes | `uagb/icon-list` (avec quirks #11) ou 3 mini-cards |
| Citation simple | `core/quote` |
| Citation visuelle / pull quote | `uagb/blockquote` ou `core/pullquote` |
| Image isolée | `core/image` |
| Galerie | `uagb/image-gallery` ou `core/gallery` |
| Hero avec image bg + overlay | `uagb/container` (cf `patterns/hero-image-overlay.md`) |
| Cover simple sans drama | `core/cover` |
| 50/50 image + texte | `core/media-text` ou `uagb/container` row (`patterns/about-story-split.md`) |
| Vidéo YouTube | `core/embed` (provider youtube) |
| 3 features cards | `uagb/container` row + 3 enfants |
| Pricing 3 tiers | `uagb/container` row + 3 cards composition |
| FAQ accordéon | `uagb/faq` (avec quirks #3 #12) |
| Stats 4 chiffres | `uagb/container` row + 4 wrappers (cf quirk #2) |
| Testimonials | `uagb/container` row + 3 cards composition |
| Embed YouTube | `core/embed` |
| Code source | `core/code` |
| HTML custom (SVG, iframe Calendly) | `core/html` |
| Shortcode legacy (CF7, WC) | `core/shortcode` |
| Espace vertical entre blocs | `core/spacer` |
| Séparateur visuel décoratif | `uagb/separator` |
| Navigation menu | `core/navigation` (header habituellement) |

## Pièges core/* connus

### `core/embed` YouTube — auto-resume bloqué
Sur certains thèmes, l'iframe YouTube est wrappé dans `<figure>` qui peut break le aspect-ratio responsive. Si la vidéo apparaît coupée, ajouter CSS :

```css
.wp-block-embed.is-type-video iframe {
  aspect-ratio: 16/9;
  width: 100%;
  height: auto;
}
```

### `core/columns` sans wrap mobile
Par défaut, les colonnes ne wrappent pas sur mobile. Pour wrap, ajouter classe `is-stack-on-mobile` :

```html
<!-- wp:columns {"className":"is-stack-on-mobile"} -->
```

### `core/gallery` mode masonry
Default = grille fixe, pas masonry. Pour masonry, utiliser `uagb/image-gallery` ou ajouter CSS Masonry custom.

## TODO v1.1+

- [ ] `references/core-blocks-attributes.md` exhaustif (tous les attributs JSON pour chaque bloc)
- [ ] Validation que le user a bien WordPress 6.0+ (certains blocs core sont apparus en 6.0/6.1)
- [ ] Patterns combinés core+uagb pour articles éditoriaux (cf `patterns/article-content-rich.md`)
