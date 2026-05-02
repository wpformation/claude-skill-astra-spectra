# Référence : règles i18n — français correct (et autres langues)

> **Lecture obligatoire si tu génères du contenu en français.** Mes 4 premiers tests de skill ont produit du français sans accents (« Reussir », « rediges », « eleves »), faute considérée par l'utilisateur comme « inacceptable, niveau amateur ». Cette doc liste les règles strictes pour ne plus jamais reproduire ça.

## Règle absolue

**Tout contenu en français DOIT avoir ses accents corrects.** Pas d'ASCII fold. Pas de tirets `--` à la place d'em-dash. Pas de guillemets droits `"` à la place des guillemets typographiques.

Méthode de production préférée selon contexte :

| Où le texte va vivre | Méthode |
|---|---|
| Markup Gutenberg (innerContent) | **HTML entities** (`&eacute;`, `&egrave;`, `&agrave;`, `&ecirc;`, `&ocirc;`, `&ccedil;`...) |
| Attribut JSON dans block comment (`question`, `answer`, `label`) | **UTF-8 direct** (`é`, `è`, `à`, etc.) |
| Title de la page (post_title) | **UTF-8 direct** |
| Yoast meta title/desc | **UTF-8 direct** |

## Pourquoi HTML entities dans le innerContent

Gutenberg parse les blocs avec un parser tolérant. Mais quand tu envoies du markup en JSON via REST API :
- Le JSON encode les caractères Unicode soit en UTF-8 brut (`é`) soit en escape `é`
- WordPress passe par MySQL qui peut être en `utf8` (3-byte) au lieu de `utf8mb4` (4-byte)
- Pour les caractères 4-byte (em-dash `—`, certains emojis), tu peux avoir du **mojibake** (`â€"` à la place de `—`)

**Solution simple et 100% safe** : HTML entities dans le innerContent. Elles passent par tous les pipelines sans risque.

## Table des entities critiques pour le français

| Caractère | Entity | Notes |
|---|---|---|
| é | `&eacute;` | très fréquent |
| è | `&egrave;` | |
| ê | `&ecirc;` | |
| ë | `&euml;` | rare |
| à | `&agrave;` | |
| â | `&acirc;` | |
| ç | `&ccedil;` | |
| ô | `&ocirc;` | |
| ö | `&ouml;` | rare en français, fréquent en allemand |
| ù | `&ugrave;` | |
| û | `&ucirc;` | |
| ï | `&iuml;` | |
| î | `&icirc;` | |
| œ | `&oelig;` | très spécifique français (cœur, œuf) |
| Ç É À Ê È Ô (majuscules) | `&Ccedil; &Eacute; &Agrave; &Ecirc; &Egrave; &Ocirc;` | important pour eyebrows uppercase |

## Ponctuation typographique française

