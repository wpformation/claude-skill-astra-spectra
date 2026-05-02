# Workflow : New Page From Brief (Killer Feature 1)

> **Promesse** : l'utilisateur décrit ce qu'il veut en langage naturel, le skill génère une page WordPress draft complète et cohérente design-wise en moins de 2 minutes.

## Exemple de prompt utilisateur

```
Crée-moi une landing page formation WordPress avec :
- Hero avec un titre fort et 2 CTAs (primary "Je m'inscris", secondary "Voir le programme")
- 3 features avec icônes
- Pricing 3 tiers (Starter à 19€, Pro à 49€ populaire, Enterprise sur devis)
- 2 témoignages
- FAQ avec 5 questions
- CTA final pour s'inscrire

Site cible : https://playground.wordpress.net/scope:humble-hip-valley
Application Password : xxxx xxxx xxxx xxxx
```

## Étapes du workflow

### Étape 1 — Détection environnement (5-15s)

Exécuter `scripts/detect-environment.php` sur le site cible (via REST API ou one-shot PHP) :

```bash
curl -X POST 'https://{{SITE_URL}}/wp-json/wpf-skill/v1/detect' \
  -u 'admin:{{APP_PASSWORD}}'

# Ou via execute_php si Playground :
playground_execute_php(siteId, '<?php require("/wordpress/wp-load.php"); include "detect-environment.php";')
```

Résultat attendu :

```json
{
  "wp_version": "6.9.4",
  "spectra": { "active": true, "version": "2.19.25" },
  "astra": { "active": true, "version": "4.13.1" },
  "verdict": "GO"
}
```

Si `verdict: "BLOCKED"` → afficher les blockers et arrêter. Lien d'install si Spectra manquant.

### Étape 2 — Parsing du brief (10s)

Tokeniser la demande en intentions identifiables. Pour chaque intention :

1. Mapper vers la table `references/intent-to-block-routing.md`
2. Sélectionner le pattern correspondant dans `patterns/`
3. Compter les sections principales (hero, features, pricing, FAQ, etc.)

Exemple de mapping pour le prompt ci-dessus :

| Intention | Pattern | Variables à remplir |
|-----------|---------|---------------------|
| « Hero avec titre fort et 2 CTAs » | `patterns/hero-cta-split.md` | HEADLINE, SUBLINE, CTA_PRIMARY_*, CTA_SECONDARY_* |
| « 3 features avec icônes » | `patterns/features-3-cols.md` | F1-3 TITLE/DESC/ICON |
| « Pricing 3 tiers » | `patterns/pricing-3-tiers.md` | T1-3 NAME/PRICE/DESC/CTA |
| « 2 témoignages » | `patterns/testimonials-grid.md` | TESTIMONIAL_1/2 |
| « FAQ avec 5 questions » | `patterns/faq-accordion.md` | Q1-5/A1-5 |
| « CTA final » | `patterns/cta-banner-fullwidth.md` | HEADLINE, SUBLINE, CTAs |

### Étape 3 — Remplissage du contenu (30-60s)

Pour chaque pattern sélectionné :

1. Lire le fichier pattern dans `patterns/`
2. Pour les variables marquées `{{VAR}}`, générer un contenu cohérent avec le contexte du brief
3. Pour les contenus longs (descriptions FAQ, témoignages, etc.), utiliser le contexte WordPress + thématique formation

Exemple de génération pour features :

```yaml
F1_ICON: "fa-rocket"
F1_TITLE: "Apprends à ton rythme"
F1_DESC: "Modules vidéo accessibles 24/7, exercices pratiques après chaque chapitre."

F2_ICON: "fa-users"
F2_TITLE: "Communauté active"
F2_DESC: "Pose tes questions dans le Discord privé, échange avec d'autres apprenants."

F3_ICON: "fa-certificate"
F3_TITLE: "Certification reconnue"
F3_DESC: "Examen final + certificat à ajouter à ton LinkedIn et ton CV."
```

### Étape 4 — Assemblage du markup (15s)

Concaténer tous les patterns remplis dans l'ordre logique :

```
[hero-cta-split markup]
[features-3-cols markup]
[testimonials-grid markup]
[pricing-3-tiers markup]
[faq-accordion markup]
[cta-banner-fullwidth markup]
```

Vérifier les `block_id` : tous doivent être uniques. Si deux patterns utilisent le même bloc (ex: `cta-primary`), renommer en `hero-cta-primary` et `final-cta-primary`.

### Étape 5 — Validation roundtrip (5s)

Critique. Exécuter `scripts/validate-block-markup.php` sur le markup final :

```bash
php validate-block-markup.php "$(cat assembled-markup.txt)"
```

Résultat attendu : `valid: true`, `diff_size: 0`. Si non :

- **diff_size > 0** : un attribut JSON est cassé (apostrophe non échappée, virgule traînante, etc.). Re-checker chaque pattern.
- **block_id dupliqué** : renommer.
- **Hex hardcoded warning** : remplacer par `var(--ast-global-color-X)`.

Re-itérer jusqu'à validation 100 %.

### Étape 6 — POST sur l'API REST (5-10s)

```bash
curl -X POST 'https://{{SITE_URL}}/wp-json/wp/v2/pages' \
  -u 'admin:{{APP_PASSWORD}}' \
  -H 'Content-Type: application/json' \
  -d '{
    "title": "{{PAGE_TITLE}}",
    "slug": "{{PAGE_SLUG}}",
    "status": "draft",
    "content": "{{ASSEMBLED_MARKUP}}"
  }'
```

