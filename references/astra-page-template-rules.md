# Référence : Astra page templates — règles obligatoires

> **Lecture obligatoire avant de POST une page sur un site Astra.** Sans ces règles, ton hero avec H1 sera **doublé** par le post_title affiché par défaut → SEO cassé + rendu visuel raté.

## Règle absolue : forcer no-title sur les pages avec hero

Toute page générée par le skill qui contient un `uagb/info-box` H1 dans un hero doit avoir le post_meta suivant :

```php
update_post_meta($post_id, '_wp_page_template', 'no-title.php');
// OU plus robuste, configurer Astra meta directement
update_post_meta($post_id, 'site-content-layout', 'page-builder');
update_post_meta($post_id, 'site-sidebar-layout', 'no-sidebar');
update_post_meta($post_id, 'ast-title-bar-display', 'disabled');
update_post_meta($post_id, 'ast-main-header-display', 'enabled');
update_post_meta($post_id, 'ast-banner-title-visibility', 'disabled');
```

Sans ça, Astra applique son template default qui :
- Affiche `the_title()` en `<h1 class="entry-title">` au-dessus de `the_content()`
- Affiche le breadcrumb si activé
- Applique padding-top + padding-bottom 4em sur `.entry-content`

Conséquence : ton hero dramatique est précédé d'un titre de page par défaut, et la page entière paraît bricolée.

## Méthode 1 — via REST API meta update

Au moment de POST la page, inclure le meta dans le payload :

```bash
curl -X POST -u "user:pass" /wp-json/wp/v2/pages \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Ma page",
    "content": "...",
    "status": "publish",
    "meta": {
      "site-content-layout": "page-builder",
      "site-sidebar-layout": "no-sidebar",
      "ast-title-bar-display": "disabled",
      "ast-main-header-display": "enabled",
      "ast-banner-title-visibility": "disabled"
    }
  }'
```

**Limite** : `meta` accepte uniquement les meta_keys déclarés en `show_in_rest: true`. Astra le fait pour ses meta principaux, mais si ça ne passe pas, utiliser méthode 2.

## Méthode 2 — via mu-plugin compagnon

Dans `scripts/mu-plugin-skill-test.php`, ajouter un endpoint `/skill-test/v1/configure-page` :

```php
register_rest_route('skill-test/v1', '/configure-page', [
    'methods' => 'POST',
    'permission_callback' => function () { return current_user_can('manage_options'); },
    'callback' => function ($req) {
        $post_id = (int) $req->get_param('post_id');
        $config = $req->get_param('config') ?: 'hero-page';

        if ($config === 'hero-page') {
            update_post_meta($post_id, 'site-content-layout', 'page-builder');
            update_post_meta($post_id, 'site-sidebar-layout', 'no-sidebar');
            update_post_meta($post_id, 'ast-title-bar-display', 'disabled');
            update_post_meta($post_id, 'ast-banner-title-visibility', 'disabled');
        } elseif ($config === 'article') {
            // pour articles éditoriaux : keep title + breadcrumb
            update_post_meta($post_id, 'site-content-layout', 'normal-width-container');
            update_post_meta($post_id, 'site-sidebar-layout', 'no-sidebar');
            update_post_meta($post_id, 'ast-title-bar-display', 'enabled');
        }
        return ['ok' => true, 'config' => $config];
    },
]);
```

Usage :

```bash
curl -X POST -u "user:pass" /wp-json/skill-test/v1/configure-page \
  -d '{"post_id":45,"config":"hero-page"}'
```

## Configurations Astra recommandées par type de page

### Page avec hero alignfull (landing, à propos, services, contact, formation)

```json
{
  "site-content-layout": "page-builder",
  "site-sidebar-layout": "no-sidebar",
  "ast-title-bar-display": "disabled",
  "ast-main-header-display": "enabled",
  "ast-banner-title-visibility": "disabled",
  "ast-disable-related-posts": "disabled",
  "footer-sml-layout": "default"
}
```

Effet : aucun title, aucun breadcrumb, header normal, footer normal. La page commence directement par le hero.

### Article éditorial classique (blog post)

```json
{
  "site-content-layout": "normal-width-container",
  "site-sidebar-layout": "no-sidebar",
  "ast-title-bar-display": "enabled",
  "ast-main-header-display": "enabled",
  "ast-banner-title-visibility": "enabled"
}
```

Effet : title affiché en haut, breadcrumb visible, content centré max-width 800px.

### Page de connexion / signup ultra-minimal

```json
{
  "site-content-layout": "page-builder",
  "site-sidebar-layout": "no-sidebar",
  "ast-title-bar-display": "disabled",
  "ast-main-header-display": "disabled",
  "ast-main-header-display-tablet": "disabled",
  "ast-main-header-display-mobile": "disabled",
  "footer-sml-layout": "disabled"
}
```

Effet : ni header, ni footer, ni title. Page full-screen.

### Page coming soon / 404

```json
{
  "site-content-layout": "page-builder",
  "site-sidebar-layout": "no-sidebar",
  "ast-title-bar-display": "disabled",
  "ast-main-header-display": "enabled",
  "footer-sml-layout": "minimal"
}
```

## Pièges connus

### Astra padding-bottom 4em sur .entry-content

Symptôme : ton dernier bloc CTA banner alignfull est suivi d'un espace blanc orphelin de ~80px avant le footer.

Cause : Astra applique `padding-bottom: 4em` sur `.entry-content` par default.

Fix : injecter dans `_uag_custom_page_level_css` :

```css
.entry-content { padding-bottom: 0 !important; }
.entry-content > .alignfull:last-child { margin-bottom: 0 !important; }
```

### Astra Breadcrumb persistant malgré ast-banner-title-visibility:disabled

Sur certaines versions d'Astra, le breadcrumb survit au disabled. Solution backup :

```css
.ast-breadcrumbs-section { display: none !important; }
.ast-archive-description { display: none !important; }
```

### Astra Pro Custom Layouts qui se déclenchent

Si l'utilisateur a Astra Pro avec des « Custom Layouts » configurés (e.g. notification bar globale), ils peuvent s'injecter avant le hero. Pas de fix générique, c'est de la config user.

Détection : view-source → grep `astra-custom-layout`. Si match, alerter l'utilisateur que son site a des Custom Layouts qui peuvent interférer.

### Astra Header transparent

Pour un hero avec image bg, on peut vouloir un header transparent overlay. Astra Pro a une option `theme-transparent-header-meta`. Si disponible :

```json
{
  "theme-transparent-header-meta": "enabled"
}
```

Sans Astra Pro, le header reste opaque par-dessus le hero — moins joli mais OK.

## Validation

Après POST + configure-page, vérifier :

```bash
curl -s URL | grep -c '<h1'
# Attendu : 1 (uniquement le H1 du hero info-box)

curl -s URL | grep -c 'entry-title'
# Attendu : 0 (le post_title n'est pas affiché)

curl -s URL | grep -c 'ast-breadcrumbs'
# Attendu : 0
```

## TODO v1.1+

- [ ] Auto-application du config Astra par `scripts/post-page-via-rest.php` selon le type de page détecté (hero alignfull → no-title, article → with-title)
- [ ] Documentation de Astra Pro features (header builder, footer builder, mega menu) dans `modules/astra/`
- [ ] Variantes pour Astra Pro Custom Layouts (notification bar, sticky CTA, etc.)
