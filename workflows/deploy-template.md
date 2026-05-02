# Workflow : Deploy Template (Killer Feature 3)

> **Promesse** : l'utilisateur dit « déploie le template SaaS » ou « installe le template formation » → le skill récupère un template complet de page (8-10 disponibles) et l'installe en draft sur le site cible avec adaptation automatique du contenu.

## Templates disponibles

Voir le dossier [`../templates/`](../templates/) pour les 8 templates production-ready :

| Template ID | Use case | Patterns inclus |
|-------------|----------|-----------------|
| `landing-saas` | Landing page d'une app SaaS / outil web | Hero + Features 4-cols + Stats + Pricing 3-tiers + Testimonials + FAQ + CTA |
| `page-formation` | Page d'une formation en ligne | Hero + Pourquoi nous choisir + Programme + Pricing + Témoignages + FAQ + CTA inscription |
| `blog-editorial` | Page magazine / blog éditorial | Hero éditorial + Posts grid + Newsletter + Categories |
| `e-commerce-produit` | Page produit physique ou digital | Hero produit + Features + Galerie + Reviews + Pricing + Garanties + FAQ |
| `page-agence` | Site vitrine d'agence digital / marketing | Hero impact + Services + Case studies + Team + Process + CTA |
| `page-contact` | Page contact riche | Hero + Form + Map + Coordonnées + FAQ + Social |
| `page-tarifs` | Page tarifs standalone | Hero pricing + Pricing 3-tiers + Comparatif tableau + FAQ + CTA |
| `page-a-propos` | Page À propos d'une entreprise | Hero histoire + Timeline + Team + Valeurs + Stats + CTA |
| `page-404` | Page d'erreur 404 customisée | Hero 404 + Suggestions liens + Search + CTA retour accueil |
| `coming-soon` | Page coming soon / pré-launch | Hero countdown + Newsletter + Social + Téléchargement preview |

## Exemple de prompt

```
Déploie le template page-formation sur https://playground.wordpress.net/scope:humble-hip-valley
Remplace le contenu par :
- Nom formation : "WordPress Mastery"
- Prix : 297€ une fois
- Durée : 6 modules sur 8 semaines
- Public cible : freelances et agences débutantes
- Formateur : Fabrice Ducarme
```

## Étapes du workflow

### Étape 1 — Détection environnement

Idem que `new-page-from-brief.md`.

### Étape 2 — Sélection du template

Lire le fichier `templates/{{TEMPLATE_ID}}.md` pour récupérer :

1. La structure du template (sections + patterns utilisés)
2. La liste des variables `{{...}}` à remplir
3. Le markup pré-assemblé (concaténation de patterns)
4. Les recommandations design (palette suggérée, images type)

### Étape 3 — Adaptation du contenu

Si l'utilisateur fournit des inputs (nom formation, prix, etc.), les mapper aux variables du template.

Si certaines variables ne sont pas fournies, le skill peut :
- **Mode A : « content placeholder »** — remplir avec du contenu type (« Lorem ipsum » ou phrases neutres)
- **Mode B : interactif** — demander les manquants (« Quel prix pour la formation ? »)
- **Mode C : intelligent** — déduire du contexte fourni (ex: si « formation WordPress » fourni, FAQ pré-remplie avec questions type formation)

Recommandé : **Mode C** par défaut + **Mode B** pour les variables critiques (prix, CTA URL).

### Étape 4 — Application de la palette

Si l'utilisateur a précisé une palette (« WPF orange », « corporate blue »), appliquer via `scripts/apply-design-tokens.php` AVANT le POST de la page (sinon les blocs hériteront de la palette par défaut).

```bash
# Si Astra présent : modifie astra-settings
php apply-design-tokens.php '["#FF8C00","#E67E00","#0E0E14","#334155","#FFFFFF","#F0F5FA","#111111","#D1D5DB","#000000"]'

# Ou utilise un preset Astra natif
php apply-design-tokens.php preset_8
```

### Étape 5 — Validation roundtrip

Idem que `new-page-from-brief.md` étape 5.

### Étape 6 — POST de la page

