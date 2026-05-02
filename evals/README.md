# Evals — Suite de tests automatisés

## Lancer les évals

```bash
# Toutes les évals
php evals/run-evals.php

# Filtrer par catégorie
php evals/run-evals.php --category=validation
php evals/run-evals.php --category=build
php evals/run-evals.php --category=astra

# Une eval précise
php evals/run-evals.php --id=validate-01-malformed-markup
```

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
