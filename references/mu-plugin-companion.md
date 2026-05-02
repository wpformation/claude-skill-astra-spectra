# Référence : mu-plugin compagnon

> Le skill expose le pipeline complet via 5 endpoints REST custom. Le mu-plugin compagnon doit être déployé sur le site cible pour utiliser le skill efficacement (sinon le skill tombe sur les fallbacks `temp-publish-trick` qui marchent mais sont moins propres).

## Installation

Copier `scripts/mu-plugin-skill-test.php` dans `wp-content/mu-plugins/` du site cible :

```bash
cp scripts/mu-plugin-skill-test.php /path/to/wordpress/wp-content/mu-plugins/zzz-skill-companion.php
```

Sur Local by Flywheel : `C:/Users/USER/Local Sites/<site>/app/public/wp-content/mu-plugins/`.
Sur o2switch / OVH mutu : via FTP dans `<docroot>/wp-content/mu-plugins/`.

Pas d'activation nécessaire (mu-plugin = must-use, chargé automatiquement).

## Endpoints exposés

### POST `/wp-json/skill-test/v1/setup`

Configure une palette test (palette_3 cours-ndrc.fr orange WPF par défaut). Permission : `manage_options`.

**Réponse** :
```json
{
  "ok": true,
  "palette": ["#FD9800","#E98C00","#0F172A","#454F5E","#FEF9E1","#FFFFFF","#F9F0C8","#141006","#222222"]
}
```

**Effets** :
- Met à jour `astra-settings.global-color-palette.palette` avec la palette_3
- Vide `UAGB_Helper::delete_uag_asset_dir()` (cache CSS Spectra)
- Vide `astra_dynamic_css` transient
- Flush `wp_cache_flush()`

### POST `/wp-json/skill-test/v1/upload-image`

Upload une image depuis une URL externe (Unsplash, Pexels) avec extension forcée. Permission : `upload_files`.

**Body** :
```json
{
  "url": "https://images.unsplash.com/photo-XXXX?w=1920&h=1080",
  "name": "hero-img-1.jpg"
}
```

**Réponse** :
```json
{"ok": true, "id": 23, "url": "https://site.local/wp-content/uploads/.../hero-img-1.jpg"}
```

**Pourquoi `name` est crucial** : Unsplash sert les images sans extension dans l'URL. WordPress refuse l'upload sans mime type clair. L'option `test_form: false` + un name avec `.jpg` force le mime detection correct.

### POST `/wp-json/skill-test/v1/regen-spectra`

Force la régénération des assets CSS Spectra pour un post donné. Permission : `edit_posts`.

**Body** : `{"post_id": 41}`

**Réponse** :
```json
{
  "ok": true,
  "css_len": 239568,
  "js_len": 2027
}
```

**Effets** :
- Instancie `new UAGB_Post_Assets($post_id)`
- Appelle `->generate_assets()` puis `->update_page_assets()` si dispo
- Le CSS est sauvegardé dans le post_meta `_uag_page_assets['css']`

### GET `/wp-json/skill-test/v1/inspect-faq`

Inspecte les attributs déclarés par le bloc `uagb/faq-child`. Permission : `edit_posts`.

**Réponse** :
```json
{
  "ok": true,
  "attributes": ["isPreview","block_id","anchor","question","answer","icon","iconActive","layout","headingTag","lock","metadata","className"]
}
```

**Pourquoi c'est utile** : permet de découvrir le nom EXACT d'un attribut Spectra sans plonger dans le code minifié. Ex : on sait que c'est `answer` (pas `description`) pour la réponse.

Étendable à n'importe quel bloc en généralisant l'endpoint.

### POST `/wp-json/skill-test/v1/cleanup`

Supprime toutes les pages avec meta `_skill_test_page=1`. Permission : `manage_options`.

**Réponse** : `{"ok": true, "deleted": 3}`

## Code du mu-plugin

Voir [`scripts/mu-plugin-skill-test.php`](../scripts/mu-plugin-skill-test.php) pour le code complet.

## Sécurité — recommandations production

**ATTENTION** : ce mu-plugin expose des endpoints qui modifient la palette Astra, uploadent des images, créent et suppriment des pages. **À retirer après usage** sur un site live.

Pour usage permanent en prod, ajouter :
- Vérification `wp_verify_nonce()` sur chaque endpoint
- Restriction `current_user_can('manage_options')` au lieu de capabilities granulaires
- Logger les appels dans un fichier de log dédié
- Préfixer les routes avec un secret en variable d'environnement

Pour test/dev uniquement, le mu-plugin actuel est suffisant.

## Cleanup après tests

```bash
# Supprimer pages test
curl -X POST -u "admin:APP_PASS" \
  http://site.local/wp-json/skill-test/v1/cleanup

# Supprimer le mu-plugin
rm /path/to/wp-content/mu-plugins/zzz-skill-companion.php
```

## Alternatives sans mu-plugin

Si tu ne peux/veux pas déployer le mu-plugin (site sécurisé en prod, accès limité), le skill v0.9.1 utilise des fallbacks :

| Endpoint | Fallback (sans mu-plugin) |
|----------|---------------------------|
| `/setup` | Configuration manuelle via WP Customizer > Global Colors |
| `/upload-image` | Upload manuel via Media Library admin OU `media_handle_sideload` via wp-cli |
| `/regen-spectra` | `wpf_skill_temp_publish_trick()` dans `post-page-via-rest.php` (publish→GET→draft revert) |
| `/inspect-faq` | Documentation patterns (les attributs critiques sont déjà documentés dans les patterns) |
| `/cleanup` | wp-cli : `wp post delete $(wp post list --meta_key=_skill_test_page --meta_value=1 --format=ids)` |

Le skill marche sans mu-plugin, mais avec, c'est ~3× plus rapide et plus fiable.
