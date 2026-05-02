# claude-skill-astra-spectra

> **Le premier skill Claude Code qui transforme un brief en langage naturel en page WordPress complète. Spectra (48 blocs Gutenberg) + Astra + Gutenberg core. En moins de 2 minutes. Open source MIT.**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![WordPress 6.0+](https://img.shields.io/badge/WordPress-6.0+-21759b.svg)](https://wordpress.org/)
[![Spectra Required](https://img.shields.io/badge/Spectra-Required-FF6B00.svg)](https://wpspectra.com/)
[![Astra Optional](https://img.shields.io/badge/Astra-Optional-blue.svg)](https://wpastra.com/)
[![Status: Beta](https://img.shields.io/badge/Status-v0.8.1--beta-orange.svg)](CHANGELOG.md)

---

## L'intuition

Tu connais ce moment où ton client te demande une nouvelle landing page « comme l'autre fois mais avec un hero plus impactant, 3 features, une grille de témoignages et un pricing 3 tiers » ?

Tu sais d'avance ce qui t'attend : 4 heures de Gutenberg, 200 clics, ajuster les paddings sur 3 breakpoints, replacer les `block_id`, déboguer pourquoi telle classe CSS ne s'applique pas.

Avec ce skill, tu dictes ce paragraphe à Claude Code. **90 secondes plus tard, la page draft est dans Gutenberg, validée, prête à publier.** Tu ouvres juste l'URL d'édition et tu vérifies.

C'est ça la promesse. Et ça marche déjà.

---

## Le contexte du marché (mai 2026)

| Plugin / thème | Intégration AI |
|----------------|----------------|
| **Spectra** (~700K sites, 48 blocs Gutenberg avancés) | **Rien.** Aucun MCP officiel, aucun skill, aucune intégration AI sérieuse. C'est l'angle mort du marché. |
| Astra (~1.6M sites) | MCP officiel Brainstorm Force, mais limité au Customizer |
| Gutenberg core | REST API standard, sans intent-routing curé |
| Elementor | Add-ons AI tiers, payants, fermés |
| Divi | AI built-in dans Divi 5, fermé |

Spectra est utilisé par 700 000 sites WordPress et personne ne l'a encore branché à un agent IA digne de ce nom. Ce skill comble ce gap.

---

## Démo en 30 secondes

```bash
> /astra-spectra
```

```
> Crée-moi une landing page pour ma formation Claude Code avec :
> - Hero impactant avec gradient mesh + 2 CTAs
> - 3 features (rapidité, qualité, support)
> - Pricing one-shot 297 EUR avec garantie 30 jours
> - Témoignages 3 clients
> - FAQ 5 questions
> - CTA final inscription
```

```
✅ Page créée en 1 min 47s

📊 Composition
   • 23 blocs (15 uagb + 8 core)
   • 9 variables Astra utilisées (--ast-global-color-0..8)
   • Schema FAQPage activé
   • 0 hex hardcodé
   • 0 warning Gutenberg

🔗 URL d'édition  : https://monsite.com/wp-admin/post.php?post=42&action=edit
🔗 Aperçu draft   : https://monsite.com/?page_id=42&preview=true
```

Tu ouvres l'URL d'édition. Tu vérifies. Tu ajustes les textes si besoin (les blocs sont 100 % éditables, c'est du Gutenberg standard, pas du custom HTML). Tu publies.

---

## 3 killer features

### 1. Génération depuis un brief

Tu décris ce que tu veux. Le skill route chaque intention vers le bon bloc (Spectra prioritaire pour les compositions visuelles, core pour les blocs atomiques), assemble un markup Gutenberg propre, valide le roundtrip parse/serialize, et POST sur ton site en draft.

### 2. Refonte intelligente

Tu donnes une URL existante. Le skill snapshot le contenu via REST API, analyse la structure, mappe chaque section vers un pattern Spectra moderne, reconstruit en respectant **chaque mot du contenu original**. Le draft est créé en clone (jamais sur l'URL prod sans validation).

### 3. Templates clic-bouton

3 templates v0.8 prêts à déployer (page-formation, landing-saas, page-agence). Tu fournis les inputs (titre, prix, contenu), le skill adapte. 5 templates supplémentaires en route pour la v1.0.

---

## Ce qui rend ce skill différent

### Le bloc `uagb/container` exploité au maximum

Toutes les compositions du skill sont wrappées dans `uagb/container` (jamais `core/group`, `core/cover`, `core/columns`). C'est le seul bloc qui te donne :

- Backgrounds avancés (gradient mesh, vidéo, parallax, glassmorphism)
- Dividers haut/bas (curve, wave, tilt, mountain)
- Box shadow par état (rest + hover)
- Padding/margin **par breakpoint** (desktop / tablet / mobile indépendants)
- Min-height en vh, %, em
- Animations au scroll (fade, slide, zoom)

→ **12 recettes WOW production-ready** dans [`modules/spectra/container-wow-recipes.md`](modules/spectra/container-wow-recipes.md)

### Cohérence design system automatique

Tous les patterns Spectra utilisent `var(--ast-global-color-0..8)` au lieu de hex hardcodés. Résultat : un changement de palette Astra propage instantanément sur **toutes les pages générées** sans intervention. Validé au POC : 199 occurrences héritées, 0 hex hardcoded sur 18 blocs assemblés.

### Validateur roundtrip pré-POST

Le skill ne POST jamais un markup sans avoir vérifié que `serialize_blocks(parse_blocks($content))` égale le source (modulo normalisation cosmétique des `--`). Pas de bloc cassé en prod.

### Auto-fix intelligent

Si un audit visuel détecte un problème (block_id dupliqué, hex hardcodé, H1 multiple), le skill applique la correction automatiquement et re-POST. Maximum 3 retries.

---

## Quickstart en 3 commandes

```bash
# 1. Installer le skill
cd ~/.claude/skills/
git clone https://github.com/wpformation/claude-skill-astra-spectra astra-spectra

# 2. Sur ton WP cible : activer Spectra + créer un Application Password
wp plugin install ultimate-addons-for-gutenberg --activate
# Puis WP admin > Profil > Application Passwords > new "claude-skill-astra-spectra"

# 3. Dans Claude Code
> /astra-spectra
> Détecte mon site https://monsite.com avec ce password : abcd 1234 efgh 5678
```

Détail complet : [INSTALL.md](INSTALL.md)

---

## Sous le capot

```
claude-skill-astra-spectra/
├── SKILL.md                              # routing principal
├── INSTALL.md / LICENSE / CHANGELOG.md
├── modules/
│   ├── spectra/container-wow-recipes.md  # ⭐ 12 recettes WOW
│   └── astra/customizer-map.md           # cartographie astra-settings
├── references/
│   ├── intent-to-block-routing.md        # ⭐ table de décision 45 entrées
│   ├── spectra-blocks-catalog.md         # 48 blocs uagb/* documentés
│   ├── block-markup-syntax.md            # syntaxe + 8 règles strictes
│   └── design-system-tokens.md           # palette Astra ↔ blocs
├── patterns/                             # 9 patterns hybrides v0.8
├── templates/                            # 3 templates v0.8 (5 prévus v1.0)
├── workflows/                            # 3 killer features + visual-loop
├── scripts/
│   ├── detect-environment.php            # profil site (Spectra/Astra/palette)
│   ├── post-page-via-rest.php            # POST automatique vers WP
│   ├── validate-block-markup.php         # roundtrip parse/serialize
│   ├── visual-audit.php                  # 12 checks intégrés P0-P3
│   ├── auto-fix-markup.php               # corrections auto
│   ├── astra-customizer.php              # export/apply astra-settings
│   ├── apply-design-tokens.php           # injection palette ou fallback CSS
│   └── snapshot-page.php                 # dump page existante (refonte)
├── evals/                                # 10 évals canoniques + runner
├── lead-magnet/                          # source PDF 32 pages
└── vercel-integration/                   # route API + page front lead magnet
```

---

## Status v0.8.1-beta

✅ **Ce qui marche déjà bien**
- POC validé sur WordPress Playground (3/3 tests passés en ~1h)
- Test live cours-ndrc.fr (Astra Pro 4.13 + Spectra 2.19) avec 17 blocs valides
- Architecture stable : modules / references / patterns / templates / workflows / scripts
- Validator avec normalisation `--` (corrigé 02/05/2026 PM)
- Pattern info-box compatible rendu Spectra (corrigé 02/05/2026 PM)

🚧 **En route pour v1.0**
- 6 patterns supplémentaires (tabs, slider, timeline, how-to, review, countdown)
- 5 templates supplémentaires (blog-editorial, e-commerce-produit, page-tarifs, page-contact, page-a-propos)
- Liste exhaustive des noms courts d'icônes Spectra
- Documentation curée des 30+ blocs `core/*`
- PDF lead magnet compilé + 25 captures Playground

→ Tu trouveras un test concret post-correctifs et une grille de retour dans [CHANGELOG.md](CHANGELOG.md)

---

## Le PDF guide premium (gratuit, contre email)

Un guide PDF de **32 pages** avec :

- 12 recettes WOW détaillées avec captures avant/après
- 8 templates de pages complètes
- Pilotage Astra Customizer (palette, typo, header, footer)
- 15 prompts optimisés à copier-coller
- 15 anti-patterns à éviter
- 10 erreurs résolues (troubleshooting)
- Cas d'usage agence (ROI estimé)
- À propos de l'auteur (14 ans WordPress)

👉 **[Télécharger gratuitement sur wpformation.com/skill-astra-spectra/](https://wpformation.com/skill-astra-spectra/)** *(page en cours de déploiement, ~mi-mai 2026)*

---

## Tu veux aller au-delà du skill ?

### 🎓 Formation WordPress + IA avec Claude Code

Si ce skill te parle et que tu veux **industrialiser ton WordPress avec Claude Code** au-delà de la génération de pages — créer ton propre fichier CLAUDE.md, tes propres skills, tes propres routines cloud, ton stack MCP, automatiser tout ce qui est répétitif sur ton site — j'enseigne ça en formation sur-mesure.

- 20 à 60 heures, en visio ou en présentiel
- Organisme certifié Qualiopi, financement OPCO possible
- Pour développeurs, agences, freelances, formateurs

👉 **[Découvrir la formation WordPress + IA](https://wpformation.com/formation-wordpress/)**

### 📚 Article complet : Piloter WordPress avec Claude Code

Le contexte, le stack, les 17 skills WPF en prod, le fichier CLAUDE.md commenté ligne par ligne, les MCP utilisés au quotidien, les routines cloud Anthropic.

👉 **[Lire « Piloter WordPress avec Claude Code »](https://wpformation.com/claude-code-wordpress/)**

---

## Mes plugins WordPress

Si ce skill t'a aidé, jette un œil à mes autres plugins. Ils servent tous une obsession : **laisser à WordPress ce que WordPress fait bien, et laisser au reste ce que le reste fait mieux.**

### 🛡️ Login Armor — la suite sécurité minimale qui suffit

Cache l'URL de login, limite les tentatives, ajoute le 2FA optionnel, log les accès suspects. Pas une usine à gaz comme Wordfence : **le minimum vital, configuré en 2 minutes.**

👉 **[wpformation.com/login-armor/](https://wpformation.com/login-armor/)** · *Gratuit · ~3K installations actives*

### 🍽️ OGEEAT — plugin restauration + traiteur

Menu, prise de commande en ligne, gestion des allergènes, paiement Stripe, étiquette imprimable. Pensé par un développeur qui a travaillé en cuisine pendant 5 ans. **Gratuit, sans abonnement, pas de SaaS qui meurt en 2027.**

👉 **[wpformation.com/ogeeat/](https://wpformation.com/ogeeat/)** · *Gratuit · publié sur WordPress.org*

### Plus largement (8 plugins, 2.1M+ téléchargements cumulés)

- [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/) — 2M+ installations actives
- [WPS Limit Login](https://wordpress.org/plugins/wps-limit-login/) — 100K+
- [WPS Cleaner](https://wordpress.org/plugins/wps-cleaner/), [WPS Bidouille](https://wordpress.org/plugins/wps-bidouille/), etc.

---

## L'auteur

**Fabrice Ducarme** — formateur WordPress depuis 2012, fondateur de **[WPFormation](https://wpformation.com)**.

- Co-créateur de [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/) (2M+ installations actives, le plugin qui m'a permis d'arrêter d'expliquer pourquoi /wp-admin/ est une mauvaise idée)
- 8 plugins publiés sur WordPress.org · 2.1M+ téléchargements cumulés
- Speaker WordCamp Paris 2013, Paris 2015, Lyon 2022, Marseille 2017
- Co-créateur du Meetup WordPress Montpellier
- Enseignant Pôle Sup (Nîmes)
- Qualiopi · 14 ans d'expertise WordPress

👉 **[wpformation.com/formateur-wordpress/](https://wpformation.com/formateur-wordpress/)**

---

## Contribuer

Issues et pull requests bienvenus. Les domaines où ton aide a le plus de valeur :

- 🎨 **Nouveaux patterns** : tu utilises Spectra et tu as une composition récurrente ? Propose un pattern.
- 🧪 **Bug reports** : si un block markup échoue à parser sur ton site, ouvre une issue avec le markup en question.
- 🎨 **Nouveaux templates** : sites clients réussis → templates partageables (anonymisés).
- 📚 **Doc, traductions, exemples** : toujours bienvenu.
- 🌍 **Compatibilité** : test sur GeneratePress, Kadence, Hello Elementor, Twenty Twenty-Five → si ça marche, dis-le. Si ça ne marche pas, dis-le aussi.

---

## License

MIT — voir [LICENSE](LICENSE).

Tu peux le forker, le modifier, l'utiliser commercialement, en faire un produit. Si tu construis quelque chose de cool dessus, je serais ravi de le voir : [wibeweb@gmail.com](mailto:wibeweb@gmail.com) ou [LinkedIn](https://www.linkedin.com/in/fabriceducarme/).

---

## Liens

- 🌐 [WPFormation.com](https://wpformation.com)
- 🎓 [Formation WordPress + IA](https://wpformation.com/formation-wordpress/)
- 📚 [Article Claude Code + WP](https://wpformation.com/claude-code-wordpress/)
- 🛡️ [Plugin Login Armor](https://wpformation.com/login-armor/)
- 🍽️ [Plugin OGEEAT](https://wpformation.com/ogeeat/)
- 💼 [LinkedIn Fabrice Ducarme](https://www.linkedin.com/in/fabriceducarme/)
- 🐦 [Twitter @WPFormation](https://x.com/wpformation)

---

**Made in France · Du WordPress, rien que du WordPress.**
