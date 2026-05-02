# Pattern : Tabs Section

> **Use case** : section avec tabs cliquables (3-5 onglets) pour organiser du contenu lié sans saturer la page. Pricing tiers détaillés, comparaison concurrent vs nous, fonctionnalités par cas d'usage.

## Bloc Spectra utilisé : `uagb/tabs` + `uagb/tabs-child`

Spectra a un bloc tabs natif. Pas besoin de bricoler avec containers + JS custom.

## Structure

```
uagb/container#tabs-section (root, alignfull, padding 120px)
  ├─ uagb/info-box#tabs-title (eyebrow + H2 + desc, center)
  └─ uagb/tabs#tabs (layout horizontal, default tab 1, transition smooth)
      ├─ uagb/tabs-child#tab-1 (titre = « Débutant », contenu)
      ├─ uagb/tabs-child#tab-2 (titre = « Intermédiaire »)
      └─ uagb/tabs-child#tab-3 (titre = « Avancé »)
```

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{TABS_EYEBROW}}` | Kicker | `Selon ton niveau` |
| `{{TABS_HEADING}}` | H2 | `Choisis ton parcours selon o&ugrave; tu en es.` |
| `{{TAB_1_LABEL}}` | Onglet 1 label | `D&eacute;butant` |
| `{{TAB_1_CONTENT}}` | Contenu HTML onglet 1 | `<p>Tu d&eacute;couvres le BTS NDRC...</p>` |
| `{{TAB_2_LABEL}}` `{{TAB_2_CONTENT}}` | Onglet 2 | ... |
| `{{TAB_3_LABEL}}` `{{TAB_3_CONTENT}}` | Onglet 3 | ... |
| `{{ACCENT_COLOR}}` | Couleur tab active | `#FD9800` |

## Block markup (squelette)

```html
<!-- wp:uagb/tabs {"block_id":"{slug}-tabs","tabsStyle":"horizontal","defaultTab":1,"tabActiveColor":"#ffffff","tabActiveBgColor":"{{ACCENT_COLOR}}","tabInactiveColor":"#0F172A","tabInactiveBgColor":"#fafafa","tabsContentBgColor":"#ffffff","tabBorderColor":"#e5e7eb","tabBorderTopWidth":1,"tabBorderBottomWidth":1,"tabBorderLeftWidth":1,"tabBorderRightWidth":1,"tabBorderStyle":"solid","tabBorderRadius":12,"tabFontSize":16,"tabFontWeight":700,"tabPaddingTop":18,"tabPaddingBottom":18,"tabPaddingLeft":28,"tabPaddingRight":28,"contentPaddingTop":40,"contentPaddingBottom":40,"contentPaddingLeft":40,"contentPaddingRight":40,"gap":12} -->
<div class="wp-block-uagb-tabs uagb-block-{slug}-tabs">

  <!-- wp:uagb/tabs-child {"block_id":"{slug}-tab-1","title":"D&eacute;butant"} -->
  <div class="wp-block-uagb-tabs-child uagb-tab-content__wrap uagb-block-{slug}-tab-1">
    <div class="uagb-tabs-content__wrap">
      <p>{{TAB_1_CONTENT}}</p>
    </div>
  </div>
  <!-- /wp:uagb/tabs-child -->

  <!-- wp:uagb/tabs-child {"block_id":"{slug}-tab-2","title":"Interm&eacute;diaire"} -->
  ...
  <!-- /wp:uagb/tabs-child -->

  <!-- wp:uagb/tabs-child {"block_id":"{slug}-tab-3","title":"Avanc&eacute;"} -->
  ...
  <!-- /wp:uagb/tabs-child -->

</div>
<!-- /wp:uagb/tabs -->
```

## CSS overrides (`_uag_custom_page_level_css`)

```css
/* Tab active hover effect */
.uagb-block-{slug}-tabs .uagb-tabs__panel li:hover {
  background-color: rgba(253, 152, 0, 0.1) !important;
  cursor: pointer;
}

/* Tab content typography */
.uagb-block-{slug}-tabs .uagb-tabs-content__wrap p {
  font-size: 17px !important;
  line-height: 1.7 !important;
  color: #454F5E !important;
}
```

## Variantes

### Variante 1 — Tabs vertical (sidebar layout)

`tabsStyle: "vertical"` → onglets en colonne gauche, content en colonne droite. Utile pour FAQ longues ou comparaison features.

### Variante 2 — Tabs avec icônes

Ajouter `iconColor`, `iconActiveColor`, `icon` dans chaque tabs-child. Validés via `references/spectra-icons-list.md`.

### Variante 3 — Tabs auto-rotate

Ajouter `autoRotateTabs: true` + `rotateInterval: 5000` (ms). Les onglets s'animent automatiquement.

## Pièges

- Tabs avec contenu très long (>500px) : prévoir scroll interne ou switch en accordion sur mobile (`mobileBreakpoint`)
- Tabs avec content qui contient des blocs uagb imbriqués : tester soigneusement, certains blocs peuvent ne pas se ré-init après tab switch
- L'attribut `title` du tab n'accepte pas le HTML (uniquement texte plain)

## Test post-génération

1. Screenshot tab 1 actif (par défaut)
2. Click tab 2 → content change, animation smooth
3. Click tab 3 → idem
4. Test responsive 768 → tabs s'empilent en colonne ou switch en accordion selon config
