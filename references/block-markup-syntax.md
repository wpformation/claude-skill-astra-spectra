# Block Markup Syntax

> **Rôle** : règles strictes de syntaxe Gutenberg block markup pour éviter les erreurs de parsing. Tout le markup généré par le skill DOIT respecter ces règles, sinon Gutenberg refuse d'ouvrir la page ou affiche « this block contains unexpected or invalid content ».

## Règle fondamentale

Le contenu d'un post WordPress (`post_content`) n'est pas du HTML, c'est du **block markup Gutenberg** : un mix de commentaires HTML spéciaux + HTML.

```
<!-- wp:NAMESPACE/BLOCK_NAME {ATTRS_JSON} -->
<HTML_RENDU>
<!-- /wp:NAMESPACE/BLOCK_NAME -->
```

## Anatomie d'un bloc

### Bloc auto-fermant (sans contenu HTML)

```
<!-- wp:core/separator /-->
```

### Bloc avec HTML rendu (cas standard)

```
<!-- wp:core/paragraph -->
<p>Texte du paragraphe.</p>
<!-- /wp:core/paragraph -->
```

### Bloc avec attributs JSON

```
<!-- wp:core/heading {"level":2,"textColor":"primary"} -->
<h2 class="wp-block-heading has-primary-color has-text-color">Titre</h2>
<!-- /wp:core/heading -->
```

### Bloc imbriqué (innerBlocks)

```
<!-- wp:core/group {"className":"wrapper"} -->
<div class="wp-block-group wrapper">
  <!-- wp:core/heading -->
  <h2 class="wp-block-heading">Titre interne</h2>
  <!-- /wp:core/heading -->

  <!-- wp:core/paragraph -->
  <p>Paragraphe interne.</p>
  <!-- /wp:core/paragraph -->
</div>
<!-- /wp:core/group -->
```

## Règles strictes

### 1. JSON des attributs

- **Toujours** entre accolades `{...}` même si vide (`{}` ou ne pas mettre les accolades du tout)
- **Espace obligatoire** entre nom du bloc et accolades : `wp:core/heading {"level":2}` (avec espace)
- **JSON valide strict** : pas de virgule traînante, pas de single quotes, pas de commentaires JS
- **Strings échappées** correctement : `\"`, `\\`, `\n`, etc.

✅ **Bon** :
```
<!-- wp:uagb/container {"block_id":"hero","contentWidth":"alignwide"} -->
```

❌ **Mauvais** :
```
<!-- wp:uagb/container {block_id:"hero",contentWidth:"alignwide"} -->   <!-- pas de quotes sur les keys -->
<!-- wp:uagb/container {"block_id":"hero",} -->                          <!-- virgule traînante -->
<!-- wp:uagb/container {'block_id':'hero'} -->                           <!-- single quotes -->
```

### 2. Espacement et casse

- **Espace UNIQUE** entre `wp:` et le nom : `<!-- wp:core/heading -->` (pas `<!--wp:core/heading-->`)
- **Casse** : `wp:` en lowercase, namespace en lowercase, block name en kebab-case
- **Pas de retour à la ligne** dans le commentaire d'ouverture

### 3. Cohérence ouverture/fermeture

- Le commentaire de fermeture **doit reprendre exactement** le namespace + block name : `<!-- /wp:NAMESPACE/BLOCK_NAME -->`
- Pas d'attrs dans le commentaire de fermeture
- Auto-fermant : `/-->` à la fin du commentaire d'ouverture, pas de fermeture séparée

### 4. HTML rendu fidèle

Le HTML entre `<!-- wp:* -->` et `<!-- /wp:* -->` doit être **exactement** ce que le bloc serait rendu côté frontend. Si tu changes manuellement le HTML rendu, Gutenberg détecte la différence et affiche un warning « invalid content ».

Pour Spectra (`uagb/*`), il y a une particularité : le HTML rendu inclut souvent une `<div class="wp-block-uagb-{block_name} uagb-block-{block_id}">` avec le block_id en classe.

✅ **Bon** :
```
<!-- wp:uagb/info-box {"block_id":"feat-1","headingTitle":"Feature 1","headingDesc":"Desc","headingTag":"h3"} -->
<div class="wp-block-uagb-info-box uagb-block-feat-1"><h3 class="uagb-ifb-title">Feature 1</h3><p class="uagb-ifb-desc">Desc</p></div>
<!-- /wp:uagb/info-box -->
```

