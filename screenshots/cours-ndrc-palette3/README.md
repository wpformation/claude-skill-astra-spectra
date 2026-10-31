# Test sur site client réel : cours-ndrc.fr, palette_3 (31/10/2026)

Le 30 octobre, Fabrice a autorisé ce test en décision D18 : une page **brouillon**, jamais publiée. Il ne lance pas le reste de la feuille de route v1.0.

## Environnement

| Élément | Version |
|---|---|
| WordPress | 7.1.4 |
| Astra | 4.14.2 (palette active `palette_3`) |
| Astra Pro (astra-addon) | 4.13.12 |
| Spectra | 2.20.5, Spectra Pro 1.3.2 |
| Cache | LiteSpeed Cache 7.9.2 (sans effet sur un brouillon) |
| Réglages Spectra | `uag_enable_on_page_css_button = yes`, `uag_btn_inherit_from_theme = enabled` |
| Skill | 1.0-rc6 installé, commit 5fa3305 |

## Déroulé du test

- Brief : page de présentation du Bachelor RMDP, 3 sections (règle 2 du skill) : hero sombre sans image, trois leviers numérotés (`features-numbered`), quatre chiffres (`stats-bar-editorial`).
- Brouillon #2889 « TEST astra-spectra 1.0-rc6, brouillon à ne pas publier », posé par WP-CLI (`wp_insert_post` en `draft`, avec `wp_slash`), 28 blocs, block_id préfixés `t18-`.
- Contrôle d'aller-retour `parse_blocks` puis `serialize_blocks` sur le serveur avant chaque écriture : identique.
- Rendu dans le thème réel par un script de rendu de brouillon (connecté, sans publication), puis Playwright à 1440, 820 et 390 px.
- Trois itérations, puis arrêt (plafond du `visual-validation-loop`).

## Résultat de l'itération 3

| Mesure | Bureau 1440 | Tablette 820 | Mobile 390 |
|---|---|---|---|
| Débordement horizontal | 0 px | 0 px | 0 px |
| Nombre de H1 | 1 | 1 | 1 |
| H1 hero | 76 px | 54 px | 38 px |
| Eyebrow | 14 px | 14 px | 13 px |
| H2 de section | 44 px | 34 px | 28 px |
| Chiffres | 56 px | 48 px | 40 px |
| Cartes | 3 colonnes de 425 px | empilées | empilées |
| Chiffres clés | 4 colonnes | grille 2 x 2 | empilés |
| Erreurs JavaScript | 0 | 0 | 0 |

Toutes les valeurs typographiques sont dans les plages de `references/design-baselines.md`. Le CSS de page sort bien dans `<style id="uagb-style-frontend-2889">` : le piège #23 ne se produit pas ici, sans le mu-plugin compagnon. Aucune couleur n'emploie `color-4`, `color-6`, `color-7` ni `color-8` : le piège du noir `#141006` (quirk #7) n'a pas pu se produire.

Captures : `d18-v3-*-page-entiere.png` (trois largeurs), `d18-v3-bureau-t18-*.png` (sections), `d18-v1-bureau-page-entiere.png` (avant corrections). Sources : `d18-v3-markup.html`, `d18-v3-page-level.css`.

Le rendu n'a pas encore été jugé par Fabrice : il reste « vérifié par mesures et captures », pas validé.

## 2 pièges absents du skill

1. **Quirk #13 incomplet sous Astra 4.14.** Les trois métas recommandées (`ast-title-bar-display`, `site-content-layout`, `site-sidebar-layout`) laissent le titre de la page en `<h1 class="entry-title">` : double H1. La méta qui masque le titre est `site-post-title = disabled`, celle des 8 pages publiées du site. Avec elle : 1 seul H1.
2. **Boutons ignorés quand le site hérite du thème.** Avec `uag_btn_inherit_from_theme = enabled`, l'attribut `inheritFromTheme` vaut `true` par défaut : Spectra n'émet ni couleur, ni bordure, ni arrondi pour `uagb/buttons-child`, et les deux boutons sortent au style du thème (capture v1). Correctif : `"inheritFromTheme": false` dans chaque bouton. Même ainsi, `paddingBtnTop/Left/...` des exemples ne produit aucun CSS en Spectra 2.20.5 : le padding 20/40 n'est obtenu que par le CSS de page.

Autres constats :

- L'attribut `prefixFontSizeDesktop` de l'info-box n'est pas émis : eyebrow à 17 px (taille du thème), hors de la plage 13-16 px. Corrigé par le CSS de page.
- Une carte en colonne à hauteur égale centre son contenu (`justify-content: center` par défaut) : les numéros « 01 », « 02 », « 03 » ne sont pas alignés. Correctif : `justifyContentDesktop: "flex-start"` sur chaque carte.
- `serialize_blocks` écrit les attributs JSON en UTF-8 brut : un balisage généré avec `î` ne passe pas l'aller-retour (écart de 4 octets par caractère accentué). Générer les attributs avec `ensure_ascii=False` et garder les entités HTML pour le texte.
- Spectra retire le saut de ligne final de `_uag_custom_page_level_css` : comparer après `trim`.

## Limites du test

- Une seule palette (`palette_3`), un seul site, 3 sections sur les 9 patterns.
- Pas de passage par l'éditeur Gutenberg : la validation des blocs côté JavaScript (bloc invalide, récupération) n'a pas été vue.
- Pose par WP-CLI, pas par l'API REST ni par `scripts/post-page-via-rest.php` du skill.
- Pas d'image (règle 5), donc ni `hero-image-overlay` ni ratio contrôlés.
- Le brouillon reste sur cours-ndrc.fr en `draft` (404 pour un visiteur, absent du plan de site), pour une relecture par Fabrice dans l'éditeur.
