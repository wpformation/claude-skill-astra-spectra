# Référence design system — démos officiels Spectra (Natures, Astra Sites)

> **Origine** : analyse de 4 pages réelles importées du démo officiel Spectra **Natures** (websitedemos.net/natures-01) sur cours-ndrc.fr en mai 2026 : Homepage, Services, Contact, About. Le user a fourni les markups bruts en session — ces fichiers font office de **gold reference visuelle** pour les patterns du skill.

> **Pourquoi cette référence** : la v0.8.x du skill produisait du markup techniquement valide mais visuellement raté sur palette_3. Le démo Natures, lui, est **éprouvé en prod** sur des dizaines de milliers de sites. C'est notre boussole de qualité visuelle.

## Techniques visuelles clés extraites

### 1. Hero pleine page avec image background + overlay couleur

```json
{
  "blockName": "uagb/container",
  "isBlockRootParent": true,
  "backgroundType": "image",
  "backgroundImageDesktop": { "url": "..." },
  "backgroundImageColor": "var(--ast-global-color-6)",
  "overlayType": "color",
  "overlayOpacity": 0.7,
  "topPaddingDesktop": 225,
  "bottomPaddingDesktop": 225,
  "topPaddingTablet": 100,
  "bottomPaddingTablet": 80,
  "topPaddingMobile": 80,
  "bottomPaddingMobile": 64
}
```

Texte du hero en `#ffffff` direct (pas en var) parce qu'on est sur fond image overlay → garantit la lisibilité quelle que soit la palette.

**Techniques empruntées au démo** :

- `isBlockRootParent: true` + classe `uagb-is-root-container` sur les sections root (CSS reset, alignment, z-index)
- `overlayType: "color"` + `overlayOpacity: 0.7` rend l'image douce et le texte très lisible
- Padding hero énorme (225px desktop) pour donner du **vrai espace dramatique**
- Padding tablet/mobile dégressif : 225 → 100 → 80 → 64 px

### 2. Kicker "prefix" au-dessus du heading (eyebrow text)

Spectra `uagb/info-box` a un attribut `prefixHeadingTag` souvent oublié. Convention démo Natures :

```json
{
  "showPrefix": true,
  "prefixHeadingTag": "p",
  "prefixColor": "var(--ast-global-color-2)",
  "prefixFontSize": 16,
  "prefixFontWeight": "500",
  "prefixTransform": "uppercase",
  "prefixLetterSpacing": 2,
  "prefixSpace": 24
}
```

Le rendu HTML :

```html
<div class="uagb-ifb-title-wrap">
  <p class="uagb-ifb-title-prefix">Discover Our Story</p>
  <h2 class="uagb-ifb-title">About Us</h2>
</div>
```

C'est un détail qui transforme un heading banal en heading **éditorial**.

### 3. Section avec couleur de fond `var(--ast-global-color-4)`

Le démo Natures utilise systématiquement `var(--ast-global-color-4)` comme fond de section secondaire. Sur la palette Natures, color-4 = `#FEF9E1` (crème pâle), un off-white légèrement teinté qui structure visuellement les sections sans agresser.

