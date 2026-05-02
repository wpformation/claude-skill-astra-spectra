# Référence : Spectra attributs — pièges connus et fixes

> **Lecture obligatoire avant de générer du markup `uagb/*`.** Spectra a des comportements non-documentés qui font échouer du markup techniquement valide. Ce fichier liste les pièges détectés en production et les fixes validés.

## Convention de lecture

Chaque piège a 4 sections : **Symptôme**, **Cause**, **Fix**, **Détection**.

---

## 1. `headingFontSize:80` ignoré quand `headingTag:"p"`

**Symptôme** : tu mets `"headingTag":"p","headingFontSizeDesktop":80` dans un `uagb/info-box`. Au rendu, le `<p>` est en font-size par défaut (16px), pas 80px.

**Cause** : Spectra applique le `headingFontSize` via une CSS class générée dynamiquement (`uagb-style-frontend-{post_id}`). Cette CSS n'est PAS toujours injectée dans `<head>` (voir piège #6). Et même quand elle l'est, le sélecteur CSS cible parfois `.uagb-ifb-title` mais pas avec assez de spécificité pour override le default `<p>` du thème.

**Fix** : utiliser le meta natif `_uag_custom_page_level_css` avec `!important` :

```css
.uagb-block-{block_id} .uagb-ifb-title {
  font-size: 80px !important;
  font-weight: 800 !important;
  line-height: 1 !important;
  letter-spacing: -3px !important;
  margin: 0 !important;
}
```

Voir `references/persistent-css-overrides.md` pour le mécanisme complet.

**Détection** : screenshot en agent-browser. Si les chiffres / titres font visuellement 16px alors que le markup demande 80px, tu es dans ce piège.

---

## 2. `info-box` ne supporte pas `widthDesktop`

**Symptôme** : tu mets 4 `uagb/info-box` dans un container `directionDesktop:"row"` avec `widthDesktop:25` sur chaque info-box. Au rendu, les 4 sont **empilés verticalement** en colonne unique pleine largeur.

**Cause** : `widthDesktop` est un attribut **container** (`uagb/container`), pas info-box. Sur info-box il est ignoré, le bloc prend 100% width, ce qui force le wrap dans un row container.

**Fix** : wrapper chaque info-box dans un `uagb/container` enfant avec width :

```
container parent (direction:row, wrap:wrap)
  ├─ container#stat-1-w (widthDesktop:22)
  │     └─ info-box stat-1
  ├─ container#stat-2-w (widthDesktop:22)
  │     └─ info-box stat-2
  └─ ... etc
```

**Détection** : valider dans le DOM rendu : `display:flex` sur le parent + chaque `.uagb-container` enfant a `width:22%`.

---

## 3. `uagb/faq-child` attribut `answer` (PAS `description`)

**Symptôme** : tu génères des FAQ-child avec `"description":"Oui, totalement..."`. Au rendu, l'accordéon affiche `Lorem ipsum dolor sit amet, consectetur...` (placeholder).

**Cause** : l'attribut s'appelle `answer`, pas `description`. C'est documenté nulle part dans la doc Spectra publique. Vérifié via `WP_Block_Type_Registry::get_registered('uagb/faq-child')->attributes` qui retourne `[isPreview, block_id, anchor, question, answer, icon, iconActive, layout, ...]`.

**Fix** : utiliser `answer` :

```html
<!-- wp:uagb/faq-child {"block_id":"faq-1","question":"Le site est-il gratuit ?","answer":"Oui, totalement..."} -->
```

L'inner content `<p class="uagb-faq-content">` doit AUSSI contenir le texte de réponse pour le rendu initial avant que JS prenne le relais.

**Détection** : screenshot l'accordéon expanded. Si tu vois Lorem Ipsum, c'est ce piège.

---

## 4. Inline `style="..."` strippé par Gutenberg save

**Symptôme** : tu injectes `<p class="uagb-ifb-title" style="font-size:80px;color:#FD9800">227</p>` dans le innerContent du bloc Gutenberg. Au rendu initial, ça marche. Mais dès que l'utilisateur ouvre la page dans Gutenberg admin et clique « Mettre à jour », le `style="..."` disparaît.

