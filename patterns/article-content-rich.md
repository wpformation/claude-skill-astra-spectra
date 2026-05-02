# Pattern : Article Content Rich (Éditorial)

> **Use case** : Article de blog éditorial type « 5 plugins SEO WordPress » ou « Comment installer X ». Mix de blocs core (paragraph, heading, image, embed) et Spectra (TOC, FAQ, info-box, inline-notice). Optimisé SEO et lisibilité.

## Composition standard d'un article éditorial

```
1. Container (alignwide, max 800px) avec direction column
   ├── core/heading (H1) — Titre article
   ├── core/paragraph — Lead (2-3 phrases accrocheuses)
   ├── uagb/table-of-contents — Sommaire auto depuis H2/H3
   ├── core/heading (H2) — Section 1
   │   ├── core/paragraph — Texte
   │   ├── core/image — Illustration
   │   ├── uagb/inline-notice — Callout important
   │   └── core/list — Liste à puces
   ├── core/heading (H2) — Section 2
   │   ├── core/paragraph
   │   ├── core/embed — Vidéo YouTube
   │   └── core/heading (H3) — Sous-section
   │       └── core/paragraph
   ├── uagb/info-box — Récap encadré
   ├── core/heading (H2) — FAQ
   ├── uagb/faq + N× uagb/faq-child
   └── uagb/container (CTA fin d'article)
```

## Block markup (squelette adaptable)

