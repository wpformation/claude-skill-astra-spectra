# Pattern : Table of Contents (uagb/table-of-contents)

> **Use case** : sommaire automatique en début d'article long (1500+ mots) qui parse les H2/H3 et génère des liens d'ancre. Améliore UX (scan-reading) + SEO (Google peut afficher le sommaire en SERP comme « jump-to »).

> **Bloc Spectra** : `uagb/table-of-contents`. Génération automatique via JavaScript au render. Niveaux configurables (H2 seul, H2+H3, H2+H3+H4). Optionnel : collapsible (click sur le titre du sommaire), smooth scroll, sticky.

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{TOC_HEADING}}` | Titre du sommaire | `Sommaire` |
| `{{TOC_DEPTH}}` | Niveaux à inclure | `[2,3]` (H2+H3) ou `[2]` seul |
| `{{TOC_COLLAPSIBLE}}` | Toggle expand/collapse | `true` / `false` |
| `{{TOC_INITIAL_STATE}}` | Si collapsible : `expanded` ou `collapsed` au load | `expanded` |
| `{{TOC_SMOOTH_SCROLL}}` | Smooth scroll au clic ancre | `true` |
| `{{TOC_STICKY}}` | Sidebar sticky desktop | `false` (par défaut, voir variante 2) |

## Block markup

```html
<!-- wp:uagb/table-of-contents {"block_id":"{slug}-toc","heading":"{{TOC_HEADING}}","headingTag":"h2","mappingHeaders":[false,true,true,false,false,false],"makeCollapsible":{{TOC_COLLAPSIBLE}},"initialCollapse":false,"smoothScroll":{{TOC_SMOOTH_SCROLL}},"smoothScrollOffset":80,"smoothScrollDelay":800,"scrollToTop":true,"scrollToTopColor":"var(--ast-global-color-0)","disableBullets":false,"customWidth":true,"widthDesktop":100,"widthTypeDesktop":"%","tColumnsDesktop":1,"tColumnsTablet":1,"tColumnsMobile":1,"backgroundColor":"#fafafa","headingColor":"#0F172A","linkColor":"#0F172A","linkHoverColor":"var(--ast-global-color-0)","headingFontSizeDesktop":18,"headingFontWeight":"800","fontSizeDesktop":15,"fontWeight":"500","leftPaddingDesktop":32,"rightPaddingDesktop":32,"topPaddingDesktop":24,"bottomPaddingDesktop":24,"borderTopLeftRadius":12,"borderTopRightRadius":12,"borderBottomLeftRadius":12,"borderBottomRightRadius":12} -->
<div class="wp-block-uagb-table-of-contents uagb-block-{slug}-toc">
  <div class="uagb-toc__wrap">
    <div class="uagb-toc__title-wrap">
      <h2 class="uagb-toc__title">{{TOC_HEADING}}</h2>
    </div>
    <div class="uagb-toc__list-wrap">
      <ol class="uagb-toc__list">
        <!-- Auto-généré au render à partir des H2/H3 du post -->
      </ol>
    </div>
  </div>
</div>
<!-- /wp:uagb/table-of-contents -->
```

## CSS overrides recommandés

```css
/* Sommaire — fond gris clair, padding éditorial */
.uagb-block-{slug}-toc .uagb-toc__wrap {
  border-radius: 12px !important;
  border: 1px solid #e5e7eb !important;
}

/* Titre sommaire — uppercase tracking */
.uagb-block-{slug}-toc .uagb-toc__title {
  font-size: 14px !important;
  font-weight: 800 !important;
  text-transform: uppercase !important;
  letter-spacing: 2px !important;
  color: #454F5E !important;
  margin: 0 0 16px !important;
}

/* Liste — bullets none, espacement éditorial */
.uagb-block-{slug}-toc .uagb-toc__list {
  list-style: none !important;
  padding-left: 0 !important;
  margin: 0 !important;
}

.uagb-block-{slug}-toc .uagb-toc__list li {
  padding: 6px 0 !important;
  border-left: 3px solid transparent !important;
  padding-left: 14px !important;
  transition: border-color 0.15s ease, color 0.15s ease !important;
}

.uagb-block-{slug}-toc .uagb-toc__list li.uagb-toc__active {
  border-left-color: var(--ast-global-color-0) !important;
  color: var(--ast-global-color-0) !important;
  font-weight: 700 !important;
}

/* Liens — sans soulignement, color hover */
.uagb-block-{slug}-toc a {
  color: #0F172A !important;
  text-decoration: none !important;
  display: block !important;
}

