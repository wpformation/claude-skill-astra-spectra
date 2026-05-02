# Template : Page Formation

> **Use case** : Page de vente d'une formation en ligne. Structure éprouvée pour conversion : hero promesse + bénéfices + programme + témoignages + tarif + FAQ + CTA inscription.

## Variables à fournir

| Variable | Description | Exemple |
|----------|-------------|---------|
| `{{FORMATION_NAME}}` | Nom de la formation | "WordPress Mastery" |
| `{{PROMISE}}` | Promesse principale (1 phrase) | "Maîtrise WordPress comme un pro en 8 semaines" |
| `{{SUBPROMISE}}` | Argument de soutien | "Formation 100 % en ligne, accessible à vie, certifiante" |
| `{{CTA_LABEL}}` | Texte CTA principal | "Je m'inscris à la formation" |
| `{{CTA_URL}}` | URL d'inscription | `/inscription-formation/` |
| `{{HERO_IMAGE}}` | Image hero (illustration ou photo formateur) | URL |
| `{{B1_TITLE}}`–`{{B4_TITLE}}` | 4 bénéfices clés | "Apprends à ton rythme" |
| `{{B1_DESC}}`–`{{B4_DESC}}` | 4 descriptions courtes | "Modules vidéo accessibles 24/7" |
| `{{MODULE_1}}`–`{{MODULE_6}}` | 6 modules / chapitres | "Module 1 : Installation et configuration" |
| `{{PRICE}}` | Prix | "297 €" |
| `{{PRICE_DETAIL}}` | Modalité | "Paiement unique, accès à vie" |
| `{{T1}}`–`{{T3}}` | 3 témoignages (name, company, desc, photo) | — |
| `{{Q1}}`–`{{Q8}}` | 8 questions FAQ + réponses | — |
| `{{TRAINER_NAME}}` | Nom formateur | "Fabrice Ducarme" |
| `{{TRAINER_PHOTO}}` | Photo formateur | URL |
| `{{TRAINER_BIO}}` | Bio courte | "15 ans WordPress, 8 plugins WP.org, 2.1M+ téléchargements" |

## Structure de la page

```
1. Hero CTA Split
   - Headline : {{PROMISE}}
   - Subline : {{SUBPROMISE}}
   - CTA primary : {{CTA_LABEL}} → {{CTA_URL}}
   - CTA secondary : "Voir le programme" → #programme
   - Image : {{HERO_IMAGE}}

2. Stats Counters (preuve sociale)
   - 1500+ apprenants
   - 12 ans d'expertise
   - 98% taux de satisfaction
   - 50+ plugins créés

3. Features 4-Cols (4 bénéfices clés)
   - {{B1_TITLE}} / {{B1_DESC}}
   - {{B2_TITLE}} / {{B2_DESC}}
   - {{B3_TITLE}} / {{B3_DESC}}
   - {{B4_TITLE}} / {{B4_DESC}}

4. Container split image-text (présentation formateur)
   - Photo + bio formateur
   - 3-5 lignes d'argument autorité

5. Section "Programme" (timeline ou liste numérotée des 6 modules)
   - {{MODULE_1}} à {{MODULE_6}}

6. Pricing one-shot (un seul prix)
   - {{PRICE}}
   - {{PRICE_DETAIL}}
   - Liste de 5-7 features incluses
   - CTA primaire grand

7. Testimonials Grid (3 témoignages)
   - {{T1}}, {{T2}}, {{T3}}

8. FAQ (8 questions) + schema FAQPage
   - Q1-Q8 : prix, durée, accès, garantie, certifs, support, modalités, public

9. CTA Banner Final
   - Headline : "Prêt à transformer ta carrière WordPress ?"
   - CTA primary : {{CTA_LABEL}}
```

## Palette suggérée

**Si formation tech / pro** : preset_1 (Bleu corporate) ou wpf-corporate.
**Si formation créative / indie** : preset_8 (Orange) ou wpf-orange (signature WPFormation).
**Si formation business** : preset_2 (Violet) ou wpf-creative.
**Si formation luxe / haut de gamme** : preset_5 (Doré).

