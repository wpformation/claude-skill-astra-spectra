# Pattern : Icon List (uagb/icon-list)

> **Use case** : liste à puces avec icônes (« features incluses » dans un pricing, « ce que tu apprends » dans une landing formation, « horaires » dans une page contact). Différent d'une simple `core/list` : chaque item a une icône colorée, taille et espacement contrôlables.

> **Bloc Spectra** : `uagb/icon-list` (parent) + `uagb/icon-list-child` (items). Layouts : `vertical` (par défaut, recommandé) ou `horizontal` (pour navigation pills, social share).

> **Note** : icon-list est aussi utilisé en sub-component dans `pricing-3-tiers.md`, `testimonials-cards.md` (étoiles), `social-share.md`. Ce pattern documente l'usage standalone.

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{ICONLIST_LAYOUT}}` | `vertical` / `horizontal` | `vertical` |
| `{{ICONLIST_GAP}}` | Espacement entre items en px | `12` |
| `{{ICONLIST_ICON_COLOR}}` | Couleur icônes | `var(--ast-global-color-0)` |
| `{{ICONLIST_LABEL_COLOR}}` | Couleur labels | `#0F172A` |
| `{{ICONLIST_ICON_SIZE}}` | Taille icônes en px | `20` |
| `{{ICONLIST_ITEMS[]}}` | Array d'items `{icon, label, link?}` | (cf ci-dessous) |

### Structure d'un item

```json
{
  "icon": "check-circle",
  "label": "6 modules vidéo (15h+)",
  "link": null
}
```

Si `link` non null : l'item devient un lien cliquable.

## Block markup

```html
<!-- wp:uagb/icon-list {"block_id":"{slug}-list","icon_layout":"{{ICONLIST_LAYOUT}}","gap":{{ICONLIST_GAP}},"size":{{ICONLIST_ICON_SIZE}},"sizeUnit":"px","label-display":"true","icon_color":"{{ICONLIST_ICON_COLOR}}","icon_hover_color":"var(--ast-global-color-1)","label_color":"{{ICONLIST_LABEL_COLOR}}","label_hover_color":"var(--ast-global-color-0)","fontSize":16,"fontWeight":"500","lineHeight":1.6,"alignment":"left"} -->
<div class="wp-block-uagb-icon-list uagb-block-{slug}-list uagb-icon-list__layout-{{ICONLIST_LAYOUT}}">
  <ul class="uagb-icon-list__wrap">

    <!-- wp:uagb/icon-list-child {"block_id":"{slug}-item-1","label":"6 modules vidéo (15h+)","icon":"check-circle"} -->
    <li class="uagb-icon-list-repeater">
      <a class="uagb-icon-list__source-wrap" href="">
        <span class="uagb-icon-list__source-icon"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
        <span class="uagb-icon-list__label">6 modules vid&eacute;o (15h+)</span>
      </a>
    </li>
    <!-- /wp:uagb/icon-list-child -->

    <!-- wp:uagb/icon-list-child {"block_id":"{slug}-item-2","label":"Exercices pratiques","icon":"check-circle"} -->
    <li class="uagb-icon-list-repeater">
      <a class="uagb-icon-list__source-wrap" href="">
        <span class="uagb-icon-list__source-icon"><i class="fas fa-check-circle" aria-hidden="true"></i></span>
        <span class="uagb-icon-list__label">Exercices pratiques</span>
      </a>
    </li>
    <!-- /wp:uagb/icon-list-child -->

    <!-- ... -->

  </ul>
</div>
<!-- /wp:uagb/icon-list -->
```

## CSS overrides recommandés

