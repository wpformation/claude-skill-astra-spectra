---
title: "Le guide complet — Piloter WordPress avec Claude Code (Spectra + Astra)"
subtitle: "30 recettes pour générer, refondre et déployer des pages en moins de 2 minutes"
author: "Fabrice Ducarme — WPFormation.com"
edition: "v1.0 — mai 2026"
pages_target: 32
audience: "développeurs WordPress, agences, formateurs"
license: "Lead magnet WPFormation — diffusion gratuite avec inscription newsletter"
---

# Page de couverture

Logo WPFormation · titre du guide · sous-titre · édition v1.0 mai 2026 · auteur · « Inclus : 30 recettes prêtes à l'emploi · 12 effets WOW · 8 templates de pages »

---

# Sommaire (page 2)

1. Pourquoi ce guide
2. Pré-requis et installation
3. Le routing automatique : comment Claude Code choisit le bon bloc
4. Recette n°1 — Hero pleine page avec gradient mesh
5. Recette n°2 — Glassmorphism cards
6. Recette n°3 — Section avec divider diagonal SVG
7. Recette n°4 — Background vidéo en boucle
8. Recette n°5 — Sticky sidebar layout 70/30
9. Recette n°6 — Pattern repeat + parallax léger
10. Recette n°7 — Conic gradient (rotation 1deg)
11. Recette n°8 — Icon list hoverable
12. Recette n°9 — Stats counters animés au scroll
13. Recette n°10 — FAQ avec schema FAQPage automatique
14. Recette n°11 — Témoignages en grille avec photos
15. Recette n°12 — Pricing 3 tiers avec tier central populaire
16. Templates prêts à l'emploi (page formation, landing SaaS, page agence)
17. Workflow complet — De la page blanche au draft validé
18. Workflow refonte — Moderniser une page existante
19. Le bloc uagb/container expliqué (le bloc-fondation)
20. Pilotage Astra Customizer (palette, typo, header, footer)
21. Anti-patterns et pièges courants
22. Troubleshooting (10 erreurs résolues)
23. Prompts optimisés (15 templates de prompts)
24. Cas d'usage agence
25. FAQ
26. Pour aller plus loin (skill, repo, communauté)
27. À propos de l'auteur

---

# Chapitre 1 — Pourquoi ce guide (pages 3-4)

L'IA pilote des sites WordPress depuis 2024. Mais piloter un site WordPress avec **Claude Code**, ça veut dire quoi concrètement ?

Avec ce guide, tu vas pouvoir :

- Générer une page de vente complète en moins de 2 minutes à partir d'une description en langage naturel
- Refondre une page legacy (paragraphe + heading WordPress 5) en page Spectra moderne
- Déployer un template de page formation en 30 secondes
- Comprendre quel bloc utiliser quand (49 blocs Spectra + 30 blocs core, ça fait du choix)
- Maîtriser les 12 effets WOW que permet `uagb/container`
- Piloter intégralement Astra Customizer (palette, typo, header, footer) sans passer par l'admin

**Public cible** : développeurs WordPress, agences, formateurs. Pas besoin de connaître chaque attribut Spectra, le skill s'occupe du markup. Toi, tu décris.

---

# Chapitre 2 — Pré-requis et installation (pages 5-6)

## Stack minimal

