# Template : Page d'accueil

> **Use case** : page d'accueil d'un site WordPress, quel que soit son secteur (boutique e-commerce, agence, restaurant, formation, SaaS, association, etc.). Structure éprouvée pour conversion : hero promesse + preuves chiffrées + bénéfices + qui-on-est + témoignages + offre/CTA + FAQ + CTA final.
>
> **Type** : blueprint d'assemblage. Ce template ne contient pas le markup Gutenberg complet — il indique l'ordre des patterns à assembler, les variables à remplir et les effets WOW recommandés. L'assemblage final est produit par le workflow [`deploy-template.md`](../workflows/deploy-template.md). Voir [`templates/README.md`](README.md) pour comprendre cette architecture.

## Variables à fournir

| Variable | Description | Exemple générique |
|----------|-------------|-------------------|
| `{{BRAND_NAME}}` | Nom de la marque / entreprise | "Atelier Lumen" |
| `{{TAGLINE}}` | Promesse en une phrase | "Le café de spécialité torréfié à Marseille" |
| `{{SUBPROMISE}}` | Argument de soutien | "Origines tracées, livraison en 48 h, abonnement flexible" |
| `{{CTA_LABEL}}` | Texte CTA principal | "Découvrir nos cafés" |
| `{{CTA_URL}}` | URL CTA principal | `/boutique/` |
| `{{CTA_SECONDARY_LABEL}}` | Texte CTA secondaire | "Comment on torréfie" |
| `{{CTA_SECONDARY_URL}}` | URL CTA secondaire | `#story` |
| `{{HERO_IMAGE}}` | Image hero (héro produit, lieu, équipe) | URL |
| `{{S1}}–{{S4}}` | 4 stats (chiffre + label) | "200+ cafés référencés" |
| `{{B1_TITLE}}–{{B3_TITLE}}` | 3 bénéfices clés | "Origine tracée" |
| `{{B1_DESC}}–{{B3_DESC}}` | 3 descriptions courtes | "Du producteur à votre tasse en 4 étapes" |
| `{{STORY_TITLE}}` | Titre H2 about | "Pourquoi on fait ça" |
| `{{STORY_TEXT}}` | 2-3 paragraphes | — |
| `{{STORY_IMAGE}}` | Image about (équipe, lieu, processus) | URL |
| `{{T1}}–{{T3}}` | 3 témoignages (name, meta, quote, avatar) | — |
| `{{Q1}}–{{Q6}}` | 6 questions FAQ + réponses | — |
| `{{CTA_FINAL_HEADING}}` | Titre du CTA final | "Prêt à goûter la différence ?" |
| `{{CTA_FINAL_DESC}}` | Sous-titre CTA final | "Première commande livrée gratuitement" |

## Structure de la page

```
1. Hero CTA Split (ou Hero Image Overlay si la marque est visuelle)
   - Eyebrow : nom de la marque ou catégorie
   - H1 : {{TAGLINE}}
   - Subline : {{SUBPROMISE}}
   - CTA primary : {{CTA_LABEL}} → {{CTA_URL}}
   - CTA secondary : {{CTA_SECONDARY_LABEL}} → {{CTA_SECONDARY_URL}}
   - Image : {{HERO_IMAGE}}

2. Stats Bar Editorial (preuve chiffrée immédiate)
   - {{S1}} / {{S2}} / {{S3}} / {{S4}}
   - 4 chiffres orange + labels uppercase tracking

3. Features 3-Cols (3 bénéfices clés)
   - {{B1_TITLE}} / {{B1_DESC}}
   - {{B2_TITLE}} / {{B2_DESC}}
   - {{B3_TITLE}} / {{B3_DESC}}

4. About Story Split (qui-on-est + processus + photo)
   - H2 : {{STORY_TITLE}}
   - Texte : {{STORY_TEXT}}
   - Image : {{STORY_IMAGE}}

5. Testimonials Cards (3 témoignages avec citation + avatar)
   - {{T1}}, {{T2}}, {{T3}}

6. CTA Banner Fullwidth (offre commerciale ou inscription newsletter)
   - Headline + sous-titre
   - 1 ou 2 CTAs

7. FAQ Accordion (6 questions) + schema FAQPage
   - {{Q1}}–{{Q6}} : adapter selon secteur (livraison, retour, contact, etc.)

8. CTA Banner Final (dernière conversion)
   - Headline : {{CTA_FINAL_HEADING}}
   - Subline : {{CTA_FINAL_DESC}}
   - CTA primary : {{CTA_LABEL}}
```

