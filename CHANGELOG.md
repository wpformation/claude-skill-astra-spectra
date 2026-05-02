# Changelog

Toutes les modifications notables de ce skill sont documentées dans ce fichier. Format basé sur [Keep a Changelog](https://keepachangelog.com/), versions selon [Semantic Versioning](https://semver.org/).

## [Unreleased]

### À venir (v1.0)

- 5+ patterns supplémentaires (timeline, contact-form, newsletter, 404, hero variants)
- 5+ templates supplémentaires (blog-editorial, e-commerce-produit, page-tarifs, page-contact, page-a-propos, page-404, coming-soon)
- Module Astra avec couverture exhaustive du Customizer (header builder, footer builder, typography)
- Évals automatiques + benchmarks de performance
- PDF guide premium 25-40 pages (lead magnet)
- Infrastructure lead magnet (Vercel route + page front + Brevo)
- Article WPFormation dédié

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
