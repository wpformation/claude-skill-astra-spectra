# Evals — Suite de tests automatisés

## Lancer les évals

### En CLI sur un site cible

```bash
# Le runner doit avoir wp-load.php accessible. Lancer depuis la racine WP :
cd /chemin/vers/wordpress
php /chemin/vers/skill/evals/run-evals.php

# Ou via WP-CLI :
wp eval-file /chemin/vers/skill/evals/run-evals.php

# Filtrer par catégorie
php evals/run-evals.php --category=validation
php evals/run-evals.php --category=build
php evals/run-evals.php --category=astra

# Une eval précise
php evals/run-evals.php --id=validate-01-malformed-markup
```

### En CI (proposition)

Pas encore intégré. Pour ajouter une CI dans un fork :

```yaml
# .github/workflows/evals.yml (exemple à adapter)
name: Evals
on: [pull_request]
jobs:
  validation-evals:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:8
        env: { MYSQL_ROOT_PASSWORD: root, MYSQL_DATABASE: wp }
    steps:
      - uses: actions/checkout@v4
      - name: Setup WP test instance
        run: |
          curl -O https://wordpress.org/latest.tar.gz
          tar xzf latest.tar.gz
          cd wordpress && wp config create --dbname=wp --dbuser=root --dbpass=root && wp core install
          wp plugin install ultimate-addons-for-gutenberg --activate
      - name: Run validation evals only (no LLM)
        run: php evals/run-evals.php --category=validation
```

Les évals `category=build / refonte / template / astra` nécessitent une session live (LLM + WP). Elles ne sont pas testables en CI sans Anthropic API key. Le runner les marque `SKIP` automatiquement avec note explicative.

## Catégories

| Catégorie | Description | Auto |
|-----------|-------------|------|
| `validation` | Tests de roundtrip parse/serialize, détection block_id, hex hardcodés | ✅ |
| `auto-fix` | Vérifie que `auto-fix-markup.php` corrige les problèmes | ✅ |
| `build` | Génération from brief (5 prompts canoniques) | ⚠ Manuel |
| `refonte` | Refonte de page existante | ⚠ Manuel |
| `template` | Déploiement de template clic-bouton | ⚠ Manuel |
| `astra` | Update palette + vérification CSS frontend | ⚠ Manuel |

Les évals `build/refonte/template/astra` nécessitent une invocation skill réelle (LLM + site WP live). Le runner les marque `SKIP` avec note d'instructions.

## Seuils de succès (release v1.0)

```json
{
  "build_first_pass_rate": 0.80,
  "build_after_3_retries_rate": 0.95,
  "manual_intervention_rate": 0.05,
  "average_block_count_per_page": 18,
  "average_css_var_per_page": 80
}
```

## Benchmarks

| Métrique | Cible | Max acceptable |
|----------|-------|----------------|
| Génération page formation | 90s | 180s |
| Validation markup 1500 blocs | 200ms | 500ms |
| Update palette Astra | 5s | 15s |

## Comment ajouter une eval

1. Créer une fixture si besoin dans `fixtures/`
2. Ajouter un objet dans `evals.json` avec `id`, `category`, `prompt`, `assertions`
3. Documenter le type d'assertion s'il est nouveau
4. Tester avec `php run-evals.php --id=<nouveau-id>`

## Types d'assertions supportés

- `block_count` (`min`/`max`)
- `uagb_block_id_unique` (`expect: true`)
- `css_var_count` (`min`)
- `hex_hardcoded_count` (`max`)
- `h1_count` (`expected`)
- `roundtrip_real_diff` (`expected`)
- `validator_status` (`expected: OK|FAILED`)
- `errors_contain` (`value`)
- `warnings_contain` (`value`)
- `fixes_count` (`min`)
- `post_fix_validator_status` (`expected`)
- `contains_text` (`value`)
- `core_block_proportion` (`min`)
- `uagb_block_proportion` (`min`)
- `content_preserved` (`expect: true`)
- `background_type_gradient` (`expect`)
- `has_dividers` (`expect`)
- `has_overlay` (`expect`)
- `css_var_value` (`var`, `expected`)
- `no_other_keys_modified` (`expect: true`)
