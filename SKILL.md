---
name: astra-spectra
description: Génère des pages WordPress complètes avec Spectra (48 blocs Gutenberg) + Gutenberg core, avec ou sans Astra. Utiliser quand l'utilisateur demande de créer/générer/construire/refondre une page WordPress, une landing page, un site WordPress, ou mentionne Spectra, Astra, Gutenberg blocks, page builder, ou un template de page WP. 3 killer features cumulatives — génération depuis un brief en langage naturel, refonte intelligente d'une page existante, déploiement de templates clic-bouton (page-formation, page-tarifs, page-contact, page-a-propos, blog-editorial, e-commerce-produit, landing-saas, page-agence). Spectra obligatoire au runtime, n'importe quel thème WP accepté, Astra optionnel. Toujours invoquer ce skill avant de générer du markup Gutenberg manuel.
---

# Skill `claude-skill-astra-spectra` — knowledge base v1.0-rc5

> **Ce skill n'est pas un template. C'est une base de connaissance opérationnelle pour générer des pages WordPress correctes du premier coup.** Il liste les 24 pièges Spectra documentés en production, les workflows validés, les conventions de code et les techniques natives à utiliser.

---

## ⚠️ AVERTISSEMENT À L'INSTANCE CLAUDE QUI LIT CE SKILL

Si tu es une instance Claude qui s'apprête à utiliser ce skill, **lis cette section en entier avant de faire QUOI QUE CE SOIT**. Elle contient les règles non-négociables qui empêchent les désastres documentés.

### Le skill assume du goût design + discipline visuelle. Si tu ne les as pas, RESTE STRICT.

Plusieurs sessions ont produit du markup techniquement valide (`pre-flight: STATUS OK`, roundtrip diff:0, 24 quirks verts) mais **visuellement catastrophique** parce que l'instance Claude a inventé des moves design hors baselines, n'a pas screenshot avant de claim, et a livré 5+ sections d'un coup sans validation intermédiaire.

Le user a explicitement noté la différence entre des sessions qui produisent du « niveau débutant » et celles qui tiennent la route : **la différence n'est pas le talent, c'est la discipline.**

### Règles non-négociables (anti-désastre)

#### 🔴 Règle 1 — Gate visuel BLOQUANT avant claim

Tu **ne peux pas** qualifier une composition de **« WOW / impeccable / studio editorial / propre / éditorial / beau / réussi / solide »** dans ta réponse au user **tant que tu n'as pas un screenshot validé à montrer**.

Si tu n'as pas de screenshot (tooling absent, pas d'accès agent-browser / Playwright / chrome-headless, pas de Playground actif), tu DOIS :

1. Qualifier la composition de **« non vérifiée visuellement »** dans ta réponse
2. Donner l'URL frontend au user
3. Demander explicitement : « Peux-tu me montrer un screenshot de la page rendue ? Je n'ai pas pu vérifier visuellement et je ne veux pas claim un succès sans preuve. »

Voir [`workflows/screenshot-options.md`](workflows/screenshot-options.md) pour les 5 options concrètes de capture.

#### 🔴 Règle 2 — Max 3 sections par itération, pas 8

Tu **ne livres pas** 8 sections d'un coup. Tu livres **3 sections max**, screenshot, validation user OU validation visuelle si tooling, **puis** tu ajoutes les sections suivantes.

Pourquoi : si la 1re section a un défaut design (typo trop petite, accent saturé, layout cassé), les 8 suivantes ont le même défaut. Catch tôt = corriger vite.

Exception : pour les **templates committed avec baselines screenshots** (cf `examples/` + `screenshots/`), tu peux livrer le template entier d'un coup car il a déjà été validé.

#### 🔴 Règle 3 — Référencer une baseline, ne pas inventer

Avant de poser une valeur de typographie / spacing / couleur / accent, **vérifie qu'elle est dans `references/design-baselines.md`**. Si la valeur n'y est pas, soit tu utilises la valeur recommandée par défaut du baseline, soit tu demandes au user de valider explicitement la valeur custom.

Pourquoi : « inventer 76px ou 88px en hero » sans repère = roulette russe. Le baseline donne la valeur fiable + le range acceptable.

#### 🔴 Règle 4 — Pas de 2e attempt avant que le user ait vu le 1er

