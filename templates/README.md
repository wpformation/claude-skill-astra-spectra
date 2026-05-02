# Templates — blueprints d'assemblage

## Qu'est-ce qu'un template dans ce skill ?

Un **template** ici n'est PAS un fichier de 1500 lignes de markup Gutenberg pré-écrit. C'est un **blueprint** : la liste ordonnée des patterns à assembler, les variables à remplir, la palette suggérée, et les effets WOW recommandés.

Le workflow [`deploy-template.md`](../workflows/deploy-template.md) lit le blueprint et **assemble dynamiquement** la page finale en concaténant les patterns dans l'ordre indiqué.

## Pourquoi ce choix d'architecture

1. **Maintenabilité** : un fix dans `patterns/pricing-3-tiers.md` se propage automatiquement à tous les templates qui l'utilisent. Pas de drift entre 8 copies du même bloc pricing.
2. **Composition** : les utilisateurs peuvent mixer patterns (`hero-cta-split` + leurs propres sections + `cta-banner-fullwidth`) sans dupliquer du markup.
3. **Variables centrées** : un template = une liste de variables minimales (titre, prix, sections), pas 50 variables éparpillées dans du markup pré-écrit.
4. **Compatibilité Spectra évolutif** : si Spectra change la structure de `uagb/info-box` en v3.0, on met à jour 1 pattern, pas 8 templates.

## Templates disponibles (8)

| Template | Use case | Patterns assemblés |
|----------|--------|-------------------|
| [page-accueil.md](page-accueil.md) | Page d'accueil universelle (e-commerce, restaurant, agence, formation, etc. — variantes par secteur) | hero-cta-split + stats-bar-editorial + features-3-cols + about-story-split + testimonials-cards + cta-banner-fullwidth + faq-accordion + cta final |
| [landing-saas.md](landing-saas.md) | Landing page SaaS (acquisition payante) | hero-cta-split + features-3-cols (×2 → 4-cols) + testimonials-grid + pricing-3-tiers + faq-accordion + cta-banner-fullwidth |
| [page-agence.md](page-agence.md) | Page d'accueil agence créative ou tech | hero-cta-split + features-3-cols + team-grid + stats-counters + testimonials-grid + cta-banner-fullwidth |
| [page-tarifs.md](page-tarifs.md) | Page tarifs dédiée avec comparateur | hero + pricing-3-tiers + comparateur tableau + faq-accordion + cta |
| [page-contact.md](page-contact.md) | Page contact avec formulaire + map | hero + forms + google-maps + horaires + team-grid + cta |
| [page-a-propos.md](page-a-propos.md) | Page « à propos » avec timeline + équipe | hero + about-story-split + timeline-vertical + team-grid + testimonials + cta |
| [blog-editorial.md](blog-editorial.md) | Article éditorial long mix core + Spectra | core/heading + table-of-contents + article-content-rich + uagb/inline-notice + uagb/faq + author-bio + cta |
| [e-commerce-produit.md](e-commerce-produit.md) | Page produit e-commerce | hero produit + image-gallery + uagb/review + price-list + faq + cta |

## Comment utiliser un template

### Via le workflow `deploy-template`

```
> /astra-spectra deploy template=page-accueil \
    brand="Atelier Lumen" tagline="Le café de spécialité torréfié à Marseille" cta_url="/boutique/"
```

Le workflow lit `templates/page-accueil.md`, mappe les variables, assemble les patterns, valide le markup roundtrip, exécute le pre-flight check, POST en draft via REST API, retourne l'URL d'édition.

### Manuellement (si tu veux contrôler)

1. Ouvrir le template souhaité
2. Lire la section « Structure » (ordre des patterns)
3. Lire la section « Variables minimales » (ce qu'il faut fournir)
4. Pour chaque pattern listé : lire le fichier `patterns/<nom>.md`, remplir les variables, concaténer le markup
5. Valider via `php scripts/validate-block-markup.php < page.html`
6. POST via `php scripts/post-page-via-rest.php --site-url=... --user=... --app-password=... --content-file=page.html --title="..."`

## Pourquoi pas un template avec markup pré-écrit complet ?

J'ai testé. Sur 9 sections × 50-150 lignes de markup chacune, on obtient un fichier de 1500 lignes par template. Les 3 inconvénients :

1. **Drift** : un changement dans `pricing-3-tiers.md` ne se propage pas → templates obsolètes en 2 mois
2. **Variables explosent** : `{{T1_FEATURE_1}}` `{{T1_FEATURE_2}}` ... × 3 tiers × 8 templates = 96 variables, le LLM se perd
3. **Maintenance** : un fix block_id sur info-box (comme celui de v0.8.1) demande de refaire tous les templates manuellement

Le blueprint sépare la composition (template) de la mécanique (pattern). Tout fix de pattern bénéficie à tous les templates instantanément. C'est délibéré.
