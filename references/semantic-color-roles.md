# Convention couleur — slots Astra GARANTIS vs hex neutres

> **Origine** : rapport visuel cours-ndrc.fr du 02/05/2026. La v0.8.2 supposait que tous les slots `--ast-global-color-X` avaient une sémantique stable d'une palette à l'autre (ex : `color-7` = off-white). C'est faux. Sur palette_3 d'Astra, `color-7 = #141006` (presque noir). Tous les patterns qui utilisaient `color-7` comme fond clair produisaient des sections noires illisibles.

## Slots GARANTIS (vérifié sur les 11 presets Astra natifs)

Ces 5 slots ont une sémantique stable. On peut les utiliser via `var(--ast-global-color-X)` sans risque, sur n'importe quelle palette Astra (default + 11 presets natifs + palettes utilisateur qui suivent la convention).

| Slot | Rôle garanti | Plage luminance | Utilisation pattern |
|------|--------------|-----------------|---------------------|
| `0` | **Primary** (CTA, accents, links) | saturé (S > 0.5 typique) | `iconColor`, bouton primary `backgroundColor`, link color |
| `1` | **Secondary primary** (hover state, variant) | saturé, plus sombre que 0 | bouton primary `hoverBackgroundColor` |
| `2` | **Heading text** | < 0.20 (très sombre) | `headingColor` sur fond clair |
| `3` | **Body text** | 0.15-0.45 (sombre dim) | `subHeadingColor`, `descColor`, body |
| `5` | **Body bg** | > 0.95 (presque blanc) | fond de section principale, fond de card |

## Slots VARIABLES (à NE PAS utiliser pour des rôles fixes)

Ces slots changent radicalement de sémantique selon la palette. Utiliser à ses risques.

| Slot | Sur defaults Astra | Sur palette_3 | Sur palette_5 (rouge) |
|------|---------------------|---------------|------------------------|
| `4` | identique au 0 (accent) | `#FEF9E1` (crème pâle) | variable |
| `6` | `#f5f5f5` (off-white) | `#F9F0C8` (jaune pâle) | variable |
| `7` | `#fafafa` (off-white) | `#141006` (presque noir) | variable |
| `8` | `#e7e7e7` (border) | `#222222` (gris foncé) | variable |

**Conclusion** : ne JAMAIS utiliser `var(--ast-global-color-4|6|7|8)` dans un pattern pour un rôle fixe. Soit utiliser un hex neutre hardcodé, soit appeler `wpf_skill_resolve_color()` à la génération pour résoudre dynamiquement selon la palette active.

## Hex neutres (alternative sûre)

Pour les rôles non-couverts par les slots garantis, le skill utilise des hex neutres qui fonctionnent visuellement sur toutes les palettes (clarté/contraste universels) :

| Rôle | Hex hardcodé | Justification |
|------|--------------|---------------|
| `bg_section_alt` | `#fafafa` | Off-white universel, pas dépendant de la palette |
| `bg_card_alt` | `#ffffff` | White pur, le plus contrastant possible |
| `bg_dark` | `#0f172a` | Dark slate (un peu plus chaud qu'un noir pur) |
| `border_subtle` | `#e5e7eb` | Border gris très clair (Tailwind gray-200) |
| `border_strong` | `#9ca3af` | Border gris medium (Tailwind gray-400) |
| `text_inverse` | `#ffffff` | Texte sur fond primary saturé : white toujours OK |
| `shadow_neutral` | `rgba(0,0,0,0.08)` | Shadow douce, neutre |
| `shadow_strong` | `rgba(0,0,0,0.16)` | Shadow plus marquée pour featured |

**Tradeoff assumé** : ces hex ne propagent pas au changement de palette Astra. Mais ils garantissent que la page reste lisible sur n'importe quelle palette. Pour un site qui doit absolument se synchroniser avec la palette Astra à 100%, utiliser `wpf_skill_resolve_color()` au moment de la génération.

## Convention pratique pour les patterns

```html
<!-- ✅ BON : slots garantis + hex neutres -->
<!-- wp:uagb/container {"backgroundColor":"var(--ast-global-color-5)"} -->  <!-- bg main -->
<!-- wp:uagb/info-box {"backgroundColor":"#fafafa"} -->                       <!-- bg card alt -->
<!-- wp:uagb/buttons-child {"borderColor":"#e5e7eb"} -->                      <!-- border subtle -->
<!-- wp:uagb/buttons-child {"backgroundColor":"var(--ast-global-color-0)","color":"#ffffff"} --> <!-- CTA primary -->

<!-- ❌ MAUVAIS : slots variables pour rôles fixes -->
<!-- wp:uagb/container {"backgroundColor":"var(--ast-global-color-7)"} -->  <!-- 🔴 noir sur palette_3 -->
<!-- wp:uagb/info-box {"backgroundColor":"var(--ast-global-color-4)"} -->     <!-- 🔴 crème sur palette_3 -->
<!-- wp:uagb/buttons-child {"borderColor":"var(--ast-global-color-8)"} -->    <!-- 🔴 gris foncé sur palette_3 -->
```

## Résolution dynamique avec `wpf_skill_resolve_color()`

Pour les patterns avancés qui veulent une couleur exacte selon la palette active :

```php
require_once 'scripts/resolve-palette.php';
$palette = wpf_skill_get_active_palette();
$bg_hex = wpf_skill_resolve_color('bg_section_alt', $palette);
// Sur palette_3 : retourne le slot le plus clair après color-5 (ex: #FEF9E1)
// Sur defaults : retourne #fafafa
```

Rôles supportés :

- `bg_page`, `bg_section`, `bg_section_alt`, `bg_card`, `bg_dark`
- `text_heading`, `text_body`, `text_muted`, `text_inverse`
- `accent_primary`, `accent_secondary`
- `border_subtle`, `border_strong`
- `shadow_neutral`, `shadow_strong`, `shadow_hover`

Voir `scripts/resolve-palette.php` pour le détail de chaque rôle.

## Commande de transpile (post-traitement)

Pour les patterns qui restent avec les slots variables, le skill peut les transpiler avant POST :

```bash
php scripts/resolve-palette.php transpile < markup.html > markup-safe.html
```

Cette commande détecte les `var(--ast-global-color-{4,6,7,8})` sur des attrs sémantiquement risqués (`backgroundColor`, `borderColor`, `iconBgColor`) et les remplace par le hex sémantiquement correct selon la palette active.

## Test de contraste WCAG AA

Le `visual-audit.php` v0.9.0 ajoute un check 9 qui résout les couleurs et calcule le ratio de contraste WCAG AA :

- < 1.5:1 → P0 illisible (texte invisible)
- < 3.0:1 sur heading → P1 (large text)
- < 4.5:1 sur body text → P1 (normal text)

Output JSON contient `wcag_violations[]` avec les paires problématiques résolues en hex.