## Block markup (squelette assemblé)

> **Note** : pour économiser l'espace, je liste ici les patterns à concaténer. Le skill assemble dynamiquement à partir des fichiers `patterns/`.

```
[hero-cta-split.md] avec variables remplies
[stats-counters.md] avec 4 stats formation
[features-3-cols.md] adapté en 4 cols (variante 1)
[trainer-bio-split-section] (bloc custom : uagb/container split avec photo + bio)
[program-timeline] (uagb/timeline 6 items)
[pricing-one-shot] (uagb/container + uagb/info-box pricing card large)
[testimonials-grid.md]
[faq-accordion.md] avec 8 questions formation
[cta-banner-fullwidth.md] adapté formation
```

## Block markup détaillé (sections custom du template)

### Section "Pricing one-shot" (single tier centered)

```html
<!-- wp:uagb/container {"block_id":"pricing-formation","variationSelected":true,"contentWidth":"alignwide","innerContentCustomWidthDesktop":600,"directionDesktop":"column","alignItemsDesktop":"center","topPaddingDesktop":80,"bottomPaddingDesktop":80,"backgroundType":"color","backgroundColor":"var(--ast-global-color-5)"} -->
<div class="wp-block-uagb-container alignwide uagb-block-pricing-formation"><!-- wp:uagb/advanced-heading {"block_id":"pricing-form-heading","headingTag":"h2","headingTitle":"Tarif","headingDesc":"Une formation, un prix juste, accès à vie","headingColor":"var(--ast-global-color-2)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontWeight":700,"headingFontSizeDesktop":36} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-form-heading"><h2 class="uagb-heading-text">Tarif</h2><p class="uagb-desc-text">Une formation, un prix juste, accès à vie</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/container {"block_id":"pricing-card-form","variationSelected":true,"contentWidth":"boxed","directionDesktop":"column","alignItemsDesktop":"center","rowGapDesktop":24,"topPaddingDesktop":48,"bottomPaddingDesktop":48,"leftPaddingDesktop":48,"rightPaddingDesktop":48,"backgroundType":"color","backgroundColor":"var(--ast-global-color-4)","borderStyle":"solid","borderTopWidth":3,"borderRightWidth":3,"borderBottomWidth":3,"borderLeftWidth":3,"borderColor":"var(--ast-global-color-0)","borderRadiusTopLeft":20,"borderRadiusTopRight":20,"borderRadiusBottomLeft":20,"borderRadiusBottomRight":20,"boxShadowColor":"rgba(255,140,0,0.18)","boxShadowVOffset":12,"boxShadowBlur":40} -->
<div class="wp-block-uagb-container uagb-block-pricing-card-form"><!-- wp:uagb/advanced-heading {"block_id":"pricing-form-price","headingTag":"div","headingTitle":"{{PRICE}}","headingDesc":"{{PRICE_DETAIL}}","headingColor":"var(--ast-global-color-0)","subHeadingColor":"var(--ast-global-color-3)","headingAlign":"center","headingFontWeight":800,"headingFontSizeDesktop":72,"subHeadingFontSizeDesktop":16} -->
<div class="wp-block-uagb-advanced-heading uagb-block-pricing-form-price"><div class="uagb-heading-text">{{PRICE}}</div><p class="uagb-desc-text">{{PRICE_DETAIL}}</p></div>
<!-- /wp:uagb/advanced-heading -->

<!-- wp:uagb/icon-list {"block_id":"pricing-form-features","gap":12,"icon_layout":"horizontal","label_color":"var(--ast-global-color-2)","icon_color":"var(--ast-global-color-0)"} -->
<div class="wp-block-uagb-icon-list uagb-block-pricing-form-features"><!-- wp:uagb/icon-list-child {"block_id":"feat-form-1","label":"6 modules vidéo (15h+)","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-feat-form-1"><span class="uagb-icon-list-source"><svg></svg></span><span class="uagb-icon-list-label">6 modules vidéo (15h+)</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"feat-form-2","label":"Exercices pratiques après chaque chapitre","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-feat-form-2"><span class="uagb-icon-list-source"><svg></svg></span><span class="uagb-icon-list-label">Exercices pratiques après chaque chapitre</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"feat-form-3","label":"Communauté Discord privée","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-feat-form-3"><span class="uagb-icon-list-source"><svg></svg></span><span class="uagb-icon-list-label">Communauté Discord privée</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"feat-form-4","label":"Certificat de complétion","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-feat-form-4"><span class="uagb-icon-list-source"><svg></svg></span><span class="uagb-icon-list-label">Certificat de complétion</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"feat-form-5","label":"Accès à vie aux mises à jour","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-feat-form-5"><span class="uagb-icon-list-source"><svg></svg></span><span class="uagb-icon-list-label">Accès à vie aux mises à jour</span></div>
<!-- /wp:uagb/icon-list-child -->

<!-- wp:uagb/icon-list-child {"block_id":"feat-form-6","label":"Support email + Discord","icon":"check","link":""} -->
<div class="wp-block-uagb-icon-list-child uagb-block-feat-form-6"><span class="uagb-icon-list-source"><svg></svg></span><span class="uagb-icon-list-label">Support email + Discord</span></div>
<!-- /wp:uagb/icon-list-child --></div>
<!-- /wp:uagb/icon-list -->

<!-- wp:uagb/buttons {"block_id":"pricing-form-cta-wrap","align":"center"} -->
<div class="wp-block-uagb-buttons uagb-block-pricing-form-cta-wrap"><!-- wp:uagb/buttons-child {"block_id":"pricing-form-cta","label":"{{CTA_LABEL}}","link":"{{CTA_URL}}","backgroundColor":"var(--ast-global-color-0)","color":"var(--ast-global-color-4)","hoverBackgroundColor":"var(--ast-global-color-1)","hoverColor":"var(--ast-global-color-4)","borderRadius":8,"sizeType":"px","size":18,"paddingTop":18,"paddingBottom":18,"paddingLeft":40,"paddingRight":40,"fontWeight":700,"boxShadowColor":"rgba(0,0,0,0.15)","boxShadowVOffset":8,"boxShadowBlur":24} -->
<div class="wp-block-uagb-buttons-child uagb-block-pricing-form-cta"><a class="uagb-buttons-repeater wp-block-uagb-buttons-child__link" href="{{CTA_URL}}"><span class="uagb-button-text">{{CTA_LABEL}}</span></a></div>
<!-- /wp:uagb/buttons-child --></div>
<!-- /wp:uagb/buttons --></div>
<!-- /wp:uagb/container --></div>
<!-- /wp:uagb/container -->
```