Tu **ne proposes pas** « v2 » / « refonte » / « amélioration » d'une composition tant que le user n'a pas explicitement vu et commenté la v1. Sinon tu accumules du custom non-validé empilé sur du custom non-validé.

#### 🔴 Règle 5 — Pas d'images partagées entre 2 pages du même site

Tu **n'utilises PAS la même image** (même URL, même ID média) dans 2 pages différentes du même site sauf si le user l'a demandé. Réutiliser une image entre `/contact/` et `/a-propos/` cliente = signe de paresse, le user le voit immédiatement.

#### 🔴 Règle 6 — Pas plus de 3 accents couleur dans une même section

Watermark numérique géant `#FF8C00` + accent line orange `#FF8C00` + barre eyebrow `::before` orange `#FF8C00` = **3 accents identiques dans le même bloc** = saturation visuelle + perception « overdose orange » = section ratée.

Règle : 1 accent dominant par section + 1 accent secondaire max + (optionnel) 1 accent tertiaire **différent en hue** (pas la même couleur trois fois). Voir [`references/visual-pitfalls.md`](references/visual-pitfalls.md) pour les patterns à éviter.

### Anti-patterns instance Claude — ❌/✅ liste

À garder en tête tout au long de la session :

- ❌ NE JAMAIS qualifier une page de WOW / impeccable / éditorial sans screenshot
- ❌ NE JAMAIS livrer 5-6 sections d'un coup. Max 3, screenshot, valide, ajoute
- ❌ NE JAMAIS inventer typo/spacing/move design hors des baselines documentées
- ❌ NE JAMAIS proposer un 2e attempt si le user n'a pas vu le 1er
- ❌ NE JAMAIS réutiliser la MÊME image entre 2 pages du même site
- ❌ NE JAMAIS empiler 3+ accents couleur identiques dans la même section (saturation)
- ❌ NE JAMAIS utiliser CSS `::first-letter` (drop cap) sans avoir vérifié qu'il rend correctement (pas garanti dans `_uag_custom_page_level_css`)
- ❌ NE JAMAIS poser font `monospace` isolé sur 1 élément (ex: timestamp) sans cohérence thème — c'est perçu comme un bug
- ❌ NE JAMAIS coller des moves design « créatifs » sans avoir lu `references/visual-pitfalls.md`
- ✅ TOUJOURS commencer par 1 section minimal viable, screenshot, valide, ajoute
- ✅ TOUJOURS demander au user de fournir le screenshot si tooling absent
- ✅ TOUJOURS référencer une baseline documentée plutôt qu'inventer
- ✅ TOUJOURS prefix block_id par version + slug page (ex: `cdc-v1-`, `cdc-v2-`) si tu refais la page après une critique user — évite les conflits CSS scope `body.page-id-{ID}`
- ✅ TOUJOURS lire `references/visual-pitfalls.md` AVANT d'oser un move créatif (watermark, drop cap, asymetric layout, mono fonts, etc.)
- ✅ TOUJOURS lire `references/design-baselines.md` AVANT de poser typo/spacing
- ✅ TOUJOURS lire `references/impeccable-bridge.md` si tu veux composer avec un principe `/impeccable`

### Mode `--strict` (recommandé pour instances sans tooling visuel)

