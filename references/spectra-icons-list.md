# Référence : Spectra icônes — whitelist validée et fallback strategy

> **Tu DOIS lire ce fichier avant de générer du markup `uagb/icon` ou des icons sur n'importe quel bloc Spectra.** Mes 3 premiers tests ont produit 3 cards features avec **la même icône fallback** parce que `book-open`, `clipboard-check`, `timer` ne sont pas reconnus.

## Le problème en bref

Spectra utilise un sous-ensemble Font Awesome **5 Free** (pas FA 6, pas FA Pro). Si tu mets un nom d'icône invalide, Spectra **ne crash pas** — il affiche silencieusement une icône fallback (souvent un rectangle vide ou une icône placeholder identique sur toutes les instances). Donc tu n'as **aucune erreur** au markup mais tes 3 cards ont la même icône invisible.

## Stratégies recommandées

### Stratégie A — Numéros éditoriaux (PRÉFÉRÉE)

Au lieu d'icônes Font Awesome génériques, utiliser des **numéros 01 / 02 / 03** dans un `uagb/info-box` avec gros chiffre orange. Plus distinctif visuellement, 0 risque de fallback.

Voir `patterns/features-numbered.md`.

### Stratégie B — Whitelist d'icônes validées

Si tu as vraiment besoin d'icônes (ex : pricing tiers avec checkmarks), utiliser uniquement des noms de la whitelist ci-dessous, validés sur Spectra v2.19.x.

### Stratégie C — Custom SVG via core/html

Pour des icônes vraiment custom (logos, illustrations), utiliser `core/html` avec un SVG inline. Pas Font Awesome.

```html
<!-- wp:html -->
<svg width="48" height="48" viewBox="0 0 24 24" fill="#FD9800">
  <path d="..."/>
</svg>
<!-- /wp:html -->
```

## Whitelist Font Awesome 5 Free icônes courantes (validées Spectra)

### Communication & Social

```
envelope envelope-open phone phone-alt mobile-alt fax
comments comment comment-alt comment-dots
share-alt share-square external-link-alt link
```

### Business & E-commerce

```
shopping-cart shopping-bag credit-card store
chart-bar chart-line chart-pie chart-area
briefcase building city
dollar-sign euro-sign pound-sign
tags tag percent percentage
```

### Education & Documents

```
book book-reader graduation-cap school
file file-alt file-pdf file-word file-excel
folder folder-open
clipboard tasks check-square
pencil-alt edit
```

### Navigation & UI

```
home user users user-circle
search filter sort
plus minus times check
arrow-right arrow-left arrow-up arrow-down
chevron-right chevron-left chevron-up chevron-down
caret-right caret-left caret-up caret-down
bars cog cogs ellipsis-h ellipsis-v
```

### Time & Calendar

```
calendar calendar-alt clock stopwatch hourglass
history bell bell-slash
```

### Tech & Cloud

```
cloud cloud-upload-alt cloud-download-alt
wifi globe globe-americas globe-europe
database server hdd
desktop laptop mobile tablet-alt
```

### Health & Wellness

```
heart heart-broken hand-holding-heart
running walking biking
apple-alt utensils
medkit pills
```

### Travel & Map

```
map map-marker-alt map-marked-alt
plane car bus train ship
suitcase
```

### Multimedia

```
play play-circle pause stop
music headphones video volume-up volume-mute
camera image film
```

### Status & Notifications

```
check-circle exclamation-circle exclamation-triangle
info-circle question-circle
times-circle ban
star star-half-alt
thumbs-up thumbs-down
```

### Sécurité

```
lock lock-open unlock shield-alt
key fingerprint user-shield
```

## Comment tester un nom d'icône avant de l'utiliser

### Méthode 1 — Test live sur Playground / WP local

```php
<?php
require_once ABSPATH . 'wp-content/plugins/ultimate-addons-for-gutenberg/dist/blocks.asset.php';
// Pas d'API publique pour la liste, donc tester par essais
```

Plus simple : créer un `uagb/icon` block avec le nom à tester, screenshot. Si l'icône matche le nom Font Awesome attendu, c'est OK. Sinon, fallback.

### Méthode 2 — Comparer avec FA 5 Free

Tous les noms FA 5 Free sont sur https://fontawesome.com/v5/search?o=r&m=free.

**ATTENTION** : Spectra mappe les noms FA en court (sans préfixe `fas` ou `far`). Donc `fa-book` → `book`, `fa-clipboard` → `clipboard`.

### Méthode 3 — Test programmatique

Le mu-plugin compagnon `scripts/mu-plugin-skill-test.php` peut être étendu avec un endpoint `/inspect-icon/{name}` qui :
1. Crée un bloc `uagb/icon` avec le nom
2. Render via `do_blocks()`
3. Check si le HTML contient `<svg>` ou `<i class="fa-{name}">` valide
4. Si oui : valide. Si non : fallback détecté.

## Pièges connus avec les noms

| Nom que tu pourrais essayer | Réel nom Spectra | Notes |
|---|---|---|
| `book-open` | `book` ou `book-reader` | `book-open` n'existe pas en FA 5 Free |
| `clipboard-check` | `clipboard` ou `tasks` | `clipboard-check` est FA 5 Pro |
| `timer` | `clock` ou `stopwatch` | `timer` n'existe pas |
| `house` | `home` | `house` est FA 6 |
| `magnifying-glass` | `search` | FA 5 vs FA 6 |
| `xmark` | `times` | FA 5 vs FA 6 |
| `gear` | `cog` | FA 5 vs FA 6 |
| `arrow-up-right-from-square` | `external-link-alt` | FA 5 vs FA 6 |

## Fallback strategy — Que faire si une icône foire en prod

Si tu détectes une icône doublonnée/invisible après screenshot :

1. **Re-tester avec un nom de la whitelist** ci-dessus
2. **Si toujours KO** → switch vers numéros éditoriaux 01/02/03
3. **Si vraiment besoin** → custom SVG inline via `core/html`

## TODO v1.1+

- [ ] Endpoint `/skill-test/v1/inspect-icon/{name}` dans le mu-plugin compagnon
- [ ] Auto-validation des noms d'icônes dans `scripts/validate-block-markup.php` (P1 si nom hors whitelist)
- [ ] Whitelist exhaustive auto-générée depuis le code source Spectra (parser `dist/blocks.min.js` pour extraire la liste FA)
- [ ] Tester sur Spectra v2.20+ pour voir si la liste évolue
