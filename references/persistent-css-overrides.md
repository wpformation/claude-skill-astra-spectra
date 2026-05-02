# Référence : CSS overrides persistants via `_uag_custom_page_level_css`

> **Découverte critique du 02/05/2026** : les styles inline injectés dans le innerHTML d'un bloc Spectra (e.g. `<p class="uagb-ifb-title" style="font-size:80px">227</p>`) sont **strippés par Gutenberg dès le premier save via l'éditeur**. Le parser regenère le innerContent à partir du JSON `attrs` et ignore tout HTML inline qui n'est pas dans le schéma de bloc. Conséquence : tous les workarounds inline ne survivent pas à une édition.

> **Solution propre et persistante** : Spectra a un meta natif `_uag_custom_page_level_css` qui est concaténé au stylesheet dynamique de la page à chaque rendu. Le CSS y est stocké séparément du `post_content`, donc Gutenberg ne le touche jamais.

## Pré-requis Spectra (CRITIQUE)

**Avant tout déploiement, vérifier que l'option globale Spectra `uag_enable_on_page_css_button` est à `yes`.** Sans ça, **tout le mécanisme de cette doc est inopérant** (cf quirk #20 dans `references/spectra-attributes-quirks.md`).

```php
// Vérification
$toggle = get_option('uag_enable_on_page_css_button', 'yes');
// 'yes'    → OK, meta lu et concaténé au stylesheet
// 'no' / autre → meta ignoré silencieusement, AUCUNE erreur affichée

// Auto-fix
update_option('uag_enable_on_page_css_button', 'yes');
```

**Comportement par défaut** :
- Spectra (gratuit) : default `yes` ✓
- Spectra Pro : default `yes` mais peut être désactivé volontairement par admin
- Sites multi-auteurs / hardening sécurité : peut être à `no` pour bloquer l'injection CSS arbitraire

**Vérification automatique** : `scripts/detect-environment.php` flagge en `blockers[]` si l'option n'est pas à `yes`. Le mu-plugin compagnon expose `POST /wp-json/skill-test/v1/enable-on-page-css` pour forcer le toggle.

## Pourquoi cette technique est nécessaire

Bug Spectra v2.19 confirmé sur loginarmor-dev (Astra 4.13.1) ET cours-ndrc.fr (palette_3) : le `<style id="uagb-style-frontend-{post_id}">` qui devrait être injecté dans `<head>` via le hook `wp_head` est **absent du HTML rendu**. Le `_uag_page_assets['css']` post_meta contient pourtant 240K+ chars.

Sans ce CSS dynamique :
- Tous les `headingFontSizeDesktop:80`, `headingFontSizeDesktop:120`, `letter-spacing` du markup sont ignorés
- Les chiffres restent en font-size par défaut (16px)
- Le rendu est plat, pas WOW

## Comment Spectra utilise `_uag_custom_page_level_css`