⚠️ **Toujours `status: draft`** (jamais `publish` direct). L'utilisateur valide visuellement avant de publier.

Récupérer l'`id` de la page créée dans la réponse.

### Étape 7 — Récap utilisateur

Output structuré :

```markdown
✅ Page créée avec succès

**ID** : 42
**Title** : Formation WordPress complète
**Slug** : /formation-wordpress-complete/
**Status** : draft

**URL d'édition** : https://{{SITE_URL}}/wp-admin/post.php?post=42&action=edit
**URL de prévisualisation** : https://{{SITE_URL}}/?page_id=42&preview=true

**Composition** :
- 1 hero (uagb/container + advanced-heading + buttons)
- 3 features (uagb/info-box dans uagb/container 3-cols)
- 2 testimonials (uagb/container avec uagb/testimonial)
- 3 pricing tiers (uagb/container avec 3× composition)
- 5 FAQ items (uagb/faq + 5× uagb/faq-child) + schema FAQPage activé
- 1 CTA banner final (uagb/container gradient + advanced-heading + buttons)

**Total blocs** : 23 valides, 0 erreur de parsing.

**Étapes suivantes recommandées** :
1. Ouvre l'URL d'édition et vérifie le rendu Gutenberg
2. Ajuste le contenu textuel si nécessaire
3. (Optionnel) Lance /screenshot-loop pour validation visuelle
4. (Optionnel) Lance /impeccable pour audit design
5. Publie quand prêt
```

### Étape 8 (optionnelle) — Validation visuelle automatique

Si le skill `/screenshot-loop` est disponible :

```
/screenshot-loop --url "https://{{SITE_URL}}/?page_id={{PAGE_ID}}&preview=true" --steps "scroll-fullpage,viewport-tablet,viewport-mobile"
```

Vérifie que :
- Aucun overflow horizontal mobile
- Hiérarchie visuelle claire
- Couleurs cohérentes (palette respectée)
- Padding/margin OK sur les 3 breakpoints

### Étape 9 (optionnelle) — Audit design `/impeccable`

```
/impeccable audit "https://{{SITE_URL}}/?page_id={{PAGE_ID}}&preview=true"
```

Note : `/impeccable` est en **lecture seule** sur WPFormation (la palette orange #FF8C00 est verrouillée). Ici, sur un site client neutre, les commandes `colorize|bolder|typeset` peuvent être autorisées.

### Étape 10 — Cleanup des pages de test

Si le titre de la page commence par `TEST `, `POC `, `DEMO ` ou `[skill]`, **proposer la suppression à l'utilisateur** après validation visuelle :

```
🧹 Cette page est marquée comme test (titre : "{{TITLE}}").
Veux-tu :
  [1] La conserver (status: draft, manuel à publier ou supprimer)
  [2] La supprimer maintenant (DELETE /wp-json/wp/v2/pages/{{PAGE_ID}}?force=true)
```

Si réponse 2, exécuter :

```bash
curl -X DELETE \
  -u "{{USER}}:{{APP_PASSWORD}}" \
  "https://{{SITE_URL}}/wp-json/wp/v2/pages/{{PAGE_ID}}?force=true"
```

Sinon, sur un site de prod testé itérativement, l'utilisateur se retrouve avec 5-10 brouillons « TEST skill astra-spectra » à nettoyer manuellement.

## Cas d'erreurs courants

### Erreur 1 — REST API 401 Unauthorized

→ Application Password invalide ou expiré. Demander à l'utilisateur de regénérer dans WP admin.

### Erreur 2 — REST API 403 Forbidden

→ L'utilisateur n'a pas les droits d'édition. Vérifier le rôle WordPress (doit être au minimum `editor`).

### Erreur 3 — Validation roundtrip diff > 0

→ Un attribut JSON est cassé. Le validator log l'attribut fautif. Re-checker l'apostrophe / la virgule / l'échappement.

### Erreur 4 — Block parse warning « invalid content » dans Gutenberg

→ Le HTML rendu dans `<!-- wp:* --> ... <!-- /wp:* -->` ne correspond pas aux attrs. Re-checker le pattern (voir `references/block-markup-syntax.md` règle 4).

### Erreur 5 — block_id dupliqué

→ Deux blocs ont le même `block_id`. Renommer pour rendre unique.

### Erreur 6 — Spectra absent ou outdated

→ Détection environnement aurait dû bloquer. Vérifier que detect-environment.php a tourné.

## Tests recommandés (POC)

Avant utilisation production, tester ce workflow sur ces 3 prompts :

1. **Page formation simple** : « Crée-moi une page sur ma formation WordPress avec hero, 3 features, 1 témoignage et CTA. »
2. **Landing SaaS complexe** : « Génère une landing page pour un SaaS de gestion de projet : hero animé, 4 features avec icônes, pricing 3 tiers (avec un plan free), 6 FAQ, footer CTA. »
3. **Article éditorial** : « Article 1500 mots sur les 5 plugins SEO WordPress avec sommaire, comparatif tableau, vidéo YouTube et FAQ. »

Si les 3 produisent des pages draft sans erreur de parsing en moins de 2 min, le workflow est validé.

## Pour aller plus loin

- Killer feature 2 : `refonte-page-existante.md`
- Killer feature 3 : `deploy-template.md`
- Patterns disponibles : `../patterns/`
- Validation : `../scripts/validate-block-markup.php`
- Détection : `../scripts/detect-environment.php`
