# Référence : impeccable-bridge — mapping principes /impeccable → patterns Spectra

> **Use case** : tu veux composer une page avec les principes de design enseignés par le skill `/impeccable` (ratios typo, palette committed, hiérarchie, motion, etc.) MAIS tu dois rester dans les contraintes Spectra (49 blocs `uagb/*`, `_uag_custom_page_level_css`, pas de JS custom, etc.).

> **Origine** : retour reviewer 02/05/2026 — `/impeccable` a donné des principes design cohérents (drop cap, mono fonts, watermarks) mais zéro mapping vers ce que Spectra peut réellement supporter. Résultat : CSS jamais validé Spectra-compatible, drop cap qui foire silencieusement, mono fonts en fallback Courier moche.

## Comment utiliser ce fichier

Quand tu invoques `/impeccable` pour avoir une direction design + tu vas implémenter avec ce skill astra-spectra, **vérifie chaque principe** /impeccable dans ce fichier avant de l'implémenter.

3 résultats possibles :

- ✅ **Supporté** : Spectra a un bloc/pattern qui implémente ce principe nativement. Mappage direct.
- ⚠️ **Supporté avec workaround** : implémentable via CSS overrides + caveats à connaître (cf `references/visual-pitfalls.md`)
- ⛔ **Non supporté** : refuser le principe, proposer une alternative qui rend le même effet

---

## Tags par registre design

Chaque pattern dans `patterns/` peut être tagué par registre. Tags principaux :

| Tag | Description | Patterns recommandés |
|---|---|---|
| `editorial` | Magazine éditorial, typo lourde, contraste contenu | `hero-image-overlay`, `article-content-rich`, `about-story-split` |
| `minimal` | Sobre, beaucoup de whitespace, peu d'accents | `hero-cta-split`, `features-3-cols`, `pricing-3-tiers` (variante minimale) |
| `bold` | Couleurs saturées, typo extreme, animation hover | `stats-bar-editorial`, `cta-banner-fullwidth`, `marketing-buttons` |
| `SaaS-corporate` | Tech, blue + slate, hover lift, propre | `features-numbered`, `pricing-3-tiers`, `testimonials-grid` |
| `luxe` | Premium, palette restreinte (noir/or/blanc), serif | `hero-cta-split` (variante serif), `testimonials-cards` (variante luxury) |
| `playful` | Friendly, radius pill, illustration Lottie | `lottie`, `hero-image-overlay` (variante illustration) |

Quand tu invoques `/impeccable` et qu'il te donne un registre (« bold tech », « luxe éditorial », etc.), **filtre les patterns** sur le tag correspondant.

---

## Principes /impeccable et leur mapping Spectra

### 1. Ratios typographiques

`/impeccable` recommande souvent des ratios typo type **modular scale** (1.25, 1.333, 1.5, golden ratio).

**Mapping Spectra** :

| /impeccable principe | Implémentation Spectra | Status |
|---|---|---|
| `H1 desktop = 4em → 64px` | `headingFontSizeDesktop:64` sur `uagb/info-box headingTag:h1` | ✅ Supporté |
| `H1 mobile = 2.625em → 42px` | `headingFontSizeMobile:42` même bloc | ✅ Supporté |
| `body = 1.125em → 18px` | `subHeadingFontSizeDesktop:18` sur info-box, OU `core/paragraph` avec font-size CSS | ✅ Supporté |
| Modular scale strict 1.5 / 1.333 / 1.25 | Tu poses les valeurs calculées dans le CSS overrides | ✅ Supporté |