Code source confirmé dans [`class-uagb-post-assets.php:1434`](https://github.com/brainstormforce/wp-spectra/blob/main/classes/class-uagb-post-assets.php) :

```php
$enable_on_page_css_button = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_on_page_css_button', 'yes' );

if ( 'yes' === $enable_on_page_css_button ) {
    $custom_css = get_post_meta( $this->post_id, '_uag_custom_page_level_css', true );
    if ( ! empty( $custom_css ) && ! self::$custom_css_appended ) {
        $this->stylesheet .= UAGB_Admin_Helper::sanitize_inline_css( $custom_css );
        self::$custom_css_appended = true;
    }
}
```

Spectra :
1. Lit le meta `_uag_custom_page_level_css`
2. Le sanitize via `sanitize_inline_css()` (sécurité)
3. Le concatène au `$this->stylesheet` qui sera injecté dans la page

**Important** : `uag_enable_on_page_css_button` doit être `yes` (default Spectra).

## Comment l'utiliser depuis le skill

### Étape 1 — Préparer les overrides CSS

Cibler les classes stables `.uagb-block-{block_id}` qui Spectra génère pour chaque bloc :

```css
/* Stats — chiffres 80px orange */
.uagb-block-v93-stat-1 .uagb-ifb-title,
.uagb-block-v93-stat-2 .uagb-ifb-title,
.uagb-block-v93-stat-3 .uagb-ifb-title,
.uagb-block-v93-stat-4 .uagb-ifb-title {
  font-size: 80px !important;
  color: #FD9800 !important;
  font-weight: 800 !important;
  line-height: 1 !important;
  letter-spacing: -3px !important;
}

/* Recipe story — chiffres 88px */
.uagb-block-v93-recipe-1-num .uagb-ifb-title,
.uagb-block-v93-recipe-2-num .uagb-ifb-title,
.uagb-block-v93-recipe-3-num .uagb-ifb-title {
  font-size: 88px !important;
  ...
}
```

`!important` est nécessaire pour override le CSS Spectra dynamique par défaut.

### Étape 2 — Injecter via REST API

```bash
curl -X POST -u "user:app-pass" \
  https://monsite.com/wp-json/wp/v2/pages/{id} \
  -H "Content-Type: application/json" \
  -d '{
    "content": "...",
    "meta": {
      "_uag_custom_page_level_css": "/* CSS overrides ici */"
    }
  }'
```

Ou via PHP CLI (mu-plugin compagnon) :

```php
update_post_meta($post_id, '_uag_custom_page_level_css', $custom_css);
```

### Étape 3 — Régénérer les assets Spectra

```bash
curl -X POST -u "user:app-pass" \
  https://monsite.com/wp-json/skill-test/v1/regen-spectra \
  -d '{"post_id":45}'
```

Spectra reconcatène le CSS du meta dans son stylesheet. Le `css_len` retourné doit avoir augmenté du nombre de chars de ton CSS.

## Persistance prouvée à travers les éditions

**Test du 02/05/2026** : page 45 modifiée 3× via REST API (équivalent à éditer + sauvegarder dans Gutenberg admin). Chaque save :
- `post_content` est re-parsé par Gutenberg → strip de tout inline `style="..."` non-schéma
- `_uag_custom_page_level_css` reste intact

Verdict : les chiffres énormes / guillemets / accent lines persistent à travers les éditions car **les classes CSS ciblées (`.uagb-block-v93-stat-1`, etc.) sont stables** dans le content Gutenberg.

## Convention naming pour le skill

Pour générer un CSS robuste, le skill utilise des `block_id` préfixés par version pattern :

| Pattern | Préfixe block_id | Convention CSS |
|---------|-----------------|----------------|
| `landing-formation-complete` | `v93-` | `.uagb-block-v93-{section}-{element}` |
| `hero-image-overlay` | `v93-hero-` | `.uagb-block-v93-hero-text` |
| Custom user pattern | `{slug}-` | `.uagb-block-{slug}-{element}` |

Exemple complet : voir [`examples/landing-formation-complete-page-css.css`](../examples/landing-formation-complete-page-css.css).

## Workflow skill recommandé

```
1. Génération markup → POST /wp-json/wp/v2/pages
2. Génération CSS overrides → meta _uag_custom_page_level_css
3. Régénération Spectra assets → /skill-test/v1/regen-spectra
4. Hit URL frontend (force pipeline) → temp-publish trick si draft
5. Validation visuelle agent-browser
```

## Limitations connues

1. **`uag_enable_on_page_css_button` doit être `yes`** : si désactivé globalement, le meta n'est pas lu (cf section Pré-requis Spectra ci-dessus + quirk #20). Vérifier dans Spectra Admin > Settings, ou auto-fix via l'endpoint compagnon.

2. **Pas de CSS Unicode escapes `\HHHH` dans les valeurs `content:`** (cf quirk #21). `UAGB_Admin_Helper::sanitize_inline_css()` strippe le backslash. Résultat : `content: "\201C"` rend le texte littéral `201C` au lieu du caractère « guillemet ». **Solution** : utiliser le caractère UTF-8 littéral directement :

   ```css
   /* MAUVAIS — strippé par sanitize, rendu littéral */
   .card::before { content: "\201C"; }

   /* BON — caractère UTF-8 littéral */
   .card::before { content: "“"; }
   ```

   Caractères concernés (les plus fréquents) :

   | Escape CSS | UTF-8 littéral | Nom |
   |---|---|---|
   | `\201C` | `“` | guillemet ouvrant anglais |
   | `\201D` | `”` | guillemet fermant anglais |
   | `\00AB` | `«` | guillemet ouvrant français |
   | `\00BB` | `»` | guillemet fermant français |
   | `\2014` | `—` | em-dash |
   | `\2026` | `…` | hellip |
   | `\00A9` | `©` | copyright |

   **Encoding obligatoire** : le file CSS DOIT être UTF-8 sans BOM. Si Windows enregistre avec BOM (0xEF 0xBB 0xBF), les caractères UTF-8 littéraux peuvent être corrompus. Vérifier : `file --mime-encoding overrides.css` → attendu `utf-8`, pas `utf-8 with BOM`.

3. **Autres déclarations CSS sanitize** : `UAGB_Admin_Helper::sanitize_inline_css()` peut filtrer d'autres déclarations CSS (e.g. `behavior:`, `expression()`, IE legacy, `@import` distant). Tester ton CSS pour confirmer.

4. **Limite de taille** : le meta peut avoir des limites de taille MySQL (16 MB pour LONGTEXT). En pratique, on est très loin (5K chars typique pour un pattern complexe).

5. **Cache LiteSpeed** : sur cours-ndrc.fr (LiteSpeed actif), purger les caches `wp litespeed-purge all` après update du meta. Sinon le CSS du meta est appliqué mais peut ne pas remonter dans le HTML cached.

## Workaround Quirk #23 — `wp_head` non-hook

Découvert 02/05/2026 sur loginarmor-dev (Twenty Twenty-Five FSE) + Spectra v2.19. Confirmé aussi sur Astra 4.13.1 + Spectra v2.19 (cf quirk #6 originel sur cours-ndrc.fr).

**Symptôme** : tout est OK côté serveur (`_uag_page_assets.css` contient 250K+ chars, `uagb_flag: true`), mais le HTML rendu n'a **AUCUN** `<style id="uagb-style-frontend-{post_id}">`. Tous les overrides perdus silencieusement.

**Diagnostic rapide** :

```bash
curl -s "{site}/{slug}/" | grep -c "uagb-style-frontend-{post_id}"
# Attendu : 1
# Si retourne 0 → quirk #23 actif → installer le workaround mu-plugin
```

**Fix universel** : ajouter dans le mu-plugin compagnon (`scripts/mu-plugin-skill-test.php`) un hook `wp_head` qui lit `_uag_page_assets.css` et l'injecte directement :

```php
add_action('wp_head', function () {
    if (!is_singular()) return;
    $pid = get_queried_object_id();
    if (!$pid) return;
    $pa = get_post_meta($pid, '_uag_page_assets', true);
    if (!is_array($pa)) return;
    $css = $pa['css'] ?? '';
    if (empty($css)) return;
    echo "\n<style id=\"uagb-style-frontend-{$pid}\" data-skill-injection=\"workaround-quirk-23\">\n";
    echo $css;
    echo "\n</style>\n";
}, 100);
```

**Coexistence** avec le hook Spectra natif : si Spectra réussit à hook (sur d'autres pages, ou après une mise à jour Spectra qui fixerait le bug), on aura 2 `<style>` identiques dans `<head>`. C'est inoffensif (le navigateur applique les deux, le second écrase le premier sur conflits → idempotent).

Depuis v1.0-rc4, ce workaround est **inclus par défaut** dans `scripts/mu-plugin-skill-test.php`. Aucune action utilisateur supplémentaire requise tant que le mu-plugin est déployé.

## TODO v1.1+

- [ ] Auto-générer le CSS depuis le markup template (parse `block_id` + lookup table de styles attendus)
- [ ] Détecter les conflits entre CSS skill et CSS user existant (autre que via balises tag-aware)
- [ ] Documenter pour chaque pattern (`patterns/*.md`) le CSS overrides associé attendu
- [ ] Endpoint REST pour valider post-render que le `<style id="uagb-style-frontend-X">` est bien dans le HTML
- [ ] Si Spectra v2.20+ fixe le bug `wp_head`, marquer le workaround comme deprecated (mais le garder pour les sites non-upgradés)
