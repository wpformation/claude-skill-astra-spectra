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
- Itération 4, après l'avis de Fabrice : fond des 2 sections sombres en `#222222` au lieu de `#0F172A`.

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

## L'avis de Fabrice du 31/10/2026

Sur l'itération 3, vue dans l'éditeur et en aperçu : « Le rendu est vraiment très bien, en dehors de l'espèce de bleu marine dans la zone HERO. Mais je trouve ça absolument magnifique. »

L'itération 4 corrige ce seul reproche (piège 3 ci-dessous) : `d18-v4-*.png`, balisage `d18-v4-markup.html`. Les mesures de l'itération 3 ne changent pas (0 débordement, 1 H1, mêmes tailles). Elle n'a pas encore été revue par Fabrice.

## 3 pièges absents du skill

1. **Quirk #13 incomplet sous Astra 4.14.** Les trois métas recommandées (`ast-title-bar-display`, `site-content-layout`, `site-sidebar-layout`) laissent le titre de la page en `<h1 class="entry-title">` : double H1. La méta qui masque le titre est `site-post-title = disabled`, celle des 8 pages publiées du site. Avec elle : 1 seul H1.
2. **Boutons ignorés quand le site hérite du thème.** Avec `uag_btn_inherit_from_theme = enabled`, l'attribut `inheritFromTheme` vaut `true` par défaut : Spectra n'émet ni couleur, ni bordure, ni arrondi pour `uagb/buttons-child`, et les deux boutons sortent au style du thème (capture v1). Correctif : `"inheritFromTheme": false` dans chaque bouton. Même ainsi, `paddingBtnTop/Left/...` des exemples ne produit aucun CSS en Spectra 2.20.5 : le padding 20/40 n'est obtenu que par le CSS de page.
3. **Le « dark » de la palette n'est pas un fond de section.** Sur palette_3, `#0F172A` (color-2) est la couleur des titres. Les surfaces sombres du site, la barre de menu et le pied de page, sont en `#222222` (color-8). Posé en fond de section juste sous la barre de menu, `#0F172A` paraît bleu marine : c'est le seul reproche de Fabrice. Règle proposée : prendre le fond d'une section sombre dans la couleur calculée de l'en-tête ou du pied de page du site (une mesure `getComputedStyle` suffit), jamais dans le slot du texte des titres. Avec un fond identique au pied de page, le bandeau des chiffres ne s'en distingue plus que par le filet du pied.

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
