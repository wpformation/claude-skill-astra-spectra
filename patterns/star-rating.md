# Pattern : Star Rating (uagb/star-rating)

> **Use case** : note moyenne 5 étoiles affichée pour un produit, un cours, un témoignage, un avis client. Différent de `review-product.md` (qui est un schema.org Review complet avec auteur + texte). `star-rating` est juste les étoiles + score numérique optionnel.

> **Bloc Spectra** : `uagb/star-rating`. Génère 5 étoiles (full / half / empty) selon le score. Schema.org `aggregateRating` injecté automatiquement si activé.

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{RATING_SCORE}}` | Score sur 5 (entier ou demi : 4, 4.5, 5) | `4.5` |
| `{{RATING_MAX}}` | Score max (généralement 5) | `5` |
| `{{RATING_COUNT}}` | Nombre d'avis (pour `aggregateRating`) | `127` |
| `{{RATING_TITLE}}` | Texte associé (optionnel) | `Note moyenne` |
| `{{RATING_SHOW_NUMBER}}` | Afficher « 4.5 / 5 » à côté | `true` |
| `{{RATING_SHOW_COUNT}}` | Afficher « (127 avis) » | `true` |
| `{{RATING_SIZE}}` | Taille étoiles en px | `24` |
| `{{RATING_COLOR}}` | Couleur étoiles | `#FBBF24` (jaune) |
| `{{RATING_SCHEMA}}` | Inclure schema.org markup | `true` (pour SEO) |

## Block markup

```html
<!-- wp:uagb/star-rating {"block_id":"{slug}-rating","title":"{{RATING_TITLE}}","stars":{{RATING_SCORE}},"maxStars":{{RATING_MAX}},"unmarkedStars":"empty","ratingNumberShow":{{RATING_SHOW_NUMBER}},"ratingCount":{{RATING_COUNT}},"ratingNumberSuffix":"/{{RATING_MAX}}","sizeUnit":"px","size":{{RATING_SIZE}},"color":"{{RATING_COLOR}}","unmarkedColor":"#cbd5e1","gap":4,"alignment":"left","schema":{{RATING_SCHEMA}}} -->
<div class="wp-block-uagb-star-rating uagb-block-{slug}-rating" itemscope itemtype="https://schema.org/AggregateRating">
  <span class="uagb-star-rating__title">{{RATING_TITLE}}</span>
  <div class="uagb-star-rating__stars" role="img" aria-label="{{RATING_SCORE}} sur {{RATING_MAX}} étoiles">
    <i class="fas fa-star" aria-hidden="true"></i>
    <i class="fas fa-star" aria-hidden="true"></i>
    <i class="fas fa-star" aria-hidden="true"></i>
    <i class="fas fa-star" aria-hidden="true"></i>
    <i class="fas fa-star-half-alt" aria-hidden="true"></i>
  </div>
  <span class="uagb-star-rating__number">
    <span itemprop="ratingValue">{{RATING_SCORE}}</span> / <span itemprop="bestRating">{{RATING_MAX}}</span>
  </span>
  <span class="uagb-star-rating__count">
    (<span itemprop="ratingCount">{{RATING_COUNT}}</span> avis)
  </span>
</div>
<!-- /wp:uagb/star-rating -->
```

## CSS overrides recommandés

```css
/* Container — flex inline */
.uagb-block-{slug}-rating {
  display: inline-flex !important;
  align-items: center !important;
  gap: 12px !important;
  flex-wrap: wrap !important;
}

/* Title — uppercase tracking petit */
.uagb-block-{slug}-rating .uagb-star-rating__title {
  font-size: 12px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 1.5px !important;
  color: #454F5E !important;
}

/* Stars — gap entre étoiles */
.uagb-block-{slug}-rating .uagb-star-rating__stars {
  display: inline-flex !important;
  gap: 2px !important;
  font-size: 24px !important;
}

.uagb-block-{slug}-rating .uagb-star-rating__stars .fa-star,
.uagb-block-{slug}-rating .uagb-star-rating__stars .fa-star-half-alt {
  color: #FBBF24 !important;
}

/* Score numérique 4.5/5 */
.uagb-block-{slug}-rating .uagb-star-rating__number {
  font-size: 16px !important;
  font-weight: 800 !important;
  color: #0F172A !important;
}

/* Count avis */
.uagb-block-{slug}-rating .uagb-star-rating__count {
  font-size: 14px !important;
  color: #6B7280 !important;
}

/* Hover (si rating cliquable, lien vers reviews) */
.uagb-block-{slug}-rating a:hover .uagb-star-rating__count {
  color: var(--ast-global-color-0) !important;
  text-decoration: underline !important;
}
```

