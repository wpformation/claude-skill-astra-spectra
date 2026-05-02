# Template : E-commerce Produit (page produit complète)

> **Use case** : page produit e-commerce / lancement produit. Hero produit + galerie + features + reviews + pricing + FAQ + CTA achat. Compatible WooCommerce mais aussi pour produits info digitaux (cours, ebook, templates).

## Composition de patterns

```
1. Hero produit split (image left / heading + price + CTA right)
2. Galerie images produit (uagb/image-gallery 6-12 images)
3. Features 3-cols (patterns/features-numbered.md)
4. patterns/review-product.md             (avec note 5 ⭐ + pros/cons)
5. patterns/testimonials-cards.md         (3 témoignages clients)
6. Pricing comparison (3 tiers)
7. patterns/faq-accordion.md              (questions produit)
8. patterns/cta-banner-fullwidth.md       (CTA final achat)
```

## Variables d'entrée

| Variable | Description |
|---|---|
| `{{PRODUCT_NAME}}` | Nom produit |
| `{{PRODUCT_TAGLINE}}` | Tagline 1-line |
| `{{PRODUCT_HERO_IMAGE_ID}}` | Image hero produit (1:1 ou 4:5) |
| `{{PRODUCT_GALLERY_IDS[]}}` | Images galerie produit |
| `{{PRODUCT_PRICE}}` | Prix actuel |
| `{{PRODUCT_PRICE_OLD}}` | Prix barré (optionnel) |
| `{{PRODUCT_CTA_LINK}}` | URL achat (Stripe, WooCommerce, Gumroad, etc.) |
| `{{PRODUCT_FEATURES[3]}}` | 3 features clés |
| `{{PRODUCT_RATING}}` | Note moyenne /5 |
| `{{TESTIMONIALS[3]}}` | 3 témoignages clients |
| `{{PRICING_TIERS[3]}}` | 3 paliers prix (basic / pro / premium) |
| `{{FAQ_ITEMS[5-8]}}` | 5-8 questions produit |

## Sections clés

### 1. Hero produit split

Container row 50/50 :
- Colonne gauche : `core/image` produit (1:1 ratio, 600×600)
- Colonne droite : nom + tagline + rating ⭐ + prix + 2 CTAs (acheter primary + voir démo secondary)

```html
<!-- wp:uagb/container {"block_id":"{slug}-hero","directionDesktop":"row","alignItemsDesktop":"center","topPaddingDesktop":120,"isBlockRootParent":true} -->
<div class="wp-block-uagb-container uagb-block-{slug}-hero alignfull">
  <!-- Container left: image -->
  <!-- Container right: heading + rating + prix + CTAs -->
</div>
<!-- /wp:uagb/container -->
```

### 2. Galerie images

Bloc `uagb/image-gallery` :

```html
<!-- wp:uagb/image-gallery {"block_id":"{slug}-gallery","layout":"grid","columns":4,"tcolumns":3,"mcolumns":2,"gap":16,"lightbox":true,"images":[{"id":XX,"url":"...","alt":"..."}]} -->
```

### 3. Features 3-cols

Pattern `features-numbered.md`. Numéros 01/02/03, label « Garantie », « Mises à jour », « Support ».

### 4. Review schema

Pattern `review-product.md`. Note moyenne 4.5/5 + features rated + pros/cons + CTA achat.

### 5. Testimonials clients

Pattern `testimonials-cards.md`. 3 témoignages avec avatar + citation + nom + meta.

### 6. Pricing comparison

3 tiers en row container :

```
container#pricing (alignfull, padding 120)
  ├─ container#tier-basic (white card, border, prix barré + prix actuel)
  │     └─ liste features (avec/sans checkmark)
  │     └─ CTA « Acheter Basic »
  ├─ container#tier-pro (highlighted, border accent, "Recommandé" badge)
  │     └─ ...
  └─ container#tier-premium (...)
```

### 7. FAQ produit

Pattern `faq-accordion.md`. 5-8 questions classiques :
- Quel est le délai de livraison ?
- Puis-je l'utiliser sur plusieurs sites ?
- Quelle politique de remboursement ?
- Mises à jour gratuites ?
- Support inclus ?

### 8. CTA final

Pattern `cta-banner-fullwidth.md`. Image bg + overlay + heading « Prêt à passer à la vitesse supérieure ? » + 2 CTAs.

## Schema SEO Product

JSON-LD à injecter dans le `<head>` via mu-plugin ou plugin Yoast :

```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "{{PRODUCT_NAME}}",
  "image": ["{{PRODUCT_HERO_IMAGE_URL}}"],
  "description": "{{PRODUCT_TAGLINE}}",
  "offers": {
    "@type": "Offer",
    "url": "{{PRODUCT_CTA_LINK}}",
    "priceCurrency": "EUR",
    "price": "{{PRODUCT_PRICE}}",
    "availability": "https://schema.org/InStock"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "{{PRODUCT_RATING}}",
    "reviewCount": "{{REVIEW_COUNT}}"
  }
}
```

## CSS overrides minimum

```css
/* Pricing tier highlighted */
.uagb-block-{slug}-tier-pro {
  border: 2px solid var(--ast-global-color-0) !important;
  position: relative;
}
.uagb-block-{slug}-tier-pro::before {
  content: "Recommand&eacute;";
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--ast-global-color-0);
  color: #fff;
  padding: 4px 16px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 700;
}

/* Old price barré */
.uagb-block-{slug}-hero .price-old {
  text-decoration: line-through;
  color: #94a3b8;
  font-size: 24px;
}

/* Current price big */
.uagb-block-{slug}-hero .price-current {
  font-size: 56px;
  font-weight: 800;
  color: var(--ast-global-color-0);
}
```

## Configuration Astra

```json
{
  "site-content-layout": "page-builder",
  "site-sidebar-layout": "no-sidebar",
  "ast-title-bar-display": "disabled"
}
```

## Variantes par type de produit

- **Produit physique** : galerie photos multi-angles, options taille/couleur, livraison
- **Produit digital** (ebook, template, cours) : preview pages, format file, accès download
- **Service** (consulting, coaching) : booking calendar embed, packages
- **SaaS abonnement** : pricing tiers prominents, free trial CTA

## Workflow

1. Brief : `« crée une page produit pour mon ebook BTS NDRC à 29 € »`
2. Récupérer infos produit (titre, tagline, prix, image, etc.)
3. Composer en assemblant 8 sections
4. Générer CSS overrides
5. Injecter schema JSON-LD Product
6. POST + meta + regen
7. Test :
   - Rich Results Test pour Product schema
   - Click sur tous les CTAs (Stripe / WC checkout)
   - Galerie lightbox fonctionne
   - Pricing tiers responsive
