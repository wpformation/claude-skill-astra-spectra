# claude-skill-astra-spectra

> **Le premier skill Claude Code qui pilote intégralement Spectra (49 blocs Gutenberg) + Astra theme + Gutenberg core. Génère des pages WordPress complètes en moins de 2 minutes depuis un brief en langage naturel.**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![WordPress 6.0+](https://img.shields.io/badge/WordPress-6.0+-21759b.svg)](https://wordpress.org/)
[![Spectra Required](https://img.shields.io/badge/Spectra-Required-FF6B00.svg)](https://wpspectra.com/)
[![Astra Optional](https://img.shields.io/badge/Astra-Optional-blue.svg)](https://wpastra.com/)

## Pourquoi ce skill

**Spectra** (Ultimate Addons for Gutenberg, 700K+ sites WordPress) est l'un des plugins page-builder les plus populaires de l'écosystème WordPress. Pourtant, jusqu'à mai 2026, **aucun MCP, aucun skill Claude Code ne permettait de le piloter par IA**.

Astra a son MCP officiel (limité au Customizer). Spectra n'avait rien.

Ce skill comble ce trou. Il route intelligemment chaque intention exprimée en langage naturel vers le bon bloc (Spectra prioritaire, Gutenberg core en fallback), assure la cohérence design system via les variables CSS Astra, et orchestre la génération depuis un brief en langage naturel jusqu'à la page draft validée visuellement.

## 3 killer features

### 1. Génération depuis un brief

```
> crée-moi une landing page formation avec hero, 3 features, pricing 3 tiers, FAQ et CTA YouTube

✅ Page créée en 1 min 47s
- 23 blocs valides (15 uagb + 8 core)
- Schema FAQPage activé
- Design cohérent (palette WPF)
- URL d'édition fournie
```

### 2. Refonte intelligente

```
> modernise /a-propos/ en mode glassmorphism avec timeline et team

✅ Refonte créée en draft (clone)
- 8 blocs core convertis en mix Spectra+core
- 1 section "équipe" remplacée par uagb/team
- 1 hero ajouté avec gradient mesh
- Diff visuel disponible
```

### 3. Templates clic-bouton

```
> déploie le template page-formation avec WordPress Mastery 297€

✅ Template déployé en 38s
- 9 sections complètes
- Palette wpf-orange appliquée
- Variables remplies depuis tes inputs
- 0 erreur Gutenberg
```

## Pré-requis

### Bloquants

- WordPress **6.0+**
- PHP **7.4+**
- **Spectra plugin** activé : [wordpress.org/plugins/ultimate-addons-for-gutenberg](https://wordpress.org/plugins/ultimate-addons-for-gutenberg/)
- **Application Password** valide (WP admin > Users > ton profil > Application Passwords)
- REST API accessible (`/wp-json/wp/v2/pages` retourne 200 ou 401)

### Optionnels (débloquent des bonus)

- **Astra theme** activé → débloque le module Customizer (palette pilotage, header builder, footer builder)
- **Astra Pro** → options avancées Astra (header transparent, mega menu, white label)
- **Skill `/screenshot-loop`** → validation visuelle automatique post-génération
- **Skill `/impeccable`** → audit design post-génération

## Installation

### Pour Claude Code utilisateur

```bash
# Option 1 : clone direct dans le dossier skills
cd ~/.claude/skills/
git clone https://github.com/wpformation/claude-skill-astra-spectra.git astra-spectra

# Option 2 : symlink depuis un repo local
ln -s /path/to/your/clone ~/.claude/skills/astra-spectra
```

Puis dans Claude Code, le skill est automatiquement détecté et triggable. Voir [INSTALL.md](INSTALL.md) pour le détail.

### Pour le site WordPress cible

Installer **Spectra plugin** :

```bash
# Via WP-CLI
wp plugin install ultimate-addons-for-gutenberg --activate

# Ou via WP admin > Extensions > Ajouter > rechercher "Spectra"
```

(Optionnel) Installer Astra theme :

```bash
wp theme install astra --activate
```

Générer une **Application Password** : WP admin > Users > ton profil > Application Passwords > New Password (nom : "claude-skill-astra-spectra").

## Utilisation

### Quickstart (3 commandes)

```
# 1. Demande au skill de détecter ton site
> Détecte mon site https://monsite.com avec ce password : abcd 1234 efgh 5678

✅ Profil détecté :
- WordPress 6.9.4
- Spectra 2.19.25 ✓
- Astra theme 4.13.1 ✓
- Verdict : GO

# 2. Génère ta première page
> Crée-moi une landing page pour ma formation avec hero, 3 features et CTA

✅ Page créée (ID 42, draft)
URL d'édition : https://monsite.com/wp-admin/post.php?post=42&action=edit

# 3. Ajuste si besoin
> Sur la page 42, ajoute une section témoignages après les features
```

### Documentation complète

- **[SKILL.md](SKILL.md)** : routing principal et règles
- **[INSTALL.md](INSTALL.md)** : installation pas-à-pas (5 minutes)
- **[references/](references/)** : table de décision intent → bloc, catalogue 49 blocs Spectra, syntaxe block markup, design system
- **[modules/spectra/container-wow-recipes.md](modules/spectra/container-wow-recipes.md)** : 12 recettes wow avec `uagb/container`
- **[patterns/](patterns/)** : 7+ patterns hybrides production-ready
- **[templates/](templates/)** : 3+ templates de pages complètes
- **[workflows/](workflows/)** : 3 killer features détaillés

## Architecture

```
claude-skill-astra-spectra/
├── SKILL.md                              # routing principal
├── README.md                             # ce fichier
├── INSTALL.md                            # installation pas-à-pas
├── LICENSE                               # MIT
├── CHANGELOG.md                          # journal des versions
├── modules/
│   ├── spectra/                          # ⭐ GOLD WIN
│   │   ├── blocks-catalog.md             # référence 49 blocs uagb/*
│   │   ├── markup-recipes.md             # patterns markup éprouvés
│   │   └── container-wow-recipes.md      # 12 recettes WOW
│   ├── astra/                            # bonus si Astra présent
│   ├── core/                             # fallback Gutenberg core
│   └── design-tokens/                    # palettes pré-construites
├── references/
│   ├── intent-to-block-routing.md        # ⭐ table de décision (45 entrées)
│   ├── spectra-blocks-catalog.md
│   ├── block-markup-syntax.md
│   └── design-system-tokens.md
├── patterns/                             # 7+ patterns réutilisables
├── templates/                            # 3+ templates de pages
├── workflows/                            # 3 killer features
├── scripts/                              # PHP helpers (REST API direct)
└── evals/                                # tests
```

## PDF guide premium (lead magnet WPFormation)

Un guide PDF de 25-40 pages avec recettes avancées, troubleshooting détaillé, prompts optimisés et cas d'usage agence est disponible **gratuitement contre inscription** à la newsletter WPFormation :

👉 **[Télécharger le guide complet sur wpformation.com/skill-astra-spectra/](https://wpformation.com/skill-astra-spectra/)**

Le PDF contient :
- Install pas-à-pas avec captures d'écran
- 15-20 recettes design system avancées (non publiées dans ce repo)
- Prompts d'invocation optimisés
- Troubleshooting (10-15 problèmes courants + fix)
- Cas d'usage agence : standardiser ta production de sites client
- Bonus : prompts pour générer des variantes par secteur

## Contribuer

Issues et pull requests bienvenus. En particulier :

- 🎨 **Nouveaux patterns** : si tu utilises Spectra et as une composition récurrente, propose un pattern
- 🎨 **Nouveaux templates** : sites clients réussis → templates partageables
- 🐛 **Bug reports** : si un block markup échoue à parser, ouvre une issue avec le markup en question
- 📚 **Doc** : améliorations, traductions, exemples

## Crédits

Créé par **[Fabrice Ducarme](https://wpformation.com/formateur-wordpress/)** — formateur WordPress depuis 2012, 8 plugins WordPress.org publiés (2.1M+ téléchargements cumulés), speaker WordCamp Paris/Lyon/Marseille, co-créateur de [WPS Hide Login](https://wordpress.org/plugins/wps-hide-login/) (2M+ installations actives).

WPFormation : [wpformation.com](https://wpformation.com) — formations WordPress sur-mesure (Qualiopi).

## License

MIT — voir [LICENSE](LICENSE).

## Liens

- 🌐 [WPFormation.com](https://wpformation.com)
- 📚 [Guide PDF complet](https://wpformation.com/skill-astra-spectra/)
- 🐦 [Twitter @WPFormation](https://twitter.com/wpformation)
- 💼 [LinkedIn Fabrice Ducarme](https://www.linkedin.com/in/fabriceducarme/)
- 💬 Discord : (lien dans le PDF premium)

---

**Made with ❤️ in France for the WordPress + AI community.**