**Cause** : Gutenberg parse le bloc, regenère le innerContent à partir du JSON `attrs` via la fonction `save()` du bloc. Tout HTML inline qui n'est pas dans le schéma de bloc (notamment les attributs `style="..."`) est strippé pour rester clean.

**Fix** : NE JAMAIS mettre de `style="..."` inline dans le innerContent. Utiliser à la place le meta `_uag_custom_page_level_css` (cf piège #1 et `references/persistent-css-overrides.md`).

**Détection** : édit la page dans Gutenberg admin, sauvegarde, recharge le front. Si le style a disparu, c'est ce piège.

---

## 5. `block_id` manquant ou non-unique = re-compute Gutenberg

**Symptôme** : tu génères 3 blocs `uagb/container` sans `block_id` ou avec le même `block_id`. Au save Gutenberg, certains blocs deviennent `uagb-block-undefined` ou perdent leurs styles, ou Gutenberg recompute un nouveau block_id qui change les classes CSS générées.

**Cause** : Spectra utilise `block_id` comme identité unique pour générer les classes CSS dynamiques (`uagb-block-{block_id}`). Sans block_id ou avec doublons, le système est cassé.

**Fix** : tout bloc `uagb/*` DOIT avoir un `block_id` unique sur la page. Convention : `{slug-page}-{section}-{element}`, e.g. `accueil-hero-text`, `accueil-stats-1`, `accueil-feat-1-num`.

**Détection** : `scripts/validate-block-markup.php` flag les block_ids dupliqués ou manquants en P0.

---

## 6. `<style id="uagb-style-frontend-{post_id}">` absent du `<head>`

**Symptôme** : page publiée. Le `_uag_page_assets['css']` post_meta contient 200K+ chars de CSS Spectra dynamique. Mais le HTML rendu n'a aucun `<style id="uagb-style-frontend-{post_id}">` dans `<head>`. Conséquence : tous les attributs visuels (font-size, padding responsive, colors résolues) sont ignorés.

**Cause** : reproduction inconnue, lié à un hook `wp_head` qui ne s'enregistre pas. Confirmé sur loginarmor-dev (Local by Flywheel) ET cours-ndrc.fr (o2switch + LiteSpeed). Probablement bug Spectra v2.19.x non publiquement documenté.

**Fix** :
- Court terme : utiliser `_uag_custom_page_level_css` (cf #1) pour les overrides critiques. Ce meta EST concaténé au stylesheet par Spectra (vérifié dans `class-uagb-post-assets.php:1434`).
- Long terme : forcer la régénération via `(new UAGB_Post_Assets($post_id))->generate_assets()` dans un hook custom, ou attendre fix Spectra.

**Détection** : `view-source:` sur la page publiée → grep `uagb-style-frontend`. Si absent, tu es dans ce piège.

---

## 7. Slot `var(--ast-global-color-X)` arbitraire selon palette

**Symptôme** : tu utilises `containerBorderColor: "var(--ast-global-color-7)"` car la doc Astra dit que c'est le « border subtle gris ». Sur palette_3 (cours-ndrc.fr orange WPF), color-7 vaut `#141006` (presque noir). Résultat : bord noir massif au lieu de gris.

**Cause** : les slots Astra color-0 à color-8 sont **conventionnés** sur la palette default mais **arbitraires** sur les palettes preset_X et user-custom. Color-7 peut valoir gris clair (default), noir massif (palette_3), ou n'importe quoi.

**Slots GARANTIS stables sur 11 presets Astra** (mesuré 02/05/2026) :
- `color-0` : primary saturé
- `color-1` : primary darker (hover)
- `color-2` : text heading dark
- `color-3` : text body
- `color-5` : white pur

**Slots VARIABLES** (à éviter) :
- `color-4`, `color-6`, `color-7`, `color-8` : variables selon la palette

**Fix** : pour les rôles bg/border subtils, utiliser des **hex neutres directs** (`#fafafa`, `#ffffff`, `#e5e7eb`) qui marchent sur toutes les palettes. OU résoudre dynamiquement via `wpf_skill_resolve_color('border_subtle', $palette)` (script `resolve-palette.php`).

**Détection** : tester sur 3 palettes (default, preset_3 orange, preset_8 vert). Si une section apparaît noire/illisible sur l'une d'elles, c'est ce piège.

Voir `references/astra-palette-mechanics.md` pour la table complète + résolution.

---

## 8. Icônes Spectra avec noms inventés → fallback identique

**Symptôme** : tu mets `"icon":"book-open"`, `"icon":"clipboard-check"`, `"icon":"timer"` sur 3 `uagb/icon` blocs. Au rendu, les 3 affichent **la même icône fallback** (souvent un rectangle vide ou une icône placeholder).

**Cause** : Spectra utilise un sous-ensemble Font Awesome avec des noms courts spécifiques. `book-open` → en réalité `book` ou `book-reader`. `clipboard-check` → `clipboard` ou `tasks`. `timer` n'existe pas → utiliser `clock` ou `stopwatch`.

**Fix** :
- Solution préférée : utiliser des **numéros éditoriaux 01 / 02 / 03** dans un info-box (cf `patterns/features-numbered.md`). Plus distinctif visuellement, 0 risque de fallback.
- Solution alternative : consulter `references/spectra-icons-list.md` pour la whitelist d'icônes validées.

**Détection** : regarde tes 3 cards features. Si elles ont toutes la même icône (ou un placeholder), c'est ce piège.

---

## 9. Image hero overlay opacity > 0.65 → image illisible

**Symptôme** : tu mets une image hero magnifique. Tu ajoutes `overlayOpacity: 0.92` pour que le texte blanc soit lisible. Résultat : on ne voit plus du tout l'image, c'est juste un bloc sombre uniforme. L'image perd 100% de son rôle éditorial.

**Cause** : opacity 0.92 = 92% opaque. L'image est visible à 8%, ce qui est en dessous du seuil de perception.

**Fix** :
- Overlay flat : `overlayOpacity: 0.5-0.65` max
- Overlay gradient (recommandé) : color1 `rgba(0,0,0,0.7)` → color2 `rgba(0,0,0,0.20)` 135deg. Texte lisible en haut-gauche, image visible en bas-droite.

**Détection** : screenshot le hero. Si tu identifies l'image background, c'est OK. Si c'est uniformément sombre, l'overlay est trop opaque.

---

## 10. Hero `blockRightPadding > 25%` → texte écrasé

**Symptôme** : la description sous le H1 est compressée sur 1-2 lignes très étroites au lieu de respirer.

**Cause** : `blockRightPadding: 40` avec `blockPaddingUnit: "%"` = padding-right 40% sur un container 1200px = 480px de padding-right. Le texte ne peut tenir que sur 720px. Trop serré pour 19px font-size.

**Fix** : `blockRightPadding: 25` max (300px de padding sur 1200px = 900px utiles, OK pour 19-20px font-size).

**Détection** : compte le nombre de lignes de la desc hero. Si elle wrap toutes les 4-5 mots alors qu'il y a de la place, padding right trop large.

---

## 11. `uagb/icon-list` rendu en row au lieu de vertical

**Symptôme** : tu mets 3 `uagb/icon-list-child` dans un `uagb/icon-list` avec `layout-vertical`. Au rendu, les 3 items sont en **row horizontal** avec underline orange (comme une liste de tags), pas en vertical avec puces.

**Cause** : la classe CSS `uagb-icon-list__layout-vertical` n'est pas suffisante pour forcer le `flex-direction: column` sur tous les thèmes. Astra applique parfois `display: inline` par défaut sur les `<li>`.

**Fix** :
- Solution préférée : **ne pas utiliser `uagb/icon-list`** pour les bullets verticales. Préférer 3 mini-cards dans un container row (cf `patterns/about-story-split.md`).
- Solution alternative : forcer le `display:flex; flex-direction:column` via CSS dans `_uag_custom_page_level_css`.

**Détection** : screenshot la liste. Si elle est horizontale au lieu de verticale, c'est ce piège.

---

## 12. FAQ pleine largeur 1100px → questions illisibles

**Symptôme** : la FAQ accordéon dans un container alignfull s'étend sur toute la largeur du wrapper (1100px+). Chaque question fait une barre horizontale très longue.

**Cause** : `uagb/faq` n'a pas de contrainte de width par défaut. Dans un alignfull (1100-1200px), il prend toute la largeur.

**Fix** : wrapper le `uagb/faq` dans un `uagb/container` enfant avec `widthDesktop:62`, `widthTypeDesktop:"%"`, `margin-left:auto;margin-right:auto`. Largeur readability standard 720-820px.

**Détection** : ouvre la FAQ. Si chaque question fait > 900px, ce piège.

---

## 13. Page template Astra default → double H1

**Symptôme** : tu génères un hero avec H1 « Réussir ton BTS NDRC ». Au rendu, le frontend affiche d'abord « TEST skill v0.9.x » (le titre du post WP) en H1, PUIS le hero H1. Tu as 2 H1 sur la page = SEO cassé.

**Cause** : Astra default page template applique `the_title()` en H1 au-dessus de `the_content()`. Pour les pages avec un hero qui contient déjà son H1, il faut désactiver ce title.

**Fix** : forcer `_wp_page_template = "no-title.php"` OU plus robuste, configurer Astra meta :

```php
update_post_meta($post_id, 'site-content-layout', 'page-builder');
update_post_meta($post_id, 'site-sidebar-layout', 'no-sidebar');
update_post_meta($post_id, 'ast-title-bar-display', 'disabled');
```

**Détection** : view-source sur la page → grep `<h1>`. Si tu as 2+ matches, double H1.

---

## 14. Apache mutu strip Authorization header → REST API 401

**Symptôme** : tu essaies `curl -u user:app-pass /wp-json/wp/v2/users/me?context=edit`. Réponse : `{"code":"rest_not_logged_in"}`. Pourtant le password est correct (vérifié dans WP admin).

**Cause** : Apache sur hébergeurs mutualisés (o2switch, OVH mutu, 1&1, Hostinger) strippe le header `Authorization` avant de le passer à PHP. WordPress ne reçoit jamais l'auth.

**Fix** : ajouter dans `.htaccess` à la racine WP, après `RewriteEngine On` :

```apache
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```

**Détection** : test `curl -u 'user:pass' /wp-json/wp/v2/users/me`. Si 401 alors que les creds sont corrects, c'est ce piège.

---

## 15. Sections root sans alternance bg → mur de blocs

**Symptôme** : tu génères 5 sections root avec toutes `backgroundColor: "#ffffff"`. Au rendu, on ne distingue plus où une section finit et l'autre commence, c'est un long bloc de texte uniforme.

**Cause** : sans variation visuelle entre sections, l'œil n'a pas de repère.

**Fix** : alterner `#ffffff` (white) ↔ `#fafafa` (off-white) entre sections root consécutives. La respiration vient du contraste subtil + du padding interne 96-140px.

**Détection** : `scripts/visual-audit.php` check 10 flag P2 si 2 sections root consécutives ont le même `backgroundColor`.

Voir `references/section-rhythm.md`.

---

## 16. `ratio image` non documenté → portrait recadré landscape

**Symptôme** : pattern `about-story-split` attend une image landscape 1200×400 (ratio 16:5). Tu uploades une photo portrait 600×900. Spectra fait `objectFit:cover` pour la fitter dans 1200×400 → on voit juste un crop gros plan du visage.

**Cause** : ratio mismatch entre image fournie et container attendu, sans indication aux users.

**Fix** : chaque pattern documente son ratio attendu :
- Hero overlay : 16:9 ou 16:7 (1920×1080 ou 1920×850)
- About-story-split : 16:5 (1200×400)
- Testimonial avatar : 1:1 (400×400)
- CTA banner : 16:7 (1920×800)

Voir `references/images-ratios.md`.

**Détection** : screenshot. Si l'image apparaît mal cadrée, c'est ce piège.

---

## 17. `uagb/buttons-child` `paddingBtnLeft/Right` < 30 → bouton pincé

**Symptôme** : bouton CTA visible mais avec un padding horizontal de 16-20px qui le rend étriqué.

**Cause** : default Spectra. Pour un look pro, 36-44px de padding horizontal est nécessaire.

**Fix** : sur chaque `uagb/buttons-child`, ajouter :

```json
{
  "paddingBtnTop": 20,
  "paddingBtnBottom": 20,
  "paddingBtnLeft": 40,
  "paddingBtnRight": 40,
  "fontWeight": "700",
  "fontSize": 16
}
```

**Détection** : screenshot le bouton. Si le label touche les bords, padding insuffisant.

---

---

## 18. Astra `.entry-content { padding-bottom: 4em }` → marge orpheline sous le dernier alignfull

**Symptôme** : ton dernier bloc CTA banner alignfull est suivi d'un espace blanc orphelin de ~80-120px avant le footer. Ça casse le rendu pleine page éditorial.

**Cause** : Astra applique `padding-bottom: 4em` sur `.entry-content` par défaut (≈ 80px à 16px font-size). Pour les pages classiques (article blog), c'est OK. Pour une landing avec dernier bloc alignfull (CTA banner image+overlay), la marge est orpheline.

**Fix** : injecter dans `_uag_custom_page_level_css` :

```css
.entry-content { padding-bottom: 0 !important; }
.entry-content > .alignfull:last-child { margin-bottom: 0 !important; }
```

**Détection** : screenshot la page en bas. Si tu vois 60-100px de blanc entre le dernier bloc et le footer, c'est ce piège.

---

## 19. Eyebrow 13px trop discret → ressemble à un tag de debug

**Symptôme** : tu mets un eyebrow `prefixFontSizeDesktop:13` orange, font-weight 700, letter-spacing 3px, uppercase. Au rendu, il ressemble à un debug CSS tag écrasé sous le H2, pas à un kicker éditorial.

**Cause** : 13px desktop est trop petit pour la sémantique « eyebrow important ». 14-15px avec letter-spacing 3-4px et font-weight 800 (vs 700) donne le bon impact visuel. Le `prefixSpace:14-18` est aussi trop serré (espacement entre eyebrow et heading).

**Fix** : conventions eyebrow optimales :

```json
{
  "prefixFontSizeDesktop": 15,
  "prefixFontWeight": "800",
  "prefixSpace": 24,
  "prefixColor": "{{ACCENT_COLOR}}"
}
```

Avec `style="letter-spacing:4px;text-transform:uppercase"` dans le `<p class="uagb-ifb-title-prefix">`.

Pour vraiment renforcer, ajouter une **barre 32×3px orange** avant le texte (via CSS `::before`) :

```css
.uagb-ifb-title-prefix::before {
  content: "";
  display: inline-block;
  width: 32px;
  height: 3px;
  background-color: {{ACCENT_COLOR}};
  margin-right: 12px;
  vertical-align: middle;
}
```

**Détection** : compare visuellement l'eyebrow à un sub-heading 13px gris classique. Si l'eyebrow ne se distingue pas immédiatement comme un kicker éditorial (impact + impulsion vers le H2), c'est ce piège.

---

## 20. `uag_enable_on_page_css_button` doit être `yes` sinon meta `_uag_custom_page_level_css` ignoré

**Symptôme** : tu déploies markup + CSS overrides via `_uag_custom_page_level_css`, tu force `regen-spectra`. Le `_uag_page_assets['css']` ne contient PAS ton CSS. Tous les overrides (entry-content padding-bottom, watermark, accent line, etc.) sont absents du rendu. **Aucune erreur, aucun warning** — le meta est bien sauvegardé en BDD, il n'est juste jamais lu.

**Cause** : Spectra a un toggle global `uag_enable_on_page_css_button` (default `yes`, mais peut être désactivé par sécurité sur Spectra Pro / sites multi-auteurs / hardening). Code source [`class-uagb-post-assets.php:1432-1440`](https://github.com/brainstormforce/wp-spectra/blob/main/classes/class-uagb-post-assets.php) :

```php
$enable_on_page_css_button = UAGB_Admin_Helper::get_admin_settings_option( 'uag_enable_on_page_css_button', 'yes' );
if ( 'yes' === $enable_on_page_css_button ) {
    $custom_css = get_post_meta( $this->post_id, '_uag_custom_page_level_css', true );
    if ( ! empty( $custom_css ) && ! self::$custom_css_appended ) {
        $this->stylesheet .= UAGB_Admin_Helper::sanitize_inline_css( $custom_css );
    }
}
```

Si `uag_enable_on_page_css_button !== 'yes'`, toute la technique `persistent-css-overrides.md` est inopérante.

**Fix** : avant tout déploiement skill, vérifier (et activer si besoin) :

```php
update_option('uag_enable_on_page_css_button', 'yes');
```

Ou via REST API custom (mu-plugin compagnon) :

```php
register_rest_route('skill-test/v1', '/enable-on-page-css', [
    'methods' => 'POST',
    'permission_callback' => function () { return current_user_can('manage_options'); },
    'callback' => function () {
        update_option('uag_enable_on_page_css_button', 'yes');
        return ['ok' => true];
    },
]);
```

**Détection** : `pre-flight-check.php` flagge en P0 si `get_option('uag_enable_on_page_css_button')` retourne autre chose que `'yes'`. Vérification possible aussi via `wp option get uag_enable_on_page_css_button`.

---

## 21. CSS `content: "\HHHH"` (Unicode escapes) strippés par `sanitize_inline_css()`

**Symptôme** : tu mets dans `_uag_custom_page_level_css` :

```css
.uagb-block-{slug}-testi-1::before {
  content: "\201C"; /* &ldquo; */
  font-size: 240px;
  color: #f1f5f9;
}
```

Au rendu, au lieu du caractère « guillemet anglais ouvrant » U+201C (ldquo `“`), le navigateur affiche le **texte littéral `201C`** sur chaque card. Watermark cassé, rendu catastrophique : code source en clair sur fond clair.

**Cause** : `UAGB_Admin_Helper::sanitize_inline_css()` strippe le backslash `\` dans les valeurs CSS (probablement filtre `wp_kses` ou regex sécurité qui considère `\` comme suspect dans un context CSS inline). Résultat : `content: "\201C"` devient `content: "201C"` après sanitize, qui est interprété comme texte littéral par le navigateur (et non pas comme un escape Unicode CSS).

**Fix** : utiliser le caractère UTF-8 direct dans le file CSS source (et s'assurer que le file est encodé UTF-8 sans BOM) :

```css
.uagb-block-{slug}-testi-1::before {
  content: "“"; /* U+201C littéral, file CSS doit être UTF-8 */
  font-size: 240px;
  color: #f1f5f9;
}
```

Caractères concernés (les plus fréquents en design éditorial) :

| Escape CSS | Caractère UTF-8 | Nom |
|---|---|---|
| `\201C` | `“` | guillemet ouvrant anglais (ldquo) |
| `\201D` | `”` | guillemet fermant anglais (rdquo) |
| `\00AB` | `«` | guillemet ouvrant français (laquo) |
| `\00BB` | `»` | guillemet fermant français (raquo) |
| `\2014` | `—` | em-dash (mdash) |
| `\2013` | `–` | en-dash (ndash) |
| `\00A9` | `©` | copyright |
| `\2026` | `…` | hellip |

Tous **survivent** `sanitize_inline_css()` quand écrits en UTF-8 direct.

**Détection** :
- Pre-flight check : regex `content\s*:\s*['"]\\[0-9a-fA-F]{1,6}` sur le file CSS overrides → P0 BLOCKER
- Visuel : screenshot la section où le watermark/icon::before doit apparaître. Si tu vois le code source brut (`201C`, `2014`, etc.) au lieu du caractère, c'est ce piège.

**Pré-requis encoding** : le file CSS doit être en UTF-8 sans BOM. Si Windows en BOM, le `“` peut être encodé en bytes parasites. Toujours `file --mime-encoding overrides.css` → attendu `utf-8`, pas `utf-8 with BOM` ni `utf-16`.

---

## 22. `uagb/image` conflit `width:N` (px) + `widthDesktop:N` (%)

**Symptôme** : tu génères un `uagb/image` avec :

```json
{
  "width": 1200,
  "widthTablet": 900,
  "widthMobile": 600,
  "widthDesktop": 42,
  "widthTypeDesktop": "%",
  "widthTablet": 100,
  "widthTypeTablet": "%"
}
```

Au roundtrip `parse_blocks() → serialize_blocks()`, on a un `diff_size > 0` et `valid: false` chez `validate-block-markup.php`. La clé `widthTablet` est définie deux fois (ligne 3 = `900` px, ligne 7 = `100` %) → le parser PHP retient la dernière, mais l'ordre n'est pas garanti.

**Cause** : Spectra documente flou les attributs `widthTablet` / `widthMobile` qui peuvent signifier soit la dimension de l'image en px, soit la largeur du container en %, selon la valeur de `widthTypeXxx`. Quand tu mets les deux sets ensemble dans le même JSON, le parser perd la cohérence.

**Fix** : sur chaque `uagb/image`, choisir UNE convention par breakpoint :

**Option A — image fixed-size px** (recommandé pour avatars, icônes) :

```json
{
  "width": 64, "height": 64,
  "widthTablet": 64, "heightTablet": 64,
  "widthMobile": 64, "heightMobile": 64,
  "sizeSlug": "custom",
  "objectFit": "cover"
}
```

**Option B — image % container** (pour images responsive en grid) :

```json
{
  "widthDesktop": 42, "widthTypeDesktop": "%",
  "widthTablet": 100, "widthTypeTablet": "%",
  "widthMobile": 100, "widthTypeMobile": "%"
}
```

**Ne JAMAIS mélanger** : `width:1200` ET `widthDesktop:42` → conflit. Le `width:1200` est l'attribut media library WordPress, qui peut être ignoré par Spectra mais qui peut aussi recevoir un override CSS dynamique parasite.

**Détection** :
- Pre-flight check : flag P1 si une `uagb/image` contient à la fois `"width":<int>` ET `"widthDesktop":<int>` ET `"widthTypeDesktop":"%"`.
- Validation roundtrip : `validate-block-markup.php` doit retourner `valid:true, diff_size:0`. Si `diff_size > 0` sur une `uagb/image`, c'est ce piège.

---

## 23. Spectra v2.19 ne hook PAS `wp_head` → CSS jamais injecté dans le HTML rendu

**Symptôme** : tu as fait tout ce qu'il faut. POST page OK. Meta `_uag_custom_page_level_css` bien sauvegardé. `regen-spectra` retourne `css_len: 255143`. Le post_meta `_uag_page_assets.css` contient bien tout ton CSS (vérifié via PHP one-shot). MAIS le HTML rendu du frontend n'a **AUCUN** `<style id="uagb-style-frontend-{post_id}">` dans le `<head>`. Tous tes overrides sont perdus silencieusement. Aucune erreur, aucun warning.

C'est différent du quirk #6 (qui parlait du même bug en termes vagues). Ce quirk #23 documente le **mécanisme exact** et le **workaround robuste**.

**Cause** : Spectra `class-uagb-post-assets.php` détermine au runtime s'il doit injecter le `<style>` via une combinaison de checks :

1. `is_singular()` retourne true ?
2. Le post_meta `_uag_page_assets` existe ?
3. Le flag `uagb_flag` (dans `_uag_page_assets`) est `true` ?
4. Les hooks `wp_head` sont bien attachés au moment du render frontend ?

Sur certains setups (testé sur **Twenty Twenty-Five FSE block theme** + Spectra 2.19.x, et reproduit aussi sur **Astra 4.13.1** dans le quirk #6), le hook `wp_head` n'est pas appelé pour le bloc Spectra correspondant. Même avec `uagb_flag: true` forcé manuellement.

L'explication probable : le timing d'enregistrement des hooks vs le contexte du render. Dans certains plugins / themes / configurations, le hook Spectra est enregistré **après** que `wp_head` ait fini de fire.

**Fix** : workaround universel via mu-plugin compagnon. Ajouter un hook `wp_head` custom qui lit `_uag_page_assets.css` du post courant et l'injecte directement :

```php
// Dans scripts/mu-plugin-skill-test.php (ou un mu-plugin dédié)
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

Ce workaround est **safe à coexister** avec le hook Spectra natif : si Spectra réussit à hook, on aura 2 `<style>` identiques, ce qui est inoffensif. Si Spectra échoue à hook, on a au moins le nôtre.

**Détection** :
- Côté skill : après POST + regen, fetch l'URL frontend, grep `uagb-style-frontend-{post_id}`. Si retourne 0, quirk #23 actif → installer le workaround mu-plugin.
- Pre-flight check post-render : `scripts/post-render-check.php` peut être appelé après POST + regen pour valider que le CSS est bien dans le HTML.

**Stratégie skill** : depuis v1.0-rc4, le mu-plugin compagnon `scripts/mu-plugin-skill-test.php` inclut **par défaut** ce hook `wp_head` workaround. Plus besoin d'investiguer si Spectra hook ou non — le mu-plugin garantit l'injection.

**Confirmé sur** : loginarmor-dev.local + Twenty Twenty-Five (block theme FSE) + Spectra v2.19 — page 59 du test 02/05/2026.

---

## 24. Block theme FSE (Twenty Twenty-Five, Twenty Twenty-Four, etc.) → double H1 automatique

**Symptôme** : tu génères un hero avec un H1 `uagb/info-box` propre. Au rendu, le HTML contient **2 `<h1>`** :

```html
<h1 class="wp-block-post-title">claude-skill-astra-spectra — knowledge base Spectra v1.0</h1>
<h1 class="uagb-ifb-title">La knowledge base Spectra que personne n'a jamais écrite.</h1>
```

Le premier est généré automatiquement par le block theme à partir du post_title. Le second est ton hero. Résultat : SEO cassé (Google n'aime pas 2 H1, ignorera l'un des deux). UX cassée (deux titres visuels).

**Cause** : les block themes FSE (Full Site Editing) WordPress 6.0+ utilisent des **templates HTML** dans `/wp-content/themes/{theme}/templates/` (e.g. `single.html`, `page.html`). Ces templates contiennent des blocs `wp:post-title` hardcodés :

```html
<!-- wp:post-title {"level":1} /-->
```

Ce bloc rend automatiquement le `<h1 class="wp-block-post-title">` à partir du post_title. **Tu ne peux pas le désactiver via post_meta** comme tu ferais avec Astra (`update_post_meta($pid, 'ast-title-bar-display', 'disabled')` ne fonctionne PAS sur les block themes).

Différent du quirk #13 qui parle du cas Astra (page template + meta). Ici c'est un mécanisme FSE différent.

**Fix** : 3 options selon la robustesse souhaitée.

### Option A — CSS scope (plus simple, robuste, recommandé)

Dans `_uag_custom_page_level_css`, ajouter un sélecteur scopé sur l'ID de la page :

```css
body.page-id-{ID} .wp-block-post-title,
.page-id-{ID} main .wp-block-post-title {
  display: none !important;
}
```

WordPress génère automatiquement la classe `body.page-id-{ID}` sur les pages singulières. Le scope évite d'affecter les autres pages du site.

**Avantage** : pas de modification de template, persistant à travers les éditions Gutenberg, fonctionne sur tout block theme.

### Option B — Custom template via mu-plugin

Filter `template_include` pour utiliser un template custom sans `wp:post-title` :

```php
add_filter('template_include', function ($template) {
    if (is_singular('page') && get_post_meta(get_queried_object_id(), '_skill_no_title', true) === '1') {
        $custom = WP_PLUGIN_DIR . '/skill-test/no-title-template.php';
        if (file_exists($custom)) return $custom;
    }
    return $template;
});
```

Plus invasif, demande un fichier template custom, casse si l'utilisateur change de thème.

### Option C — Hook `block_core_post_title_render` (pas dispo en core, ignore)

Pas de filtre core officiel pour disable wp:post-title à la pièce. Skip cette option.

**Détection** : grep `<h1 class="wp-block-post-title">` dans le HTML rendu. Si présent ET ton hero contient déjà un H1 → quirk #24 actif.

**Pre-flight check** : `pre-flight-check.php` détecte les block themes FSE via `wp_is_block_theme()` (côté détection environnement) et flag P1 si le user a un H1 dans son hero ET un block theme actif → recommande l'option A.

**Stratégie skill** : depuis v1.0-rc4, le skill ajoute **automatiquement** la règle CSS option A dans `_uag_custom_page_level_css` quand il détecte :
- Block theme actif (`wp_is_block_theme() === true`)
- Au moins un H1 dans le markup généré

Cas confirmé sur : loginarmor-dev.local + **Twenty Twenty-Five** (block theme FSE) + Spectra v2.19 — page 59 du test 02/05/2026. La règle CSS scope `body.page-id-59 .wp-block-post-title { display: none !important; }` masque correctement le double H1 sans affecter les autres pages.

---

## Comment cette doc évolue

À chaque nouveau piège détecté lors d'un test sur un nouveau site / nouvelle palette / nouvelle version Spectra, ajouter une entrée numérotée avec les 4 sections **Symptôme / Cause / Fix / Détection**.

La session Claude Code qui hérite de ce skill DOIT lire ce fichier en entier avant de générer du markup `uagb/*`. Pas optionnel.
