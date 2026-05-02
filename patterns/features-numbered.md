# Pattern : Features Numbered (alternative anti-icônes Font Awesome)

> **Use case** : section « 3 piliers / méthode / approche » avec 3 cards en grille. Au lieu d'icônes Font Awesome (souvent doublonnées par fallback Spectra, cf piège #8), utilise des **gros numéros 01 / 02 / 03** orange + label uppercase + heading + desc. Style éditorial print magazine, plus distinctif.

> **Origine** : créé après échec v0.9.2 où les 3 cards features avaient toutes la même icône fallback parce que `book-open`, `clipboard-check`, `timer` n'étaient pas reconnus par Spectra. Validé visuellement le 02/05/2026.

## Structure

```
uagb/container#features (root, alignfull, bg #fafafa, padding 140px)
  ├─ uagb/info-box#features-title (eyebrow + H2 + desc, center)
  └─ uagb/container#feat-row (direction:row, wrap, equalHeight)
      ├─ uagb/container#feat-1 (white card, border, shadow)
      │     ├─ uagb/info-box#feat-1-num (« 01 » + label « THÉORIE »)
      │     └─ uagb/info-box#feat-1-text (H3 + desc)
      ├─ uagb/container#feat-2 (...)
      └─ uagb/container#feat-3 (...)
```

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{FEATURES_EYEBROW}}` | Kicker uppercase | `Trois piliers pour r&eacute;ussir` |
| `{{FEATURES_HEADING}}` | H2 contextuel | `Une m&eacute;thode qui combine th&eacute;orie, pratique et auto-&eacute;valuation.` |
| `{{FEATURES_DESC}}` | Sous-titre (optionnel) | `Pas de blabla&nbsp;: chaque s&eacute;ance commence par...` |
| `{{FEAT_1_LABEL}}` … `{{FEAT_3_LABEL}}` | Labels uppercase tracking | `Th&eacute;orie`, `Pratique`, `Auto-&eacute;valuation` |
| `{{FEAT_1_TITLE}}` … `{{FEAT_3_TITLE}}` | H3 cards | `227 cours r&eacute;dig&eacute;s.` |
| `{{FEAT_1_DESC}}` … `{{FEAT_3_DESC}}` | Desc cards | `Chaque chapitre du r&eacute;f&eacute;rentiel...` |
| `{{ACCENT_COLOR}}` | Numéros + eyebrow | `#FD9800` (orange WPF) |
| `{{TEXT_HEADING}}` | H3 cards color | `#0F172A` (slate dark) |
| `{{TEXT_BODY}}` | Desc color | `#454F5E` (slate medium) |

## Block markup (squelette)

