---
name: astra-spectra
description: Génère des pages WordPress complètes avec Spectra (48 blocs Gutenberg) + Gutenberg core, avec ou sans Astra. Utiliser quand l'utilisateur demande de créer/générer/construire/refondre une page WordPress, une landing page, un site WordPress, ou mentionne Spectra, Astra, Gutenberg blocks, page builder, ou un template de page WP. 3 killer features cumulatives — génération depuis un brief en langage naturel, refonte intelligente d'une page existante, déploiement de templates clic-bouton (page-formation, landing-saas, page-agence). Spectra obligatoire au runtime, n'importe quel thème WP accepté, Astra optionnel. Toujours invoquer ce skill avant de générer du markup Gutenberg manuel.
---

# Skill `claude-skill-astra-spectra`

Skill Claude Code de génération de pages WordPress avec **Spectra (48 blocs Gutenberg)** + **Gutenberg core**, avec **Astra theme** en bonus si présent.

## Promesse

Tu décris ce que tu veux en langage naturel, le skill génère le markup Gutenberg complet et coherent design-wise, le POST sur ton site WordPress en draft, et te donne l'URL d'édition. Tu ouvres Gutenberg, tu vérifies, tu publies.

## 3 killer features

1. **Génération depuis un brief** (`workflows/new-page-from-brief.md`) — « fais-moi une landing page formation avec hero, 3 features, pricing 3 tiers, FAQ et CTA YouTube » → page draft Spectra+core complète en moins de 2 minutes
2. **Refonte intelligente** (`workflows/refonte-page-existante.md`) — « modernise /a-propos/ » → snapshot, analyse, reconstruction Spectra cohérente
3. **Templates clic-bouton** (`workflows/deploy-template.md`) — 3 templates v0.8 (page-formation, landing-saas, page-agence). 5 templates supplémentaires (blog-editorial, e-commerce-produit, page-tarifs, page-contact, page-a-propos) prévus en v1.1

## Utilisation rapide

L'utilisateur fournit :
1. **URL du site WordPress cible** (ex: `https://monsite.com`)
2. **Application Password** (généré dans WP admin > Users > ton profil > Application Passwords)
3. **Demande en langage naturel** ou template à déployer

Le skill exécute :

```
1. detect-environment    → check Spectra ✓, Astra ?, thème actif, WP version
2. intent-routing        → mappe la demande vers patterns + blocs concrets
3. assemble-markup       → compose le block markup Gutenberg (uagb/* + core/*)
4. validate-markup       → roundtrip parse_blocks → serialize_blocks (must be 0 char diff)
5. POST page draft       → /wp-json/wp/v2/pages
6. (optionnel) screenshot validation via /screenshot-loop
7. (optionnel) audit via /impeccable
```

## Pré-requis bloquants

- **Spectra plugin** activé sur le site cible (slug WP.org : `ultimate-addons-for-gutenberg`). Sans Spectra, le skill s'arrête avec un message clair et un lien d'install
- **WordPress 6.0+** minimum (block editor v2)
- **PHP 7.4+** minimum
- **Application Password** valide (WP admin > Users > ton profil > Application Passwords)
- **REST API accessible** (`/wp-json/wp/v2/pages` → 200 ou 401, pas 404)

## Pré-requis optionnels (active des fonctionnalités bonus)

- **Astra theme** activé → débloque le module Customizer (palette, typo, header builder, footer builder)
- **Astra Pro plugin** → débloque les options avancées Astra (header transparent, mega menu, white label)
- **Skill `/screenshot-loop`** → validation visuelle automatique post-génération
- **Skill `/impeccable`** → audit/polish design post-génération

## Routing principal — quel workflow choisir

| Demande utilisateur | Workflow à invoquer |
|---------------------|---------------------|
| « crée-moi une page X », « fais-moi une landing Y », « génère une page sur Z » | `workflows/new-page-from-brief.md` |
| « modernise /url/ », « refonds /a-propos/ », « refresh la page X » | `workflows/refonte-page-existante.md` |
| « déploie le template SaaS », « installe le template formation » | `workflows/deploy-template.md` |
| Validation visuelle après génération | `workflows/visual-validation-loop.md` |

## ⭐ `uagb/container` = bloc fondation pour les effets WOW

**Toute section nouvelle est wrappée dans un `uagb/container`** — jamais `core/group`, jamais `core/columns`, jamais `core/cover` quand on veut un effet design. C'est le bloc le plus puissant et le plus utilisé du skill, le vrai différenciateur Spectra vs Gutenberg core. Il offre :