**Caveat** : Spectra applique les font-sizes via classes générées dynamiquement (`uagb-block-{id}`). Ne PAS poser inline `style="font-size:..."` (cf quirk #4). Toujours via `_uag_custom_page_level_css`.

### 2. Palette committed (1-2 accents max)

`/impeccable` recommande **1 accent primary** + **1 accent secondaire** + neutrals (text + bg).

**Mapping Spectra** :

| /impeccable principe | Implémentation Spectra | Status |
|---|---|---|
| Primary accent | `var(--ast-global-color-0)` (Astra) ou hex direct | ✅ Supporté |
| Secondary accent (différente hue) | `var(--ast-global-color-1)` ou hex direct | ✅ Supporté |
| Neutrals (5 nuances de gris) | hex directs `#0F172A` / `#475569` / `#64748B` / `#E2E8F0` / `#F8FAFC` | ✅ Supporté |
| « Pas plus de 3 occurrences accent dans une section » | Cf `visual-pitfalls.md` Pitfall #5 — règle skill | ✅ Codé en règle non-négociable |

### 3. Hiérarchie visuelle (focal points)

`/impeccable` recommande **1 focal point fort** par section (le CTA) + 2-3 points secondaires.

**Mapping Spectra** :

| /impeccable principe | Implémentation Spectra |
|---|---|
| Focal CTA = bouton primary 56-72px high, bg accent saturé | `uagb/buttons-child` avec `paddingBtnTop/Bottom:20-24`, `background:var(--ast-global-color-0)`, `paddingBtnLeft/Right:40-48` |
| Focal CTA = `uagb/marketing-button` enrichi prefix+label+suffix | Pattern `marketing-buttons.md` |
| Secondaires = ghost button avec border 2px | `uagb/buttons-child` avec `backgroundType:transparent`, `btnBorderTopWidth:2`, `btnBorderColor:rgba(...)` |

### 4. Drop cap éditorial (1re lettre énorme)

`/impeccable` recommande parfois un **drop cap** en début de paragraphe pour effet magazine.

**Mapping Spectra** :

| Implémentation | Status |
|---|---|
| CSS `::first-letter` dans `_uag_custom_page_level_css` | ⚠️ **Risqué** — Spectra wrap parfois le texte dans des `<span>` internes qui cassent `::first-letter`. Le rendu varie par browser. Voir `visual-pitfalls.md` Pitfall #3 |
| Pull quote `uagb/blockquote` au milieu du texte | ✅ Alternative recommandée |
| Eyebrow uppercase au-dessus du paragraphe | ✅ Alternative recommandée — effet éditorial sans risque |

**Verdict** : ⚠️ Drop cap = ne PAS faire sauf si pattern `article-content-rich.md` le supporte explicitement avec screenshot validé.

### 5. Mono fonts (timestamps, code, badges)

`/impeccable` recommande parfois **font monospace** sur certains éléments pour effet « tech / dev ».

**Mapping Spectra** :

| Implémentation | Status |
|---|---|
| `font-family: 'JetBrains Mono', monospace` sur `.uagb-block-{slug}-timestamp` | ⛔ **Non recommandé** sauf site dev/tech committed (cf `visual-pitfalls.md` Pitfall #4) |
| Bloc `core/code` (rendu mono natif géré par le thème) | ✅ Supporté pour code source |
| `font-variant-numeric: tabular-nums` (chiffres alignés) | ✅ Alternative — tabular nums dans la font système, plus propre |

### 6. Watermarks numériques géants

`/impeccable` recommande parfois un **chiffre watermark** géant en background pour effet éditorial.

**Mapping Spectra** :

| Implémentation | Status |
|---|---|
| Pseudo-element `::before` avec `content: "247"` 480px transparent | ⚠️ **Risqué** — sans design global validé, paraît bug. Cf `visual-pitfalls.md` Pitfall #1 |
| Stats bar avec chiffres 56-72px en row | ✅ Alternative recommandée — pattern `stats-bar-editorial.md` |
| Numéros 01/02/03 dans features cards | ✅ Alternative — pattern `features-numbered.md` |

### 7. Asymetric layout (40/30/20/10)

`/impeccable` recommande parfois une **grille asymétrique** pour hiérarchie visuelle dramatique.

**Mapping Spectra** :

| Implémentation | Status |
|---|---|
| `widthDesktop: 40 / 30 / 20 / 10` sur 4 cards | ⚠️ **Risqué** sans calibration typo extrême. Cf `visual-pitfalls.md` Pitfall #2 |
| Hero asymétrique 60/40 (texte gauche, image droite) | ✅ Supporté — pattern `hero-cta-split.md` |
| Stats 33/33/33 ou 25/25/25/25 equal | ✅ Alternative recommandée — baseline éprouvée |

### 8. Motion / animations (hover lift, scroll-triggered)

`/impeccable` recommande parfois **motion** pour interactions premium.

**Mapping Spectra** :

| Implémentation | Status |
|---|---|
| Hover simple `transform: translateY(-2px); transition: 0.15s ease;` | ✅ Supporté via `_uag_custom_page_level_css` |
| Animation infinite (float, pulse) | ⚠️ Cf `visual-pitfalls.md` Pitfall #9 — risqué, distrait |
| Scroll-triggered animation (parallax, fade-in on scroll) | ⛔ Non supporté natif Spectra — demande JS custom (out of scope skill) |
| `prefers-reduced-motion` respect | ✅ OBLIGATOIRE — `@media (prefers-reduced-motion: reduce) { animation: none; }` |
| Lottie animation | ✅ Pattern `lottie.md` |

### 9. Glassmorphism (frosted glass, backdrop-filter)

`/impeccable` recommande parfois **backdrop-filter blur** pour effet « glass ».

**Mapping Spectra** :

| Implémentation | Status |
|---|---|
| `backdrop-filter: blur(8px); background: rgba(255,255,255,0.6);` | ✅ Supporté via `_uag_custom_page_level_css` mais `backdrop-filter` Safari iOS < 16 = pas supporté |
| Caveat : nécessite que le bg derrière soit visible (image, gradient) | ⚠️ Sur fond blanc uniforme, l'effet ne se voit pas |

### 10. Neon glow effects (text-shadow saturé)

`/impeccable` recommande parfois **text-shadow saturé** pour effet retro/cyberpunk.

**Mapping Spectra** :

| Implémentation | Status |
|---|---|
| `text-shadow: 0 0 16px rgba(255,140,0,0.6)` sur H1 | ⛔ **Non recommandé** sauf brand cyberpunk committed |
| Alternative : H1 avec accent line solide + accent color sur le texte | ✅ Alternative recommandée |

---

## Workflow recommandé : `/impeccable` + skill astra-spectra

### Étape 1 — Demander une direction design à `/impeccable`

```
/impeccable « Page contact pour formation en ligne, registre minimal éditorial,
              palette committed (1 accent), hiérarchie focal CTA »
```

`/impeccable` te donne 5-10 principes design : palette, ratios typo, focal CTA, motion, hiérarchie sections, etc.

### Étape 2 — Filtrer les principes via ce fichier

Pour chaque principe `/impeccable`, regarder dans la table ci-dessus :

- ✅ Supporté → implémenter directement avec le pattern Spectra mappé
- ⚠️ Supporté avec workaround → lire les caveats dans `visual-pitfalls.md` avant d'implémenter
- ⛔ Non supporté → utiliser l'alternative recommandée OU demander au user de valider

### Étape 3 — Choisir les patterns par tag de registre

Filtrer les patterns sur le tag `/impeccable` (editorial / minimal / bold / SaaS-corporate / luxe / playful).

### Étape 4 — Composer en respectant les baselines

Avant de poser une typo/spacing/couleur, vérifier dans `references/design-baselines.md`.

### Étape 5 — Pre-flight + post-render check

```bash
php scripts/pre-flight-check.php --content-file=markup.html --css-file=overrides.css
# Si BLOCKED → corriger
# Si OK → POST

php scripts/post-render-check.php --url={URL} --post-id={ID}
# Si BLOCKED → workaround Quirk #23/#24 si besoin
```

### Étape 6 — Screenshot validation OBLIGATOIRE

Avant de claim « impeccable », screenshot de la page rendue (cf `workflows/screenshot-options.md`). Sans screenshot, tu qualifies de « composition non vérifiée visuellement » (cf règle 1 SKILL.md).

---

## TODO v1.1+

- [ ] Tagger explicitement chaque pattern dans `patterns/*.md` (frontmatter `tags: [editorial, bold, ...]`)
- [ ] Endpoint `/impeccable` qui détecte si le skill astra-spectra est dispo et oriente directement vers les patterns mappés
- [ ] Variantes par registre dans chaque pattern (e.g. `hero-cta-split-luxe.md`, `hero-cta-split-saas.md`)
- [ ] Whitelist des CSS techniques `/impeccable` validés Spectra (drop cap version safe, etc.)