```html
<!-- wp:uagb/container {"block_id":"{slug}-features","backgroundColor":"#fafafa","topPaddingDesktop":140,"bottomPaddingDesktop":140,"leftPaddingDesktop":40,"rightPaddingDesktop":40,...,"isBlockRootParent":true,"backgroundType":"color"} -->
<div class="wp-block-uagb-container uagb-block-{slug}-features alignfull uagb-is-root-container">
  <div class="uagb-container-inner-blocks-wrap">

    <!-- Title : eyebrow + H2 + desc center -->
    <!-- wp:uagb/info-box {"block_id":"{slug}-features-title","showPrefix":true,"prefixHeadingTag":"p","headingTag":"h2","headingAlign":"center", ... } -->
    ...

    <!-- Cards row -->
    <!-- wp:uagb/container {"block_id":"{slug}-feat-row","directionDesktop":"row","alignItemsDesktop":"stretch","wrapDesktop":"wrap","columnGapDesktop":28,"rowGapDesktop":28,"equalHeight":true,"widthSetByUser":true} -->
    <div class="wp-block-uagb-container uagb-block-{slug}-feat-row">

      <!-- Card 1 -->
      <!-- wp:uagb/container {"block_id":"{slug}-feat-1","backgroundColor":"#ffffff","topPaddingDesktop":56,"bottomPaddingDesktop":56,"leftPaddingDesktop":44,"rightPaddingDesktop":44,"variationSelected":true,"rowGapDesktop":20,"containerBorderTopWidth":1,"containerBorderLeftWidth":1,"containerBorderRightWidth":1,"containerBorderBottomWidth":1,"containerBorderStyle":"solid","containerBorderColor":"#e5e7eb","containerBorderTopLeftRadius":18,"containerBorderTopRightRadius":18,"containerBorderBottomLeftRadius":18,"containerBorderBottomRightRadius":18,"boxShadowColor":"rgba(15,23,42,0.06)","boxShadowVOffset":4,"boxShadowBlur":24,"widthDesktop":31.5,"widthTypeDesktop":"%","widthTablet":100,"widthTypeTablet":"%","widthMobile":100,"widthTypeMobile":"%"} -->
      <div class="wp-block-uagb-container uagb-block-{slug}-feat-1">

        <!-- Numéro 01 + label uppercase -->
        <!-- wp:uagb/info-box {"block_id":"{slug}-feat-1-num","headingTag":"p","headingAlign":"left", ... } -->
        <div class="wp-block-uagb-info-box uagb-block-{slug}-feat-1-num ...">
          <div class="uagb-ifb-content">
            <div class="uagb-ifb-title-wrap"><p class="uagb-ifb-title">01</p></div>
            <p class="uagb-ifb-desc">{{FEAT_1_LABEL}}</p>
          </div>
        </div>
        <!-- /wp:uagb/info-box -->

        <!-- H3 + desc -->
        <!-- wp:uagb/info-box {"block_id":"{slug}-feat-1-text","headingTag":"h3","headingAlign":"left", ... } -->
        <div class="wp-block-uagb-info-box uagb-block-{slug}-feat-1-text ...">
          <div class="uagb-ifb-content">
            <div class="uagb-ifb-title-wrap"><h3 class="uagb-ifb-title">{{FEAT_1_TITLE}}</h3></div>
            <p class="uagb-ifb-desc">{{FEAT_1_DESC}}</p>
          </div>
        </div>
        <!-- /wp:uagb/info-box -->
      </div>
      <!-- /wp:uagb/container -->

      <!-- Card 2 (idem feat-1, num 02) -->
      <!-- Card 3 (idem feat-1, num 03) -->

    </div>
    <!-- /wp:uagb/container -->
  </div>
</div>
<!-- /wp:uagb/container -->
```

## CSS overrides obligatoires (`_uag_custom_page_level_css`)

```css
/* Numéros 01 / 02 / 03 — 48px orange */
.uagb-block-{slug}-feat-1-num .uagb-ifb-title,
.uagb-block-{slug}-feat-2-num .uagb-ifb-title,
.uagb-block-{slug}-feat-3-num .uagb-ifb-title {
  font-size: 48px !important;
  color: {{ACCENT_COLOR}} !important;
  font-weight: 800 !important;
  line-height: 1 !important;
  letter-spacing: -2px !important;
  margin: 0 !important;
}

/* Labels THÉORIE / PRATIQUE / AUTO-ÉVALUATION — uppercase tracking */
.uagb-block-{slug}-feat-1-num .uagb-ifb-desc,
.uagb-block-{slug}-feat-2-num .uagb-ifb-desc,
.uagb-block-{slug}-feat-3-num .uagb-ifb-desc {
  font-size: 13px !important;
  font-weight: 700 !important;
  color: {{TEXT_BODY}} !important;
  text-transform: uppercase !important;
  letter-spacing: 2.5px !important;
  margin-top: 8px !important;
}

/* H3 cards */
.uagb-block-{slug}-feat-1-text .uagb-ifb-title,
.uagb-block-{slug}-feat-2-text .uagb-ifb-title,
.uagb-block-{slug}-feat-3-text .uagb-ifb-title {
  font-size: 26px !important;
  font-weight: 800 !important;
  line-height: 1.3 !important;
  letter-spacing: -0.5px !important;
  color: {{TEXT_HEADING}} !important;
}

/* Desc cards */
.uagb-block-{slug}-feat-1-text .uagb-ifb-desc,
.uagb-block-{slug}-feat-2-text .uagb-ifb-desc,
.uagb-block-{slug}-feat-3-text .uagb-ifb-desc {
  font-size: 16px !important;
  line-height: 1.7 !important;
  color: {{TEXT_BODY}} !important;
}

/* Eyebrow + H2 section */
.uagb-block-{slug}-features-title .uagb-ifb-title-prefix {
  font-size: 15px !important;
  font-weight: 800 !important;
  color: {{ACCENT_COLOR}} !important;
  letter-spacing: 3px !important;
}
.uagb-block-{slug}-features-title .uagb-ifb-title {
  font-size: 52px !important;
  font-weight: 800 !important;
  line-height: 1.1 !important;
  letter-spacing: -1.5px !important;
}
@media (max-width: 1024px) {
  .uagb-block-{slug}-features-title .uagb-ifb-title { font-size: 40px !important; }
}
@media (max-width: 600px) {
  .uagb-block-{slug}-features-title .uagb-ifb-title { font-size: 30px !important; }
}
```

