# Pattern : FAQ Accordéon

> **Use case** : Section FAQ avec accordéon expand/collapse. Schema `FAQPage` JSON-LD généré automatiquement par Spectra (gain SEO direct). 5-10 questions recommandé par section.

## Variables d'entrée

| Variable | Description |
|----------|-------------|
| `{{SECTION_HEADLINE}}` | Titre section (ex: « Questions fréquentes ») |
| `{{Q1}}` à `{{QN}}` | Questions |
| `{{A1}}` à `{{AN}}` | Réponses (peuvent contenir HTML simple : `<a>`, `<strong>`, `<em>`) |

## Block markup (5 questions exemple)

```html
<!-- wp:uagb/container {"block_id":"faq-section","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":900,"directionDesktop":"column","alignItemsDesktop":"stretch","rowGapDesktop":40,"topPaddingDesktop":80,"bottomPaddingDesktop":80,"topPaddingTablet":60,"bottomPaddingTablet":60,"topPaddingMobile":48,"bottomPaddingMobile":48,"leftPaddingTablet":24,"rightPaddingTablet":24,"leftPaddingMobile":16,"rightPaddingMobile":16,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-faq-section"><!-- wp:uagb/advanced-heading {"block_id":"faq-heading","headingTag":"h2","headingTitle":"{{SECTION_HEADLINE}}","headingColor":"var(--ast-global-color-2)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":36} -->
<div class="wp-block-uagb-advanced-heading uagb-block-faq-heading"><h2 class="uagb-heading-text">{{SECTION_HEADLINE}}</h2></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/faq {"block_id":"faq-main","headingAlign":"left","equalHeight":false,"layout":"accordion","inactiveOtherItems":true,"expandFirstItem":true,"iconAlign":"right","iconActiveColor":"var(--ast-global-color-0)","iconColor":"var(--ast-global-color-3)","headingColor":"var(--ast-global-color-2)","answerColor":"var(--ast-global-color-3)","headingFontSize":18,"answerFontSize":16,"headingFontWeight":600,"questionBgColor":"var(--ast-global-color-5)","questionActiveBgColor":"var(--ast-global-color-5)","borderStyle":"solid","borderWidth":1,"borderColor":"#e5e7eb","borderRadius":8,"questionPaddingDesktop":20,"answerPaddingDesktop":20,"rowGap":16,"loadGoogleFonts":false,"enableSchemaSupport":true} -->
<div class="wp-block-uagb-faq uagb-block-faq-main"><!-- wp:uagb/faq-child {"block_id":"faq-q1","question":"{{Q1}}","answer":"{{A1}}"} -->
<div class="wp-block-uagb-faq-child uagb-block-faq-q1"><h3 class="uagb-question">{{Q1}}</h3><div class="uagb-faq-content"><p>{{A1}}</p></div></div>
<!-- /wp:uagb/faq-child -->

<!-- wp:uagb/faq-child {"block_id":"faq-q2","question":"{{Q2}}","answer":"{{A2}}"} -->
<div class="wp-block-uagb-faq-child uagb-block-faq-q2"><h3 class="uagb-question">{{Q2}}</h3><div class="uagb-faq-content"><p>{{A2}}</p></div></div>
<!-- /wp:uagb/faq-child -->

<!-- wp:uagb/faq-child {"block_id":"faq-q3","question":"{{Q3}}","answer":"{{A3}}"} -->
<div class="wp-block-uagb-faq-child uagb-block-faq-q3"><h3 class="uagb-question">{{Q3}}</h3><div class="uagb-faq-content"><p>{{A3}}</p></div></div>
<!-- /wp:uagb/faq-child -->

<!-- wp:uagb/faq-child {"block_id":"faq-q4","question":"{{Q4}}","answer":"{{A4}}"} -->
<div class="wp-block-uagb-faq-child uagb-block-faq-q4"><h3 class="uagb-question">{{Q4}}</h3><div class="uagb-faq-content"><p>{{A4}}</p></div></div>
<!-- /wp:uagb/faq-child -->

<!-- wp:uagb/faq-child {"block_id":"faq-q5","question":"{{Q5}}","answer":"{{A5}}"} -->
<div class="wp-block-uagb-faq-child uagb-block-faq-q5"><h3 class="uagb-question">{{Q5}}</h3><div class="uagb-faq-content"><p>{{A5}}</p></div></div>
<!-- /wp:uagb/faq-child --></div>
<!-- /wp:uagb/faq --></div>
<!-- /wp:uagb/container -->
```

## Notes importantes

- **`enableSchemaSupport: true`** active le JSON-LD `FAQPage` automatiquement (gain SEO direct, rich snippets dans Google)
- **`expandFirstItem: true`** ouvre la première question par défaut (UX recommandée selon les études)
- **`inactiveOtherItems: true`** ferme les autres quand on en ouvre une (mode classique vs « tout ouvert »)
- **Largeur 900px max** sur desktop (lecture confortable, pas pleine largeur)
- **Apostrophes typographiques** : utiliser `&apos;` ou `&#8217;` dans les questions/réponses pour éviter de casser le JSON

## Variantes

### Variante 1 — Layout grid 2 colonnes

Remplacer `layout: "accordion"` par `layout: "grid"` et ajouter `columns: 2`. Mieux pour 6+ questions courtes.

### Variante 2 — Mode dark

Container parent : `backgroundColor: var(--ast-global-color-2)`.
Faq : `questionBgColor: var(--ast-global-color-6)`, `headingColor: var(--ast-global-color-4)`, `answerColor: var(--ast-global-color-7)`.

### Variante 3 — Avec icônes catégories

Ajouter avant chaque `faq-child` un emoji ou icône en début de question (ex: « 💰 Quels sont vos tarifs ? »). Pas de modification structurelle, juste enrichissement texte.

## Block IDs

- `faq-section`, `faq-heading`, `faq-main`
- `faq-q1`, `faq-q2`, ..., `faq-qN`

## Bonnes pratiques rédactionnelles

- Question = comme l'utilisateur la pose (avec « ? » à la fin)
- Réponse = directe et complète (>30 mots, <200 mots idéalement pour le rich snippet)
- Pas de marketing speak dans les réponses (Google pénalise les FAQ « commerciales »)
- Couvrir : prix, durée, garanties, support, prérequis, modalités d'inscription, livrables
- Au moins 5 questions pour être éligible aux rich snippets

## Compatibilité schema

Le `FAQPage` schema généré est conforme aux specs Google (vérifié 02/05/2026). Pour validation : utiliser https://search.google.com/test/rich-results sur l'URL après publication.