❌ **Mauvais** :
```
<!-- wp:uagb/info-box {"block_id":"feat-1","headingTitle":"Feature 1"} -->
<h3>Mon titre custom</h3>   <!-- HTML divergent des attrs -->
<!-- /wp:uagb/info-box -->
```

### 5. Encodage des caractères spéciaux

- **Apostrophes typographiques** dans les contenus : utiliser `&apos;` ou `&#8217;` au lieu de `'` directement (sinon JSON cassé)
- **Guillemets typographiques** : `«&nbsp;` et `&nbsp;»` (ou `&laquo;`, `&raquo;`)
- **Tirets** : pas de tirets cadratin `—`. Utiliser `-` simple. Voir aussi la règle `feedback-no-emdash.md` du repo WPF.
- **Émojis** : OK dans le HTML rendu (UTF-8), à éviter dans les attrs JSON

### 6. Block_id unique (Spectra obligatoire)

Chaque bloc Spectra DOIT avoir un attribut `block_id` UNIQUE dans le markup de la page. Le block_id apparaît :
- Dans les attrs JSON
- Dans les classes CSS du HTML rendu (`uagb-block-{block_id}`)

Pattern de génération recommandé : `<contexte>-<type>-<index>`. Exemples :
- `hero-container`
- `hero-heading`
- `hero-buttons`
- `hero-cta-1`
- `hero-cta-2`
- `feat-1`, `feat-2`, `feat-3`
- `pricing-tier-1`, `pricing-tier-2`, `pricing-tier-3`
- `faq-main`
- `faq-q1`, `faq-q2`, `faq-q3`

### 7. Hiérarchie parent/enfant stricte

Certains blocs Spectra DOIVENT être enfants d'un parent spécifique. Hors hiérarchie = bloc cassé.

| Parent | Enfants obligatoires |
|--------|---------------------|
| `uagb/buttons` | `uagb/buttons-child` |
| `uagb/faq` | `uagb/faq-child` |
| `uagb/icon-list` | `uagb/icon-list-child` |
| `uagb/tabs` | `uagb/tabs-child` |
| `uagb/slider` | `uagb/slider-child` |
| `uagb/social-share` | `uagb/social-share-child` |
| `uagb/price-list` | `uagb/price-list-child` |
| `uagb/columns` | `uagb/column` |

### 8. Commentaires JSON multi-lignes interdits

Les attrs JSON doivent tenir sur une seule ligne dans le commentaire HTML d'ouverture. Pour les attrs longs, utiliser le minify JSON (pas de `\n` ni d'indentation).

❌ **Mauvais** :
```
<!-- wp:uagb/container {
  "block_id": "hero",
  "contentWidth": "alignwide"
} -->
```

✅ **Bon** :
```
<!-- wp:uagb/container {"block_id":"hero","contentWidth":"alignwide"} -->
```

## Validation programmatique

**Toujours** valider via roundtrip avant POST :

```php
$blocks = parse_blocks($content);
$reserialized = serialize_blocks($blocks);

if ($content !== $reserialized) {
  // ❌ ERREUR : le markup est invalide
  // Lance le validator pour détailler le problème
}
```

Si la diff est nulle (parse → serialize identique), le markup est garanti valide.

Le script `scripts/validate-block-markup.php` du skill fait ce check + détecte :
- block_id manquant ou dupliqué (Spectra)
- Hex hardcoded dans les couleurs (anti-pattern design system)
- Hiérarchie parent/enfant cassée

## Pièges courants observés (POC du 02/05/2026)

1. **Oubli du `block_id`** sur un `uagb/buttons-child` → Gutenberg recompute et le bouton perd sa couleur
2. **Apostrophe typographique non échappée** dans `headingDesc` → JSON casse au parsing
3. **`uagb/buttons-child` standalone** sans parent `uagb/buttons` → bloc orphan, ignoré au render
4. **Inversion ouverture/fermeture** entre `uagb/container` et un inner `uagb/info-box` → catastrophe en cascade
5. **Espacement irrégulier** dans le commentaire HTML (`<!--wp:` au lieu de `<!-- wp:`) → parser échoue silencieusement

## Pour aller plus loin

- Validation : `../scripts/validate-block-markup.php`
- Recettes markup éprouvées : `../modules/spectra/markup-recipes.md`
- Catalogue des 49 blocs Spectra : `spectra-blocks-catalog.md`
- Documentation officielle Gutenberg : https://developer.wordpress.org/block-editor/reference-guides/block-api/
