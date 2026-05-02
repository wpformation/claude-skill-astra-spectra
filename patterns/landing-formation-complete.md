# Pattern : Landing Formation Complète (Natures-style)

> **Use case** : page d'atterrissage complète pour une formation, un site éducatif, un cours en ligne, un programme de coaching. Combine les 7 sections clé du démo Spectra Natures dans un assemblage validé visuellement sur palette agressive (palette_3 cours-ndrc.fr orange/dark/cream).

> **Origine** : ce pattern est l'aboutissement du test cycle d'avril-mai 2026. Démarche : 4 itérations, validation visuelle agent-browser sur loginarmor-dev.local (Astra 4.13.1 + Spectra 2.19.25 + palette_3). Screenshots de baseline dans `screenshots/loginarmor-dev-palette3/v091-iter3-WOW-fullpage.png` et zoomés par section.

## Sections incluses (dans l'ordre)

1. **Hero overlay** — image background pleine page + overlay gradient sombre + eyebrow kicker + H1 + sous-titre + 2 CTAs
2. **Stats bar** — barre sombre full-width avec 4 stats (#0F172A bg, accent_primary)
3. **3 Features 3-cols** — section bg #fafafa + heading + 3 cards (white bg, border subtle, shadow douce, icône orange en haut)
4. **About-story-split** — section bg #ffffff + image éditoriale paysage 1200×380 + heading + desc en 2 colonnes
5. **Testimonials 3-cols** — section bg #fafafa + heading + 3 cards (white bg, border-radius 16px, shadow plus marquée, citation italique avec guillemet `&ldquo;` orange + auteur en gras + sub-info)
6. **FAQ accordéon** — section bg #ffffff + heading + 4 questions (uagb/faq avec layout:accordion, première ouverte, autres collapsées)
7. **CTA banner final** — image background + overlay gradient sombre + eyebrow + H2 + desc + 2 CTAs

## Variables d'entrée

| Variable | Description | Exemple |
|----------|-------------|---------|
| `{{HERO_IMG_URL}}` + `{{HERO_IMG_ID}}` | Image background hero (1920×1080+) | `https://exemple.com/hero.jpg`, `23` |
| `{{HERO_EYEBROW}}` | Kicker uppercase au-dessus du H1 | `PROMOTION 2026 — DERNIERES PLACES` |
| `{{HERO_HEADLINE}}` | Titre H1 (3-5 mots punchy) | `Réussir ton BTS NDRC en 2026 sans te noyer dans 800 pages de cours` |
| `{{HERO_SUBHEADLINE}}` | Sous-titre 1-2 phrases | `227 cours rédigés, 33 exercices types épreuve...` |
| `{{HERO_CTA1_LABEL}}` + `{{HERO_CTA1_LINK}}` | CTA primary | `Réviser maintenant`, `#cours` |
| `{{HERO_CTA2_LABEL}}` + `{{HERO_CTA2_LINK}}` | CTA secondary | `Voir un cours gratuit`, `#cours` |
| `{{STAT_1_VALUE}}` … `{{STAT_4_VALUE}}` | 4 valeurs stats | `227`, `33`, `22`, `87%` |
| `{{STAT_1_LABEL}}` … `{{STAT_4_LABEL}}` | 4 labels | `cours rédigés`, `exercices types`, `QCM corrigés`, `de réussite` |
| `{{FEATURES_EYEBROW}}` + `{{FEATURES_HEADING}}` + `{{FEATURES_DESC}}` | Intro features | `Trois piliers pour réussir`, `Une méthode qui combine théorie...`, `Pas de blabla...` |
| `{{FEAT_1_ICON}}` … | Icônes Spectra | `book-open`, `clipboard-check`, `timer` (voir lib Spectra) |
| `{{FEAT_1_TITLE}}` … `{{FEAT_3_TITLE}}` | Titres cards | `227 cours rédigés` |
| `{{FEAT_1_DESC}}` … `{{FEAT_3_DESC}}` | Descs cards | `Chaque chapitre couvert...` |
| `{{STORY_IMG_URL}}` + `{{STORY_IMG_ID}}` | Image éditoriale 1200×380 | `https://exemple.com/team.jpg`, `24` |
| `{{STORY_EYEBROW}}` + `{{STORY_HEADING}}` + `{{STORY_DESC}}` | About story split | `Notre approche`, `Le BTS NDRC explique comme un ami qui l'a déjà eu`, `Tu n'es pas là pour relire...` |
| `{{TESTI_EYEBROW}}` + `{{TESTI_HEADING}}` | Intro testimonials | `Ils ont décroché leur BTS`, `3 anciens étudiants...` |
| `{{TESTI_1_QUOTE}}` + `{{TESTI_1_AUTHOR}}` + `{{TESTI_1_INFO}}` | Testi 1 (idem 2 et 3) | `Les fiches synthèses...`, `Lea — BTS NDRC obtenu 2025`, `Promotion CFA Marseille — mention bien` |
| `{{FAQ_HEADING}}` + `{{FAQ_DESC}}` | Intro FAQ | `Questions fréquentes`, `Tout ce que les élèves nous demandent...` |
| `{{FAQ_1_Q}}` + `{{FAQ_1_A}}` | Question + Réponse (idem 2, 3, 4) | `Le site est-il vraiment gratuit ?`, `Oui, totalement...` |
| `{{CTA_FINAL_IMG_URL}}` + `{{CTA_FINAL_IMG_ID}}` | Image bg CTA final | `https://exemple.com/students.jpg`, `28` |
| `{{CTA_FINAL_EYEBROW}}` + `{{CTA_FINAL_HEADING}}` + `{{CTA_FINAL_DESC}}` | CTA banner | `Prêt à réviser intelligemment ?`, `Plus de 250 ressources...`, `Aucune inscription requise...` |

## Block markup complet

Le markup complet (~40 KB, 7 sections) est dans le fichier référence :
[`templates/landing-formation-complete-markup.html`](../templates/landing-formation-complete-markup.html)

Structure narrative en 7 root containers Spectra (`uagb/container` `isBlockRootParent:true`) :

```
uagb/container#hero           (image+overlay gradient, 220px padding, isRoot)
uagb/container#stats          (#0F172A bg, 4 stats, isRoot)
uagb/container#features       (#fafafa bg, heading + 3 cards in row container, isRoot)
uagb/container#story          (#ffffff bg, image full + 2-cols heading/desc, isRoot)
uagb/container#testimonials   (#fafafa bg, heading + 3 cards in row container, isRoot)
uagb/container#faq-section    (#ffffff bg, heading + uagb/faq accordion, isRoot)
uagb/container#cta-final      (image+overlay gradient, 140px padding, isRoot)
```

## Recettes WOW utilisées

### 1. Hero gradient overlay top-left → bottom-right
Au lieu d'un overlay flat, utiliser un gradient linear 135deg, color-1 rgba(15,23,42,0.92) → color-2 rgba(15,23,42,0.45). Drama maximum, image visible coin bas-droit, lisibilité texte garantie coin haut-gauche.

```json
{
  "overlayType": "gradient",
  "overlayBackgroundType": "gradient",
  "overlayBackgroundGradientType": "linear",
  "overlayBackgroundGradientColor1": "rgba(15,23,42,0.92)",
  "overlayBackgroundGradientLocation1": 0,
  "overlayBackgroundGradientColor2": "rgba(15,23,42,0.45)",
  "overlayBackgroundGradientLocation2": 100,
  "overlayBackgroundGradientAngle": 135,
  "overlayBackgroundSelectGradient": "basic",
  "overlayOpacity": 1
}
```

### 2. Stats bar inversée full-width
Section dédiée avec `backgroundColor: "#0F172A"` (slate dark direct, palette-agnostic), 4 info-box centrés en row avec `justifyContentDesktop: "space-around"`. Chiffre en `#FD9800` (orange WPF direct) font-weight 800, label en `#ffffff` font-size 13. Crée un break visuel fort entre hero et features.

### 3. Features cards avec icône en haut
Container blanc, border 1px `#e5e7eb`, border-radius 12px, box-shadow `rgba(15,23,42,0.06) 0 2px 18px`. Icône Spectra (taille 40px, color `#FD9800`) au-dessus du H3 noir. Padding 40px desktop pour respiration.

### 4. About-story image full + 2-cols split
Image full-width `align: left` width 1200 height 380, border-radius 12px. En dessous, container `directionDesktop: row` avec deux info-box (40% / 58% en width, columnGap 80px desktop). Heading colonne gauche court, desc colonne droite longue.

### 5. Testimonials avec guillemet typo orange
Citation visuelle utilise `&ldquo;` (« 8220) en `#FD9800` font-size 36, font-weight 800. Plus élégant que `"` straight. Citation en `#0F172A` italique. Auteur en gras, sub-info en `#454F5E` plus petit.

### 6. CTA banner final image+overlay
Identique au hero (image + gradient overlay) mais padding réduit (140 vs 220 desktop). Heading H2 plus petit que le hero (50 vs 62). 2 CTAs identiques au hero (orange primary + ghost border blanc).

## Protocole de validation visuelle (workflow)

Voir [`workflows/visual-validation-loop.md`](../workflows/visual-validation-loop.md) pour le process complet. Résumé :

1. POST le markup via REST API (`scripts/post-page-via-rest.php` + temp-publish trick si draft)
2. Régénérer assets Spectra (`UAGB_Post_Assets::generate_assets()`)
3. Ouvrir l'URL frontend dans agent-browser viewport 1440×900
4. Force `loading="eager"` sur toutes les images + scroll bottom + scroll top
5. `agent-browser screenshot --full` → fullpage.png
6. Pour chaque section : `scrollIntoView` + `screenshot` zoomé
7. Audit visuel : comparer aux baselines `screenshots/loginarmor-dev-palette3/`

## Block IDs (référencés dans la baseline)

- `v3-hero`, `v3-hero-text`, `v3-hero-buttons`, `v3-cta-1`, `v3-cta-2`
- `v3-stats`, `v3-stat-1`, `v3-stat-2`, `v3-stat-3`, `v3-stat-4`
- `v3-features`, `v3-features-title`, `v3-features-row`, `v3-feat-1`, `v3-feat-2`, `v3-feat-3`
- `v3-story`, `v3-story-image`, `v3-story-row`, `v3-story-heading`, `v3-story-desc`
- `v3-testimonials`, `v3-testi-title`, `v3-testi-row`, `v3-testi-1`, `v3-testi-2`, `v3-testi-3`
- `v3-faq-section`, `v3-faq-title`, `v3-faq`, `v3-faq-1`, `v3-faq-2`, `v3-faq-3`, `v3-faq-4`
- `v3-cta-final`, `v3-cta-final-text`, `v3-cta-final-buttons`, `v3-cta-final-1`, `v3-cta-final-2`

## Conseils production

- **Images** : choisir des photos cohérentes thématiquement (la démo utilise une image fruits/légumes pour l'about-story qui ne match pas BTS NDRC — c'est un placeholder, à remplacer par une vraie photo équipe/cours/étudiants)
- **uagb/faq attribute critique** : le contenu de la réponse DOIT être dans l'attribut JSON `answer` (PAS `description`, ce piège a coûté une itération). Sans ça, Spectra affiche un Lorem Ipsum placeholder
- **lazy loading** : agent-browser screenshot full ne déclenche pas toujours le lazy loading si l'image est en bas de page. Pour reproduire le screenshot baseline, force `loading="eager"` via JS avant capture
- **block_id unique** : chaque bloc uagb DOIT avoir un block_id unique. Le validateur (`validate-block-markup.php`) le vérifie. Sans block_id, Gutenberg recompute et casse le rendu
- **temp-publish trick** : si le site est sur Apache mutu (o2switch, OVH) ou avec LiteSpeed Cache, la preview draft anonyme n'injecte PAS le CSS Spectra. Le skill v0.9.1 utilise `wpf_skill_temp_publish_trick()` qui publie temporairement (~1s) pour forcer la génération CSS, puis revert au statut original

## Inspiration directe

Inspiré du démo officiel **Spectra Natures** (websitedemos.net/natures-01) — assemblage des sections Homepage + About + Services + Contact réelles importées le 02/05/2026. Voir [`references/spectra-demo-reference.md`](../references/spectra-demo-reference.md) pour l'analyse design system détaillée.

Validation visuelle finale baseline : `screenshots/loginarmor-dev-palette3/v091-iter3-WOW-fullpage.png` (Astra 4.13.1 + Spectra 2.19.25 + palette_3 orange WPF, 02/05/2026).
