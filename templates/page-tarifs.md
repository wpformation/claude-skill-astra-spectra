# Template : Page Tarifs (pricing)

> **Use case** : page tarifs SaaS, formation, abonnement, service. 3 tiers minimum + comparison table + FAQ + testimonials clients + CTA.

## Composition de patterns

```
1. patterns/hero-image-overlay.md         (variante hero court)
2. Pricing 3 tiers (composition custom, voir ci-dessous)
3. Comparison table (uagb/table ou container row)
4. patterns/testimonials-cards.md         (3 clients qui ont choisi)
5. patterns/features-numbered.md          (3 garanties : remboursement / support / mises à jour)
6. patterns/faq-accordion.md              (questions tarifs)
7. patterns/cta-banner-fullwidth.md       (CTA contact si question)
```

## Variables d'entrée

| Variable | Description |
|---|---|
| `{{HERO_HEADING}}` | « Des tarifs simples, transparents » |
| `{{HERO_SUBHEADING}}` | Tagline pricing |
| `{{TIER_BASIC}}` `{{TIER_PRO}}` `{{TIER_PREMIUM}}` | Configurations 3 tiers |
| Pour chaque tier : `name, price, period, features[], cta_label, cta_link, recommended (bool)` |
| `{{COMPARISON_FEATURES[]}}` | Features pour table comparison |
| `{{TESTIMONIALS[3]}}` | Clients qui ont choisi chaque tier |
| `{{FAQ_PRICING[5-7]}}` | Questions tarifs courantes |

## Sections clés

### 1. Hero court tarifs

Pattern `hero-image-overlay.md` variante :
- Padding 120 (court)
- Heading « Des tarifs simples, transparents »
- Pas de CTA hero (les CTAs sont sur les tiers)
- Optionnel : toggle mensuel/annuel avec discount

### 2. Pricing 3 tiers

Container row 3-cols :

```
container#pricing-row (alignfull, bg #fafafa, padding 100)
  ├─ container#tier-basic (white card, padding 48, border subtle)
  │     ├─ tier name « Basic »
  │     ├─ tier price « 29&nbsp;€ /mois »
  │     ├─ tier desc
  │     ├─ uagb/icon-list features (avec ✓ / ✗)
  │     └─ uagb/buttons CTA
  ├─ container#tier-pro (highlighted, border accent, "Le plus populaire")
  └─ container#tier-premium (...)
```

Variantes prix :
- Mensuel (29 € / mois)
- Annuel (290 € / an, 17% off)
- One-time (149 € paiement unique)
- Custom (Devis sur mesure)

### 3. Comparison table

Plus détaillé que les tiers. Liste de 15-25 features avec ✓/✗ par tier.

```html
<!-- wp:uagb/table {"block_id":"{slug}-comparison","columns":4,"rows":20,"hasFixedLayout":true,"head":[{"cells":[{"content":"Feature","tag":"th"},{"content":"Basic","tag":"th"},{"content":"Pro","tag":"th"},{"content":"Premium","tag":"th"}]}],"body":[{"cells":[{"content":"227 cours","tag":"td"},{"content":"&check;","tag":"td"},{"content":"&check;","tag":"td"},{"content":"&check;","tag":"td"}]},{"cells":[{"content":"22 QCM","tag":"td"},{"content":"5","tag":"td"},{"content":"&check; 22","tag":"td"},{"content":"&check; 22","tag":"td"}]},{"cells":[{"content":"Suivi personnalis&eacute;","tag":"td"},{"content":"&times;","tag":"td"},{"content":"&times;","tag":"td"},{"content":"&check;","tag":"td"}]}]} -->
```

### 4. Testimonials par tier

Pattern `testimonials-cards.md` mais avec 3 clients :
- 1 sur tier Basic
- 1 sur tier Pro
- 1 sur tier Premium
Chaque card mentionne « J'ai choisi {{TIER}} parce que... ».

