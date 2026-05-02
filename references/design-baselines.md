# Référence : design baselines — rulers concrets typo/spacing par section

> **LECTURE OBLIGATOIRE** avant de poser une valeur de typographie, de spacing, de couleur ou de layout dans un pattern. Le but : éliminer l'improvisation (« 88px ? 76px ? 62px ? »).

> **Origine** : retour reviewer 02/05/2026 — 3 pages contact pour cours-ndrc.fr toutes qualifiées « moches, niveau débutant » par le user. Cause : valeurs typo/spacing inventées sans repère, sortant des conventions implicites du skill.

## Comment utiliser ces baselines

Pour chaque section type, ce fichier donne :

- **Default** : la valeur recommandée à utiliser par défaut
- **Range** : la fourchette acceptable si tu as une raison de t'écarter du default (ex: brand bold = +20%, brand sobre = -10%)
- **Hard limits** : les valeurs au-delà desquelles **tu ne dois jamais aller** sans validation explicite du user

Si tu envisages de sortir du **range**, **demande** au user d'abord. Si tu envisages de dépasser le **hard limit**, c'est NON sauf instruction explicite.

---

## HERO

### H1 (titre principal hero)

| Breakpoint | Default | Range | Hard limit |
|---|---|---|---|
| Desktop | **76px** | 60-88px | 100px |
| Tablet | **54px** | 44-64px | 72px |
| Mobile | **38px** | 30-44px | 52px |

| Prop | Default | Notes |
|---|---|---|
| `font-weight` | **800** | 700-900 OK selon font weight max disponible |
| `line-height` | **1.05** desktop, 1.1 tablet, 1.15 mobile | Ne JAMAIS aller en dessous de 1.0 (lettres se touchent) |
| `letter-spacing` | **-2px** desktop, -1.5px tablet, -1px mobile | Range -1 à -3, hard limit -4 |

### Eyebrow hero (kicker uppercase)

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` | **14px** desktop, 13px mobile | 13-16px | 18px max |
| `font-weight` | **800** | 700-900 | — |
| `letter-spacing` | **4px** | 3-5px | 6px max |
| `text-transform` | uppercase | uppercase only | — |
| `color` | accent primary (`var(--ast-global-color-0)` ou hex direct) | — | — |
| `prefixSpace` (gap eyebrow → H1) | **24px** | 20-28px | 36px max |

⚠️ **Ne pas descendre sous 13px** sur l'eyebrow (cf quirk #19 : ressemble à un debug tag).

### Subline / desc hero (paragraphe sous H1)

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` desktop | **20px** | 17-22px | 24px max |
| `font-size` tablet | **17px** | 16-19px | — |
| `font-size` mobile | **16px** | 15-17px | — |
| `line-height` | **1.6** | 1.55-1.7 | — |
| `color` | gris atténué (`#94A3B8` sur fond sombre, `#475569` sur fond clair) | — | — |

### Hero — padding section

| Breakpoint | Default | Range éditorial | Hard limit |
|---|---|---|---|
| Desktop | **140/140** | 120-200/120-200 | 240/240 |
| Tablet | 96/96 | 80-140/80-140 | 160/160 |
| Mobile | 72/72 | 56-110/56-110 | 140/140 |

⚠️ **Padding 200/160 ou 220/220** : éditorial only, justifie. **Hero compact** : 96/96 desktop OK pour landing tech sobre.

### Hero — overlay image background