- Backgrounds avancés (color, gradient, image avec parallax/fixed/scale, video)
- Mise en page flex/grid responsive (1-6 cols, direction desktop ≠ tablet ≠ mobile)
- Box shadow + hover state, border radius par coin, dividers haut/bas (curve, wave, tilt)
- Backdrop-filter blur (glassmorphism), animations au scroll (fade/slide/zoom)
- Min height (vh, %, em), padding/margin responsive sur 3 breakpoints

→ **12 recettes WOW prêtes à l'emploi** dans [`modules/spectra/container-wow-recipes.md`](modules/spectra/container-wow-recipes.md) : hero pleine page avec parallax, glassmorphism cards, gradient mesh, dividers diagonaux, background video, sticky sidebar, etc. Tous les patterns du skill exploitent ces recettes.

## Décision intent → bloc

Le skill route chaque intention vers le bloc adapté via la table de décision dans `references/intent-to-block-routing.md` (40+ entrées). Règles principales :

- **Spectra prioritaire** quand le bloc apporte un gain visuel/UX significatif (info-box, testimonial, FAQ, how-to, review, countdown, modal, tabs, slider) ou embarque du schema SEO (FAQ → FAQPage, how-to → HowTo)
- **Gutenberg core prioritaire** pour les blocs atomiques simples (paragraph, heading H3+, image isolée, embed YouTube/Twitter, quote, code, separator, spacer)
- **Toujours wrapper** les compositions complexes dans un `uagb/container` pour la cohérence layout/responsive

## Cohérence design system

**Règle critique** : tous les patterns Spectra du skill utilisent `var(--ast-global-color-X)` dans leurs attrs `backgroundColor` / `textColor` / `iconColor` / `headingColor` / `subHeadingColor` / `borderColor` (référence : `references/design-system-tokens.md`).

Cela permet la propagation automatique au changement de palette Astra (validée au POC du 02/05/2026 : 199 occurrences `var(--ast-global-color)` héritées sans intervention).

**Hors-Astra** : si le site cible n'a pas Astra activé, le skill injecte automatiquement un `wpf-design-tokens.css` qui mappe `--ast-global-color-X` vers les couleurs choisies par l'utilisateur (5-6 tokens via une palette pré-construite ou custom).

## Structure du skill (v0.8.1 — état réel du repo)

```
astra-spectra/
├── SKILL.md                              # ← ce fichier (routing principal)
├── README.md                             # pitch GitHub public
├── INSTALL.md                            # setup pas-à-pas
├── LICENSE                               # MIT
├── CHANGELOG.md                          # historique versions
├── modules/
│   ├── spectra/
│   │   └── container-wow-recipes.md      # ⭐ GOLD : 12 recettes WOW uagb/container
│   └── astra/
│       └── customizer-map.md             # ⭐ pilotage exhaustif astra-settings
├── references/
│   ├── intent-to-block-routing.md        # ⭐ table de décision 45 entrées
│   ├── spectra-blocks-catalog.md         # 48 blocs uagb/* documentés
│   ├── block-markup-syntax.md            # syntaxe Gutenberg comments + 8 règles
│   └── design-system-tokens.md           # palette Astra ↔ blocs
├── patterns/                             # 9 patterns hybrides v0.8 (15+ cible v1.1)
│   ├── hero-cta-split.md
│   ├── features-3-cols.md
│   ├── pricing-3-tiers.md
│   ├── faq-accordion.md
│   ├── cta-banner-fullwidth.md
│   ├── testimonials-grid.md
│   ├── team-grid.md
│   ├── stats-counters.md
│   └── article-content-rich.md
├── templates/                            # 3 templates v0.8 (8 cible v1.1)
│   ├── page-formation.md
│   ├── landing-saas.md
│   └── page-agence.md
├── workflows/                            # ⭐ killer features + helpers
│   ├── new-page-from-brief.md            # killer feature 1
│   ├── refonte-page-existante.md         # killer feature 2
│   ├── deploy-template.md                # killer feature 3
│   └── visual-validation-loop.md         # boucle audit + retries (max 3)
├── scripts/
│   ├── detect-environment.php            # profil site (Spectra/Astra/thème/palette)
│   ├── apply-design-tokens.php           # inject palette Astra ou fallback CSS
│   ├── astra-customizer.php              # export/apply astra-settings (préserve toutes les keys non touchées)
│   ├── validate-block-markup.php         # roundtrip parse/serialize (normalise --)
│   ├── visual-audit.php                  # 12 checks intégrés P0-P3
│   ├── auto-fix-markup.php               # corrections auto block_id/hex/H1
│   ├── snapshot-page.php                 # dump page existante (refonte)
│   └── post-page-via-rest.php            # POST automatique vers WP REST API
├── evals/
│   ├── evals.json                        # 10 évals canoniques
│   ├── run-evals.php                     # runner CLI
│   ├── README.md
│   └── fixtures/
│       ├── malformed-markup.html
│       └── astra-palette-orange.json
└── lead-magnet/
    ├── pdf-source.md                     # source PDF 32 pages
    └── README.md                         # workflow Pandoc/Typst
```

