# Pattern : Stats Bar Editorial

> **Use case** : barre de statistiques full-width avec drama éditorial. Eyebrow + heading + desc + 4 chiffres ÉNORMES en colonne avec accent line orange. Pattern pivot pour landings, pages services, à propos, formations.

> **Origine** : développé après échec v0.9.0/0.9.1/0.9.2 où les stats étaient empilées verticalement faute de width sur les info-box (cf piège #2). Validé visuellement sur loginarmor-dev (Astra 4.13.1 + Spectra 2.19.25 + palette_3) le 02/05/2026.

## Structure

```
uagb/container#stats (root, alignfull, bg #0F172A dark, padding 96px)
  ├─ uagb/info-box#stats-title (eyebrow + H2 + desc, center)
  └─ uagb/container#stats-row (direction:row, wrap, justify:space-between)
      ├─ uagb/container#stat-1 (width 22%, border-bottom 4px orange)
      │     └─ uagb/info-box#stat-1-text (chiffre 80px + label uppercase)
      ├─ uagb/container#stat-2 (...)
      ├─ uagb/container#stat-3 (...)
      └─ uagb/container#stat-4 (...)
```

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{STATS_EYEBROW}}` | Kicker uppercase | `Le site en chiffres` |
| `{{STATS_HEADING}}` | H2 contextuel | `Tout ce qu&rsquo;il te faut pour pr&eacute;parer le BTS&nbsp;NDRC.` |
| `{{STATS_DESC}}` | Sous-titre court (optionnel) | `Aucun mur de paiement. Toutes les ressources sont accessibles librement.` |
| `{{STAT_1_VALUE}}` … `{{STAT_4_VALUE}}` | Les 4 chiffres | `227`, `33`, `22`, `87&thinsp;%` |
| `{{STAT_1_LABEL}}` … `{{STAT_4_LABEL}}` | Les 4 labels uppercase | `Cours r&eacute;dig&eacute;s`, `Exercices types`, `QCM corrig&eacute;s`, `De r&eacute;ussite` |
| `{{ACCENT_COLOR}}` | Couleur accent (chiffres + line) | `#FD9800` (orange WPF) ou `var(--ast-global-color-0)` |
| `{{BG_COLOR}}` | Background section | `#0F172A` (slate dark) ou `var(--ast-global-color-2)` |
| `{{TEXT_COLOR}}` | Couleur texte sur bg dark | `#ffffff` |

## Block markup (squelette)

```html
<!-- wp:uagb/container {"block_id":"{slug}-stats","backgroundColor":"{{BG_COLOR}}","topPaddingDesktop":96,"bottomPaddingDesktop":96,"leftPaddingDesktop":40,"rightPaddingDesktop":40,"topPaddingTablet":72,"bottomPaddingTablet":72,"leftPaddingTablet":32,"rightPaddingTablet":32,"topPaddingMobile":56,"bottomPaddingMobile":56,"leftPaddingMobile":24,"rightPaddingMobile":24,"directionDesktop":"column","alignItemsDesktop":"center","variationSelected":true,"rowGapDesktop":48,"rowGapTablet":40,"rowGapMobile":32,"columnGapDesktop":0,"isBlockRootParent":true,"backgroundType":"color"} -->
<div class="wp-block-uagb-container uagb-block-{slug}-stats alignfull uagb-is-root-container">
  <div class="uagb-container-inner-blocks-wrap">

    <!-- Title block (eyebrow + H2 + desc) -->
    <!-- wp:uagb/info-box {"block_id":"{slug}-stats-title","headingTag":"h2","showPrefix":true,"prefixHeadingTag":"p","headingAlign":"center","headingColor":"{{TEXT_COLOR}}","subHeadingColor":"rgba(255,255,255,0.65)","prefixColor":"{{ACCENT_COLOR}}", ... } -->
    ...
    <!-- /wp:uagb/info-box -->

    <!-- Stats row container -->
    <!-- wp:uagb/container {"block_id":"{slug}-stats-row","directionDesktop":"row","alignItemsDesktop":"stretch","wrapDesktop":"wrap","justifyContentDesktop":"space-between","columnGapDesktop":24,"rowGapDesktop":36,"widthSetByUser":true} -->
    <div class="wp-block-uagb-container uagb-block-{slug}-stats-row">

      <!-- Stat 1 wrapper (CRITIQUE : container avec widthDesktop) -->
      <!-- wp:uagb/container {"block_id":"{slug}-stat-1","widthDesktop":22,"widthTypeDesktop":"%","widthTablet":48,"widthTypeTablet":"%","widthMobile":100,"widthTypeMobile":"%","topPaddingDesktop":36,"bottomPaddingDesktop":36,"variationSelected":true,"widthSetByUser":true} -->
      <div class="wp-block-uagb-container uagb-block-{slug}-stat-1">
        <!-- wp:uagb/info-box {"block_id":"{slug}-stat-1-text","headingTag":"p","headingAlign":"center", ... } -->
        <div class="wp-block-uagb-info-box uagb-block-{slug}-stat-1-text ...">
          <div class="uagb-ifb-content">
            <div class="uagb-ifb-title-wrap"><p class="uagb-ifb-title">{{STAT_1_VALUE}}</p></div>
            <p class="uagb-ifb-desc">{{STAT_1_LABEL}}</p>
          </div>
        </div>
        <!-- /wp:uagb/info-box -->
      </div>
      <!-- /wp:uagb/container -->

      <!-- Stat 2 wrapper (idem stat-1) -->
      <!-- wp:uagb/container {"block_id":"{slug}-stat-2","widthDesktop":22, ... } -->
      ...
      <!-- /wp:uagb/container -->

      <!-- Stat 3, 4 idem -->

    </div>
    <!-- /wp:uagb/container -->

  </div>
</div>
<!-- /wp:uagb/container -->
```