```html
<!-- wp:uagb/container {"block_id":"article-wrapper","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":800,"directionDesktop":"column","alignItemsDesktop":"stretch","rowGapDesktop":24,"topPaddingDesktop":40,"bottomPaddingDesktop":80,"topPaddingTablet":32,"bottomPaddingTablet":60,"topPaddingMobile":24,"bottomPaddingMobile":48,"leftPaddingTablet":24,"rightPaddingTablet":24,"leftPaddingMobile":16,"rightPaddingMobile":16} -->
<div class="wp-block-uagb-container alignwide uagb-block-article-wrapper">

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">{{ARTICLE_TITLE}}</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{{LEAD_PARAGRAPH}}</p>
<!-- /wp:paragraph -->

<!-- wp:uagb/table-of-contents {"block_id":"article-toc","headingTitle":"Sommaire","headingTag":"div","heading":"h2,h3","headingColor":"var(--ast-global-color-2)","tColor":"var(--ast-global-color-0)","initialCollapse":false,"makeCollapsible":true,"backgroundColor":"var(--ast-global-color-5)","containerPaddingTop":24,"containerPaddingBottom":24,"containerPaddingLeft":24,"containerPaddingRight":24,"borderRadius":12} -->
<div class="wp-block-uagb-table-of-contents uagb-block-article-toc">[TOC render]</div>
<!-- /wp:uagb/table-of-contents -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">{{H2_1}}</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{{P1_TEXT}}</p>
<!-- /wp:paragraph -->

<!-- wp:image {"sizeSlug":"large"} -->
<figure class="wp-block-image size-large"><img src="{{IMG1_URL}}" alt="{{IMG1_ALT}}"/></figure>
<!-- /wp:image -->

<!-- wp:uagb/inline-notice {"block_id":"notice-1","noticeAlignment":"left","noticeTitle":"💡 À retenir","noticeContent":"{{NOTICE_TEXT}}","noticeType":"info","noticeColor":"var(--ast-global-color-0)","noticeBgColor":"var(--ast-global-color-5)","contentColor":"var(--ast-global-color-3)","headingColor":"var(--ast-global-color-2)","titleType":"icon","borderRadius":12,"containerPaddingTop":20,"containerPaddingBottom":20,"containerPaddingLeft":24,"containerPaddingRight":24} -->
<div class="wp-block-uagb-inline-notice uagb-block-notice-1">[Notice render]</div>
<!-- /wp:uagb/inline-notice -->

<!-- wp:list -->
<ul class="wp-block-list"><li>{{LIST_ITEM_1}}</li><li>{{LIST_ITEM_2}}</li><li>{{LIST_ITEM_3}}</li></ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">{{H2_2}}</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>{{P2_TEXT}}</p>
<!-- /wp:paragraph -->

<!-- wp:embed {"url":"{{YOUTUBE_URL}}","type":"video","providerNameSlug":"youtube"} -->
<figure class="wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube"><div class="wp-block-embed__wrapper">{{YOUTUBE_URL}}</div></figure>
<!-- /wp:embed -->

<!-- wp:uagb/info-box {"block_id":"recap-box","headingTag":"h3","headingTitle":"📌 En résumé","headingDesc":"{{RECAP_TEXT}}","showIcon":false,"headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"left","ctaType":"none","backgroundType":"color","backgroundColor":"var(--ast-global-color-5)","containerPaddingTopDesktop":32,"containerPaddingBottomDesktop":32,"containerPaddingLeftDesktop":32,"containerPaddingRightDesktop":32,"borderRadiusTopLeft":12,"borderRadiusTopRight":12,"borderRadiusBottomLeft":12,"borderRadiusBottomRight":12,"borderStyle":"solid","borderLeftWidth":4,"borderColor":"var(--ast-global-color-0)"} -->
<div class="wp-block-uagb-info-box uagb-block-recap-box"><h3 class="uagb-ifb-title">📌 En résumé</h3><p class="uagb-ifb-desc">{{RECAP_TEXT}}</p></div>
<!-- /wp:uagb/info-box -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Questions fréquentes</h2>
<!-- /wp:heading -->

<!-- wp:uagb/faq {"block_id":"article-faq","layout":"accordion","expandFirstItem":false,"inactiveOtherItems":true,"iconActiveColor":"var(--ast-global-color-0)","iconColor":"var(--ast-global-color-3)","headingColor":"var(--ast-global-color-2)","answerColor":"var(--ast-global-color-3)","headingFontSize":17,"answerFontSize":15,"questionBgColor":"var(--ast-global-color-5)","borderStyle":"solid","borderWidth":1,"borderColor":"var(--ast-global-color-7)","borderRadius":8,"questionPaddingDesktop":18,"answerPaddingDesktop":18,"rowGap":12,"enableSchemaSupport":true} -->
<div class="wp-block-uagb-faq uagb-block-article-faq"><!-- wp:uagb/faq-child {"block_id":"art-faq-1","question":"{{FAQ_Q1}}","answer":"{{FAQ_A1}}"} -->
<div class="wp-block-uagb-faq-child uagb-block-art-faq-1"><h3 class="uagb-question">{{FAQ_Q1}}</h3><div class="uagb-faq-content"><p>{{FAQ_A1}}</p></div></div>
<!-- /wp:uagb/faq-child -->

<!-- wp:uagb/faq-child {"block_id":"art-faq-2","question":"{{FAQ_Q2}}","answer":"{{FAQ_A2}}"} -->
<div class="wp-block-uagb-faq-child uagb-block-art-faq-2"><h3 class="uagb-question">{{FAQ_Q2}}</h3><div class="uagb-faq-content"><p>{{FAQ_A2}}</p></div></div>
<!-- /wp:uagb/faq-child -->

<!-- wp:uagb/faq-child {"block_id":"art-faq-3","question":"{{FAQ_Q3}}","answer":"{{FAQ_A3}}"} -->
<div class="wp-block-uagb-faq-child uagb-block-art-faq-3"><h3 class="uagb-question">{{FAQ_Q3}}</h3><div class="uagb-faq-content"><p>{{FAQ_A3}}</p></div></div>
<!-- /wp:uagb/faq-child --></div>
<!-- /wp:uagb/faq -->

</div>
<!-- /wp:uagb/container -->
```

## Notes éditoriales

- **Largeur lecture confortable** : 800px max sur desktop (Medium / Substack standard)
- **`uagb/inline-notice`** pour les call-outs (mieux que des blockquotes pour l'attention)
- **`uagb/info-box` avec `borderLeftWidth: 4`** pour les récaps (style « news ticker »)
- **`uagb/table-of-contents` collapsible** pour les longs articles (>1500 mots)
- **`uagb/faq` avec `enableSchemaSupport: true`** pour le schema FAQPage SEO
- **Apostrophes** : utiliser `&apos;` dans les attrs JSON, OK directement dans le rendu HTML

## Variantes

### Variante « Tutoriel pas-à-pas »

Remplacer la section H2 par un `uagb/how-to` avec schema HowTo. Idéal pour articles type « Comment installer X ».

### Variante « Comparatif produits »

Ajouter un tableau comparatif via `core/table` ou plusieurs `uagb/info-box` côte à côte. Idéal pour articles « Top 5 plugins X ».

### Variante « Long-form 5000+ mots »

Ajouter `uagb/container` (sticky sidebar gauche) avec TOC sticky permanent. Voir recette 12 dans `modules/spectra/container-wow-recipes.md`.
