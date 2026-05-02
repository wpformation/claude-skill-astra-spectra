# Pattern : Price List (uagb/price-list)

> **Use case** : menu restaurant, carte des vins, liste de prestations avec prix. Différent de `pricing-3-tiers.md` (qui est une comparaison de 3 plans). `price-list` est plus simple : nom + description courte + prix + (optionnel) image, en liste verticale.

> **Bloc Spectra** : `uagb/price-list` (parent) + `uagb/price-list-child` (chaque item). Ideal pour :
> - Menu restaurant (entrées, plats, desserts)
> - Carte coffee shop (espresso, latte, mocha, etc.)
> - Liste prestations agence (logo design, site web, branding)
> - Tarifs spa / soins (massage 30 min, soin visage, etc.)

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{PL_HEADING}}` | Titre H2 de la section | `Carte des vins` |
| `{{PL_ITEMS[]}}` | Array des items | (cf ci-dessous) |
| `{{PL_LAYOUT}}` | `1-col` / `2-col` (grid) | `1-col` |

### Structure d'un item

```json
{
  "title": "Risotto safran",
  "description": "Riz arborio, safran de Sault, parmesan 24 mois, lait d'amande",
  "price": "18 €",
  "image_url": "https://site.com/wp-content/uploads/risotto.jpg",
  "image_id": 47
}
```

## Block markup

```html
<!-- wp:uagb/price-list {"block_id":"{slug}-price-list","columns":1,"tcolumns":1,"mcolumns":1,"rowGap":24,"columnGap":32,"showImage":true,"imageWidth":80,"titleColor":"#0F172A","titleFontSizeDesktop":18,"titleFontWeight":"800","priceColor":"var(--ast-global-color-0)","priceFontSizeDesktop":18,"priceFontWeight":"800","descColor":"#454F5E","descFontSizeDesktop":14,"descFontWeight":"500","descLineHeightDesktop":1.5,"separatorStyle":"dotted","separatorColor":"#cbd5e1","separatorThickness":2,"separatorSpace":12} -->
<div class="wp-block-uagb-price-list uagb-block-{slug}-price-list">

  <!-- wp:uagb/price-list-child {"block_id":"{slug}-pl-1","title":"Risotto safran","description":"Riz arborio, safran de Sault, parmesan 24 mois, lait d&rsquo;amande","price":"18 €","image":{"id":47,"url":"https://site.com/...risotto.jpg"}} -->
  <div class="wp-block-uagb-price-list-child uagb-block-{slug}-pl-1">
    <div class="uagb-pl__image">
      <img src="https://site.com/...risotto.jpg" alt="Risotto safran" width="80" height="80">
    </div>
    <div class="uagb-pl__content">
      <div class="uagb-pl__title-row">
        <h3 class="uagb-pl__title">Risotto safran</h3>
        <span class="uagb-pl__separator"></span>
        <span class="uagb-pl__price">18 €</span>
      </div>
      <p class="uagb-pl__description">Riz arborio, safran de Sault, parmesan 24 mois, lait d&rsquo;amande</p>
    </div>
  </div>
  <!-- /wp:uagb/price-list-child -->

  <!-- wp:uagb/price-list-child {"block_id":"{slug}-pl-2",...} -->
  <!-- ... -->
  <!-- /wp:uagb/price-list-child -->

</div>
<!-- /wp:uagb/price-list -->
```

## CSS overrides recommandés

```css
/* Container — espacement éditorial entre items */
.uagb-block-{slug}-price-list {
  display: flex !important;
  flex-direction: column !important;
  gap: 28px !important;
}

/* Item — flex avec image gauche, titre+desc+prix droite */
.uagb-block-{slug}-price-list .wp-block-uagb-price-list-child {
  display: flex !important;
  gap: 20px !important;
  align-items: flex-start !important;
}

/* Image circulaire 80×80 */
.uagb-block-{slug}-price-list .uagb-pl__image img {
  width: 80px !important;
  height: 80px !important;
  border-radius: 12px !important;
  object-fit: cover !important;
  flex-shrink: 0 !important;
}

/* Title row — titre à gauche, séparateur dotted, prix à droite */
.uagb-block-{slug}-price-list .uagb-pl__title-row {
  display: flex !important;
  align-items: baseline !important;
  gap: 12px !important;
  margin-bottom: 6px !important;
}

.uagb-block-{slug}-price-list .uagb-pl__title {
  font-size: 18px !important;
  font-weight: 800 !important;
  color: #0F172A !important;
  margin: 0 !important;
  flex-shrink: 0 !important;
}

