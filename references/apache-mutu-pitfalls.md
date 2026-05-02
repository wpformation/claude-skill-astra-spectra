# Référence : pièges hébergeurs mutualisés Apache + LiteSpeed

> **Lecture obligatoire si tu déploies sur o2switch, OVH mutualisé, 1&1/IONOS, Hostinger, ou tout autre hébergeur WordPress mutualisé.** Ces hébergeurs ont des comportements spécifiques qui font échouer le pipeline standard du skill.

## Bug critique #1 — Authorization header strippé par Apache

**Symptôme** : `curl -u 'user:app-password' /wp-json/wp/v2/users/me` retourne `{"code":"rest_not_logged_in","data":{"status":401}}` malgré des credentials corrects.

**Reproductible sur** : o2switch, OVH mutu, Hostinger, 1&1/IONOS, certains plans GoDaddy.

**Cause** : ces hébergeurs utilisent Apache avec FastCGI/PHP-FPM. Par défaut, Apache **ne forwarde pas le header `Authorization`** à PHP pour des raisons « sécurité » legacy. Donc quand WordPress reçoit la requête, l'header est absent, donc l'authentification Application Password échoue.

**Fix** : ajouter dans `.htaccess` à la racine WordPress, après `RewriteEngine On` :

```apache
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```

Variante plus permissive si la précédente ne fonctionne pas :

```apache
SetEnvIfNoCase ^Authorization$ "(.+)" HTTP_AUTHORIZATION=$1
```

**Détection** : test après ajout :

```bash
curl -u 'user:pass' https://monsite.com/wp-json/wp/v2/users/me
# Attendu : { "id": 1, "name": "admin", ... }
# Pas attendu : { "code": "rest_not_logged_in" }
```

**Note** : si l'utilisateur n'a pas accès au `.htaccess` (rare mais possible), proposer la méthode alternative WP-CLI pour les opérations critiques.

## Bug critique #2 — LiteSpeed Cache sert du cache stale

**Symptôme** : tu update une page via REST API. tu hit la URL frontend via curl : la réponse est l'**ancienne version** de la page. Pas de regen Spectra CSS car le HTML cached n'a pas changé.

**Reproductible sur** : tous les sites avec LiteSpeed Cache plugin (très courant sur o2switch, certains hébergeurs LiteSpeed-based).

**Cause** : LiteSpeed Cache met en cache la réponse HTML. Le hit interne PHP (curl localhost depuis le serveur) sert le cache au lieu de déclencher les hooks Astra/Spectra qui généreraient le CSS dynamique.

**Fix** : avant chaque test/screenshot, purger les caches LiteSpeed :

```bash
# Via mu-plugin compagnon (à étendre)
curl -X POST -u 'user:pass' /wp-json/skill-test/v1/purge-caches

# Ou via WP-CLI
wp litespeed-purge all

# Ou côté WP, programmatiquement
do_action('litespeed_purge_post', $post_id);
do_action('litespeed_purge_all');
```

**Endpoint à ajouter au mu-plugin compagnon** :

```php
register_rest_route('skill-test/v1', '/purge-caches', [
    'methods' => 'POST',
    'permission_callback' => function () { return current_user_can('manage_options'); },
    'callback' => function () {
        $purged = [];
        if (function_exists('litespeed_purge_all') || class_exists('LiteSpeed\Purge')) {
            do_action('litespeed_purge_all');
            $purged[] = 'litespeed';
        }
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
            $purged[] = 'w3tc';
        }
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
            $purged[] = 'object_cache';
        }
        if (class_exists('UAGB_Helper') && method_exists('UAGB_Helper', 'delete_uag_asset_dir')) {
            UAGB_Helper::delete_uag_asset_dir();
            $purged[] = 'spectra_assets_dir';
        }
        delete_transient('astra_dynamic_css');
        $purged[] = 'astra_dynamic_css';
        return ['ok' => true, 'purged' => $purged];
    },
]);
```

**Détection** : modifier le content de la page, hit la frontend, comparer le HTML rendu au content envoyé. Si différence = cache.

## Bug critique #3 — Rate limiting WAF / mod_security

**Symptôme** : après 5-10 requêtes API rapides, les suivantes retournent 403 ou 503. Ou le serveur devient lent et timeout.

**Reproductible sur** : o2switch (mod_security strict), OVH (rate limit IP), GoDaddy.

**Cause** : Web Application Firewall détecte un pattern de requêtes anormalement rapide depuis ta machine et bloque.

