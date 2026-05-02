# Screenshots — preuve visuelle des patterns sur palettes Astra

> **Origine** : rapport visuel cours-ndrc.fr (palette_3) du 02/05/2026 a montré que la v0.8.2 produit des sections noires illisibles. Tous les fixes techniques (validator roundtrip, block_id unique, hex 0) passaient mais le rendu était inutilisable. **Conclusion : un pattern non screenshooté est un pattern non testé.**

## Process obligatoire avant tag v1.0

Pour qu'un pattern passe en `tested: true`, il doit être screenshooté sur **3 palettes Astra minimum** :

| Palette | Justification |
|---------|---------------|
| Astra default | référence baseline (la majorité des sites debutants) |
| `preset_3` (rouge passion) | palette saturée chaude, pour valider le contraste sur fond color-0 |
| `preset_8` (orange gourmand) | palette avec slots variables remappés (le plus piège, vu sur cours-ndrc.fr) |

Optionnel mais recommandé :

| Palette | Justification |
|---------|---------------|
| `preset_5` (vert nature) | palette saturée froide |
| `preset_10` (gris professionnel) | palette monochrome (test extrême) |
| 1 site client réel | régression sur palette utilisateur custom |

## Structure du dossier

```
screenshots/
├── README.md                              # ce fichier
├── _baseline/                              # captures de référence pour comparaison
│   ├── astra-default-features-3-cols.png
│   ├── astra-default-pricing-3-tiers.png
│   └── ...
├── astra-default/
│   ├── hero-cta-split.png
│   ├── features-3-cols.png
│   ├── pricing-3-tiers.png
│   └── ...
├── preset_3/
│   └── ...
├── preset_8/
│   └── ...
└── cours-ndrc-fr-palette3/                 # régression test sur site client réel
    └── ...
```

## Comment produire les screenshots

### Manuellement (référence baseline)

1. Lancer un site Playground avec `wp plugin install ultimate-addons-for-gutenberg --activate`
2. Configurer la palette : Customizer > Global Colors > sélectionner la palette cible
3. Créer une page test avec le pattern (via le skill ou copy-paste markup)
4. Ouvrir la page en preview frontend (déconnecté ET connecté pour comparer)
5. Capture : viewport 1440×900 desktop, 768×1024 tablet, 375×667 mobile
6. Format : PNG, optimisé < 500 KB
7. Nommer : `{pattern}-{viewport}.png` (ex: `hero-cta-split-desktop.png`)

### Automatiquement (CI)

Workflow GitHub Actions à ajouter (`.github/workflows/visual-regression.yml`) :

```yaml
name: Visual regression
on:
  pull_request:
    paths:
      - 'patterns/**'
      - 'templates/**'
      - 'modules/**'
      - 'scripts/**'

jobs:
  screenshot:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        palette: [astra-default, preset_3, preset_8]
    steps:
      - uses: actions/checkout@v4
      - name: Setup WP Playground
        run: |
          # script qui spinup un Playground avec le pattern + la palette
      - name: Generate page from pattern
        run: |
          php scripts/post-page-via-rest.php --site-url=$PLAYGROUND_URL ...
      - name: Capture with Playwright
        run: |
          npx playwright screenshot --viewport-size=1440x900 \
            $PLAYGROUND_URL/?p=$PAGE_ID screenshots/${{ matrix.palette }}/${PATTERN}.png
      - name: Compare against baseline
        run: |
          # diff vs screenshots/_baseline/ — fail si > 5% pixels diff
```

## Convention frontmatter pattern

Chaque pattern doit déclarer son statut de test dans son frontmatter :

```yaml
---
name: features-3-cols
tested-on-palettes:
  - astra-default
  - preset_3
  - preset_8
last-tested: 2026-05-03
status: stable  # stable | experimental | broken
---
```

Si `tested-on-palettes` est vide ou ne contient que 1-2 palettes → `status: experimental` automatiquement. Pas de tag v1.0 sur le repo tant qu'au moins **75 % des patterns** ne sont pas `status: stable`.

## Suite de palettes test fournies

Le dossier `_palettes/` (à créer en v0.9+) contient des fixtures `.json` pour chaque palette à tester :

```
_palettes/
├── astra-default.json
├── preset_3.json
├── preset_8.json
└── cours-ndrc-fr-palette3.json   # palette réelle relevée sur le site
```

Chacun contient les 9 hex de la palette + le mapping résolu via `wpf_skill_resolve_color()` pour permettre des tests offline.

Exemple `preset_8.json` :

```json
{
  "name": "preset_8",
  "label": "Orange gourmand (Astra preset)",
  "palette": ["#FD9800", "#E98C00", "#0F172A", "#454F5E", "#FEF9E1", "#FFFFFF", "#F9F0C8", "#141006", "#222222"],
  "luminances": [0.62, 0.55, 0.10, 0.30, 0.97, 1.00, 0.93, 0.06, 0.13],
  "resolved": {
    "bg_section": "#FFFFFF",
    "bg_section_alt": "#FEF9E1",
    "text_heading": "var(--ast-global-color-2)",
    "text_inverse": "#FFFFFF",
    "border_subtle": "#e5e7eb"
  }
}
```

## TODO avant v1.0

- [ ] Capturer les 9 patterns sur les 3 palettes minimum (27 screenshots)
- [ ] Capturer les 3 templates (page-formation, landing-saas, page-agence) sur 3 palettes (9 screenshots)
- [ ] Ajouter les fixtures `_palettes/*.json` (3 fichiers minimum)
- [ ] Implémenter le workflow GitHub Actions de régression visuelle
- [ ] Marquer `tested-on-palettes` dans tous les frontmatters de patterns
- [ ] Tester sur un site client réel (cours-ndrc.fr palette_3 idéalement, qui sert de baseline régression)

Tant que cette TODO n'est pas cochée à 100 %, ne pas tag v1.0. La leçon de la v0.8.2 : 17 fixes techniques validés ne valent rien si le rendu visuel ne marche pas en production.