## Pièges

| # | Quirk |
|---|---|
| **Half-star** | Pour score `.5` (4.5, 3.5), Spectra utilise `fa-star-half-alt`. Pour score `.3`, `.7`, etc. — pas de demi-étoile précise. Arrondir à `.5` ou afficher juste `4.3 / 5` numérique sans visuel demi |
| **Schema fake** | NE JAMAIS générer un `aggregateRating` schema.org si tu n'as pas de vraies reviews. Google peut sanctionner (SEO penalty) pour fake ratings. Mettre `schema: false` si pas de count réel |
| **Couleur jaune** | `#FBBF24` (amber-400) est le standard universel des étoiles. Éviter de remplacer par une couleur de palette (orange palette) — le user attend du jaune |
| **Stars empty** | Pour 4/5 étoiles, afficher 4 pleines + 1 vide grise (`#cbd5e1`). NE PAS masquer la 5e (UX cassée, l'utilisateur ne sait pas que le max est 5) |
| **a11y** | `role="img"` + `aria-label="4.5 sur 5 étoiles"` sur le wrapper. Sinon les screen readers lisent juste « étoile étoile étoile » sans contexte |
| **Lien vers reviews** | Si la note est cliquable (lien vers la page des avis), wrapper le tout dans `<a href="#reviews">`, pas juste le score |

## Variantes

### Variante 1 — Inline avec témoignage (testimonials-cards)

Au-dessus de chaque card de testimonial, 5 étoiles jaunes pleine note. Pour renforcer la crédibilité.

Cf `patterns/testimonials-cards.md` variante 1 (déjà documenté avec `uagb/icon-list` + `fa-star`).

### Variante 2 — Aggregate rating en haut de page produit

```
NOTE MOYENNE
★★★★★ 4.8 / 5  (327 avis)
```

Lien vers `#reviews` ancre. Schema.org `aggregateRating` activé. Ce cas d'usage est le plus rentable en SEO (rich snippet Google avec étoiles dans la SERP).

### Variante 3 — Note formation / cours

```
NOTE DES APPRENANTS
★★★★★ 4.9 / 5  (1240 avis)
```

Schema `Course` + `aggregateRating` imbriqué. Pour landing formation.

### Variante 4 — Note service (Trustpilot-style)

Si tu intègres Trustpilot ou Google Reviews via API, afficher le score live + lien vers la source :

```
4.7 ★★★★★  basé sur 89 avis Google
```

Lien vers la fiche Google Business. Pas de schema fake (Google détecte si le score ne correspond pas au cumul réel).

### Variante 5 — Rating interactif (user vote)

Pour permettre à l'utilisateur de noter (cliquable étoiles), il faut :
- JS pour capter le clic
- Backend pour stocker (custom endpoint ou plugin tiers : YASR, RatingSystem, etc.)

`uagb/star-rating` n'est PAS interactif natif. Pour rating user, préférer un plugin dédié.

## Test post-génération

1. Vérifier le bon nombre d'étoiles (4.5 → 4 full + 1 half + 0 empty)
2. Vérifier la couleur jaune (`#FBBF24`)
3. Vérifier l'affichage du score numérique « 4.5 / 5 »
4. Vérifier le count d'avis « (127 avis) »
5. SEO : tester le rich snippet via [Google Rich Results Test](https://search.google.com/test/rich-results) → doit afficher les étoiles
6. a11y : `aria-label` lu par screen readers (« 4 virgule 5 sur 5 étoiles »)

## Pour aller plus loin

- Review complet (avec auteur + texte + photo) : `patterns/review-product.md`
- Star rating dans testimonial : `patterns/testimonials-cards.md` variante 1
- Whitelist icônes (`star`, `star-half-alt` sont OK) : `references/spectra-icons-list.md`
- Plugins reviews avancés : YASR, Site Reviews, Schema & Structured Data for WP