```css
/* Layout vertical — items stack verticalement (par défaut Spectra peut casser, voir quirk #11) */
.uagb-block-{slug}-list.uagb-icon-list__layout-vertical .uagb-icon-list__wrap {
  display: flex !important;
  flex-direction: column !important;
  gap: 12px !important;
  list-style: none !important;
  padding-left: 0 !important;
  margin: 0 !important;
}

.uagb-block-{slug}-list.uagb-icon-list__layout-vertical .uagb-icon-list-repeater {
  display: flex !important;
  align-items: center !important;
  gap: 14px !important;
}

/* Layout horizontal — items en ligne, wrap si dépasse */
.uagb-block-{slug}-list.uagb-icon-list__layout-horizontal .uagb-icon-list__wrap {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 16px !important;
  list-style: none !important;
  padding-left: 0 !important;
  margin: 0 !important;
}

/* Icon — circle bg subtil pour effet card */
.uagb-block-{slug}-list .uagb-icon-list__source-icon {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  flex-shrink: 0 !important;
  width: 32px !important;
  height: 32px !important;
  border-radius: 50% !important;
  background: rgba(253,152,0,0.12) !important;  /* tinted accent */
  color: var(--ast-global-color-0) !important;
  font-size: 16px !important;
}

/* Label — taille + couleur */
.uagb-block-{slug}-list .uagb-icon-list__label {
  font-size: 16px !important;
  font-weight: 500 !important;
  line-height: 1.6 !important;
  color: #0F172A !important;
}

/* Hover (si liens) */
.uagb-block-{slug}-list .uagb-icon-list__source-wrap:hover .uagb-icon-list__source-icon {
  background: var(--ast-global-color-0) !important;
  color: #FFFFFF !important;
}
```

## Pièges

| # | Quirk |
|---|---|
| **#11 layout vertical** | Spectra peut rendre le layout `vertical` en row sur certains thèmes (cf quirk #11 dans `references/spectra-attributes-quirks.md`). Forcer en CSS overrides : `display: flex; flex-direction: column;` |
| **Icônes whitelist** | Toujours utiliser des icônes de `references/spectra-icons-list.md`. `check`, `check-circle`, `check-square` sont sûrs. `tick` n'existe pas → fallback identique sur tous les items |
| **Apostrophes labels** | Si un label contient une apostrophe (`l'élève`), encoder en HTML entity (`l&rsquo;élève`) ou utiliser ASCII selon convention site cible (cf `references/i18n-rules.md`) |
| **Sub-list nested** | `uagb/icon-list-child` ne peut pas contenir d'autre `uagb/icon-list-child` (pas de hiérarchie). Pour sub-bullets, utiliser `core/list` imbriqué |
| **Lien vide** | Si `link: null`, mettre `href=""` rend le label encore cliquable (mauvais UX). Préférer ne pas wrapper dans `<a>` du tout : `<span class="uagb-icon-list__source-wrap">` |
| **Alignment center** | Pour titre + 4 items centrés (style hero), mettre `alignment: "center"` ET sur chaque `.uagb-icon-list-repeater` : `justify-content: center;` |

## Variantes

### Variante 1 — Liste features dans pricing card

Layout `vertical`, icône `check-circle` orange, label noir, gap 12px. Le cas d'usage le plus fréquent.

### Variante 2 — Horaires page contact

Layout `vertical`, icône `clock` (heure) ou `calendar-alt` (jour). Format :
- 🕒 Lundi - Vendredi : 9h - 18h
- 🕒 Samedi : 10h - 17h
- 🕒 Dimanche : Fermé

### Variante 3 — Coordonnées (téléphone, email, adresse)

Layout `vertical`, icônes `phone-alt`, `envelope`, `map-marker-alt`. Avec liens cliquables :
- 📞 +33 1 23 45 67 89 → `tel:+33123456789`
- ✉️ contact@site.com → `mailto:contact@site.com`
- 📍 15 rue de la République, 13002 Marseille → `https://maps.google.com/?q=...`

### Variante 4 — Social links horizontaux

Layout `horizontal`, icônes brand (`facebook-f`, `twitter`, `instagram`, `linkedin-in`, `youtube`). Pas de label visible (icon-only). Pour footer ou hero CTA secondaire.

Préférer dans ce cas : `patterns/social-share.md` qui est dédié.

### Variante 5 — Étoiles 5/5 testimonials

Layout `horizontal`, icône `star`, gap 3px, taille 18px, couleur `#FBBF24` (jaune). 5× le même item. Pour testimonials.

Préférer le sub-pattern dans `patterns/testimonials-cards.md` (variante 1).

## Test post-génération

1. Vérifier le layout vertical (items stack) ou horizontal (wrap row)
2. Hover sur un item (si link) → couleur icon + label change
3. Vérifier toutes les icônes affichées (pas de fallback identique)
4. Mobile : layout reste lisible, pas de débordement horizontal
5. a11y : `<i aria-hidden="true">` sur icônes décoratives, le `<span>` label est lu par screen readers

## Pour aller plus loin

- Whitelist icônes validées : `references/spectra-icons-list.md`
- Pricing avec icon-list features : `patterns/pricing-3-tiers.md`
- Star rating dédié : `patterns/star-rating.md`
- Social share dédié : `patterns/social-share.md`
