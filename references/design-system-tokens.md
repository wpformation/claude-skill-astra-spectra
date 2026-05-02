# Design System Tokens

> **Rôle** : règles de cohérence design system. Tous les patterns du skill utilisent les variables CSS Astra (`--ast-global-color-X`) pour garantir que le changement de palette se propage à 100 % de la page sans intervention manuelle.

## Pourquoi des tokens et pas des hex ?

Validé au POC du 02/05/2026 :
- **199 occurrences** de `var(--ast-global-color-X)` dans une page hybride core+Spectra rendue
- **0 hex hardcoded** dans les blocs uagb (toujours via variables CSS)
- **Propagation automatique** : changer une couleur dans Astra met à jour tous les blocs Spectra de toutes les pages, sans toucher au markup

C'est ce qui permet aux templates du skill d'être **rebrandable en 30 secondes** : on change la palette, tout suit.

## Mapping des 9 tokens Astra

| Token | Slot Astra | Usage suggéré pour le skill |
|-------|-----------|----------------------------|
| `--ast-global-color-0` | Primary | Couleur primaire (CTA, liens, accents, icônes actives) |
| `--ast-global-color-1` | Primary darker | Hover/active state des boutons, icônes secondaires |
| `--ast-global-color-2` | Heading dark | Titres H1/H2 (texte foncé sur fond clair) |
| `--ast-global-color-3` | Body text | Paragraphes, sous-titres |
| `--ast-global-color-4` | White / page bg | Background page principale, texte sur fond foncé |
| `--ast-global-color-5` | Light bg / cards | Sections alternées, cards background, footer light |
| `--ast-global-color-6` | Dark variant | Footer dark, headers fixes |
| `--ast-global-color-7` | Border / divider | Bordures, separators, hover backgrounds |
| `--ast-global-color-8` | Black / emphasis | Texte ultra-emphasé, fond contraste max |

## Recommandations d'usage par bloc

### Hero / Container principal

```json
{
  "backgroundColor": "var(--ast-global-color-4)",   // bg page (white)
  // ou pour hero contrasté :
  "backgroundColor": "var(--ast-global-color-2)"    // bg dark
}
```

### Heading H1/H2

```json
{
  "headingColor": "var(--ast-global-color-2)",     // dark sur clair
  "subHeadingColor": "var(--ast-global-color-3)"   // body text
}
// OU sur background sombre :
{
  "headingColor": "var(--ast-global-color-4)",     // white
  "subHeadingColor": "var(--ast-global-color-7)"   // gris clair
}
```

### Buttons CTA

```json
// CTA primaire
{
  "backgroundColor": "var(--ast-global-color-0)",
  "color": "var(--ast-global-color-4)",
  "hoverBackgroundColor": "var(--ast-global-color-1)"
}

// CTA secondaire (ghost)
{
  "backgroundColor": "transparent",
  "color": "var(--ast-global-color-0)",
  "borderColor": "var(--ast-global-color-0)",
  "borderWidth": 2,
  "hoverBackgroundColor": "var(--ast-global-color-0)",
  "hoverColor": "var(--ast-global-color-4)"
}
```

### Info-box (features)

```json
{
  "iconColor": "var(--ast-global-color-0)",         // primary
  "iconBgColor": "var(--ast-global-color-5)",       // light bg
  "headingColor": "var(--ast-global-color-2)",      // dark
  "subHeadingColor": "var(--ast-global-color-3)"    // body
}
```

### FAQ (uagb/faq)

```json
{
  "iconActiveColor": "var(--ast-global-color-0)",   // primary quand expanded
  "iconColor": "var(--ast-global-color-3)",         // body text quand collapsed
  "headingColor": "var(--ast-global-color-2)",      // titre question
  "answerColor": "var(--ast-global-color-3)"        // texte réponse
}
```

### Testimonial / Team

```json
{
  "headingColor": "var(--ast-global-color-2)",      // nom personne
  "subHeadingColor": "var(--ast-global-color-3)",   // poste / company
  "iconColor": "var(--ast-global-color-0)",         // étoiles, guillemets décoratifs
  "descColor": "var(--ast-global-color-3)"          // texte témoignage
}
```

