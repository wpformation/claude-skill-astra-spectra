# Examples — pas le skill, juste des références concrètes

> **CES FICHIERS NE SONT PAS LE SKILL.** Ce sont des exemples concrets validés sur loginarmor-dev.local (Astra 4.13.1 + Spectra 2.19.25 + palette_3) le 02/05/2026. Ils servent à voir ce que le skill peut produire quand il est correctement utilisé.

> **NE PAS COPIER-COLLER** ces fichiers dans un site. Ils contiennent des `block_id` préfixés `v93-` et des URLs absolues `loginarmor-dev.local` qui ne fonctionneront chez personne.

## Qu'est-ce que tu trouves ici

| Fichier | Description |
|---|---|
| `landing-formation-complete-markup.html` | Markup Gutenberg complet (~58 KB, 28+ blocs uagb) d'une page formation BTS NDRC. Hero overlay + stats bar + 3 features numbered + about-story split + 3 testimonials + FAQ accordéon + CTA banner |
| `landing-formation-complete-page-css.css` | CSS overrides à mettre dans `_uag_custom_page_level_css` pour styler les chiffres énormes, guillemets, accent lines (5,2 KB) |

## Comment ces fichiers ont été produits

En suivant exactement le workflow `workflows/new-page-from-brief.md` :

1. Brief utilisateur : « page d'accueil cours-ndrc.fr avec hero + stats + 3 piliers + témoignages + FAQ + CTA final »
2. Application des patterns `patterns/hero-image-overlay.md`, `stats-bar-editorial.md`, `features-numbered.md`, `about-story-split.md`, `testimonials-cards.md`, `faq-accordion.md`, `cta-banner-fullwidth.md`
3. Application des conventions `references/i18n-rules.md` (accents en HTML entities, espace insécable français)
4. Application des techniques `references/persistent-css-overrides.md` (CSS dans meta natif, pas inline)
5. Validation visuelle via `workflows/visual-validation-loop.md` (3 itérations agent-browser)

## Comment t'en inspirer

Pour construire ta propre page :

1. Lis `SKILL.md` racine
2. Lis les patterns dont tu as besoin (e.g. `patterns/hero-image-overlay.md`)
3. Lis `references/spectra-attributes-quirks.md` pour ne pas tomber dans les pièges
4. Génère ton propre markup avec **tes propres `block_id`** (préfixe au choix, e.g. `accueil-`, `services-`, etc.)
5. Génère ton CSS overrides avec **tes propres classes ciblées**
6. POST + meta update + regen Spectra
7. Screenshot + valide visuellement

## Galerie de screenshots

Voir `screenshots/loginarmor-dev-palette3/` à la racine du repo pour les baselines visuelles iter 1 → iter 4.