| Caractère | Entity | Usage |
|---|---|---|
| ’ | `&rsquo;` | apostrophe typographique (l'élève → l&rsquo;élève) |
| « | `&laquo;` | guillemet ouvrant français |
| » | `&raquo;` | guillemet fermant français |
| “ | `&ldquo;` | guillemet ouvrant anglais (citations) |
| ” | `&rdquo;` | guillemet fermant anglais |
| — | `&mdash;` | tiret cadratin (jamais `--`) |
| – | `&ndash;` | tiret demi-cadratin |
| … | `&hellip;` | points de suspension (jamais `...`) |
| · | `&middot;` | bullet point (séparateur sub-info) |
|   | `&nbsp;` | espace insécable |
|   | `&thinsp;` | espace fine (avant `%`) |

## Espace insécable français — règle stricte

En typographie française, **espace insécable obligatoire** avant ces caractères :

| Avant | Espace |
|---|---|
| `?` `!` `:` `;` | `&nbsp;` |
| `»` (guillemet fermant) | `&nbsp;` |
| `%` `°` `€` (unités) | `&thinsp;` (espace fine) |

Exemples corrects :

```html
Le site est-il vraiment gratuit&nbsp;?
3 sessions par semaine&nbsp;: c'est suffisant.
&laquo;&nbsp;Le BTS NDRC&nbsp;&raquo;
87&thinsp;% de réussite
30&nbsp;€ par module
```

Sans `&nbsp;`, le `?` peut wrapper sur la ligne suivante isolé du mot précédent — disgracieux et incorrect typographiquement.

## Guillemets — choisir le bon style

Pour citations dans testimonials :

- **Style français** : `&laquo;&nbsp;Citation&nbsp;&raquo;` → « Citation »
- **Style anglais / display oversized** : `&ldquo;Citation&rdquo;` → "Citation"

Pour les guillemets display 100-120px en haut de card testimonials, préférer `&ldquo;` (forme géométrique plus dense, meilleur effet visuel à grosse taille).

## Apostrophes — typographique vs droite

**Convention skill par défaut : typographique** dans le contenu rédactionnel (Académie française, manuels, presse écrite) :

```html
<!-- BAD -->
J'avais 2 mois pour réviser

<!-- GOOD -->
J&rsquo;avais 2 mois pour r&eacute;viser
```

L'apostrophe droite `'` est seulement pour le code (URLs, JSON keys).

### Cas particulier — convention site cible vs convention typographique

Certains sites ont une convention webdev pragmatique opposée : **apostrophe droite ASCII partout** (`'` U+0027, pas `’` U+2019). Raisons techniques :

- Compatibilité moteurs de recherche WordPress (`SELECT * FROM posts WHERE post_content LIKE "%l'élève%"` ne match pas `l’élève` typo)
- Facilité copy-paste utilisateur (les browsers convertissent inconsistamment `’` ↔ `'`)
- Cohérence avec le code source du site (sans transformation typographique automatique)

**Avant de générer du contenu**, le skill DOIT vérifier la convention du site cible :

| Indicateur convention site | Convention skill à utiliser |
|---|---|
| Le site a une mémoire/CLAUDE.md disant « apostrophes ASCII » ou « pas d'apostrophe typographique » | `'` U+0027 (ASCII) |
| Le site n'a pas de convention documentée | `&rsquo;` U+2019 (typographique, default) |
| Le site est éditorial / luxe / institutionnel | `&rsquo;` U+2019 (force typo) |
| Le site est blog tech / e-commerce volume / SaaS | confirmer avec l'utilisateur |

**Comment vérifier** : grep dans le repo source du site `[‘’]` (rsquo / lsquo) et `'` (ASCII). Si le ratio est > 80 % en faveur d'un, suivre cette convention.

Cas concret rencontré : site `cours-ndrc.fr` a la convention « apostrophes ASCII strict » dans son MEMORY.md. Le skill doit donc générer `l'élève` (ASCII) et NON `l&rsquo;élève` typographique. Conflit avec la convention skill default → **toujours respecter la convention du site cible**.

## Em-dash, en-dash, hyphen — choisir le bon

| Caractère | Entity | Usage |
|---|---|---|
| `-` (hyphen) | direct | mots composés (ex-employé, sub-info, BTS-NDRC) |
| `–` (en-dash) | `&ndash;` | plages numériques (2018–2024, pages 12–18) |
| `—` (em-dash) | `&mdash;` | incise/parenthèse stylée (« Paris &mdash; ville lumière ») |

**Convention française classique** : tiret demi-cadratin `–` pour les incises, em-dash `—` plutôt anglo-saxon. Mais en design web moderne, les deux passent. Le skill utilise `&mdash;` par cohérence.

## Validation post-génération

Après avoir POST une page, faire un GET de la URL frontend et grep :

```bash
# Aucune occurrence de mojibake
curl -s URL | grep -c 'â€'
# Attendu : 0

# Tous les accents français présents (sample test)
curl -s URL | grep -E '(é|è|à|ê|ô|ç)' | head -5
# Attendu : matches multiples

# Aucune apostrophe droite dans le contenu rédactionnel
curl -s URL | grep -E "<p[^>]*>[^<]*'[^<]*</p>"
# Attendu : 0 ou false positives (URLs/code)
```

Si mojibake détecté : revérifier que le markup utilise bien HTML entities (pas UTF-8 direct).

## Cas particulier : eyebrows uppercase

Les eyebrows comme `PROMOTION 2026 — DERNIÈRES PLACES` doivent garder leurs accents en majuscule. CSS `text-transform:uppercase` préserve les accents (Ê, È, À, etc.).

```html
<p class="uagb-ifb-title-prefix" style="letter-spacing:4px;text-transform:uppercase">Promotion 2026 &mdash; Derni&egrave;res places</p>
```

Au rendu : `PROMOTION 2026 — DERNIÈRES PLACES` avec `È` correctement majusculisé par CSS.

## Cas particulier : noms propres

Préserver les accents et apostrophes des noms propres :
- Léa (pas Lea)
- Inès (pas Ines)
- Lycée Notre-Dame de Lyon (pas Lycee Notre-Dame de Lyon)
- L'École polytechnique (avec apostrophe typographique L&rsquo;&Eacute;cole)

## Autres langues

| Langue | Notes |
|---|---|
| **Allemand** | umlauts `ä ö ü ß` → `&auml; &ouml; &uuml; &szlig;`. Pas de `&nbsp;` typo |
| **Espagnol** | `ñ á é í ó ú ¿ ¡` → `&ntilde; &aacute;` etc. `¿` `¡` ouvrants |
| **Italien** | `à è é ì ò ù` → entities standard. Pas de `&nbsp;` typo |
| **Portugais** | `ã õ á é í ó ú ç ª º` → entities standard |

Pour chaque langue, le pattern doit avoir une variante `<lang>-<region>.json` qui contient les variables (eyebrows, headlines, descs) traduites avec les bonnes entities.

## TODO v1.1+

- [ ] Variantes `fr-FR.json`, `en-US.json`, `de-DE.json`, `es-ES.json` pour les 12 patterns
- [ ] Auto-vérification mojibake post-POST via `scripts/validate-i18n.php`
- [ ] Linter dans `validate-block-markup.php` qui flag les apostrophes droites dans le contenu rédactionnel