Si l'instance Claude **n'a aucun moyen** de screenshot (pas d'agent-browser, pas de Playwright, pas de chrome-headless, pas de Playground actif), elle DOIT entrer en **mode strict** :

- Ne génère **que des templates committed** dans `templates/` qui ont leurs `screenshots/` validés
- Ne propose **aucun move design custom** (watermark, drop cap, asymetric, etc.)
- Reste **dans les baselines** de `references/design-baselines.md`
- Demande **systématiquement** au user de screenshotter et de valider chaque livrable

Pour passer en mode strict explicite, dis au user : « Je n'ai pas de tooling de capture visuelle. Je vais rester strict sur les templates baseline + tu me valides chaque section par screenshot. OK pour cette approche ? »

---

## Règle absolue avant de générer du markup

**Lire AVANT de toucher un seul caractère de markup `uagb/*`** :

1. [`references/spectra-attributes-quirks.md`](references/spectra-attributes-quirks.md) — les **24 pièges** Spectra qui font échouer du markup techniquement valide
2. [`references/i18n-rules.md`](references/i18n-rules.md) — règles strictes pour le français (HTML entities, espaces insécables typo)
3. [`references/persistent-css-overrides.md`](references/persistent-css-overrides.md) — comment styler durablement via `_uag_custom_page_level_css` (le seul moyen pour que ça survive aux éditions Gutenberg)
4. [`references/design-baselines.md`](references/design-baselines.md) — ⭐ **rulers concrets** typo/spacing par section (anti-improvisation)
5. [`references/visual-pitfalls.md`](references/visual-pitfalls.md) — ⭐ **moves design qui sonnent créatifs mais foirent en pratique**

Sans ces 5 lectures, tu vas reproduire les mêmes erreurs que les versions 0.8.x à 0.9.4 du skill, **plus** les désastres design des sessions reviewer 02/05/2026 sur cours-ndrc.fr (3 pages contact qualifiées « moches, niveau débutant » par le user, supprimées). C'est documenté, c'est connu, c'est évitable.

## Quand activer ce skill

L'utilisateur dit (ou équivalent sémantique) :

- « Crée-moi une page WordPress »
- « Fais-moi une landing pour ma formation / mon SaaS / mon ebook »
- « Génère la page À propos / Contact / Tarifs »
- « Refonds la page /a-propos/ existante »
- « Construis une page produit pour mon ebook à 29€ »
- « Déploie un template de page formation »
- Mention de **Spectra**, **Astra**, **Gutenberg blocks**, **page builder WP**

## Pré-requis bloquants

Vérifier que le site cible a :

| Requis | Comment vérifier | Action si KO |
|---|---|---|
| **Spectra plugin actif** | `GET /wp-json/wp/v2/types` ; ou détection classes `uagb-` dans HTML d'une page | Demander install `ultimate-addons-for-gutenberg` (gratuit) |
| **WordPress 6.0+** | `GET /wp-json/` retourne `wp_version` | Demander upgrade |
| **Application Password** | Test : `curl -u user:pass /wp-json/wp/v2/users/me` | Demander création dans WP admin > Users > son profil |
| **REST API accessible** | Test : `GET /wp-json/wp/v2/pages` (200 ou 401, pas 404) | Vérifier que le site n'a pas désactivé la REST API |

Pré-requis optionnels (active fonctionnalités bonus) :

- **Astra theme actif** → débloque module Customizer (palette, header builder, footer builder)
- **Mu-plugin compagnon `scripts/mu-plugin-skill-test.php` déployé** → débloque endpoints custom (regen-spectra, configure-page, purge-caches) qui rendent le pipeline plus fiable

## Architecture du skill

```
SKILL.md                            ← TU ES ICI (entry point)

references/                         ← LIRE EN PREMIER (knowledge base critique)
├── spectra-attributes-quirks.md    ← 17 pièges Spectra documentés (OBLIGATOIRE)
├── i18n-rules.md                   ← FR : entities + nbsp typo (OBLIGATOIRE FR)
├── persistent-css-overrides.md     ← _uag_custom_page_level_css (la SEULE technique fiable)
├── spectra-icons-list.md           ← whitelist icônes validées + fallback strategy
├── gutenberg-core-blocks.md        ← 30+ blocs core/* curés
├── astra-page-template-rules.md    ← forcer no-title, anti double-H1
├── apache-mutu-pitfalls.md         ← o2switch / OVH mutu : auth strip, LiteSpeed
├── images-ratios.md                ← ratio attendu par pattern (16:9, 16:5, 1:1)
├── spectra-blocks-catalog.md       ← 49 blocs uagb avec attributs critiques
├── intent-to-block-routing.md      ← table de décision intent → bloc
├── section-rhythm.md               ← convention alternance bg sections
├── semantic-color-roles.md         ← 16 rôles sémantiques (slots GARANTIS vs VARIABLES)
├── design-system-tokens.md         ← convention tokens (font-sizes, spacings)
├── block-markup-syntax.md          ← syntaxe Gutenberg comments + pièges parsing
├── mu-plugin-companion.md          ← installer le mu-plugin compagnon
└── spectra-demo-reference.md       ← analyse design system Spectra Natures

patterns/                           ← COMMENT construire (PAS du copier-coller)
├── hero-image-overlay.md           ← hero pleine page image bg + overlay
├── hero-cta-split.md               ← hero 50/50 texte | image
├── stats-bar-editorial.md          ← 4 stats horizontales avec drama
├── features-numbered.md            ← 3 features avec numéros 01/02/03 (anti-piège icônes)
├── features-3-cols.md              ← 3 features classiques avec icônes whitelist
├── about-story-split.md            ← « Notre histoire » image + texte
├── testimonials-cards.md           ← 3 cards avec grands guillemets et avatars
├── testimonials-grid.md            ← grille classique testimonials
├── pricing-3-tiers.md              ← 3 paliers tarifaires
├── faq-accordion.md                ← FAQ accordéon (avec wrapper max-width)
├── cta-banner-fullwidth.md         ← CTA banner image bg + 2 CTAs
├── tabs-section.md                 ← section avec onglets cliquables
├── slider-carousel.md              ← carrousel autoplay + dots
├── timeline-vertical.md            ← timeline chronologique
├── how-to-steps.md                 ← tutoriel pas-à-pas (avec schema HowTo)
├── review-product.md               ← review produit (avec schema Review)
├── countdown-launch.md             ← compte à rebours événement
├── stats-counters.md               ← compteurs animés
├── team-grid.md                    ← grille équipe
└── article-content-rich.md         ← article éditorial mix core+uagb

templates/                          ← BLUEPRINTS de pages complètes (composition de patterns)
├── page-formation.md
├── page-tarifs.md
├── page-contact.md
├── page-a-propos.md
├── blog-editorial.md
├── e-commerce-produit.md
├── landing-saas.md
├── page-agence.md
└── README.md

workflows/                          ← PIPELINES validés
├── new-page-from-brief.md          ← workflow génération from brief (10 étapes)
├── refonte-page-existante.md       ← workflow refonte (snapshot → analyse → reconstruction)
├── visual-validation-loop.md       ← boucle screenshot + audit + retry
└── deploy-template.md              ← workflow déploiement template

scripts/                            ← OUTILS PHP
├── post-page-via-rest.php          ← POST page draft via REST + temp-publish trick
├── update-page-meta-css.php        ← update _uag_custom_page_level_css TAG-AWARE (préserve CSS user)
├── regen-spectra.php               ← force regen assets Spectra (4 stratégies cascadées)
├── validate-block-markup.php       ← roundtrip parse → serialize (anti-crash Gutenberg)
├── visual-audit.php                ← 10 checks intégrés sur le markup
├── resolve-palette.php             ← resolve var(--ast-global-color-X) → hex via palette active
├── apply-design-tokens.php         ← appliquer tokens depuis lib design-tokens
├── snapshot-page.php               ← dump page existante (pour refonte)
├── astra-customizer.php            ← export/apply Astra Customizer settings
├── auto-fix-markup.php             ← auto-fix common markup errors
├── detect-environment.php          ← detect Spectra/Astra/version/hébergeur
├── cleanup-test-pages.php          ← cleanup pages test
└── mu-plugin-skill-test.php        ← mu-plugin compagnon (5 endpoints custom)

modules/                            ← MODULES domaine spécifiques
├── astra/                          ← module Astra (Customizer, palette, typo, header/footer)
└── spectra/                        ← module Spectra (recettes WOW container)

evals/                              ← Suite d'évals
└── evals.json                      ← 10+ prompts test pour mesurer qualité

screenshots/                        ← Baselines visuelles validées
└── loginarmor-dev-palette3/        ← baseline Astra 4.13.1 + Spectra 2.19.25 + palette_3

examples/                           ← EXEMPLES (PAS le skill, des références concrètes)
├── landing-formation-complete-markup.html  ← démo page BTS NDRC produite par le skill
├── landing-formation-complete-page-css.css ← CSS overrides associé
└── README.md
```

## Les 3 killer features

### 1. Génération from brief

```
User : « fais-moi une landing pour ma formation BTS NDRC avec hero, 3 piliers,
        stats, about story, testimonials, FAQ et CTA final »

Skill :
  1. Lire references/spectra-attributes-quirks.md (17 pièges)
  2. Lire references/i18n-rules.md
  3. Lire patterns nécessaires : hero-image-overlay, features-numbered,
     stats-bar-editorial, about-story-split, testimonials-cards, faq-accordion,
     cta-banner-fullwidth
  4. Demander à l'utilisateur les variables (titre, sous-titre, images, contenus, etc.)
  5. Générer markup Gutenberg en composant les patterns + utiliser entities FR
  6. Générer CSS overrides pour _uag_custom_page_level_css
  7. POST page draft via scripts/post-page-via-rest.php
  8. Update meta CSS via scripts/update-page-meta-css.php (tag-aware)
  9. Force regen Spectra via scripts/regen-spectra.php
  10. Configure Astra page template via mu-plugin (no-title)
  11. Validation visuelle via workflows/visual-validation-loop.md
```

Workflow détaillé : [`workflows/new-page-from-brief.md`](workflows/new-page-from-brief.md)

### 2. Refonte intelligente

```
User : « modernise ma page /a-propos/ »

Skill :
  1. Snapshot la page existante via scripts/snapshot-page.php
  2. Analyser content + structure (titres, sections, contenu rédactionnel)
  3. Mapper vers patterns équivalents
  4. Reconstruire en respectant le contenu original
  5. POST en draft (jamais écraser le live sans confirmation)
  6. Validation visuelle
  7. Diff content original vs reconstruction → confirmer avec utilisateur
```

Workflow : [`workflows/refonte-page-existante.md`](workflows/refonte-page-existante.md)

### 3. Templates blueprints

8 templates clic-bouton documentés dans `templates/`. Chaque template = une **composition de patterns** + variables d'entrée + CSS overrides + schema SEO + tests.

## Workflow type pour générer une page

```
ÉTAPE 1 — Détecter l'environnement
  scripts/detect-environment.php
    → Spectra version, Astra version, palette active, thème, hébergeur, mu-plugin présent

ÉTAPE 2 — Lire la knowledge base critique
  references/spectra-attributes-quirks.md  (les 17 pièges)
  references/i18n-rules.md                 (FR si applicable)
  references/persistent-css-overrides.md   (technique de styling)

ÉTAPE 3 — Choisir les patterns selon le brief
  Brief utilisateur → mapper vers patterns dans patterns/
  Lire chaque pattern utilisé pour comprendre sa structure + ses pièges

ÉTAPE 4 — Récupérer les variables utilisateur
  Pour chaque pattern, demander les variables manquantes
  Vérifier ratios images attendus via references/images-ratios.md

ÉTAPE 5 — Générer le markup
  Composer en assemblant les patterns
  Utiliser HTML entities pour le français (cf i18n-rules)
  Appliquer conventions block_id : {slug}-{section}-{element}
  Wrapper info-box dans containers pour width responsive (cf quirk #2)
  Utiliser numéros 01/02/03 si features (anti-piège #8 icônes)

ÉTAPE 6 — Valider le markup
  scripts/validate-block-markup.php
    → roundtrip parse → serialize : doit être 0 char diff
    → block_id uniques
    → no missing required attrs

ÉTAPE 7 — Générer le CSS overrides
  Cibler les .uagb-block-{slug}-* avec font-size !important
  Inclure media queries responsive (1024 / 600 px)
  Encapsuler entre balises /* === skill-generated v1.0 START === */ et END

ÉTAPE 8 — POST + meta + regen
  scripts/post-page-via-rest.php  (POST page + Astra meta config)
  scripts/update-page-meta-css.php (tag-aware update _uag_custom_page_level_css)
  scripts/regen-spectra.php       (force régénération assets)

ÉTAPE 9 — Validation visuelle
  workflows/visual-validation-loop.md
    → screenshot agent-browser
    → audit visuel (3-cols vs stack, accents OK, no mojibake, etc.)
    → si défaut : itérer markup → re-publish → re-screenshot
    → max 3 itérations

ÉTAPE 10 — Livrer à l'utilisateur
  URL frontend de la page
  URL admin Gutenberg pour édition
  Screenshots des sections
  Note sur quoi peut être modifié et où (Spectra Page Level CSS dans admin)
```

## Conventions strictes

### Conventions de naming

| Type | Convention | Exemple |
|---|---|---|
| Slug page | kebab-case | `page-tarifs`, `formation-bts-ndrc` |
| block_id | `{slug-page}-{section}-{element}` | `formation-bts-ndrc-hero-text`, `formation-bts-ndrc-stat-1` |
| Classes CSS générées Spectra | `.uagb-block-{block_id}` | `.uagb-block-formation-bts-ndrc-hero-text` |
| Variables CSS Astra | `var(--ast-global-color-X)` UNIQUEMENT pour slots GARANTIS (0,1,2,3,5) | — |
| Hex direct | Pour rôles VARIABLES (#fafafa, #ffffff, #e5e7eb) | — |

### Conventions de style

- **Padding sections root** : 96-160px desktop / 72-120px tablet / 56-96px mobile
- **Padding cards** : 40-56px desktop / 32-40px tablet / 24-32px mobile
- **Border-radius cards** : 12-24px
- **Box-shadow cards** : `rgba(15,23,42,0.06-0.10) 0 4-8px 24-40px`
- **Font-weight headings** : 700-800
- **Letter-spacing headings** : -0.5px à -2px (typo display)
- **Line-height headings** : 1.05-1.2
- **Eyebrow** : 13-15px, font-weight 800, letter-spacing 3-4px, uppercase, color accent

### Conventions de contenu

- **Headlines** : 5-12 mots max
- **Subheadlines** : 1-2 phrases courtes
- **Eyebrows** : 2-5 mots uppercase
- **Card descs** : 2-4 phrases
- **CTA labels** : 2-4 mots impératifs

## Pour les sites en production

1. **Toujours en draft d'abord**, jamais publish direct
2. **Backup avant** opération bulk (>3 pages)
3. **Tester sur 1 page** avant de déployer un template sur 10
4. **Vérifier hébergeur** : si Apache mutualisé, lire `references/apache-mutu-pitfalls.md`
5. **Avertir** si user a Astra Pro Custom Layouts ou plugins de cache agressifs

## Versioning et migrations

Le skill utilise des balises `/* === skill-generated v1.X.X START/END === */` dans le `_uag_custom_page_level_css`. La session courante :

- Détecte la version via regex
- Remplace SEULEMENT la section skill-generated (préserve CSS user)
- Met à jour la version dans les balises

Cf `scripts/update-page-meta-css.php` pour la logique tag-aware.

## Que faire si un test échoue

| Symptôme | Référence | Fix |
|---|---|---|
| Stats empilées vertical au lieu de 4-cols | quirks #2 | Wrapper info-box dans containers width 22% |
| FAQ avec Lorem Ipsum | quirks #3 | Attribut `answer` (PAS `description`) |
| 3 cards avec même icône | quirks #8 + spectra-icons-list | Numéros 01/02/03 ou icônes whitelist |
| Mojibake `â€` | i18n-rules | HTML entities, pas UTF-8 direct |
| Stats vert/bleu sur palette_3 | semantic-color-roles | Slots GARANTIS uniquement, hex direct sinon |
| Inline styles disparus après save Gutenberg | persistent-css-overrides | Mettre dans meta `_uag_custom_page_level_css` |
| Double H1 (post_title + hero H1) | astra-page-template-rules | Forcer no-title via post_meta |
| 401 REST API o2switch | apache-mutu-pitfalls | RewriteRule HTTP_AUTHORIZATION dans .htaccess |
| Image story portrait recadrée | images-ratios | Image landscape 16:5 (1200×400) |
| Hero overlay illisible | patterns/hero-image-overlay | overlayOpacity 0.65 max |

## Si tu hésites

1. **Lire le pattern** correspondant dans `patterns/`
2. **Lire les quirks** dans `references/spectra-attributes-quirks.md`
3. **Tester sur un site dev** (Local by Flywheel, WP Playground) avant prod
4. **Toujours screenshot** avant de claim que ça marche

> **Une page non screenshootée = une page non testée.** Le mainteneur a perdu 4 versions en livrant du markup techniquement valide mais visuellement raté. Ne reproduis pas ça.

## Versions du skill

- **v1.0** (actuelle) : knowledge base complète + 20 patterns + 8 templates + 17 pièges documentés
- v0.9.4 : CSS persistants via meta natif Spectra
- v0.9.3 : refonte WOW + workaround inline styles
- v0.9.2 : accents français HTML entities
- v0.9.1 : temp-publish trick + WCAG walker
- v0.9.0 : refonte couleur structurelle
- v0.8.x : refactoring divers, multiple échecs visuels
- v0.5.0 : alpha squelette