## Pièges connus

| # | Quirk |
|---|---|
| #1 | headingFontSize ignoré sur p → CSS overrides obligatoires |
| #5 | block_id unique par card (feat-1, feat-2, feat-3 ; feat-1-num, feat-1-text, etc.) |
| #6 | CSS Spectra dynamique non injecté → meta `_uag_custom_page_level_css` |
| #8 | **NE PAS** utiliser `uagb/icon` avec book-open/clipboard-check/timer (fallback identique). Utiliser ce pattern numéros à la place |

## Variantes

### Variante 1 — 4 features (au lieu de 3)

Width cards : `widthDesktop:23.5`. Numéros 01/02/03/04. Container `columnGapDesktop:20`.

### Variante 2 — Avec icône au-dessus du numéro

Si tu veux ajouter une icône Spectra **validée** (cf `references/spectra-icons-list.md`), insérer un `uagb/icon` AVANT le numéro :

```html
<!-- wp:uagb/icon {"block_id":"{slug}-feat-1-icon","icon":"book","iconColor":"{{ACCENT_COLOR}}","size":40,"sizeUnit":"px"} -->
<div class="wp-block-uagb-icon-wrapper uagb-block-{slug}-feat-1-icon"></div>
<!-- /wp:uagb/icon -->
```

### Variante 3 — Cards horizontales (numéro à gauche, texte à droite)

Au lieu de num au-dessus, utiliser un container interne `direction:row` :
- Colonne gauche (width 25%) : numéro 64px orange
- Colonne droite (width 70%) : H3 + desc

### Variante 4 — Sans border ni shadow (style brut éditorial)

Retirer `containerBorder*` et `boxShadow*`. Padding cards 0. Séparation entre cards uniquement via `columnGap`. Look magazine pur.

### Variante 5 — Background dark mode

`backgroundColor: "#0F172A"` sur la section. Cards `backgroundColor: "#1E293B"`. Heading et desc colors inversées (white). Border-color rgba(255,255,255,0.1).

## Ratio image
N/A (pattern sans image).

## Test post-génération

1. Screenshot 1440 → 3 cards en row, équipées de numéros visibles 01/02/03 en orange grand
2. Vérifier que les 3 cards ont la **même hauteur** (`equalHeight:true` sur le row container)
3. Test responsive 768 → 1 colonne stack
4. Test responsive 375 → idem
5. Vérifier qu'aucune **icône fallback** n'apparaît (si tu vois un rectangle vide ou identique sur les 3, retour au pattern de base sans icônes)

## Inspiration

Pattern numérique éditorial classique (cf démos NYT, Bloomberg, Stripe). Adapté à Spectra avec wrappers container pour résoudre les pièges info-box width.