## Bonnes pratiques copywriting (formation)

- **Hero promesse** : 1 transformation concrète + 1 timeline (« Maîtrise WP en 8 semaines »)
- **Bénéfices** : centrés sur l'apprenant (« Apprends à ton rythme » > « Vidéos disponibles 24/7 »)
- **Témoignages** : profils similaires au public cible (freelance pour freelance, agence pour agence)
- **Pricing** : un seul prix one-shot ou paiement en 2-3 fois (éviter abonnement pour formation)
- **Garantie** : « Satisfait ou remboursé sous 14 jours » (lever objection finale)
- **FAQ** : couvrir prix, accès, durée, garantie, certifications, support, prérequis, modalités

## Compatibilité

- **Spectra** ≥ 2.10
- **Astra** : optionnel mais recommandé (tient au pilotage palette + header builder)
- **Schema SEO** : Course (manuel à ajouter via Yoast ou JSON-LD plugin) + FAQPage (auto via uagb/faq) + Review (manuel ou via uagb/review optionnel)

## Pour aller plus loin

- Workflow déploiement : `../workflows/deploy-template.md`
- Patterns inclus : `hero-cta-split.md`, `features-3-cols.md`, `stats-counters.md`, `testimonials-grid.md`, `faq-accordion.md`, `cta-banner-fullwidth.md`
- Recettes wow uagb/container : `../modules/spectra/container-wow-recipes.md`
