# Pattern : Marketing Buttons (uagb/marketing-button)

> **Use case** : bouton CTA enrichi avec **prefix + label + suffix** (par exemple : « Inscris-toi · 199 € · puis 19 € / mois ») et icône optionnelle. Différent de `uagb/buttons-child` qui est un simple bouton à un label. Idéal pour pricing, hero CTA fort, banner conversion.

> **Bloc Spectra** : `uagb/marketing-button`. Anatomie : `[icon] [prefix] [label] [suffix]`. Les 3 éléments texte ont chacun leur taille / couleur / weight indépendants. Effet visuel : bouton « cinq étoiles » qui guide le regard.

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{MB_PREFIX}}` | Texte petit avant le label (eyebrow inline) | `À partir de` |
| `{{MB_LABEL}}` | Label principal grand | `199 €` |
| `{{MB_SUFFIX}}` | Texte petit après le label | `puis 19 € / mois` |
| `{{MB_LINK}}` | URL cible | `/inscription/` |
| `{{MB_ICON}}` | Icône Spectra (optionnelle, FA5 Free) | `arrow-right` |
| `{{MB_VARIANT}}` | `primary` / `secondary` / `ghost` | `primary` |

## Block markup

```html
<!-- wp:uagb/marketing-button {"block_id":"{slug}-mb-cta","prefixText":"{{MB_PREFIX}}","title":"{{MB_LABEL}}","subText":"{{MB_SUFFIX}}","iconImage":{"url":"","slug":""},"icon":"{{MB_ICON}}","iconPosition":"after-text","iconSpace":12,"iconSize":18,"link":"{{MB_LINK}}","linkTarget":false,"borderStyle":"none","borderRadius":12,"borderRadiusUnit":"px","color":"#FFFFFF","background":"var(--ast-global-color-0)","hColor":"#FFFFFF","hBackground":"var(--ast-global-color-1)","prefixSpace":4,"subTextSpace":4,"vPadding":20,"hPadding":36,"prefixFontSizeDesktop":12,"prefixFontWeight":"700","titleFontSizeDesktop":24,"titleFontWeight":"800","subTextFontSizeDesktop":12,"subTextFontWeight":"500","letterSpacing":0.3} -->
<div class="wp-block-uagb-marketing-button uagb-block-{slug}-mb-cta">
  <a class="uagb-marketing-btn__link" href="{{MB_LINK}}" rel="follow noopener" role="button">
    <div class="uagb-marketing-btn__title-wrap">
      <p class="uagb-marketing-btn__prefix">{{MB_PREFIX}}</p>
      <h3 class="uagb-marketing-btn__title">{{MB_LABEL}}</h3>
      <p class="uagb-marketing-btn__sub-text">{{MB_SUFFIX}}</p>
    </div>
    <span class="uagb-marketing-btn__icon-wrap">
      <i class="fas fa-{{MB_ICON}}" aria-hidden="true"></i>
    </span>
  </a>
</div>
<!-- /wp:uagb/marketing-button -->
```

## CSS overrides recommandés

```css
/* Marketing button primary — pleine couleur orange WPF dramatique */
.uagb-block-{slug}-mb-cta .uagb-marketing-btn__link {
  display: inline-flex !important;
  align-items: center !important;
  gap: 16px !important;
  text-decoration: none !important;
  border-radius: 12px !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease !important;
  box-shadow: 0 8px 24px rgba(15,23,42,0.20) !important;
}

.uagb-block-{slug}-mb-cta .uagb-marketing-btn__link:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 12px 32px rgba(15,23,42,0.30) !important;
}

/* Prefix — uppercase tracking petit */
.uagb-block-{slug}-mb-cta .uagb-marketing-btn__prefix {
  text-transform: uppercase !important;
  letter-spacing: 1.5px !important;
  margin: 0 0 2px !important;
  opacity: 0.9 !important;
}

/* Label — gros et bold */
.uagb-block-{slug}-mb-cta .uagb-marketing-btn__title {
  margin: 0 !important;
  line-height: 1 !important;
}

