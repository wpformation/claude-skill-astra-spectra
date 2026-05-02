# Pattern : Review Product (avec schema Review SEO)

> **Use case** : présentation détaillée d'un produit/service avec note ⭐, pros/cons, prix, CTA d'achat. Spectra `uagb/review` génère le schema Review JSON-LD pour rich results Google (étoiles dans la SERP).

## Bloc Spectra utilisé : `uagb/review`

## Structure

```
uagb/container#review-section (root, alignfull, padding 120px)
  └─ uagb/review#review (image + headline + summary + features rated + pros + cons + CTA)
```

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{REVIEW_TITLE}}` | Titre du produit reviewé | `Manuel Foucher BTS NDRC 2026` |
| `{{REVIEW_DESC}}` | Sous-titre / accroche | `Le manuel le plus complet pour pr&eacute;parer les 4 blocs.` |
| `{{REVIEW_IMAGE_URL}}` | Image produit | ... |
| `{{REVIEW_AUTHOR}}` | Auteur de la review | `Fabrice Ducarme` |
| `{{REVIEW_DATE}}` | Date publication review | `2026-05-02` |
| `{{REVIEW_RATING}}` | Note globale /5 | `4.5` |
| `{{REVIEW_BEST_RATING}}` | Note max | `5` |
| `{{REVIEW_FEATURES[]}}` | Features avec rating individuel | `[{name:"Couverture programme",rating:5},{name:"Exercices",rating:4},...]` |
| `{{PROS_LIST}}` | Liste avantages | ... |
| `{{CONS_LIST}}` | Liste inconvénients | ... |
| `{{CTA_LABEL}}` `{{CTA_LINK}}` | Bouton achat | `Acheter sur Amazon`, `https://...` |
| `{{PRICE}}` | Prix | `29 &euro;` |

## Block markup (squelette)

```html
<!-- wp:uagb/review {"block_id":"{slug}-review","mainTitle":"Manuel Foucher BTS NDRC 2026","description":"Le manuel le plus complet...","authorName":"Fabrice Ducarme","datePublish":"2026-05-02","starColor":"#FBBF24","starOutlineColor":"#e5e7eb","summaryTitle":"Note globale","overallRating":4.5,"showCatBg":true,"catBgColor":"#FD9800","catTitleColor":"#ffffff","showCta":true,"ctaText":"Acheter sur Amazon","ctaLink":"https://amazon.fr/...","ctaTarget":"_blank","ctaBgColor":"#FD9800","ctaTextColor":"#0F172A","ctaHoverBgColor":"#0F172A","ctaHoverTextColor":"#ffffff","ctaBorderRadius":8,"items":[{"name":"Couverture programme","value":5},{"name":"Exercices types &eacute;preuve","value":4},{"name":"Annales corrig&eacute;es","value":4.5},{"name":"Rapport qualit&eacute;-prix","value":4}],"showPros":true,"prosTitle":"Avantages","pros":[{"name":"Couvre les 4 blocs &agrave; 100%"},{"name":"800+ pages d&rsquo;exercices"},{"name":"Mises &agrave; jour annuelles"}],"showCons":true,"consTitle":"Inconv&eacute;nients","cons":[{"name":"Format papier uniquement"},{"name":"Pas de version digital"}]} -->
<div class="wp-block-uagb-review uagb-block-{slug}-review">
  <!-- Spectra auto-render -->
</div>
<!-- /wp:uagb/review -->
```

## CSS overrides (`_uag_custom_page_level_css`)

```css
/* Stars plus grosses */
.uagb-block-{slug}-review .uagb-rating-icon-style {
  font-size: 24px !important;
}

/* Overall rating bar */
.uagb-block-{slug}-review .uagb-review-overall {
  font-size: 56px !important;
  font-weight: 800 !important;
  color: {{ACCENT_COLOR}} !important;
}

/* Pros/Cons cards */
.uagb-block-{slug}-review .uagb-review-pros-cons {
  background-color: #fafafa !important;
  border-radius: 12px !important;
  padding: 32px !important;
}
```

## Schema SEO automatique

JSON-LD Review généré par Spectra :

```json
{
  "@context": "https://schema.org",
  "@type": "Review",
  "itemReviewed": {"@type": "Product", "name": "..."},
  "reviewRating": {"@type": "Rating", "ratingValue": 4.5, "bestRating": 5},
  "author": {"@type": "Person", "name": "..."},
  "datePublished": "2026-05-02"
}
```

## Variantes

### Variante 1 — Multiple reviews comparatives

3 blocs `uagb/review` côte à côte dans un container row pour comparer 3 produits. Width 32% chacun.

### Variante 2 — Verdict mini

Sans pros/cons détaillés, juste : image + nom + note + 1 phrase verdict + CTA. Plus compact.

### Variante 3 — Review avec galerie

Au lieu d'une image principale, intégrer un `uagb/image-gallery` avant la review pour montrer plusieurs angles du produit.

## Pièges

- **overallRating** : nombre décimal entre 0 et 5 (e.g. 4.5). Pas de fraction au-delà du 0.5.
- **ctaLink** : doit avoir `https://` (sinon Google flag le schema invalide).
- **datePublish** : format ISO `YYYY-MM-DD`. Pas `02/05/2026`.

## Test post-génération

1. Vue d'ensemble : image + titre + 4.5 ⭐ + features rated + pros/cons + CTA
2. View-source → JSON-LD Review présent
3. Rich Results Test → schema valide
4. CTA click → ouvre URL externe en nouvel onglet
5. Test responsive 768 → layout simplifié 1 col