.uagb-block-{slug}-price-list .uagb-pl__separator {
  flex: 1 !important;
  height: 1px !important;
  border-bottom: 2px dotted #cbd5e1 !important;
  margin: 0 8px !important;
}

.uagb-block-{slug}-price-list .uagb-pl__price {
  font-size: 18px !important;
  font-weight: 800 !important;
  color: var(--ast-global-color-0) !important;
  flex-shrink: 0 !important;
  white-space: nowrap !important;
}

/* Description — italic petit gris */
.uagb-block-{slug}-price-list .uagb-pl__description {
  font-size: 14px !important;
  color: #454F5E !important;
  line-height: 1.5 !important;
  margin: 0 !important;
  font-style: italic !important;
}

/* Mobile : image au-dessus, séparateur réduit */
@media (max-width: 600px) {
  .uagb-block-{slug}-price-list .wp-block-uagb-price-list-child {
    flex-direction: column !important;
    gap: 12px !important;
  }
  .uagb-block-{slug}-price-list .uagb-pl__image img {
    width: 64px !important;
    height: 64px !important;
  }
}
```

## Pièges

| # | Quirk |
|---|---|
| **Format prix** | Toujours uniforme : `18 €` ou `18,00 €` ou `18.00€`. Pas de mix. Espace insécable avant `€` (`&nbsp;`) pour typographie FR correcte |
| **Currency** | Si site multi-currency, garder le symbole de la devise principale dans le markup. Le switching dynamique nécessite plugin tiers (out of scope) |
| **Image optionnelle** | Si certains items n'ont pas d'image (ex : carte des vins), utiliser `showImage: false` global. Mixer items avec/sans image casse l'alignement |
| **Sub-prices** | Pour items à plusieurs prix (ex : café : `2,50 € small / 3,50 € large`), mettre les 2 prix dans le champ `price` séparés par `/`. Spectra ne supporte pas les sub-prices natifs |
| **Description longue** | Limiter à 100-120 caractères. Au-delà, considérer un nouveau pattern (carte produit avec page dédiée par item) |
| **Sections multiples** | Pour menu restaurant avec 4 sections (Entrées, Plats, Desserts, Boissons), utiliser 4 `uagb/price-list` séparés, chacun précédé d'un `uagb/advanced-heading` H3 avec le nom de la section |

## Variantes

### Variante 1 — Menu restaurant (cas d'usage principal)

Layout `1-col`, image cercle 80×80, séparateur dotted, prix orange. Plusieurs `uagb/price-list` pour les sections.

### Variante 2 — Carte des vins (sans images)

Layout `1-col`, `showImage: false`, séparateur en pointillés. Description = région + cépage + millésime.

### Variante 3 — Liste prestations agence

Layout `1-col`, image rectangulaire 120×80 (logo prestation), description = bullet list de ce qui est inclus.

### Variante 4 — Grid 2-col (carte spa avec photos)

Layout `2-col`, images plus grandes 200×130, format catalogue. Pour soins esthétiques avec photo d'ambiance.

```css
.uagb-block-{slug}-price-list {
  display: grid !important;
  grid-template-columns: 1fr 1fr !important;
  gap: 32px !important;
}
.uagb-block-{slug}-price-list .uagb-pl__image img {
  width: 100% !important;
  height: 130px !important;
}
.uagb-block-{slug}-price-list .wp-block-uagb-price-list-child {
  flex-direction: column !important;
}
```

### Variante 5 — Pricing one-shot avec features

Pas vraiment un price-list, plutôt un container avec gros prix + icon-list features. Voir `templates/page-tarifs.md` ou `patterns/pricing-3-tiers.md`.

## Test post-génération

1. Vérifier l'alignement vertical des prix (toujours à droite, jamais wrap sur 2 lignes)
2. Vérifier le séparateur dotted entre titre et prix (effet menu restaurant classique)
3. Mobile : items en colonne, image au-dessus du titre
4. Vérifier que les images sont en `loading="lazy"` (sauf si au-dessus du fold)
5. a11y : alt text sur les images (« Photo du plat », pas « image-1 »)

## Pour aller plus loin

- Pricing 3 tiers comparatif : `patterns/pricing-3-tiers.md`
- Marketing-button comme CTA après le price-list : `patterns/marketing-buttons.md`
- Template page contact restaurant (avec menu + map + horaires) : composer page-accueil + price-list + google-maps
