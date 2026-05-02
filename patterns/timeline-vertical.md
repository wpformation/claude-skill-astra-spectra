# Pattern : Timeline Vertical

> **Use case** : timeline chronologique avec étapes datées (parcours de vie d'une entreprise, étapes d'un projet, roadmap produit, parcours formation). Layout vertical avec ligne centrale et events alternés gauche/droite.

## Bloc Spectra utilisé : `uagb/timeline`

Spectra a un bloc timeline natif. Layout horizontal ou vertical, avec ou sans dates.

## Structure

```
uagb/container#timeline-section (root, alignfull, padding 120px)
  ├─ uagb/info-box#timeline-title (eyebrow + H2 center)
  └─ uagb/timeline#timeline (orientation:vertical, layout:right)
      ├─ Events array dans l'attribut « items »
      └─ Chaque event : { date, title, description, icon (validé) }
```

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{TIMELINE_EYEBROW}}` | Kicker | `Notre histoire` |
| `{{TIMELINE_HEADING}}` | H2 | `13 ans &agrave; faire grandir le BTS NDRC.` |
| `{{EVENT_N_DATE}}` | Date événement | `2012` |
| `{{EVENT_N_TITLE}}` | Titre événement | `Lancement du site` |
| `{{EVENT_N_DESC}}` | Description | `Premiers cours mis en ligne...` |
| `{{ACCENT_COLOR}}` | Couleur ligne + dots | `#FD9800` |

## Block markup (squelette)

```html
<!-- wp:uagb/timeline {"block_id":"{slug}-timeline","timeline_item":4,"orientation":"vertical","timelinAlignment":"center","verticalSpace":24,"connectorBg":"{{ACCENT_COLOR}}","stack":"tablet","date_color":"#454F5E","heading_color":"#0F172A","description_color":"#454F5E","background_color":"#ffffff","borderRadius":12,"borderColor":"#e5e7eb","borderwidth":1,"icon_color":"#ffffff","icon_bg_color":"{{ACCENT_COLOR}}","icon_size":18,"icon_focus":"{{ACCENT_COLOR}}","headingTag":"h3","headFontSize":22,"headFontWeight":700,"subHeadFontSize":15,"timelineItem":[{"date":"2012","heading":"Lancement","description":"...","icon":"flag-checkered"},{"date":"2018","heading":"100 cours","description":"...","icon":"trophy"},{"date":"2022","heading":"50K visiteurs/mois","description":"...","icon":"chart-line"},{"date":"2025","heading":"227 cours, 22 QCM","description":"...","icon":"star"}]} -->
<div class="wp-block-uagb-timeline uagb-block-{slug}-timeline">
  <!-- Spectra render auto à partir de timelineItem array -->
</div>
<!-- /wp:uagb/timeline -->
```

## CSS overrides (`_uag_custom_page_level_css`)

```css
/* Connector ligne centrale plus marquée */
.uagb-block-{slug}-timeline .uagb-timeline__line {
  background: linear-gradient(180deg, {{ACCENT_COLOR}} 0%, rgba(253,152,0,0.3) 100%) !important;
  width: 3px !important;
}

/* Event icons plus gros */
.uagb-block-{slug}-timeline .uagb-timeline__marker {
  width: 56px !important;
  height: 56px !important;
}

/* Event card */
.uagb-block-{slug}-timeline .uagb-timeline__day-new {
  font-size: 28px !important;
  font-weight: 800 !important;
  color: {{ACCENT_COLOR}} !important;
  letter-spacing: -1px !important;
}

/* Event title */
.uagb-block-{slug}-timeline .uagb-timeline__heading {
  font-size: 22px !important;
  font-weight: 700 !important;
  margin-top: 12px !important;
}
```

## Variantes

### Variante 1 — Timeline horizontale

`orientation: "horizontal"` → events disposés sur une ligne horizontale. Utile pour roadmap produit (Q1 → Q2 → Q3 → Q4).

### Variante 2 — Timeline avec icônes custom par event

Chaque event peut avoir son icône Spectra (validée via `references/spectra-icons-list.md`). Mapping suggéré :
- Lancement : `flag-checkered` ou `rocket`
- Milestone chiffre : `trophy` ou `medal`
- Croissance : `chart-line`
- Reconnaissance : `star` ou `award`
- Partenariat : `handshake`

### Variante 3 — Timeline avec photos d'archives

Au lieu d'icônes, mettre une mini-image carrée 80×80 par event. Modifier le markup pour intégrer une core/image dans chaque description.

## Pièges

- **Tablet / mobile responsive** : `stack: "tablet"` force le layout vertical sur mobile/tablet (events alternés deviennent stack). Sans ça, layout cassé en mobile.
- **timelineItem** est un array JSON dans les attributs. Maximum testé ≈ 12 events avant que la performance se dégrade.
- **Event description** accepte du HTML basique (`<strong>`, `<a>`) mais pas de blocs imbriqués.

## Test post-génération

1. Screenshot desktop → ligne centrale + events alternés gauche/droite
2. Vérifier dates en orange visible
3. Vérifier icônes correctes (pas fallback)
4. Test responsive 768 → events stack vertical, ligne décalée gauche
5. Test responsive 375 → idem