- WordPress 6.6+ (testé jusqu'à 6.9)
- PHP 8.1+
- Spectra (ex Ultimate Addons for Gutenberg) — gratuit, plugin namespace `uagb/*`
- Application Password créé sur `/wp-admin/profile.php`
- Claude Code installé (CLI ou IDE extension)

## Stack premium (optionnel)

- Astra (gratuit) ou Astra Pro (header/footer builder)
- Skill `/impeccable` pour audit visuel
- Skill `/screenshot-loop` pour validation visuelle

## Installation du skill

```bash
cd ~/.claude/skills/
git clone https://github.com/wpformation/claude-skill-astra-spectra astra-spectra
```

Dans Claude Code, lance la détection :

```
/astra-spectra detect
```

Le skill explore automatiquement ton site, vérifie Spectra, Astra, le thème actif, la version WP, et te retourne un verdict GO / DEGRADED / BLOCKED. En cas de blocage (Spectra absent), il te donne le lien pour l'installer.

---

# Chapitre 3 — Le routing automatique (pages 7-9)

Le cœur du skill, c'est sa **table de routing** intent → bloc. 45 entrées qui couvrent 95 % des cas d'usage éditoriaux.

## Logique de décision

```
1. Parser la demande utilisateur
2. Mapper vers la table de décision
3. Privilégier Spectra QUAND :
   - Le bloc apporte un gain visuel/UX significatif
   - Le bloc embarque du schema SEO (FAQ, how-to, review)
   - Pas d'équivalent core (countdown, modal, tabs, slider)
4. Privilégier core QUAND :
   - Bloc atomique simple (paragraph, heading H3+, list, image isolée)
   - Embed natif (YouTube, Twitter)
   - Maintien de la compatibilité
5. Toujours wrapper les compositions complexes dans uagb/container
```

## Extrait de la table

| Intention | Bloc choisi | Raison |
|-----------|-------------|--------|
| Hero section avec CTA | `uagb/container` + heading + buttons | wow, padding fluide, gradient |
| Section CTA pleine largeur | `uagb/call-to-action` | bloc dédié, schema attendu |
| 3 features côte à côte | `uagb/container` (3 cols) + 3× `uagb/info-box` | UX premium, hover states |
| FAQ accordéon | `uagb/faq` | schema FAQPage automatique |
| Paragraphe texte courant | `core/paragraph` | atomique, simple, compatible |
| Titre H3-H6 simple | `core/heading` | atomique, sémantique |
| Embed YouTube | `core/embed` (YouTube) | natif WP, oEmbed |
| Compteur animé | `uagb/counter` | scroll-triggered animation |
| Compte à rebours | `uagb/countdown` | pas d'équivalent core |
| Tabs | `uagb/tabs` | pas d'équivalent core |

[Tableau complet : 45 entrées dans `references/intent-to-block-routing.md`]

---

# Chapitres 4 à 15 — Les 12 recettes WOW

Chaque recette occupe 1.5 page : description, captures avant/après, code markup complet, prompt Claude Code à utiliser, anti-patterns spécifiques.

[Source : `modules/spectra/container-wow-recipes.md`]

---

# Chapitre 16 — Templates prêts à l'emploi (pages 22-24)

3 templates couvrent 80 % des besoins d'agence et de formateur :

## Template 1 — page-formation

9 sections : hero impact + bénéfices + programme détaillé + formateur + témoignages + tarifs + FAQ + CTA inscription + section OPCO.

Prompt :
```
/astra-spectra deploy template=page-formation \
  titre="Formation WordPress + IA" \
  duree="35h" \
  prix="1900 EUR HT" \
  opco=true
```

## Template 2 — landing-saas

9 sections : hero produit + problème/solution + 3 features clés + démo screenshots + témoignages + pricing 3 tiers + FAQ + CTA inscription + footer-mini.

## Template 3 — page-agence

10 sections : hero impact + services en grille + processus + équipe + projets case studies + témoignages + tech stack + FAQ + formulaire contact + footer.

---

# Chapitre 17 — Workflow complet (pages 25-26)

8 étapes :

1. **Détection** — Le skill vérifie l'environnement (Spectra OK ? Astra ? thème ?)
2. **Parsing** — Conversion de ton brief en intent map
3. **Patterns** — Sélection des patterns adéquats dans la lib
4. **Markup** — Assemblage du markup Gutenberg avec block_id uniques + tokens Astra
5. **Validation** — Roundtrip parse/serialize avant POST (anti-crash)
6. **POST** — Création du draft via REST API
7. **Validation visuelle** — Screenshot + audit /impeccable + retries (max 3)
8. **Récap** — URL draft + métriques + audit log

Si checkpoint visuel KO après 3 retries : rapport détaillé + recommandation manuelle.

---

# Chapitre 18 — Workflow refonte (pages 27-28)

8 étapes spécifiques :

1. **Détection**
2. **Snapshot** — `snapshot-page.php` dump JSON de la page existante
3. **Analyse** — Le skill identifie les sections et leur intent
4. **Mapping** — Chaque section legacy mappée vers un pattern Spectra
5. **Reconstruction** — Markup hybride core + Spectra qui préserve le contenu original
6. **POST clone** — Le draft est créé en clone (`/page-refonte/`), jamais sur l'URL prod
7. **Diff** — Comparaison contenu original vs reconstruction (tous les paragraphes préservés ?)
8. **Migration optionnelle** — Quand tu valides, le skill remplace le contenu prod et garde l'ancien en révision

---

# Chapitre 19 — Le bloc uagb/container (pages 29-30)

Le bloc-fondation. Tout effet WOW passe par lui.

**12 propriétés stratégiques** :

1. `backgroundType` (none | color | gradient | image | video)
2. `gradientType` (linear | radial | conic)
3. `gradientLocation1/2`, `gradientAngle`
4. `topDividerStyle` + `bottomDividerStyle` (tilt, wave, mountain, triangle, drops, zigzag, etc.)
5. `boxShadowOptions` (multi-layered shadows)
6. `overlayType` (color | image | gradient)
7. `parallaxRatio` (0-100, ratio de scroll lock)
8. `glassmorphismBlur` (backdrop-filter: blur)
9. `topPaddingTablet/Mobile` + `topPaddingDesktop` (responsive précis)
10. `equalHeight` (cards parfaitement alignées en hauteur)
11. `enableContentBackground` (séparation visuelle inner)
12. `customCSS` (échappatoire pour effets exotiques)

**Anti-pattern principal** : ne JAMAIS utiliser `core/group`, `core/cover` ou `core/columns` quand tu veux un effet WOW. Ces blocs n'ont pas le contrôle granulaire d'`uagb/container` et tu vas finir avec du custom CSS partout.

---

# Chapitre 20 — Pilotage Astra (pages 31-33)

## Update palette

```bash
php scripts/astra-customizer.php apply patches/palette-orange.json
```

`patches/palette-orange.json` :
```json
{ "palette": { "currentPalette": "default", "colors": ["#FF8C00", "#3a3a3a", "#0a0a0a", "#0a0a0a", "#FF8C00", "#ffffff", "#f5f5f5", "#fafafa", "#e7e7e7"] } }
```

Résultat : 9 variables CSS `--ast-global-color-0..8` régénérées en moins de 5 secondes. Cache Astra invalidé automatiquement.

## Update typographie

```json
{ "typography": { "body_family": "Inter", "headings_family": "Inter Tight", "headings_weight": 700, "h1_size": { "desktop": 48, "tablet": 38, "mobile": 32 } } }
```

## Configurer le header

```json
{
  "header": {
    "primary": { "left": ["logo"], "center": ["menu-1"], "right": ["button-1"] },
    "main_stick": true,
    "button1": { "text": "Demander un devis", "url": "/devis/", "style": "fill" }
  }
}
```

---

# Chapitre 21 — Anti-patterns (pages 34-35)

15 erreurs à ne JAMAIS commettre :

1. ❌ Hex hardcodé dans un attribut couleur — toujours `var(--ast-global-color-X)`
2. ❌ Oublier `block_id` sur un bloc Spectra — Gutenberg recompute et casse le rendu
3. ❌ Réutiliser le même `block_id` entre 2 blocs — duplicate fail
4. ❌ `update_option('astra-settings', $patch)` — écrase les centaines de keys de l'option, désastre
5. ❌ Modifier le header builder sans Astra Pro actif — fallback widgetisé
6. ❌ POST sur l'URL prod sans clone — perte du contenu legacy
7. ❌ Heading H1 multiple sur une page — SEO fail
8. ❌ Image sans alt — accessibilité fail
9. ❌ Container sans responsive padding — UX mobile fail
10. ❌ Effet WOW avec `core/group` — pas de gradient ni divider
11. ❌ Embed YouTube avec `uagb/lottie` — mauvais bloc
12. ❌ Stocker la palette dans `astra-color-palettes` au lieu de `astra-settings.global-color-palette`
13. ❌ Oublier d'invalider le cache Astra après update palette — CSS périmé 12h
14. ❌ Faire confiance au markup d'un screenshot LLM — toujours valider roundtrip
15. ❌ Skip la phase de validation visuelle — bug visible en prod

---

# Chapitre 22 — Troubleshooting (page 36)

10 erreurs résolues :

| Symptôme | Cause | Fix |
|----------|-------|-----|
| Bloc Spectra orange/cassé en éditeur | block_id manquant | Régénérer via auto-fix-markup.php |
| CSS variables introuvables | Cache Astra | `astra_clear_all_assets_cache()` |
| Update palette sans effet | Mauvaise option | `astra-settings.global-color-palette.palette` |
| Roundtrip diff > 0 mais markup OK | Whitespace cosmétique | Validator distingue maintenant |
| Page vide après POST | REST API 401 | Vérifier Application Password |
| Header builder ignoré | Astra Pro inactif | Activer ou utiliser layout legacy |
| Contraste faible | Token mal choisi | Auto-fix wrappe avec `--ast-global-color-7` |
| Compteur ne s'anime pas | JS Spectra non chargé | Vérifier `wp_enqueue_script('uagb-frontend')` |
| Schema FAQ absent | `enableSchemaSupport: false` | Forcer à `true` |
| Modal qui s'ouvre tout le temps | `triggerType` mal réglé | Choisir `button` au lieu de `auto` |

---

# Chapitre 23 — Prompts optimisés (pages 37-38)

15 templates à copier-coller dans Claude Code :

```
1. /astra-spectra build "page de vente formation [SUJET], cible [PERSONA], prix [PRIX], OPCO oui/non"

2. /astra-spectra build "landing SaaS [PRODUIT], 3 problèmes/3 solutions, pricing 3 tiers, témoignages clients, CTA inscription"

3. /astra-spectra build "site agence digitale [NOM], services [LISTE], équipe [N] personnes, 5 case studies"

4. /astra-spectra build "article 2000 mots sur [SUJET], TOC + comparatif + vidéo YouTube + FAQ"

5. /astra-spectra refonte /a-propos/ en mode Spectra moderne, conserver tout le contenu

6. /astra-spectra deploy template=page-formation titre="..." prix=... duree=... opco=true

7. /astra-spectra apply palette colors='["#FF8C00", "#3a3a3a", ...]'

8. /astra-spectra apply typography body_family="Inter" headings_family="Inter Tight"

9. /astra-spectra audit /ma-page/ et corrige si besoin

10. /astra-spectra hero "[ACCROCHE]" CTA="[TEXTE]" url="[URL]" effet=gradient-mesh

[...5 prompts supplémentaires]
```

---

# Chapitre 24 — Cas d'usage agence (page 39)

Comment une agence digitale peut intégrer ce skill dans son workflow client :

- **Pre-vente** : générer une maquette HTML draft en 5 minutes pour un brief client → impression immédiate
- **Refonte rapide** : prendre un site WordPress legacy → snapshot → reconstruction Spectra → review client → push
- **Templates white-label** : 8 templates prêts, juste les couleurs et le contenu à adapter
- **Onboarding stagiaire** : un stagiaire avec 0 expérience Spectra produit du markup propre dès jour 1
- **Maintenance évolutive** : refresh annuel du design d'un site avec préservation du contenu et des metadata SEO

ROI estimé : passage d'une page coûtant 8-15h dev à une page produite en 30 minutes (génération + ajustements).

---

# Chapitre 25 — FAQ (page 40)

10 questions courantes :

1. Le skill fonctionne-t-il sans Astra ? → Oui, n'importe quel thème WP, mais pas de pilotage Customizer auto.
2. Le skill fonctionne-t-il sans Spectra ? → Non, Spectra est obligatoire. Mais 700K+ sites l'utilisent déjà.
3. Compatibilité multilingue ? → Oui, le markup est neutre. WPML/Polylang fonctionnent.
4. Mes contenus sont-ils transformés ? → Non, le skill préserve le texte. Il restructure seulement.
5. Performance ? → Le markup Spectra est léger. Pas de surcharge vs custom HTML.
6. Le skill modifie-t-il mes plugins existants ? → Non, il ne touche qu'à `astra-settings` et au contenu posts/pages.
7. Lien avec Astra MCP officiel ? → Le skill l'utilise si présent, sinon REST API + update_option suffit.
8. Compatible WP multisite ? → Oui, mais il faut spécifier `--blog-id` dans les commandes WP-CLI.
9. Coût ? → Skill open source MIT, gratuit. Coût Claude Code selon ton plan Anthropic.
10. Évolutions à venir ? → Module Beaver Builder, module GeneratePress Premium, intégration ACF Pro pour les templates dynamiques.

---

# Chapitre 26 — Pour aller plus loin (page 41)

- Repo GitHub : https://github.com/wpformation/claude-skill-astra-spectra
- Article WPFormation : https://wpformation.com/skill-astra-spectra/ [à publier]
- Communauté Discord : https://discord.gg/wpformation [à créer si besoin]
- Documentation Spectra : https://wpspectra.com/docs/
- Documentation Astra : https://wpastra.com/docs/

---

# Chapitre 27 — À propos de l'auteur (page 42)

Fabrice Ducarme — fondateur de WPFormation.com, formateur WordPress depuis 2012.

- Co-créateur de WPS Hide Login (2M+ téléchargements WP.org)
- Co-créateur de WPS Limit Login (100K+ téléchargements)
- 8 plugins publiés sur le repo officiel WordPress.org
- Speaker WordCamp Paris, Lyon, Marseille
- Enseignant Pôle Sup (Nîmes)
- 14 ans d'expertise WordPress, Qualiopi

WPFormation forme depuis 2012 : formations sur-mesure (1 900 EUR HT, 20-60h, OPCO), audit & expertise (149 EUR/h).

---

# Page de fin (page 43-44)

CTA :
- « Réserver un audit gratuit » → /audit-gratuit/
- « S'inscrire à la formation WordPress + IA » → /formation-wordpress/
- « Suivre la veille WPFormation » (newsletter) → encart inscription

Mentions légales : © 2026 WPFormation — Fabrice Ducarme — EI · SIRET 478 478 332 00032 · Qualiopi · Diffusion gratuite avec inscription newsletter, redistribution interdite sans accord.

---

## Spécifications de production PDF

- **Format** : A4 portrait, 210 × 297 mm
- **Marge** : 18 mm × 4 côtés
- **Police titre** : Inter Tight 700, taille 32-48
- **Police corps** : Instrument Sans 400, taille 11/16
- **Couleurs** : palette WPFormation orange #FF8C00 + noir + blanc
- **Captures** : 12 captures Playground d'effets WOW + 8 captures de templates
- **Code blocks** : police mono Inter Display, fond `#1a1a1a`, texte `#f5f5f5`
- **Pages cibles** : 32-44 selon densité
- **Outil de production** : Markdown → Pandoc → LaTeX → PDF, ou Markdown → Typst → PDF
- **Watermark footer** : page X/Y · WPFormation.com · Diffusion gratuite

## Workflow de production (à exécuter)

```bash
# Itération 7 sortira ce PDF dès qu'on a les captures
pandoc lead-magnet/pdf-source.md \
  --pdf-engine=xelatex \
  --template=lead-magnet/template.tex \
  -V geometry:margin=18mm \
  -V fontsize=11pt \
  -V mainfont="Instrument Sans" \
  -V sansfont="Inter Tight" \
  -V monofont="Inter Display" \
  -V documentclass=article \
  --toc --toc-depth=2 \
  -o lead-magnet/guide-claude-code-wordpress-spectra-v1.pdf
```

## Captures à produire (à faire dans une session de production dédiée)

12 captures effets WOW + 8 captures templates + 5 captures avant/après refonte = **25 visuels** au total.

Chaque capture : 1600 × 900 px, format PNG, optimisé < 200 KB.