**Fix** :
- Ajouter `User-Agent: claude-skill-astra-spectra/1.0 (compatible)` (certains WAF whitelist les UA légitimes)
- Throttle les requêtes : sleep 1s entre chaque POST/PUT
- Utiliser une whitelist IP (l'utilisateur ajoute ton IP en liste blanche WAF)

**Pattern recommandé** :

```bash
for i in {1..N}; do
  curl -s -X POST ... -H "User-Agent: claude-skill-astra-spectra/1.0"
  sleep 1
done
```

**Détection** : si 403 après plusieurs requêtes, c'est WAF. Si 503 ou timeout, c'est rate limit.

## Bug critique #4 — Application Password désactivés

**Symptôme** : `/wp-json/wp/v2/users/me` retourne `{"code":"application_passwords_disabled"}`.

**Reproductible sur** : sites avec plugins de sécurité (Wordfence, Solid Security, Sucuri) qui désactivent les Application Passwords par défaut.

**Cause** : Application Passwords (introduit en WP 5.6) sont désactivés par certains plugins ou par un mu-plugin custom.

**Fix** :
- Demander à l'utilisateur de réactiver dans Wordfence > Login Security ou équivalent
- OU créer un mu-plugin qui force l'activation :

```php
// mu-plugins/0-enable-app-passwords.php
add_filter('wp_is_application_passwords_available', '__return_true');
```

**Détection** : vérifier `/wp-admin/profile.php` → section « Application Passwords » doit être présente. Si absente, désactivé.

## Bug critique #5 — wp-cron.php désactivé / cron serveur

**Symptôme** : les régénérations de Spectra assets en background ne se font pas. Les pages publiées ont du CSS Spectra obsolète.

**Reproductible sur** : sites avec `DISABLE_WP_CRON: true` dans wp-config.php (recommandé par o2switch pour la perf).

**Cause** : Spectra utilise wp-cron pour des tâches background. Si wp-cron désactivé, ces tâches ne s'exécutent pas.

**Fix** : forcer la régénération synchrone après chaque update :

```php
// dans le mu-plugin compagnon
public function force_spectra_sync_regen($post_id) {
    if (class_exists('UAGB_Post_Assets')) {
        $a = new UAGB_Post_Assets($post_id);
        $a->generate_assets();
        if (method_exists($a, 'update_page_assets')) {
            $a->update_page_assets();
        }
    }
}
```

C'est exactement ce que fait `/skill-test/v1/regen-spectra` dans le mu-plugin.

## Pipeline pour un site mutualisé Apache (cours-ndrc.fr / o2switch profile)

```
1. Vérifier .htaccess contient RewriteRule HTTP_AUTHORIZATION
   → si absent, demander au user de l'ajouter (ou utiliser SSH/FTP)

2. Vérifier Application Passwords actifs
   → /wp-admin/profile.php section visible

3. Tester auth
   → curl -u 'user:pass' /wp-json/wp/v2/users/me?context=edit
   → doit retourner les capabilities user

4. Déployer mu-plugin compagnon (si pas déjà)
   → cp scripts/mu-plugin-skill-test.php → /wp-content/mu-plugins/

5. POST page via REST + meta config Astra
   → /wp-json/wp/v2/pages avec status=draft
   → meta._uag_custom_page_level_css inclus

6. Configure page (no-title, sidebar, etc.)
   → /skill-test/v1/configure-page (cf astra-page-template-rules.md)

7. Force regen Spectra
   → /skill-test/v1/regen-spectra

8. Purge caches LiteSpeed
   → /skill-test/v1/purge-caches

9. Wait 2 secondes (LiteSpeed restart cache fresh)

10. Hit frontend URL avec User-Agent légitime
    → curl -A 'Mozilla/5.0 ...' https://site.com/page/

11. Validate :
    - HTTP 200
    - <style id="uagb-style-frontend-X"> présent
    - astra-theme-css link présent
    - Pas d'élément ast-breadcrumbs si configure=hero-page

12. Screenshot via agent-browser
```

## Recommandations à transmettre à l'utilisateur

Si l'utilisateur déploie sur o2switch / OVH mutu :

1. Vérifier que `.htaccess` contient le RewriteRule HTTP_AUTHORIZATION
2. Garder Application Passwords actifs dans Wordfence/Solid Security
3. Désactiver le cache LiteSpeed pour les pages en cours de génération (purge auto à chaque update)
4. Whitelist IP si tests intensifs (rate limit OVH/o2switch ~30 req/sec strict)
5. Pour des opérations bulk (>10 pages), faire des batches de 5 avec sleep 2s entre

## TODO v1.1+

- [ ] Endpoint `/skill-test/v1/diagnose-host` qui détecte automatiquement l'hébergeur et flag les pièges connus
- [ ] Auto-injection du RewriteRule HTTP_AUTHORIZATION dans `.htaccess` via mu-plugin (avec confirmation user)
- [ ] Détection LiteSpeed installé + auto-purge avant chaque hit frontend dans les workflows
- [ ] Tests sur Kinsta, WP Engine, Cloudways (managed WP) — souvent sans ces pièges Apache mutu