### 5. Garanties (3 features-numbered)

Pattern `features-numbered.md` adapté :
- 01 — REMBOURSEMENT « 30 jours satisfait ou remboursé »
- 02 — SUPPORT « Réponse en 24h ouvrées »
- 03 — MISES À JOUR « Inclues à vie sur tous les tiers »

### 6. FAQ tarifs

Pattern `faq-accordion.md`. Questions :
- Comment changer de tier en cours d'abonnement ?
- Y a-t-il une période d'essai gratuite ?
- Quels moyens de paiement acceptés ?
- Politique de remboursement ?
- Réductions étudiants / OPCO ?
- TVA / facturation entreprise ?

### 7. CTA final « Une question ? »

Pattern `cta-banner-fullwidth.md` :
- Heading « Tu hésites encore ? Parlons-en. »
- Desc « Nous répondons sous 24h. Aucun engagement. »
- 2 CTAs : Réserver un appel (primary) / Email (secondary)

## Schema SEO

JSON-LD `Service` ou `Offer` pour chaque tier. Permet à Google d'afficher les prix dans la SERP.

```json
{
  "@type": "Service",
  "serviceType": "Formation BTS NDRC",
  "offers": [
    {"@type": "Offer", "name": "Basic", "price": "29", "priceCurrency": "EUR"},
    {"@type": "Offer", "name": "Pro", "price": "59", "priceCurrency": "EUR"},
    {"@type": "Offer", "name": "Premium", "price": "129", "priceCurrency": "EUR"}
  ]
}
```

## CSS overrides minimum

```css
/* Tier cards */
.uagb-block-{slug}-tier-basic,
.uagb-block-{slug}-tier-pro,
.uagb-block-{slug}-tier-premium {
  background-color: #ffffff !important;
  border-radius: 24px !important;
  border: 1px solid #e5e7eb !important;
  padding: 48px !important;
}

/* Pro tier highlighted */
.uagb-block-{slug}-tier-pro {
  border: 2px solid var(--ast-global-color-0) !important;
  transform: scale(1.05);
  box-shadow: 0 24px 48px rgba(15,23,42,0.12);
}

/* "Recommandé" badge */
.uagb-block-{slug}-tier-pro::before {
  content: "Le plus populaire";
  position: absolute;
  top: -16px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--ast-global-color-0);
  color: #fff;
  padding: 6px 20px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Tier price */
.uagb-block-{slug}-tier-pro .price {
  font-size: 64px !important;
  font-weight: 800 !important;
  color: var(--ast-global-color-0) !important;
}

/* Comparison table styling */
.uagb-block-{slug}-comparison th {
  background-color: #0F172A;
  color: #ffffff;
  padding: 18px;
  font-weight: 700;
}
.uagb-block-{slug}-comparison td {
  padding: 14px 18px;
  border-bottom: 1px solid #e5e7eb;
}
.uagb-block-{slug}-comparison tr:nth-child(even) td {
  background-color: #fafafa;
}
```

## Configuration Astra

Identique landing : page-builder + no-sidebar + no-title.

## Variantes par secteur

- **SaaS** : 3 tiers mensuel/annuel toggle, free trial sur tier Pro
- **Formation** : 3 tiers selon durée (1 mois / 3 mois / 6 mois), accès lifetime sur Premium
- **Coaching** : 3 packages selon nombre de séances (1 / 5 / 10)
- **Service web** : 3 forfaits selon scope (basic / standard / premium)

## Workflow

1. Brief : `« crée la page tarifs de mon SaaS avec 3 plans : 9€, 29€, 79€ par mois »`
2. Lire patterns + references
3. Composer 7 sections
4. Générer CSS overrides
5. Injecter schema Service/Offer
6. POST + meta + regen
7. Test :
   - Click chaque CTA tier → flow checkout
   - Toggle mensuel/annuel si présent
   - Comparison table responsive scroll horizontal
   - Rich Results Test pour Service schema