.uagb-block-{slug}-toc a:hover {
  color: var(--ast-global-color-0) !important;
}

/* Niveau 2 — sub-list indentée */
.uagb-block-{slug}-toc ol ol {
  margin-top: 4px !important;
  padding-left: 16px !important;
  border-left: 1px dashed #e5e7eb !important;
}

.uagb-block-{slug}-toc ol ol li {
  font-size: 14px !important;
  color: #454F5E !important;
}
```

## Pièges

| # | Quirk |
|---|---|
| **Niveaux** | `mappingHeaders: [h1,h2,h3,h4,h5,h6]` est un array de 6 booléens. Pour TOC H2+H3 → `[false,true,true,false,false,false]`. Pour H2 seul → `[false,true,false,false,false,false]` |
| **JS au render** | Le TOC se génère côté **client** au load. Si JS désactivé → le sommaire est vide. Acceptable pour SEO (Google exécute JS) mais pas pour l'archivage / le print |
| **Smooth scroll offset** | Si tu as un header sticky de 80px, mettre `smoothScrollOffset: 80` sinon les ancres scrollent sous le header |
| **Position** | Le TOC se met en haut de l'article, AVANT le premier H2. Pas après les premiers paragraphes (sinon Google peut ne pas le détecter) |
| **Slug ancres** | Spectra génère les ancres `#slug-du-titre` par défaut. Si un H2 contient des accents/apostrophes, vérifier que les ancres sont bien `#mon-titre` (pas `#mon-titre-1` ou des chars échappés) |

## Variantes

### Variante 1 — TOC inline horizontal (article court)

Pour un article 800-1200 mots avec 3-4 H2 seulement, un TOC vertical fait trop. Préférer un horizontal :

```css
.uagb-block-{slug}-toc .uagb-toc__list {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 12px !important;
}

.uagb-block-{slug}-toc .uagb-toc__list li {
  background: #fff !important;
  padding: 8px 16px !important;
  border-radius: 999px !important;
  border: 1px solid #e5e7eb !important;
}
```

Effet : pills cliquables comme un breadcrumb sommaire.

### Variante 2 — TOC sidebar sticky (article très long, 3000+ mots)

Wrapper l'article dans un container `directionDesktop:row` avec :
- Sidebar gauche (`widthDesktop:25`) : le TOC avec `position: sticky; top: 100px;`
- Content droit (`widthDesktop:70`) : l'article

```css
.uagb-block-{slug}-toc {
  position: sticky !important;
  top: 100px !important;
  max-height: calc(100vh - 120px) !important;
  overflow-y: auto !important;
}

@media (max-width: 1024px) {
  .uagb-block-{slug}-toc { position: static !important; }
}
```

Sur mobile/tablette, le TOC redevient inline en haut.

### Variante 3 — TOC collapsible (mobile-first)

Sur mobile, un TOC long mange l'écran. Le rendre collapsible :

```json
{
  "makeCollapsible": true,
  "initialCollapse": true
}
```

Le user clique sur « Sommaire » pour expand. Recommandé pour 8+ items.

### Variante 4 — TOC avec scroll progress

Ajouter une barre de progression qui se remplit au scroll de l'article. CSS + JS minimal :

```css
.toc-progress-bar {
  position: fixed; top: 0; left: 0; height: 3px;
  background: var(--ast-global-color-0);
  width: 0%; transition: width 0.1s ease;
  z-index: 100;
}
```

```html
<div class="toc-progress-bar"></div>
<script>
window.addEventListener('scroll', () => {
  const article = document.querySelector('.entry-content');
  const scroll = window.scrollY;
  const max = article.scrollHeight - window.innerHeight;
  document.querySelector('.toc-progress-bar').style.width = (scroll / max * 100) + '%';
});
</script>
```

## Test post-génération

1. Vérifier que le TOC liste bien les H2/H3 du post (pas de placeholder)
2. Cliquer sur un item → smooth scroll vers la section, accent visuel sur l'item actif
3. Mobile : si collapsible, vérifier le toggle expand/collapse
4. Sticky (variante 2) : vérifier que le TOC reste visible au scroll, ne sort pas du viewport
5. SEO : view-source de la page, vérifier que les ancres `id="..."` existent sur les `<h2>` et `<h3>`

## Pour aller plus loin

- Article complet (avec TOC + sections + FAQ) : voir `patterns/article-content-rich.md`
- Template article long : voir `templates/blog-editorial.md`