Avant le POST, résoudre `{{ASTRA_TEMPLATE}}` via le mapping suivant (selon le `template_name` choisi à l'étape 1) :

| `template_name` (input) | `{{ASTRA_TEMPLATE}}` (résolu) | Justification |
|------------------------|------------------------------|---------------|
| `page-formation`       | `""` (vide = défaut Astra)    | Sidebar OK pour navigation cours |
| `landing-saas`         | `""` (sidebar désactivable au Customizer) | Conversion = pas de distraction |
| `page-agence`          | `""` (sidebar désactivable au Customizer) | Hero impact full-width |
| `blog-editorial` (v1.0) | `""` (sidebar par défaut)     | Content + widgets utiles |

Par défaut on laisse `""` : le thème applique son template configuré dans Customizer > Layout > Page Layout. Plus sûr que de hardcoder un slug de template qui peut différer selon les thèmes.

```bash
curl -X POST 'https://{{SITE_URL}}/wp-json/wp/v2/pages' \
  -u 'admin:{{APP_PASSWORD}}' \
  -H 'Content-Type: application/json' \
  -d '{
    "title": "{{PAGE_TITLE}}",
    "slug": "{{PAGE_SLUG}}",
    "status": "draft",
    "content": "{{TEMPLATE_FILLED_MARKUP}}",
    "template": "{{ASTRA_TEMPLATE}}"
  }'
```

Si tu n'es pas certain d'une valeur valide pour le thème actif, **omet le champ `template` du payload** : Astra appliquera le template par défaut configuré dans Customizer.

> **Note FSE** : sur les block themes (FSE), le champ `template` est ignoré. Le skill détecte ce cas via `detect-environment.php` (champ `theme.is_block_theme`) et omet le champ automatiquement.

### Étape 7 — Récap

```markdown
✅ Template `page-formation` déployé

**Page** : "WordPress Mastery"
**ID** : 42
**Slug** : /wordpress-mastery/
**Status** : draft

**Sections déployées** (7) :
- Hero formation (uagb/container hero-cta-split)
- Pourquoi nous choisir (uagb/container + 4× uagb/info-box)
- Programme 6 modules (uagb/timeline)
- Pricing one-shot (uagb/container + uagb/info-box pricing card)
- Témoignages (uagb/testimonial 3 cards)
- FAQ formation (uagb/faq + 8 questions)
- CTA inscription (uagb/container gradient)

**Palette appliquée** : WPF orange (`var(--ast-global-color-0)` = #FF8C00)

**URL d'édition** : https://{{SITE_URL}}/wp-admin/post.php?post=42&action=edit
**URL de prévisualisation** : https://{{SITE_URL}}/?page_id=42&preview=true

**Variables à compléter manuellement avant publication** :
- Photo formateur (currently placeholder)
- 3 photos témoignages (currently placeholder)
- Vidéo de présentation (currently placeholder YouTube)
```

## Personnalisation post-déploiement

L'utilisateur peut demander des ajustements après déploiement initial :

```
Sur la page que tu viens de déployer (ID 42), enlève la section "Programme" et ajoute une section "Garanties"
```

Le skill traite ça comme une refonte ciblée :
1. Snapshot la page (ID 42)
2. Identifie la section à supprimer
3. Génère la nouvelle section depuis le pattern adapté
4. Update la page (PUT REST API)

## Templates personnalisés (v2.0)

En v2.0, le skill permettra à l'utilisateur de **sauvegarder un template custom** depuis une page existante :

```
Sauvegarde la page /a-propos/ comme template "agence-creative"
```

→ Le skill snapshot la page, l'enregistre en `templates/custom/agence-creative.md`, et la rend disponible aux futurs déploiements.

## Cas d'erreur

### Template ID inconnu

→ Lister les templates disponibles + suggestions proches (fuzzy match sur le nom).

### Variables critiques manquantes

→ Mode B : demander à l'utilisateur (« Quel prix de la formation ? »).

### Palette non disponible

→ Liste des palettes disponibles : presets Astra (preset_1 à 11) + palettes WPF (wpf-orange, wpf-corporate, etc.). Fallback : appliquer la palette par défaut du site.

## Pour aller plus loin

- Killer feature 1 : `new-page-from-brief.md`
- Killer feature 2 : `refonte-page-existante.md`
- Templates : `../templates/`
- Patterns sous-jacents : `../patterns/`
- Application de palette : `../scripts/apply-design-tokens.php`