## CSS overrides obligatoires (`_uag_custom_page_level_css`)

À cause du piège #1 (headingFontSize ignoré sur tag=p) ET piège #6 (CSS Spectra dynamique non injecté), les chiffres énormes nécessitent du CSS dans le meta natif :

```css
/* Stats — chiffres 80px orange dramatic */
.uagb-block-{slug}-stat-1 .uagb-ifb-title,
.uagb-block-{slug}-stat-2 .uagb-ifb-title,
.uagb-block-{slug}-stat-3 .uagb-ifb-title,
.uagb-block-{slug}-stat-4 .uagb-ifb-title {
  font-size: 80px !important;
  color: {{ACCENT_COLOR}} !important;
  font-weight: 800 !important;
  line-height: 1 !important;
  letter-spacing: -3px !important;
  margin: 0 !important;
}

/* Stats — labels uppercase tracking */
.uagb-block-{slug}-stat-1 .uagb-ifb-desc,
.uagb-block-{slug}-stat-2 .uagb-ifb-desc,
.uagb-block-{slug}-stat-3 .uagb-ifb-desc,
.uagb-block-{slug}-stat-4 .uagb-ifb-desc {
  font-size: 13px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 2px !important;
  margin-top: 6px !important;
}

/* Accent line orange sous chaque stat (border-bottom 4px) */
.uagb-block-{slug}-stat-1,
.uagb-block-{slug}-stat-2,
.uagb-block-{slug}-stat-3,
.uagb-block-{slug}-stat-4 {
  border-bottom: 4px solid {{ACCENT_COLOR}} !important;
}

/* Section heading H2 — 36-52px desktop drama */
.uagb-block-{slug}-stats-title .uagb-ifb-title {
  font-size: 36px !important;
  font-weight: 800 !important;
  line-height: 1.2 !important;
  letter-spacing: -0.8px !important;
  color: {{TEXT_COLOR}} !important;
}
@media (min-width: 1025px) {
  .uagb-block-{slug}-stats-title .uagb-ifb-title { font-size: 52px !important; }
}

/* Eyebrow */
.uagb-block-{slug}-stats-title .uagb-ifb-title-prefix {
  font-size: 14px !important;
  font-weight: 800 !important;
  color: {{ACCENT_COLOR}} !important;
  letter-spacing: 3px !important;
}

/* Responsive : stats 2x2 sur tablet, 1x4 sur mobile */
@media (max-width: 1024px) {
  .uagb-block-{slug}-stat-1 .uagb-ifb-title,
  .uagb-block-{slug}-stat-2 .uagb-ifb-title,
  .uagb-block-{slug}-stat-3 .uagb-ifb-title,
  .uagb-block-{slug}-stat-4 .uagb-ifb-title { font-size: 64px !important; }
}
@media (max-width: 600px) {
  .uagb-block-{slug}-stat-1 .uagb-ifb-title,
  .uagb-block-{slug}-stat-2 .uagb-ifb-title,
  .uagb-block-{slug}-stat-3 .uagb-ifb-title,
  .uagb-block-{slug}-stat-4 .uagb-ifb-title { font-size: 56px !important; }
}
```

## Pièges connus pour ce pattern

| # | Quirk |
|---|---|
| #1 | `headingFontSize:80` ignoré sur `headingTag:"p"` → utiliser CSS overrides ci-dessus |
| #2 | `info-box` ne supporte pas `widthDesktop` → wrapper chaque stat dans un container avec `widthDesktop:22` |
| #6 | CSS Spectra dynamique non injecté → CSS overrides dans `_uag_custom_page_level_css` |

## Variantes

### Variante 1 — Stats sur fond clair (light theme)

Inverser couleurs : `BG_COLOR: #fafafa`, `TEXT_COLOR: #0F172A`, accent line + chiffres orange. Visuellement plus discret, OK pour pages secondaires.

### Variante 2 — 3 stats au lieu de 4

Width des wrappers : `widthDesktop:30` (au lieu de 22). Container `justifyContent:space-around`. Chiffres encore plus gros (96px desktop).

### Variante 3 — Stats avec prefixe (« +5K », « 500+ »)

Dans le markup, mettre le préfixe directement dans `STAT_1_VALUE` :

```
STAT_1_VALUE: 5K&thinsp;+
STAT_2_VALUE: &gt;&nbsp;15
STAT_3_VALUE: 87&thinsp;%
STAT_4_VALUE: 4&frasl;5
```

### Variante 4 — Stats inline (pas de section héro, juste une bande étroite)

Réduire padding : `topPaddingDesktop:48`, `bottomPaddingDesktop:48`. Pas de title/desc, juste les 4 stats centrées. Utile en dessous d'un hero.

## Ratio image
N/A (pattern sans image).

## Test post-génération

1. Screenshot avec agent-browser viewport 1440 → vérifier 4 stats sur **une seule ligne**
2. Vérifier que les chiffres sont en font-size visuelle ≥ 60px (sinon piège #1 actif)
3. Vérifier accent line orange visible sous chaque stat
4. Test responsive 768 → 2x2 grid
5. Test responsive 375 → 1x4 stack vertical

## Inspiration

Inspiré du démo Spectra Natures + adaptation drama-mode. Versions précédentes (v0.9.0/0.9.1) cassaient en colonne unique. Fix structurel via wrappers container documenté ici.
