# Templates — blueprints d'assemblage

## Qu'est-ce qu'un template dans ce skill ?

Un **template** ici n'est PAS un fichier de 1500 lignes de markup Gutenberg pré-écrit. C'est un **blueprint** : la liste ordonnée des patterns à assembler, les variables à remplir, la palette suggérée, et les effets WOW recommandés.

Le workflow [`deploy-template.md`](../workflows/deploy-template.md) lit le blueprint et **assemble dynamiquement** la page finale en concaténant les patterns dans l'ordre indiqué.

## Pourquoi ce choix d'architecture

1. **Maintenabilité** : un fix dans `patterns/pricing-3-tiers.md` se propage automatiquement à tous les templates qui l'utilisent. Pas de drift entre 8 copies du même bloc pricing.
2. **Composition** : les utilisateurs peuvent mixer patterns (`hero-cta-split` + leurs propres sections + `cta-banner-fullwidth`) sans dupliquer du markup.
3. **Variables centrées** : un template = une liste de variables minimales (titre, prix, sections), pas 50 variables éparpillées dans du markup pré-écrit.
4. **Compatibilité Spectra évolutif** : si Spectra change la structure de `uagb/info-box` en v3.0, on met à jour 1 pattern, pas 8 templates.

## Templates v0.8 disponibles

| Template | Status | Patterns assemblés |
|----------|--------|-------------------|
| [page-formation.md](page-formation.md) | ✅ Complet | hero-cta-split + features-3-cols + 1 section pricing custom + faq-accordion + cta-banner-fullwidth |
| [landing-saas.md](landing-saas.md) | ✅ Blueprint | hero-cta-split + features-3-cols (×2 → 4-cols variante) + testimonials-grid + pricing-3-tiers + faq-accordion + cta-banner-fullwidth |
| [page-agence.md](page-agence.md) | ✅ Blueprint | hero-cta-split + features-3-cols + team-grid + stats-counters + testimonials-grid + cta-banner-fullwidth |

## Templates prévus en v1.0

- `blog-editorial.md` : article éditorial mix core + Spectra (TOC + content + FAQ + author bio)
- `e-commerce-produit.md` : page produit avec galerie + reviews + pricing + FAQ
- `page-tarifs.md` : page tarifs dédiée avec pricing + comparateur tableau + FAQ
- `page-contact.md` : page contact avec formulaire + map + horaires + équipe
- `page-a-propos.md` : page about avec timeline + équipe + valeurs + témoignages

## Comment utiliser un template

### Via le workflow `deploy-template`

```
> /astra-spectra deploy template=page-formation \
    titre="Formation Next.js 16" prix=1900 duree=35h opco=true
```

Le workflow lit `templates/page-formation.md`, mappe les variables, assemble les patterns, valide le markup roundtrip, POST en draft via REST API, retourne l'URL d'édition.

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