/* Sub-text — petit gris clair */
.uagb-block-{slug}-mb-cta .uagb-marketing-btn__sub-text {
  margin: 4px 0 0 !important;
  opacity: 0.85 !important;
}

/* Icon — flèche / play à droite */
.uagb-block-{slug}-mb-cta .uagb-marketing-btn__icon-wrap {
  flex-shrink: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 40px !important;
  height: 40px !important;
  border-radius: 50% !important;
  background: rgba(255,255,255,0.18) !important;
}

/* Mobile : taille label réduite, padding ajusté */
@media (max-width: 600px) {
  .uagb-block-{slug}-mb-cta .uagb-marketing-btn__title { font-size: 20px !important; }
  .uagb-block-{slug}-mb-cta .uagb-marketing-btn__link { gap: 12px !important; padding: 16px 24px !important; }
}
```

## Pièges

| # | Quirk |
|---|---|
| **Anatomie 3 textes** | Bien différencier les 3 textes (`prefixText` / `title` / `subText`). Confondre `title` avec `prefixText` casse la hiérarchie visuelle |
| **Icône whitelist** | L'icône DOIT être dans `references/spectra-icons-list.md`. `arrow-right`, `play`, `check`, `external-link-alt` sont OK. `caret-right`, `chevron-right` aussi. Si l'icône n'existe pas, fallback identique à toutes les marketing-buttons (cf quirk #8) |
| **Inline styles** | Comme tous les blocs Spectra, NE PAS mettre de `style="..."` inline dans le innerContent (cf quirk #4). Tout passe par `_uag_custom_page_level_css` |
| **Couleur fond** | Préférer `var(--ast-global-color-0)` (primary stable) pour le bg. Éviter slot 7 (cf quirk #7 : noir massif sur certaines palettes) |
| **Hover lift** | Sur mobile, le `transform: translateY(-2px) hover` est ressenti seulement au tap-and-hold. Pas un piège, juste un trade-off à connaître |

## Variantes

### Variante 1 — Pricing CTA (cas d'usage le plus fréquent)

Pour pricing card, le marketing-button remplace avantageusement un simple bouton :

- Prefix : `À partir de`
- Label : `199 €`
- Suffix : `puis 19 € / mois`
- Icon : `arrow-right`

L'œil voit immédiatement le prix mensuel + le commitment annuel, en 1 seul bouton.

### Variante 2 — Hero CTA dramatic (formation, SaaS)

- Prefix : `Promotion -30%`
- Label : `Réserve ta place`
- Suffix : `dernières 3 places`
- Icon : `bolt` (signal urgence)

L'eyebrow promotion + label action + suffix scarcity = trio classique de conversion.

### Variante 3 — Download / lead magnet

- Prefix : `Téléchargement gratuit`
- Label : `Récupérer le PDF`
- Suffix : `38 pages · sans email requis`
- Icon : `file-pdf`

### Variante 4 — App store / play store

- Prefix : `Disponible sur`
- Label : `App Store`
- Suffix : `iOS 16+`
- Icon : `mobile-alt`

Doubler le bloc (1 par store) dans un container row.

## Test post-génération

1. Vérifier les 3 textes visibles (prefix / label / suffix), bien hiérarchisés
2. Hover desktop : lift 2px + shadow plus forte
3. Tap mobile : feedback visuel 200ms
4. a11y : `<a role="button">` OK, contraste prefix/label/suffix vs background ≥ 4.5:1 (WCAG AA)
5. Si icône : vérifier qu'elle s'affiche bien (pas le placeholder fallback)

## Pour aller plus loin

- Boutons standard simples : voir `patterns/cta-banner-fullwidth.md` (utilise `uagb/buttons` + `uagb/buttons-child`)
- Whitelist icônes validées : voir `references/spectra-icons-list.md`
- Composer plusieurs marketing-buttons : container `direction:row` + `gap:24` + 2-3 marketing-buttons côte à côte