| Prop | Default | Hard limit |
|---|---|---|
| `overlayOpacity` flat | **0.5-0.65** | 0.85 max (cf quirk #9 : >0.85 = image invisible) |
| Overlay gradient (recommandé) | `rgba(0,0,0,0.7)` → `rgba(0,0,0,0.20)` 110-135deg | — |
| `overlayOpacity` si gradient avec rgba semi-transparents | **1** OK car la transparence est dans les color stops | — |

### Hero — accent line orange après H1

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| Width × height | **64×4px** | 48-96 × 3-5px | 120×6px max |
| Couleur | accent primary | — | — |
| Position | sous H1, padding-top 24px | — | — |

⚠️ **Ne JAMAIS** dépasser 120×6px (devient une banderole, pas un accent).

### CTAs (boutons primary + secondary)

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| Padding desktop | **20/40** vertical/horizontal | 16-22 / 32-44 | 28/56 max |
| `font-size` | **16px** | 15-17px | 18px max |
| `font-weight` primary | **800** | 700-900 | — |
| `font-weight` secondary | **600** | 500-700 | — |
| `border-radius` | **8px** | 6-12px | 999px (pill) accepté pour brand SaaS friendly |
| `letter-spacing` | **0.3px** | 0-0.5px | — |
| Gap entre les 2 CTAs | **16px** | 12-24px | 32px max |

⚠️ **Padding < 16/32** = bouton pincé (cf quirk #17). **Padding > 28/56** = bouton qui mange l'écran.

---

## STATS BAR (4 chiffres horizontaux)

### Numbers (stats principaux)

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` desktop | **56px** | 48-72px | 88px max (stat héro unique) |
| `font-size` tablet | **48px** | 40-56px | 64px max |
| `font-size` mobile | **40px** | 36-48px | 56px max |
| `font-weight` | **800** | 700-900 | — |
| `line-height` | **1** | 0.95-1.05 | — |
| `letter-spacing` | **-2px à -3px** | — | -4px max |
| Couleur | accent primary | — | — |

### Labels stats (« Cours rédigés », etc.)

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` | **13px** | 12-14px | 16px max |
| `font-weight` | **700** | 600-800 | — |
| `letter-spacing` | **2px** | 1.5-2.5px | 3px max |
| `text-transform` | uppercase | — | — |
| Couleur | gris atténué (`#475569` sur fond clair, `#94A3B8` sur fond sombre) | — | — |

### Stats — layout

| Style | Recommandation |
|---|---|
| **Layout default** | row equal-width 4 colonnes (chacune `widthDesktop:22`, gap 24px) |
| **Asymetric 40/30/20/10** | ⛔ NE PAS utiliser sauf brand committed avec calibration typo extrême — voir `visual-pitfalls.md` |
| **Mobile** | 2x2 grid (chaque stat `widthMobile:48`) ou stack 1 col selon densité |

### Stats — section padding

| Breakpoint | Default compact | Default standard | Hard limit |
|---|---|---|---|
| Desktop | **64/64** (compact strip) | **96/96** (standard) | 140/140 max |
| Tablet | 48/48 | 72/72 | — |
| Mobile | 40/40 | 56/56 | — |

### Stats — accent line sous chaque chiffre

| Prop | Default | Hard limit |
|---|---|---|
| `border-bottom` | **4px solid accent primary** | 6px max |

---

## FEATURES 3-COLS (cards features)

### Container card

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| Padding desktop | **56/44** vertical/horizontal | 40-64 / 32-56 | 80/72 max |
| `border-radius` | **18px** | 12-24px | 32px max |
| `border` | **1px solid** `#E2E8F0` (border subtle) | — | — |
| `box-shadow` | `rgba(15,23,42,0.06) 0 4px 24px` | — | — |
| Background | `#FFFFFF` (sur section bg `#fafafa`/`#F8FAFC`) | — | — |
| `widthDesktop` | **31.5%** (3 cols equal-height) | 30-32% | — |

### H3 card title

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` | **24-26px** | 22-28px | 32px max |
| `font-weight` | **800** | 700-900 | — |
| `line-height` | **1.3** | 1.25-1.4 | — |
| `letter-spacing` | **-0.5px** | -0.3 à -1px | -1.5px max |

### Description card

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` | **16px** | 15-17px | 18px max |
| `line-height` | **1.7** | 1.55-1.75 | — |
| Couleur | `#475569` (slate body) | — | — |

### Feature numéros (pattern features-numbered)

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` | **56px** | 48-72px | 80px max |
| `font-weight` | **800** | 700-900 | — |
| `letter-spacing` | **-2px** | — | — |
| Couleur | accent primary | — | — |

⚠️ **Ne PAS** mettre des numéros 88-120px sur les cards (déséquilibre les cards).

---

## TESTIMONIALS CARDS (3 cards avec citation)

### Container card

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| Padding | **56/48** | 40-72 / 36-64 | 80/72 max |
| `border-radius` | **24px** | 16-32px | — |
| `box-shadow` | `rgba(15,23,42,0.10-0.12) 0 8-12px 40-48px` | — | — |
| `widthDesktop` | **31.5%** | — | — |

### Quote (« en grand format)

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` | **120px** | 100-140px | 180px max |
| `font-weight` | **800** | — | — |
| `line-height` | **0.4** (laisse la citation remonter) | 0.4-0.6 | — |
| Couleur | accent primary | — | — |
| `margin` | `0 0 -20px` (rapproche la citation du `“`) | — | — |

### Citation desc

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` | **18px** | 16-20px | 22px max |
| `line-height` | **1.7** | 1.6-1.75 | — |
| `font-style` | **italic** OK pour effet éditorial | normal OK aussi | — |
| Couleur | `#0F172A` (text dark) | — | — |

### Avatar

| Prop | Default | Hard limit |
|---|---|---|
| Taille | **64×64** (rond `border-radius:50%`) | 80×80 max |
| `border` | 2px solid `#fafafa` | — |
| `box-shadow` | `rgba(15,23,42,0.08) 0 2px 8px` | — |

⚠️ **Avatars 56×56** : trop petits, perdus visuellement. **Avatars 100×100+** : prennent trop de place vs la citation.

### Auteur nom + meta

| Élément | Default | Range |
|---|---|---|
| Nom | **16px / weight 700** | 15-18px / 600-800 |
| Meta sub-info | **13px / weight 500** | 12-14px / 400-600 |
| Couleur nom | `#0F172A` | — |
| Couleur meta | `#454F5E` | — |

---

## FAQ ACCORDION

### Container wrapper

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `widthDesktop` | **62%** (centré, anti-quirk #12) | 55-70% | 85% max |
| `widthTablet` | 90% | 85-95% | — |
| `widthMobile` | 100% | — | — |
| `margin-left/right` | `auto` (centré) | — | — |

### Question

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` | **17px** | 16-19px | 20px max |
| `font-weight` | **700** | 600-800 | — |
| Padding row | **24/28** | 20-28 / 24-32 | — |
| Active color | accent primary | — | — |

### Answer

| Prop | Default | Range | Hard limit |
|---|---|---|---|
| `font-size` | **15px** | 14-16px | 17px max |
| `line-height` | **1.7** | 1.6-1.75 | — |
| Padding | `0 28px 24px` | — | — |

### Card

| Prop | Default |
|---|---|
| `border-radius` | **12px** |
| `border` | 1px solid `#E2E8F0` |
| Gap entre items | 12-16px |

---

## FORMS (uagb/forms / cf7-designer / gf-designer)

### Container formulaire

| Prop | Default | Range |
|---|---|---|
| Padding | **48px** | 40-56px |
| `border-radius` | **16px** | 12-20px |
| `border` | 1px solid `#E2E8F0` | — |
| `box-shadow` | `rgba(15,23,42,0.08) 0 8px 32px` | — |

### Labels

| Prop | Default |
|---|---|
| `font-size` | **12px** |
| `font-weight` | **700** |
| `letter-spacing` | **1.5px** |
| `text-transform` | uppercase |
| Couleur | `#454F5E` (slate-700) |
| `margin-bottom` | 6px |

### Inputs

| Prop | Default | Hard limit |
|---|---|---|
| `padding` | **14/16** | 12-16 / 14-20 |
| `font-size` | **16px** (mobile = pas de zoom iOS) | NE JAMAIS < 16px sur mobile |
| `border-radius` | **8px** | 6-12px |
| `border` focus | accent primary 1px + box-shadow `accent rgba .15` 0 0 0 3px | — |

### Submit button

| Prop | Default |
|---|---|
| `padding` | **16/36** |
| `font-size` | **16px** |
| `font-weight` | **700** |
| `border-radius` | **8px** |

---

## CTA BANNER FINAL

### H2 CTA final

| Breakpoint | Default | Range | Hard limit |
|---|---|---|---|
| Desktop | **54px** | 46-62px | 72px max |
| Tablet | 40px | 36-48px | — |
| Mobile | 30px | 28-36px | — |

| Prop | Default |
|---|---|
| `font-weight` | **800** |
| `line-height` | **1.1** |
| `letter-spacing` | **-1.5px** |

### Section padding CTA final

| Breakpoint | Default | Hard limit |
|---|---|---|
| Desktop | **140/140** | 200/200 max |
| Tablet | 96/96 | — |
| Mobile | 72/72 | — |

### CTA primary + secondary final

Idem hero CTAs (cf section Hero CTAs).

---

## SECTION RHYTHM (alternance bg)

| Section | Bg recommandé |
|---|---|
| Hero | dark `#0F172A` ou image background avec overlay |
| Stats | gris clair `#F8FAFC` ou `#fafafa` |
| Story | white `#FFFFFF` |
| Features | gris clair `#F8FAFC` ou `#fafafa` |
| Testimonials | white `#FFFFFF` |
| FAQ | gris clair `#F8FAFC` ou `#fafafa` |
| CTA final | dark `#0F172A` ou accent dramatic |

⚠️ **Ne JAMAIS** 2 sections root consécutives avec **même bg** (cf quirk #15 : mur de blocs, l'œil n'a plus de repère).

---

## COULEURS ACCENT — palette types

Quand tu n'as pas Astra (ou que les slots variables sont risqués), utiliser ces palettes pré-validées par registre :

### Tech / SaaS / corporate (default)

| Rôle | Hex |
|---|---|
| Primary | `#2563EB` (blue-600) |
| Primary hover | `#1D4ED8` |
| Text dark | `#0F172A` |
| Text body | `#475569` |
| Text muted | `#64748B` |
| Bg light | `#F8FAFC` |
| Bg white | `#FFFFFF` |
| Bg dark hero | `#0F172A` |
| Border | `#E2E8F0` |
| Accent secondaire (success / open) | `#10B981` |

### Éditorial / formation / luxe

| Rôle | Hex |
|---|---|
| Primary | `#FD9800` (orange WPF) ou `#1A1A1A` (noir typo) |
| Text dark | `#0F172A` ou `#1A1A1A` |
| Text body | `#454F5E` |
| Bg cream | `#FEF9E1` ou `#F9F0C8` |
| Bg white | `#FFFFFF` |

### E-commerce / retail

| Rôle | Hex |
|---|---|
| Primary | `#DC2626` (red-600 sale) ou `#16A34A` (green-600 in stock) |
| Text dark | `#1F2937` |

### Santé / nature

| Rôle | Hex |
|---|---|
| Primary | `#16A34A` (green-600) ou `#0EA5E9` (sky-500) |
| Bg light | `#F0FDF4` ou `#F0F9FF` |

---

## Comment cette doc évolue

À chaque session où l'instance Claude doit **inventer** une valeur typo/spacing parce qu'elle n'est pas dans ce baseline, **ajouter cette valeur** au baseline avec son default + range + hard limit.

Goal v1.1+ : 100 % des valeurs typo/spacing utilisables sont dans ce baseline. Plus jamais d'improvisation.