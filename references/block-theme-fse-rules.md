# Référence : règles spécifiques aux block themes FSE

> **Lecture obligatoire si le site cible utilise un block theme FSE.** WordPress 6.0+ a introduit le Full Site Editing (FSE) avec des thèmes 100 % blocs (Twenty Twenty-Three à Twenty Twenty-Five, Frost, Ollie, etc.). Leur architecture diffère significativement des thèmes classiques (Astra, GeneratePress, Hello Elementor) et impose 5-7 contraintes spécifiques à connaître pour ne pas livrer du markup cassé.

## Détection block theme

```php
// Côté PHP (mu-plugin compagnon ou script PHP one-shot)
$is_block_theme = function_exists('wp_is_block_theme') && wp_is_block_theme();
```

Côté skill : `scripts/detect-environment.php` retourne `theme.is_block_theme: true|false`. Si `true`, le routing doit appliquer les règles ci-dessous.

## Block themes les plus fréquents (mai 2026)

| Thème | Adoption | Stack notable |
|---|---|---|
| **Twenty Twenty-Five** | Default WP 6.7+, ~5 % des nouvelles installs | block theme propre, font Manrope, palettes WCAG |
| Twenty Twenty-Four | Default WP 6.4-6.6 | block theme polyvalent |
| Twenty Twenty-Three | Default WP 6.1-6.3 | premier block theme WP officiel |
| Frost | Premium block theme (Brian Gardner) | minimaliste éditorial |
| Ollie | Block theme open source | très complet, header/footer builder via blocks |

Les **thèmes classiques** suivants restent NON-FSE et n'imposent pas ces contraintes : Astra, GeneratePress, Kadence, Hello Elementor, Blocksy.

## Règle #1 — `wp:post-title` automatique = double H1 garanti

Les block themes FSE rendent leurs templates HTML depuis `/wp-content/themes/{theme}/templates/` (e.g. `single.html`, `page.html`). Ces templates contiennent des blocs `<!-- wp:post-title /-->` hardcodés qui injectent automatiquement un `<h1 class="wp-block-post-title">` à partir du `post_title`.

**Conséquence pour le skill** : si tu génères un hero avec un H1 `uagb/info-box` propre, le frontend rend **2 H1** :

```html
<h1 class="wp-block-post-title">{{ post_title }}</h1>
<h1 class="uagb-ifb-title">{{ headline_hero }}</h1>
```

Tu **ne peux pas** désactiver ce comportement via post_meta comme tu le fais sur Astra (`update_post_meta($pid, 'ast-title-bar-display', 'disabled')` est ignoré sur les block themes — option Astra-spécifique).

### Fix v1.0-rc4 — meta `_skill_hide_post_title` + hook mu-plugin

Le mu-plugin compagnon `scripts/mu-plugin-skill-test.php` ajoute :

1. Un filtre `body_class` qui ajoute la classe `skill-hide-post-title` sur les pages qui ont le meta `_skill_hide_post_title=1`
2. Un hook `wp_head` qui injecte `.skill-hide-post-title .wp-block-post-title { display: none !important; }`

Workflow skill :

```bash
# Au POST de la page, ajouter le meta
curl -X POST -u "user:app-pass" \
  /wp-json/wp/v2/pages/{ID} \
  -d '{"meta":{"_skill_hide_post_title":"1"}}'
```

ou directement dans le payload de création :

```json
{
  "title": "Ma page",
  "content": "...",
  "meta": {
    "_uag_custom_page_level_css": "...",
    "_skill_hide_post_title": "1"
  }
}
```

**Avantage** : universel (fonctionne sur tous les block themes), persistant à travers les éditions Gutenberg, ne casse pas si l'utilisateur change de thème (le post_title sera juste réaffiché normalement).

### Fallback option B — CSS scope dans `_uag_custom_page_level_css`

Si le mu-plugin compagnon n'est pas installé, fallback CSS scopé sur `body.page-id-{ID}` directement dans le meta CSS :

```css
body.page-id-{ID} .wp-block-post-title,
.page-id-{ID} main .wp-block-post-title {
  display: none !important;
}
```

Inconvénient : tu dois connaître l'ID de la page **après** son POST (donc en 2 étapes : POST page → récupère ID → re-update meta CSS avec scope). Le mu-plugin évite ce round-trip.

## Règle #2 — `entry-content` padding

Les block themes FSE appliquent souvent un padding-left/right sur `.wp-block-post-content` ou `.entry-content` pour la lisibilité éditoriale (40-80px). Pour les blocs `alignfull` (qui doivent toucher les bords), c'est un problème : le full-width est compressé.

**Symptôme** : ton hero `uagb/container alignfull` a une marge gauche-droite parasite de 40-80px au lieu d'être edge-to-edge.

**Fix** : dans `_uag_custom_page_level_css` :

```css
body.page-id-{ID} .wp-block-post-content,
body.page-id-{ID} .entry-content {
  padding-left: 0 !important;
  padding-right: 0 !important;
}
```

## Règle #3 — Pas de `template_redirect` ni `single.php`

Les block themes n'ont pas de fichiers `single.php` / `page.php` PHP. Tout passe par les templates `.html` FSE. Conséquence : les hooks PHP traditionnels comme `template_redirect`, `the_content` filter, `wp_template_part` ne se déclenchent pas dans le même ordre.

