# Changelog

Toutes les modifications notables de ce skill sont documentées dans ce fichier. Format basé sur [Keep a Changelog](https://keepachangelog.com/), versions selon [Semantic Versioning](https://semver.org/).

## [Unreleased]

### À venir (v1.0 finale)

- Compilation effective du PDF (Pandoc/Typst + 25 captures)
- Déploiement de la page front + route API Vercel sur wpformation.com
- 6+ patterns supplémentaires (tabs-section, slider-carousel, timeline-vertical, how-to-steps, review-product, countdown-launch, contact-form-split, 404-page)
- 5 templates supplémentaires (blog-editorial, e-commerce-produit, page-tarifs, page-contact, page-a-propos)
- references/spectra-icons-list.md (liste exhaustive noms courts d'icônes)
- references/gutenberg-core-blocks.md (30+ blocs core/* curés)
- Article WPFormation dédié
- Distribution communauté (LinkedIn, Discord WP, soumission #ai-tools Slack)

## [0.8.1-beta] — 2026-05-02 (PM)

### Correctifs post-test cours-ndrc.fr (rapport 19 issues)

#### Corrigé — 3 BLOCKERS

- **`scripts/validate-block-markup.php`** : faux positif sur l'échappement `--`. `serialize_blocks()` encode systématiquement `--` en `--` dans les attrs JSON (var(--ast-global-color-X) déclenche ce reformatage). Ajout d'une normalisation Unicode des deux côtés AVANT comparaison. Le validator rejetait à tort 100 % des markups produits par les patterns du skill.
- **`patterns/features-3-cols.md`** : HTML `<i class="{{F1_ICON}}">` (style FontAwesome) incompatible avec rendu Spectra qui utilise des SVG inline. Ajout `source_type:"icon"`, `iconimgPosition:"above-title"`, structure `uagb-ifb-content` qui correspond au rendu réel. Documentation des noms courts d'icônes Spectra (rocket, lightbulb, chart-pie...).
- **`scripts/post-page-via-rest.php` (nouveau)** : POST automatique vers `/wp-json/wp/v2/pages` avec auth Basic Auth (Application Password), gestion erreurs 401/403/404, support Yoast meta, retour edit_url. Comble le gap workflow étape 6 qui ne fournissait qu'un exemple curl à recomposer manuellement.

#### Corrigé — 6 MAJEURS

- **`SKILL.md`** : section « Structure du skill » alignée avec l'état réel du repo. Suppression de 13 références à des fichiers inexistants (modules/spectra/blocks-catalog.md, modules/astra/settings-mapper.md, references/gutenberg-core-blocks.md, workflows/new-site-from-scratch.md, etc.). Ajout des fichiers présents non documentés (auto-fix-markup.php, astra-customizer.php, visual-audit.php, post-page-via-rest.php, lead-magnet/, evals/).
- **`SKILL.md`** : promesses ajustées de « 8 templates / 15+ patterns » à « 3 templates v0.8 / 9 patterns v0.8 », avec liste explicite des items à venir en v1.0.
- **`scripts/detect-environment.php`** : guard `php_sapi_name() !== 'cli' && !headers_sent()` autour du `header()` pour éviter le warning « Cannot modify header information » en mode WP-CLI.
- **`scripts/detect-environment.php`** : initialisation `pro_active: false` et `palette_colors: []` dans le profil par défaut (avant ne se définissait que si Astra actif). Ajout détection des 9 couleurs RÉELLES depuis `astra-settings.global-color-palette.palette` (pilote frontend).
- **`references/spectra-blocks-catalog.md`** : recompté à 48 blocs Gutenberg utilisables (`extensions` est un meta-bloc, pas dans le block inserter). Note d'explication ajoutée. SKILL.md description aligné « 48 blocs ».
- **Documentation** : tous les scripts présents documentés dans la nouvelle section Structure de SKILL.md.

#### Corrigé — 8 MINEURS

- **`references/block-markup-syntax.md` règle 4** : reformulée pour distinguer ce qui est CRITIQUE (texte heading ≠ `headingTitle`, balise ≠ `headingTag`, `<i class="fa-...">` au lieu de SVG, `block_id` manquant) vs ce qui est COSMÉTIQUE (whitespace, ordre des classes, encodage `--` ↔ `--`). Pattern info-box corrigé en exemple.
- **`references/block-markup-syntax.md` règle 5** : note explicite sur l'encodage des accents — UTF-8 OK dans HTML rendu, escapes Unicode recommandées dans attrs JSON pour éviter corruption charset PHP/MySQL.
- **`references/intent-to-block-routing.md`** : remplacement du « score Spectra +10 / core +5 » (jamais implémenté) par une heuristique explicite à 4 règles que Claude Code applique en lisant la table.
- **`workflows/new-page-from-brief.md`** : ajout étape 10 cleanup TEST/POC/DEMO/[skill] pages (proposer suppression à l'utilisateur après validation pour éviter accumulation de brouillons).
- **`INSTALL.md` étape 3** : reformulation pour préciser qu'il faut **invoquer le skill explicitement** (pas un prompt langage naturel ambigu) et expliquer comment le script `detect-environment.php` est exécuté (WP-CLI / mu-plugin / hébergeur).
- **`scripts/auto-fix-markup.php`** : `wpf_skill_nearest_token()` lit dynamiquement la palette ACTIVE depuis `get_option('astra-settings')` au lieu d'une palette hex codée en dur. Calcul nearest-color via distance euclidienne sur les 9 couleurs réelles → mapping correct sur n'importe quel `currentPalette`.
- **`evals/evals.json`** : assertions techniques renforcées (`must_validate_roundtrip`, `gutenberg_zero_warnings`, `frontend_min_bytes`, `rest_api_status`) sur build-01-page-formation. Modèle à dupliquer sur les autres évals build.

#### Issues notées pour v1.0

- Mineur 17 : pattern Astra-Pro-only (header transparent overlay) non implémenté → reporté v1.0
- Mineur 16 (partie 2) : adaptation des patterns à `palette_colors` détectée non implémentée côté patterns → reporté v1.0 (les patterns continuent d'utiliser les slots `--ast-global-color-X` ce qui marche déjà sur toutes les palettes par construction Astra)

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

##### Itération 8 — Distribution lead magnet

Itération réservée à la distribution côté WPFormation (page de capture + email transactionnel + suivi GA4). Tout le code de l'intégration côté front est maintenu hors de ce repo public pour ne pas exposer de détails d'infrastructure.

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