**ATTENTION COMPATIBILITÉ** : le démo assume une palette qui définit color-4 comme un beige/crème. Sur la palette Astra default, color-4 vaut le primary (#0274be par défaut) → un bg color-4 deviendrait bleu primary, illisible avec headingColor color-2 dessus.

**Notre solution** dans le skill v0.9+ :

- Pour les sections fond clair stable : `#fafafa` (hex direct, neutre, marche partout)
- Pour les sections "tinted" éditoriales (style démo Natures) : utiliser `wpf_skill_resolve_color('bg_section_alt', $palette)` qui retourne la 2e couleur la plus claire de la palette (= crème sur palette_3, off-white sur default)
- En mode "demo-inspired" (palette compatible Natures), garder `var(--ast-global-color-4)` direct

### 4. Cards services avec image sur le top + border subtle

```json
{
  "containerBorderTopWidth": 1,
  "containerBorderLeftWidth": 1,
  "containerBorderRightWidth": 1,
  "containerBorderBottomWidth": 1,
  "containerBorderStyle": "solid",
  "containerBorderColor": "var(--ast-global-color-7)",
  "containerBorderTopLeftRadius": 6,
  "overflow": "hidden"
}
```

L'image en haut a `imageBorderTopLeftRadius: 6` + `imageBorderBottomLeftRadius: 0` (radius haut seulement) pour épouser le radius du container.

**ATTENTION COMPATIBILITÉ** : `containerBorderColor: var(--ast-global-color-7)` est le piège classique — sur palette_3 = noir massif. Notre fix : utiliser `#e5e7eb` (hex neutre) ou résoudre via `wpf_skill_resolve_color('border_subtle', $palette)`.

### 5. Bouton secondary ghost avec icône chevron

```json
{
  "blockName": "uagb/buttons-child",
  "label": "Learn More",
  "color": "var(--ast-global-color-2)",
  "hColor": "var(--ast-global-color-0)",
  "icon": "chevron-right",
  "iconColor": "var(--ast-global-color-2)",
  "iconHColor": "var(--ast-global-color-0)",
  "backgroundType": "transparent",
  "hoverbackgroundType": "transparent",
  "btnBorderStyle": "none",
  "showIcon": true
}
```

Pas de fond, pas de border, juste texte + icône → look éditorial discret. Hover passe au primary, comme un lien actif.

### 6. Grille 3-cards avec `equalHeight: true`

```json
{
  "directionDesktop": "row",
  "alignItemsDesktop": "stretch",
  "wrapDesktop": "wrap",
  "equalHeight": true,
  "columnGapDesktop": 24,
  "rowGapDesktop": 24
}
```

`equalHeight: true` force toutes les cards à la même hauteur même si leur contenu diffère. Indispensable pour une grille services pro.

### 7. Section info contact avec numéros prefix (01, 02, 03, 04)

Pattern utilisé dans la page Contact :

```html
<div class="uagb-ifb-title-wrap">
  <p class="uagb-ifb-title-prefix">01</p>
  <p class="uagb-ifb-title">Location</p>
</div>
<p class="uagb-ifb-desc">2360 Hood Avenue, San Diego, CA, 92123</p>
```

Le prefix est juste un numéro stylisé en uppercase tracking 2 → effet éditorial print/magazine. Très efficace pour 3-6 entrées d'info.

### 8. CTA banner avec image background overlay + 2 boutons

Pattern récurrent (en bas de homepage, contact, about) :

```json
{
  "backgroundType": "image",
  "overlayType": "color",
  "overlayOpacity": 0.7,
  "topPaddingDesktop": 112,
  "bottomPaddingDesktop": 112
}
```

Heading en `#ffffff`, 2 boutons : un blanc transparent border, un primary plein. Identique au hero, juste plus court verticalement.

### 9. Stats card avec border + box-shadow + icône check

```json
{
  "backgroundColor": "var(--ast-global-color-4)",
  "boxShadowColor": "rgba(0,0,0,0.12)",
  "boxShadowVOffset": 4,
  "boxShadowBlur": 8,
  "containerBorderColor": "var(--ast-global-color-7)",
  "containerBorderRadius": 12
}
```

Section "About" du démo : 4 stats (92%, 2,480, 12+, 640K) dans une grande card unifiée (border 1px + shadow douce). Pattern « stat showcase » qui marche très bien pour about/services.

### 10. Gradient horizontal split section

Section "Why Choose Us" du about : background gradient avec **point de cassure** à 50 % :

```json
{
  "backgroundType": "gradient",
  "gradientColor1": "var(--ast-global-color-4)",
  "gradientColor2": "var(--ast-global-color-5)",
  "selectGradient": "advanced",
  "gradientLocation1": 50,
  "gradientLocation2": 50
}
```

Cassure dure (50/50 stop) au lieu d'un fade → visuel "split horizontal" éditorial. Une moitié en color-4 (crème), l'autre en color-5 (white). Très effet magazine.

## Conventions globales du démo Natures

| Convention | Valeur | Justification |
|------------|--------|---------------|
| Padding section desktop | 100-112 px top/bottom | Respiration généreuse, niveau premium |
| Padding section tablet | 60-80 px | Dégressif |
| Padding section mobile | 40-64 px | Dégressif |
| Padding horizontal | 40 desktop / 32 tablet / 24 mobile | Convention stricte |
| Column gap | 72 desktop / 40 tablet / 24 mobile | Espacement entre cards/colonnes |
| Border radius cards | 6 px | Discret, classique |
| Border radius highlight cards | 12 px | Plus marqué pour cards featured |
| Image radius | 6 px | Cohérent avec cards |
| Padding cards content | 32 left/right, 0 top, 4 mobile | Texte respire |
| Heading line-height | 1.15 desktop, 1.5 mobile | Compact desktop, aéré mobile |

## Reusable patterns à créer (priorité)

À partir de ces 4 pages réelles, le skill v0.9+ ajoute progressivement :

| Pattern | Source | Status v0.9.0 |
|---------|--------|----------------|
| `hero-image-overlay` | Homepage hero, Services hero, Contact hero, About hero | ✅ Créé |
| `about-story-split` | About page "Our Story" 50/50 image+text | ✅ Créé |
| `services-cards-with-images` | Homepage services, Services page 3-cards | 🔜 v0.9.1 |
| `contact-info-grid` | Contact 4-col with numbered prefix | 🔜 v0.9.1 |
| `why-choose-3-numbered` | About "Why Choose Us" 3-cards numérotées | 🔜 v0.9.1 |
| `stats-card-unified` | About stats card 4-cells border+shadow | 🔜 v0.9.1 |
| `gradient-split-50-50` | About horizontal split gradient | 🔜 v0.9.1 (recette WOW container) |

## Templates blueprints inspirés (v0.9.1+)

À partir des 4 pages réelles, le skill v0.9.1 produira :

- `templates/spectra-homepage-natures.md` (Hero overlay + About split + Services 3-card + CTA overlay + Testimonials)
- `templates/spectra-contact-page.md` (Hero overlay + Info 4-col + Form + Map + FAQ + CTA)
- `templates/spectra-about-page.md` (Hero overlay + Story split + Why-Choose 3-cards + Stats card + Team grid + CTA)
- `templates/spectra-services-page.md` (Hero overlay + Services 6-cards + Process + Pricing + CTA)

## Note de licence et inspiration

Les démos Spectra (Natures, Astra Sites en général) sont distribués sous **GPL** et accessibles publiquement sur [websitedemos.net](https://websitedemos.net). On peut s'inspirer de leur **structure** (composition de blocs, attributs Spectra) librement.

Les **images** des démos viennent d'unsplash/pexels via Brainstorm Force et sont libres pour usage en démo. Le skill ne fournit pas d'image — c'est à l'utilisateur d'uploader les siennes.

## Aller plus loin

- Tester un démo Spectra réel : `wp plugin install starter-templates --activate` (Astra Starter Templates) puis Templates → Astra Sites → choisir "Natures"
- Comparer le markup généré avec ces patterns pour s'inspirer
- Le démo Natures est le plus pertinent pour tester nos patterns parce qu'il combine : image bg overlay + cards bordées + gradient split + stats card + équipe + CTA + prefix kicker