**Implication skill** : les techniques d'injection CSS via `the_content` (parfois utilisées dans des skills d'autres frameworks) ne marchent pas. **Toujours passer par `wp_head` ou par le post_meta `_uag_custom_page_level_css`** (ce que le skill fait déjà).

## Règle #4 — Templates configurables via `wp_template`

L'utilisateur peut éditer ses templates via WP Admin > Apparence > Éditeur. Il peut donc :
- Supprimer le bloc `wp:post-title` du template `page.html` (auquel cas le quirk #24 disparaît)
- Ajouter des blocs custom au template (header, footer, sidebar)
- Créer un template page sans titre dédié à appliquer aux pages skill

**Implication skill** : ne PAS supposer que le template par défaut est intact. Toujours appliquer le workaround `_skill_hide_post_title` même si le template a été édité — au pire, le CSS hide une classe qui n'existe plus, c'est inoffensif.

## Règle #5 — Global styles via `theme.json`

Les block themes définissent leurs couleurs, fonts, spacings dans `theme.json` (à la racine du thème). Les variables sont exposées en CSS via `--wp--preset--color-{slug}`, `--wp--preset--font-size-{slug}`, etc.

**Implication skill** : le skill peut détecter ces variables via :

```bash
curl -s /wp-json/wp/v2/global-styles/themes/{theme-slug}
```

et les utiliser à la place des `var(--ast-global-color-X)` Astra. **TODO v1.1+** : router automatiquement vers `--wp--preset--color-X` si block theme détecté.

En l'état (v1.0-rc4), le skill utilise des **hex directs** (`#2563EB`, `#0F172A`, etc.) en mode block theme, ce qui marche partout mais perd l'intégration palette du thème. C'est un trade-off acceptable pour la v1.0.

## Règle #6 — Taille `wp_template_part` header/footer

Les block themes FSE utilisent `wp:template-part {"slug":"header"}` et `{"slug":"footer"}` qui rendent automatiquement le header / footer global. Tu **ne peux pas** les désactiver à la pièce sur une page (sauf via template page custom).

**Implication skill** : si l'utilisateur veut une landing « pleine page sans header/footer » (cas SaaS conversion), ce n'est pas possible nativement avec les block themes. Solutions :
- Créer un template `page-full-width.html` custom (compliqué)
- Utiliser un plugin comme **Page Builder Framework** ou **CoBlocks** qui exposent des layouts no-header-no-footer
- Switch le thème sur la page concernée (out of scope skill)

Sur les thèmes classiques (Astra), cette feature est native via `update_post_meta($pid, 'site-content-layout', 'page-builder')`. Sur les block themes, non.

## Règle #7 — Block patterns natifs vs Spectra

Les block themes proposent leurs propres « block patterns » (compositions pré-faites accessibles via le bouton « + Patterns » dans Gutenberg). Twenty Twenty-Five fournit ~30 patterns hero / about / pricing / faq.

**Implication skill** : ne PAS confondre les patterns Spectra (`uagb/*`) avec les patterns FSE natifs (composés de blocs `core/*`). Le skill génère **uniquement des compositions Spectra** (cohérence + meilleur design system + 49 blocs vs 30 blocs core). Si l'utilisateur veut mixer, c'est sa responsabilité après le POST initial.

## Stratégie skill globale pour block themes FSE

Quand `detect-environment.php` retourne `theme.is_block_theme: true` :

1. ✅ Ajouter `_skill_hide_post_title=1` dans le payload POST page (fix Quirk #24)
2. ✅ Forcer `entry-content padding: 0` dans `_uag_custom_page_level_css`
3. ✅ Utiliser des **hex directs** (pas de `var(--ast-global-color-X)`)
4. ✅ Ne PAS tenter `update_post_meta('site-content-layout', 'page-builder')` (Astra-spécifique, ignoré)
5. ✅ Ne PAS tenter `update_post_meta('ast-title-bar-display', 'disabled')` (Astra-spécifique)
6. ⚠️ Avertir l'utilisateur qu'il peut toujours éditer le template via Apparence > Éditeur s'il veut customiser le header/footer
7. 🔮 v1.1+ : détecter et utiliser les variables `--wp--preset--color-X` du thème

## Tests recommandés sur block themes FSE

Avant de déclarer le skill « stable » sur les block themes, tester sur ces 3 :

- ✅ **Twenty Twenty-Five** (default WP 6.7+) — testé 02/05/2026 sur loginarmor-dev (page 59)
- ⏳ Twenty Twenty-Four
- ⏳ Frost (premium, populaire pour les thèmes éditoriaux)

Critères de validation :
1. Pas de double H1 (grep `<h1 class="wp-block-post-title">` doit ne pas matcher dans le rendu si `_skill_hide_post_title=1`)
2. Hero alignfull touche les bords gauche/droite (pas de padding parasite)
3. Toutes les classes `uagb-block-{slug}-*` sont présentes dans le HTML rendu
4. Le CSS `_uag_custom_page_level_css` est bien injecté dans `<head>` (cf workaround Quirk #23)
5. Aucune régression sur les autres pages du site (le scope `body.page-id-{ID}` ou `_skill_hide_post_title=1` empêche les fuites)

## Pour aller plus loin

- Quirk #23 (Spectra wp_head non-hook) : `references/spectra-attributes-quirks.md`
- Quirk #24 (double H1 FSE) : `references/spectra-attributes-quirks.md`
- Mu-plugin compagnon avec hooks : `scripts/mu-plugin-skill-test.php`
- Workaround inline CSS via meta : `references/persistent-css-overrides.md`
