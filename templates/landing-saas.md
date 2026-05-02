# Template : Landing SaaS

> **Use case** : Landing page d'une application SaaS / outil web (analytics, CRM, dashboard, etc.). Structure conversion B2B classique.

## Structure

```
1. Hero CTA Split avec capture d'écran produit ou animation Lottie
   - Headline : promesse business (« Augmente ton ROI de 30% »)
   - Subline : différenciateur (« La seule plateforme qui... »)
   - CTA primary : "Essai gratuit 14 jours"
   - CTA secondary : "Voir une démo"
   - Image : screenshot dashboard

2. Logos clients (preuve sociale tier 1)
   - 5-7 logos en grid horizontal, gris semi-transparent
   - Container alignfull padding 40px verticaux

3. Features 4-Cols (les 4 features principales avec icônes)
   - Couleurs primaires sur les icônes
   - Box hover avec scale + shadow

4. Container split (50/50) — Comment ça marche
   - Texte gauche : 3-step process
   - GIF / vidéo droite

5. Stats Counters (preuve sociale tier 2)
   - "X clients", "Y K transactions/jour", "Z millions traités"

6. Testimonials Grid (3 témoignages avec photos + logos clients)
   - Variante : 1 témoignage central featured + 2 autres en bas

7. Pricing 3 Tiers (Starter / Pro / Enterprise)
   - Pro mis en avant avec badge "Le plus populaire"

8. FAQ (8 questions B2B : intégrations, sécurité, RGPD, support, contrat)

9. CTA Banner Final
   - "Prêt à tester ?" + CTA "Essai gratuit"
   - Background gradient primary → primary darker
```

## Variables minimales

```yaml
PRODUCT_NAME: "Outil X"
PROMISE: "Augmente ton ROI de 30 %"
DIFFERENTIATOR: "La seule plateforme qui [unique selling proposition]"
TRIAL_DAYS: 14
PRICING:
  starter: "29 € /mois"
  pro: "79 € /mois"
  enterprise: "Sur devis"
HERO_IMAGE_URL: "https://..."
LOGOS_URLS: ["url1", "url2", ...]
TESTIMONIALS: [...]
FAQ_QUESTIONS: [...]
```

## Palette suggérée

- **wpf-corporate** (#1E40AF bleu) — défaut B2B
- **preset_2** (#6528F7 violet) — SaaS modern / Web3
- **preset_7** (#1B9C85 turquoise) — fintech / sécurité
- **preset_4** (#54B435 vert) — éco / sustainability

## Effet wow recommandé

Hero avec **gradient mesh** (recette 11 dans `container-wow-recipes.md`) + screenshot produit avec **box shadow large** + **hover scale 1.02** subtil.

CTA banner final avec **diagonal dividers** (recette 4) pour transitions élégantes.

## Compatibilité

- Spectra ≥ 2.10, Astra optionnel
- Schema SEO : SoftwareApplication (manuel via Yoast) + FAQPage auto + Review optionnel
- Responsive : split 50/50 desktop, stack mobile, padding réduit
