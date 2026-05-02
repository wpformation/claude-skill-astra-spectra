# Baseline screenshots — loginarmor-dev.local (Astra 4.13.1 + Spectra 2.19.25 + palette_3)

> **Première baseline visuelle prouvée du skill** (02/05/2026, v0.9.1-beta).
>
> Stack identique à cours-ndrc.fr : Astra 4.13.1 + Spectra 2.19.25 + palette palette_3 (orange WPF #FD9800 / dark #0F172A / cream #FEF9E1 / piège noir #141006).
>
> Objectif : reproduire les conditions du test catastrophique cours-ndrc.fr v0.9.0 et démontrer que le skill v0.9.1 produit maintenant une page WOW.

## Méthodologie

1. WP local Local by Flywheel → Astra theme + Spectra plugin installés
2. Palette_3 configurée via mu-plugin compagnon (`/skill-test/v1/setup`)
3. 6 images Unsplash uploadées via `/skill-test/v1/upload-image`
4. Page démo générée à partir du markup `templates/landing-formation-complete-markup.html`
5. CSS Spectra régénéré via `UAGB_Post_Assets::generate_assets()`
6. Page ouverte dans agent-browser viewport 1440×900
7. `loading="eager"` forcé sur toutes les images, scroll bottom + scroll top
8. Screenshot fullpage + screenshots zoomés par section

## Index des screenshots

### Itération finale (v091-iter3-WOW + v091-FINAL-zoom)

| Fichier | Section | Description |
|---------|---------|-------------|
| `v091-iter3-WOW-fullpage.png` | **Page complète** | 1440×~4500 px, 7 sections rendues correctement |
| `v091-FINAL-zoom-v3-hero.png` | Hero | Image background + overlay gradient + eyebrow orange + H1 + 2 CTAs |
| `v091-FINAL-zoom-v3-stats.png` | Stats bar | Bg dark #0F172A + 4 stats orange (227 / 33 / 22 / 87%) |
| `v091-FINAL-zoom-v3-features.png` | 3 Features | Cards 3-cols avec icônes orange + headings + descs |
| `v091-FINAL-zoom-v3-story.png` | About-story | Image éditoriale 1200×380 + heading 2-cols + desc |
| `v091-FINAL-zoom-v3-testimonials.png` | Testimonials | 3 cards avec citation italique + auteur + sub-info |
| `v091-FINAL-zoom-v3-faq-section.png` | FAQ accordéon | 4 questions, 1ère expanded avec vraie réponse (pas Lorem Ipsum) |
| `v091-FINAL-zoom-v3-cta-final.png` | CTA banner | Image bg + overlay + heading blanc + 2 CTAs |

### Itérations intermédiaires (debug trail)

| Fichier | Itération | Note |
|---------|-----------|------|
| `v091-iter1-1440-fullpage.png` | Iter 1 | Page complète avant fix (image story manquante) |
| `v091-iter1-1440-fullpage-eager.png` | Iter 1 | Avec `loading=eager` forcé — image story apparaît |
| `v091-iter1-viewport.png` | Iter 1 | Viewport mobile par défaut (problème de viewport) |
| `v091-iter2-faq.png` | Iter 2 | FAQ avec Lorem Ipsum (avant fix `answer`) |
| `v091-iter2-faq-fixed.png` | Iter 2 | FAQ correcte (après fix `answer` au lieu de `description`) |
| `v091-iter2-FINAL-fullpage.png` | Iter 2 | Page complète post-fix FAQ |

## Comparaison v0.9.0 (catastrophique) vs v0.9.1 (WOW)

| Critère | v0.9.0 (rapport user) | v0.9.1 (cette baseline) |
|---------|----------------------|-------------------------|
| Hero gradient | Bleu/violet aléatoire (CSS pas chargé) | Orange WPF + overlay gradient sombre |
| CTAs | Liens orange souligné | Boutons stylisés (orange plein + ghost border) |
| Features | 3 cards empilées verticalement | Grille 3-cols avec icônes + border + shadow |
| Testimonials | Texte aligné gauche, pas de cards | 3 cards avec citation italique + auteur |
| FAQ | Bullet list sans toggle | Accordéon avec icône + 1ère ouverte |
| CTA banner | Heading minuscule mal aligné | Heading 50px centré + 2 CTAs |
| Stats bar | Absent | Bar dark full-width + 4 stats orange |

## Reproduction

Voir `workflows/visual-validation-loop.md` section « Pipeline pratique testé v0.9.1 » pour les commandes exactes.

## Palettes à ajouter pour v1.0

- ☐ `screenshots/astra-default/` (baseline palette default Astra)
- ☐ `screenshots/preset_3/` (palette saturée chaude)
- ☐ `screenshots/preset_8/` (palette orange gourmand — slots variables remappés, plus piège)
- ☐ `screenshots/cours-ndrc-fr-palette3/` (régression test sur site client réel)

Tant que ces 4 baselines ne sont pas produites, le skill reste en `status: experimental`. Voir `screenshots/README.md` racine pour le process complet.