## Variantes par secteur

| Secteur | Structure recommandée |
|---|---|
| **E-commerce** | Hero produit + Stats + Features qualité + Post Display (grid 8 produits récents) + Testimonials + FAQ + CTA |
| **Restaurant / café** | Hero ambiance + Story brand + Menu (Price List) + Google Maps + Testimonials + Forms réservation + CTA |
| **Agence / SaaS** | Hero promesse + Stats + Features + Logos clients + Story + Pricing 3-tiers + Testimonials + FAQ + CTA |
| **Association** | Hero mission + Stats impact + Story + Counter (membres) + Témoignages bénéficiaires + Forms don + CTA |
| **Formation / éducation** | Hero promesse + Stats résultats + Features bénéfices + Programme (timeline) + Testimonials + Pricing + FAQ + CTA |
| **Immobilier** | Hero photo + Stats biens + Features expertise + Post Display (annonces récentes) + Google Maps + Forms contact + CTA |
| **Artisan / freelance** | Hero portrait + Story + Features prestations + Galerie projets (Image Gallery) + Testimonials + Forms devis + CTA |

Pour chacun de ces secteurs, le skill substitue automatiquement les patterns spécifiques (Price List pour restaurant, Pricing pour SaaS, Post Display pour blog, etc.).

## Palette suggérée

Adapter selon l'identité de marque :

| Type de marque | Palette Astra recommandée |
|---|---|
| Premium / luxe | preset_5 (doré) ou palette user noir/or |
| Tech / SaaS / startup | preset_1 (bleu corporate) |
| Créatif / indie / bold | preset_8 (orange) ou preset_3 (saturé chaud) |
| Naturel / bio / éthique | preset_4 (vert) |
| Élégant / mode | palette user noir/blanc + accent unique |

Le skill résout automatiquement les hex finaux via `var(--ast-global-color-X)` selon la palette active du site cible (cf `references/semantic-color-roles.md` pour les slots GARANTIS vs VARIABLES).

## Block markup (squelette assemblé)

Le skill compose dynamiquement à partir des fichiers `patterns/`. Liste des patterns à concaténer dans l'ordre :

```
patterns/hero-cta-split.md         (ou hero-image-overlay.md si image dominante)
patterns/stats-bar-editorial.md    (4 chiffres clés)
patterns/features-3-cols.md        (ou features-numbered.md pour effet éditorial)
patterns/about-story-split.md      (image + texte 2-cols)
patterns/testimonials-cards.md     (3 cards avec avatars)
patterns/cta-banner-fullwidth.md   (offre commerciale intermédiaire)
patterns/faq-accordion.md          (6 questions + schema FAQPage)
patterns/cta-banner-fullwidth.md   (CTA final, variante outro)
```

## Bonnes pratiques copywriting

- **Hero** : 1 promesse concrète + 1 différenciateur (« le seul X qui Y »)
- **Stats** : chiffres vérifiables (« 200+ produits », pas « beaucoup de produits »)
- **Bénéfices** : centrés sur l'utilisateur (« Tu choisis ton rythme » > « Vidéos disponibles »)
- **Story** : pourquoi vous, pas comment
- **Témoignages** : profils similaires au public cible (B2B pour B2B, particulier pour particulier)
- **FAQ** : couvre les 6 vraies objections (prix, livraison/délai, retour/garantie, contact, sécurité, ce qui distingue)

## Compatibilité

- **Spectra** ≥ 2.10
- **Astra** : optionnel mais recommandé (palette + header builder)
- **Schema SEO** : Organization + FAQPage (auto via uagb/faq) + Review (selon secteur via uagb/review). Ajouter LocalBusiness si commerce physique.

## Pour aller plus loin

- Workflow déploiement : `../workflows/deploy-template.md`
- Patterns référencés : `hero-cta-split.md`, `stats-bar-editorial.md`, `features-3-cols.md`, `about-story-split.md`, `testimonials-cards.md`, `faq-accordion.md`, `cta-banner-fullwidth.md`
- Recettes wow uagb/container : `../modules/spectra/container-wow-recipes.md`
- Exemples concrets : `examples/landing-formation-complete-markup.html` (cas formation, ~40 KB de markup validé)