### Counter (stats animées)

```json
{
  "numberColor": "var(--ast-global-color-0)",       // chiffre primary
  "headingColor": "var(--ast-global-color-2)",      // label sous chiffre
  "iconColor": "var(--ast-global-color-0)"          // icône optionnelle
}
```

### Timeline

```json
{
  "iconColor": "var(--ast-global-color-0)",         // dot timeline
  "iconBg": "var(--ast-global-color-4)",
  "lineColor": "var(--ast-global-color-7)",         // ligne verticale
  "headingColor": "var(--ast-global-color-2)",      // titre étape
  "subHeadingColor": "var(--ast-global-color-3)"    // description étape
}
```

## Cas spéciaux

### Backgrounds avec overlay (rgba)

Pour les overlay sombre/clair sur images, on utilise `rgba()` directement (pas un token Astra) :

```json
{
  "backgroundImageColor": "rgba(0,0,0,0.55)"   // overlay sombre 55% sur hero image
}
```

### Borders subtiles

Privilégier les `rgba()` pour les bordures translucides (glassmorphism) :

```json
{
  "borderColor": "rgba(255,255,255,0.3)"   // bordure blanche translucide pour glass
}
```

### Gradients

```json
{
  "backgroundGradientColor1": "var(--ast-global-color-0)",   // primary
  "backgroundGradientColor2": "var(--ast-global-color-1)",   // primary darker
  "backgroundGradientAngle": 135
}
```

## Palettes pré-construites du skill (5 thèmes)

Le skill embarque 5 palettes prêtes à l'emploi (en plus des 11 presets Astra natifs). Listées dans `modules/design-tokens/wpf-palettes.md`.

| Nom | Primary | Heading | Body | Bg | Card bg | Border |
|-----|---------|---------|------|----|---------|--------|
| **wpf-orange** (signature WPF) | #FF8C00 | #0E0E14 | #334155 | #FFFFFF | #F0F5FA | #D1D5DB |
| **wpf-corporate** | #1E40AF | #0F172A | #475569 | #FFFFFF | #F8FAFC | #E2E8F0 |
| **wpf-creative** | #8B5CF6 | #1E1B4B | #4C1D95 | #FFFFFF | #FAF5FF | #DDD6FE |
| **wpf-minimal** | #18181B | #09090B | #52525B | #FFFFFF | #FAFAFA | #E4E4E7 |
| **wpf-dark** | #FF8C00 | #FFFFFF | #94A3B8 | #0F172A | #1E293B | #334155 |
| **wpf-vibrant** | #EC4899 | #831843 | #BE185D | #FFFFFF | #FDF2F8 | #FBCFE8 |

## Mode hors-Astra

Si Astra n'est pas activé sur le site cible, le skill injecte automatiquement un CSS inline qui définit les variables `--ast-global-color-X` dans `:root`. Logique dans `scripts/apply-design-tokens.php`.

Cela garantit que le markup généré (qui utilise `var(--ast-global-color-X)`) fonctionne quel que soit le thème.

## Anti-patterns

❌ **JAMAIS** :
```json
{ "backgroundColor": "#FF8C00" }   // hex hardcoded
{ "color": "blue" }                 // nom de couleur CSS
{ "headingColor": "rgba(255,140,0,1)" }  // rgba avec couleur statique au lieu d'un token
```

✅ **TOUJOURS** :
```json
{ "backgroundColor": "var(--ast-global-color-0)" }    // token Astra
{ "color": "var(--ast-global-color-4)" }              // token Astra
{ "backgroundColor": "rgba(0,0,0,0.55)" }             // OK pour overlay (pas de couleur "marque")
```

## Validation

Le script `scripts/validate-block-markup.php` détecte les hex hardcoded dans les attrs couleur et émet un warning pour chacun. À chaque génération, le skill doit s'assurer du 0 warning sur les couleurs.

## Pour aller plus loin

- Recettes wow `uagb/container` : `../modules/spectra/container-wow-recipes.md`
- Module Astra (presets, palette pilotage) : `../modules/astra/`
- Mode hors-Astra (CSS fallback) : `../modules/design-tokens/`