### Promesses v1.0 (à venir)

- 6+ patterns supplémentaires : `tabs-section`, `slider-carousel`, `timeline-vertical`, `how-to-steps`, `review-product`, `countdown-launch`, `contact-form-split`, `404-page`
- 5 templates supplémentaires : `blog-editorial`, `e-commerce-produit`, `page-tarifs`, `page-contact`, `page-a-propos`
- Workflow `new-site-from-scratch.md` (multi-pages depuis 1 brief global)
- `references/spectra-icons-list.md` : liste exhaustive des noms courts d'icônes Spectra utilisables
- `references/gutenberg-core-blocks.md` : doc curée des 30+ blocs `core/*`

## Workflow d'invocation typique

Quand l'utilisateur dit « crée une page X » :

1. Demander l'URL du site et l'Application Password si pas en mémoire (skill state)
2. Lancer `scripts/detect-environment.php` → obtenir le profil { spectra: bool, astra: bool, theme_slug, wp_version }
3. Si `spectra: false` → arrêt avec message clair + lien install
4. Lire le brief utilisateur → invoquer `workflows/new-page-from-brief.md`
5. Le workflow utilise `references/intent-to-block-routing.md` pour mapper et compose le markup
6. POST sur `/wp-json/wp/v2/pages` avec `status: draft`
7. Retourner l'URL d'édition Gutenberg + URL frontend
8. Si `/screenshot-loop` disponible : faire une capture pour validation visuelle
9. Si `/impeccable` disponible : proposer un audit design

## Règles strictes (NEVER)

- **NE JAMAIS** publier une page directement (toujours `status: draft` ou `status: pending`)
- **NE JAMAIS** écraser une page existante sans confirmation utilisateur (préférer le clone via copy)
- **NE JAMAIS** mettre des hex hardcoded dans les attrs de blocs uagb (toujours `var(--ast-global-color-X)` ou un token nommé)
- **NE JAMAIS** générer un bloc Spectra sans `block_id` unique (sinon Gutenberg recompute et casse)
- **NE JAMAIS** reset l'option `astra-settings` (option massive, des centaines de keys, peut atteindre 200+ KB sur configs avancées). Toujours read → modify → write
- **NE JAMAIS** envoyer le contenu d'une page sans avoir validé le markup via `validate-block-markup.php` (roundtrip parse → reserialize doit faire 0 char de diff)

## Pourquoi ce skill existe

- **Spectra** est le seul gros plugin Gutenberg sans intégration AI sérieuse (~700K sites concernés au 02/05/2026, validé sur recherche)
- **Astra** a son MCP officiel mais limité au Customizer
- **Gutenberg core** a sa REST API mais sans intent-routing curé

Le skill comble ce trou : il route intelligemment chaque intention vers le bon bloc (Spectra prioritaire), assure la cohérence design system via les variables CSS Astra (ou un fallback CSS), et orchestre la génération depuis un brief en langage naturel jusqu'à la page draft validée visuellement.

## Versionning

- **v1.0** (cible mai 2026) : 3 killer features + 15+ patterns + 8 templates + module Astra optionnel + couplage `/screenshot-loop` et `/impeccable`
- **v1.1** : refresh de patterns selon retours communauté + nouveaux templates
- **v2.0** (cible Q3 2026) : multi-sites batch, Astra Pro features, intégration WooCommerce, génération from screenshot (image → page)

## Sources et crédits

- POC validé le 02/05/2026 sur WordPress Playground (rapport : `claude-memory/poc-skill-astra-spectra-2026-05-02.md` du repo wpformation)
- Inventaire Spectra : repo officiel `brainstormforce/wp-spectra` (48 blocs)
- Doc Astra MCP officielle : https://wpastra.com/docs/astra-mcp/
- Author : Fabrice Ducarme — WPFormation.com (8 plugins WordPress.org, 2.1M+ téléchargements, speaker WordCamp Paris/Lyon/Marseille)
