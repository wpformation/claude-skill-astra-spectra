# Changelog

Toutes les modifications notables de ce skill sont documentées dans ce fichier. Format basé sur [Keep a Changelog](https://keepachangelog.com/), versions selon [Semantic Versioning](https://semver.org/).

## [Unreleased]

### À venir (v1.0 finale)

- Push & test du skill dans une autre session Claude Code
- Compilation effective du PDF (Pandoc/Typst + 25 captures)
- Déploiement de la page front + route API Vercel sur wpformation.com
- 5+ patterns supplémentaires (timeline, contact-form, newsletter, 404, hero variants)
- 5+ templates supplémentaires (blog-editorial, e-commerce-produit, page-tarifs, page-contact, page-a-propos, page-404, coming-soon)
- Article WPFormation dédié
- Distribution communauté (LinkedIn, Discord WP, soumission #ai-tools Slack)

## [0.8.0-beta] — 2026-05-02

### Itérations 4 à 8 — Préparation v1.0

#### Ajouté

##### Itération 4 — Validation visuelle automatique

- `workflows/visual-validation-loop.md` : workflow avec retries intelligents max 3 tentatives, couplage `/impeccable` + `/screenshot-loop` ou checks intégrés (12 critères P0/P1/P2/P3)
- `scripts/visual-audit.php` : 12 checks intégrés (hiérarchie titres, contraste, hex hardcodé, block_id, padding, alt images, container width, responsive, etc.)
- `scripts/auto-fix-markup.php` : corrections automatiques (block_id régénérés UUID v4, hex → tokens Astra, H1 dupliqués dégradés en H2)

##### Itération 5 — Module Astra Customizer complet

- `modules/astra/customizer-map.md` : cartographie exhaustive `astra-settings` (palette, typo, layout, header builder, footer builder, sidebar, blog, perf, custom CSS) avec workflows palette + header
- `scripts/astra-customizer.php` : pilote complet avec commandes `export` (snapshot config) et `apply` (patch JSON sécurisé qui préserve les 1942 keys)

##### Itération 6 — Evals + benchmarks

- `evals/evals.json` : 10 évals canoniques (build × 5, refonte × 1, template × 1, validation × 2, astra × 1) avec assertions précises (block_count, css_var_count, hex_hardcoded_count, etc.)
- `evals/run-evals.php` : runner CLI avec filtrage `--category` et `--id`
- `evals/fixtures/malformed-markup.html` : fixture markup volontairement cassé (H1 multiple, block_id dupliqué, hex hardcodé)
- `evals/fixtures/astra-palette-orange.json` : fixture patch palette orange WPF
- `evals/README.md` : doc évals + types d'assertions supportés

##### Itération 7 — PDF premium (lead magnet)

- `lead-magnet/pdf-source.md` : source markdown 32-44 pages (27 chapitres, 30 recettes, 12 effets WOW, 8 templates, 15 prompts, 10 anti-patterns, 10 troubleshooting, FAQ)
- `lead-magnet/README.md` : workflow de production Pandoc/Typst + spécifications PDF + métriques cibles distribution

##### Itération 8 — Infra Vercel (lead magnet)

- `vercel-integration/api-route.ts` : route POST `/api/skill-astra-spectra/` clonée du pattern `/api/guide-ia/` (Brevo liste 5, Turnstile, rate limiter, email transactionnel HTML+text)
- `vercel-integration/page.tsx` : page front `/skill-astra-spectra/` avec hero gradient mesh + 3 features + capture email + sommaire + CTA formation
- `vercel-integration/README.md` : guide d'intégration au repo WPFORMATION (à déployer en session dédiée)

#### Modifié

- `scripts/validate-block-markup.php` : distingue désormais diff cosmétique whitespace (warning) vs vraie erreur (error)

#### Métriques skill v0.8.0-beta

- 30 → 45 fichiers
- 4 332 → ~7 200 lignes
- 4 → 7 scripts PHP
- 4 → 5 références
- 2 → 3 modules (ajout `astra/customizer-map.md`)
- 0 → 10 évals
- 0 → 32 pages markdown PDF source
- 0 → 3 fichiers Vercel-ready

## [0.5.0-alpha] — 2026-05-02

### Squelette + bases du skill

#### Ajouté

- **SKILL.md** : routing principal, 3 killer features, détection environnement, règles strictes
- **README.md** : pitch communauté, install rapide, badges
- **INSTALL.md** : installation pas-à-pas en 5 étapes (5 minutes)
- **LICENSE** : MIT

#### Scripts (4)

- `detect-environment.php` : détection auto Spectra + Astra + thème + WP version + permalinks → verdict GO/DEGRADED/BLOCKED
- `apply-design-tokens.php` : application palette via Astra ou fallback CSS, support 11 presets Astra natifs + palette custom 9 hex
- `validate-block-markup.php` : roundtrip parse_blocks → serialize_blocks, détection block_id dupliqués + hex hardcoded
- `snapshot-page.php` : dump JSON d'une page existante (pour workflow refonte)

#### References (4)

- `intent-to-block-routing.md` : table de décision intent → bloc (45 entrées, règles de priorisation, anti-patterns)
- `spectra-blocks-catalog.md` : 49 blocs uagb/* documentés avec attrs critiques
- `block-markup-syntax.md` : syntaxe Gutenberg comments + 8 règles strictes + pièges courants
- `design-system-tokens.md` : mapping Astra global colors ↔ blocs Spectra, palettes pré-construites

#### Modules (1)

- `modules/spectra/container-wow-recipes.md` : **12 recettes WOW** avec uagb/container (hero parallax, glassmorphism, gradient mesh, dividers diagonaux, background video, sticky sidebar, etc.) + 4 combos puissants

#### Patterns (8)

- `hero-cta-split.md` : Hero pleine page split 50/50 avec 2 CTAs
- `features-3-cols.md` : Section 3 features en cards hoverables
- `pricing-3-tiers.md` : Pricing 3 tiers avec tier central mis en avant + badge populaire
- `faq-accordion.md` : FAQ accordéon avec schema FAQPage auto
- `cta-banner-fullwidth.md` : CTA banner full-width avec gradient + 2 CTAs
- `testimonials-grid.md` : Grille 3 témoignages avec photos + ratings
- `team-grid.md` : Grille équipe avec photos + bios + liens sociaux
- `stats-counters.md` : Bandeau 4 stats animées au scroll
- `article-content-rich.md` : Article éditorial mix core+Spectra avec TOC + FAQ + inline-notice

#### Templates (3)

- `page-formation.md` : Page de vente formation en ligne (9 sections)
- `landing-saas.md` : Landing page SaaS B2B (9 sections)
- `page-agence.md` : Site vitrine agence digitale (10 sections)

#### Workflows (3 killer features)

- `new-page-from-brief.md` : génération depuis brief en langage naturel — 8 étapes (détection → parsing → patterns → markup → validation → POST → récap → optionnel screenshot/audit)
- `refonte-page-existante.md` : refonte intelligente d'une page existante — 8 étapes (détection → snapshot → analyse → mapping → reconstruction → POST clone → diff → migration optionnelle)
- `deploy-template.md` : déploiement de template clic-bouton — 7 étapes (détection → sélection template → adaptation contenu → palette → validation → POST → récap)

#### POC (préalable, 02/05/2026)

POC validé sur WordPress Playground en ~1h, 3/3 tests passés :

- Test A : Pilotage Astra via `astra-settings.global-color-palette.palette` → 9 variables CSS régénérées
- Test B : POST page hybride core+Spectra (15 blocs) → 0 erreur Gutenberg, roundtrip parfait
- Test C : Cohérence design system → 199 occurrences `var(--ast-global-color-X)`, 0 hex hardcoded

Verdict : **GO sans réserve**.

### Découvertes structurelles importantes

- **Astra MCP officiel non requis** : pilotage via REST API + update_option suffit
- **astra-settings est massive** : 1942 keys, 242 KB. Pattern read → modify → write obligatoire
- **block_id unique obligatoire** sur tous les blocs Spectra
- **Cache Astra à invalider** après update : `astra_clear_all_assets_cache` + `delete_transient('astra_dynamic_css')` + `wp_cache_flush()`
- **uagb/container = bloc fondation** pour tous les effets WOW (préférer à core/group, core/columns, core/cover)
