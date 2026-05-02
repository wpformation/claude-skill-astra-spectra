# claude-skill-astra-spectra

> **Le premier skill Claude Code qui transforme un brief en langage naturel en page WordPress complète. Pas un template à copier-coller : une base de connaissance opérationnelle qui apprend à la session Claude Code suivante comment Spectra fonctionne réellement.**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![WordPress 6.0+](https://img.shields.io/badge/WordPress-6.0+-21759b.svg)](https://wordpress.org/)
[![Spectra Required](https://img.shields.io/badge/Spectra-Required-FF6B00.svg)](https://wpspectra.com/)
[![Astra Optional](https://img.shields.io/badge/Astra-Optional-blue.svg)](https://wpastra.com/)
[![Status: v1.0-rc2](https://img.shields.io/badge/Status-v1.0--rc2-orange.svg)](CHANGELOG.md)

---

## Ce qui rend ce skill différent

Un skill Claude Code, ce n'est pas un thème. Ce n'est pas un kit de templates. C'est de la connaissance opérationnelle structurée que la session Claude Code suivante peut lire pour générer des pages WordPress correctes du premier coup.

Ce skill documente ce que personne d'autre n'a documenté pour Spectra :

- **22 pièges** Spectra concrets identifiés en production avec Symptôme / Cause / Fix / Détection
- **La technique `_uag_custom_page_level_css`** pour styler durablement (le seul moyen pour que le CSS survive aux éditions Gutenberg)
- **Validateur pre-flight bloqueur** qui parcourt le markup généré et flag les 22 pièges + i18n + conventions AVANT POST
- **35+ patterns** documentés au format « comment construire » (pas du copier-coller) — couvre les 49 blocs Spectra principaux
- **8 templates** blueprints : compositions de patterns avec variables, schema SEO, configurations Astra
- **14 scripts** PHP utilitaires (POST tag-aware, regen Spectra 4 stratégies, validate roundtrip, pre-flight, audit, resolve palette)
- **Whitelist d'icônes** Font Awesome 5 Free validées (anti-piège fallback identique sur 3 cards)
- **Règles i18n FR** strictes (HTML entities, espaces insécables typo, em-dash, apostrophe typographique avec table de décision typo vs ASCII selon convention site cible)
- **Mécanique palette Astra** : slots GARANTIS vs VARIABLES (pour ne pas tomber sur color-7 = noir massif sur certaines palettes preset)
- **Pièges hébergeurs mutualisés** (o2switch, OVH, Hostinger : auth strip, LiteSpeed cache)

Quand une session Claude Code lit ce skill, elle sait pourquoi `headingFontSize:80` est ignoré sur `headingTag:p`, pourquoi 4 stats info-box s'empilent en colonne au lieu de row, pourquoi la FAQ affiche du Lorem Ipsum si tu mets `description` au lieu de `answer`, pourquoi `content: "\201C"` rend le texte littéral `201C` au lieu du caractère `“`. Elle évite les 22 pièges.

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

## Démo

```
> /astra-spectra
```

```
> Crée-moi une landing pour ma boutique de café avec hero + 3 piliers
> + stats + about-story + 3 testimonials + FAQ + CTA final
```

Le skill, en 10 étapes :

1. **Lit la knowledge base critique** (17 pièges + i18n + technique CSS persistante)
2. **Détecte l'environnement** (Spectra version, Astra version, palette active, hébergeur)
3. **Choisit les patterns** : `hero-image-overlay`, `features-numbered`, `stats-bar-editorial`, `about-story-split`, `testimonials-cards`, `faq-accordion`, `cta-banner-fullwidth`
4. **Demande les variables** (titre, sous-titre, images, contenu, ratios attendus)
5. **Génère le markup** Gutenberg avec HTML entities FR + conventions block_id `{slug}-{section}-{element}`
6. **Génère le CSS overrides** ciblé sur les classes du slug, encapsulé entre balises `/* === skill-generated v1.0 START/END === */`
7. **Valide le markup** (roundtrip parse/serialize, block_id uniques)
8. **POST page** + meta `_uag_custom_page_level_css` (tag-aware, préserve CSS user)
9. **Force regen Spectra** (4 stratégies cascadées : mu-plugin → endpoint native → temp-publish trick → manual)
10. **Validation visuelle** via screenshot agent-browser, retry si défaut détecté

Tu reçois une URL d'édition Gutenberg + screenshots des sections + URL frontend. Tu vérifies, tu modifies via Gutenberg admin (le CSS persiste car dans le meta natif Spectra, pas inline), tu publies.

---

## Architecture

```
SKILL.md                            ← Entry point (workflow + qui lit quoi quand)

references/                         ← Knowledge base critique (LIRE EN PREMIER)
├── spectra-attributes-quirks.md    ← 22 pièges Spectra documentés (OBLIGATOIRE)
├── i18n-rules.md                   ← FR : entities + nbsp typo + apostrophes typo vs ASCII
├── persistent-css-overrides.md     ← _uag_custom_page_level_css (la SEULE technique fiable)
├── spectra-icons-list.md           ← whitelist icônes validées
├── gutenberg-core-blocks.md        ← 30+ blocs core/* curés
├── astra-page-template-rules.md    ← anti double-H1, configurations Astra
├── apache-mutu-pitfalls.md         ← o2switch / OVH / Hostinger : auth strip, LiteSpeed
├── images-ratios.md                ← ratio attendu par pattern
├── spectra-blocks-catalog.md       ← 49 blocs uagb avec attributs
├── intent-to-block-routing.md      ← table de décision intent → bloc
├── section-rhythm.md               ← convention alternance bg
├── semantic-color-roles.md         ← slots GARANTIS vs VARIABLES
├── design-system-tokens.md         ← convention tokens
├── block-markup-syntax.md          ← syntaxe Gutenberg comments
├── mu-plugin-companion.md          ← installer le mu-plugin compagnon
└── spectra-demo-reference.md       ← analyse design Spectra Natures

patterns/                           ← Comment construire (PAS du copier-coller) — 35+ patterns
│
│   Compositions visuelles (sections de landing)
├── hero-image-overlay.md            hero-cta-split.md
├── stats-bar-editorial.md           stats-counters.md
├── features-numbered.md             features-3-cols.md
├── about-story-split.md             team-grid.md
├── testimonials-cards.md            testimonials-grid.md
├── pricing-3-tiers.md               faq-accordion.md
├── cta-banner-fullwidth.md          how-to-steps.md
├── countdown-launch.md              article-content-rich.md
├── review-product.md                landing-formation-complete.md
│
│   Blocs Spectra fonctionnels (UI/interaction/data)
├── tabs-section.md                  slider-carousel.md
├── timeline-vertical.md             post-display.md (grid/masonry/carousel/timeline)
├── google-maps.md                   modal.md
├── marketing-buttons.md             popup-builder.md
├── table-of-contents.md             forms.md (uagb/forms + cf7-designer + gf-designer)
├── image-gallery.md                 icon-list.md
├── inline-notice.md                 social-share.md
├── price-list.md                    star-rating.md
└── lottie.md

templates/                          ← Blueprints (composition de patterns) — 8 templates
├── page-accueil.md                  page-tarifs.md
├── page-contact.md                  page-a-propos.md
├── blog-editorial.md                e-commerce-produit.md
├── landing-saas.md                  page-agence.md
└── README.md

workflows/                          ← Pipelines validés
├── new-page-from-brief.md          ← 10 étapes from brief (pre-flight check OBLIGATOIRE avant POST)
├── refonte-page-existante.md       ← snapshot → analyse → reconstruction
├── visual-validation-loop.md       ← screenshot + audit + retry max 3
└── deploy-template.md              ← workflow déploiement template

scripts/                            ← 14 scripts PHP utilitaires
├── pre-flight-check.php            ← VALIDATEUR BLOQUEUR (22 quirks + i18n + conventions)
├── post-page-via-rest.php          ← POST + temp-publish trick
├── update-page-meta-css.php        ← TAG-AWARE update CSS (préserve user)
├── regen-spectra.php               ← 4 stratégies cascadées
├── validate-block-markup.php       ← roundtrip parse/serialize
├── visual-audit.php                ← 10 checks (WCAG walker, hardcoded color, etc.)
├── resolve-palette.php             ← resolve var(--ast-global-color-X)
├── apply-design-tokens.php         ← appliquer tokens design system
├── snapshot-page.php               ← dump page pour refonte
├── astra-customizer.php            ← export/apply Astra Customizer
├── auto-fix-markup.php             ← auto-fix erreurs courantes
├── detect-environment.php          ← profil site cible (check uag_enable_on_page_css_button)
├── cleanup-test-pages.php          ← cleanup pages test
└── mu-plugin-skill-test.php        ← mu-plugin compagnon (6 endpoints REST)

modules/                            ← Modules domaine
├── astra/                          ← Customizer, palette, header/footer
└── spectra/                        ← Container WOW recipes (12 recettes)

evals/                              ← 10+ prompts test pour mesurer qualité
screenshots/                        ← Baselines visuelles validées
examples/                           ← Pages de référence concrètes (PAS le skill)
```

---

## 3 killer features

### 1. Génération depuis un brief

Tu décris ce que tu veux. Le skill route chaque intention vers le bon bloc (Spectra prioritaire pour les compositions visuelles, core pour les blocs atomiques), assemble un markup Gutenberg propre, valide le roundtrip parse/serialize, génère un CSS overrides tag-aware, POST sur ton site en draft, force la régénération Spectra, valide visuellement par screenshot.

### 2. Refonte intelligente

Tu donnes une URL existante. Le skill snapshot le contenu via REST API, analyse la structure, mappe chaque section vers un pattern Spectra moderne, reconstruit en respectant **chaque mot du contenu original**. Le draft est créé en clone (jamais sur l'URL prod sans validation).

### 3. Templates blueprints

8 templates documentés au format composition de patterns. Page accueil, page tarifs, page contact, page à propos, blog editorial, e-commerce produit, landing SaaS, page agence. Chacun avec variables d'entrée, schema SEO (Service / Product / Organization / HowTo / Review), configuration Astra par type de page, variantes par secteur, workflow d'application.

---

## CSS persistant : la trouvaille v1.0

Spectra a un meta natif `_uag_custom_page_level_css` (vérifié dans le code source `class-uagb-post-assets.php:1434`) que le plugin concatène à son stylesheet à chaque rendu. Le skill exploite ce meta avec une **logique tag-aware** :

```css
/* === skill-generated v1.0 START === */
.uagb-block-accueil-stat-1 .uagb-ifb-title {
  font-size: 80px !important;
  color: var(--ast-global-color-0) !important;
  font-weight: 800 !important;
}
/* … autres overrides skill-managed … */
/* === skill-generated v1.0 END === */

/* CSS user post-skill ajouté manuellement dans Spectra UI — préservé */
.my-custom-class { ... }
```

Quand une session Claude Code régénère le CSS, elle remplace SEULEMENT la section entre balises. Le CSS user ajouté manuellement dans l'admin Spectra (« Page Level CSS » dans la sidebar Gutenberg) est préservé.

Documentation complète : [`references/persistent-css-overrides.md`](references/persistent-css-overrides.md)

---

## Quickstart en 3 commandes

```bash
# 1. Installer le skill
cd ~/.claude/skills/
git clone https://github.com/wpformation/claude-skill-astra-spectra astra-spectra

# 2. Sur ton WP cible : activer Spectra + créer un Application Password
wp plugin install ultimate-addons-for-gutenberg --activate
# Puis WP admin > Profil > Application Passwords > nouveau « claude-skill-astra-spectra »

# 3. Dans Claude Code
> /astra-spectra
> Détecte mon site https://monsite.com avec ce password : abcd 1234 efgh 5678
```

Détail complet : [INSTALL.md](INSTALL.md)

---

## Status v1.0-rc2

✅ **Ce qui est livré**

- Knowledge base complète : 16 documents de référence (quirks, i18n, icons-list, core-blocks, astra-templates, apache-mutu, images-ratios, persistent-css-overrides, design-system-tokens, etc.)
- **22 pièges Spectra documentés** (Symptôme / Cause / Fix / Détection chacun) — 19 en v1.0-rc1, +3 nouveaux en v1.0-rc2 (uag_enable_on_page_css_button toggle, CSS Unicode escapes strippés, conflit width px/% sur uagb/image)
- **35+ patterns** documentés au format « comment construire » — couvre les 49 blocs Spectra principaux : hero, stats, features, about-story, team, testimonials, pricing, FAQ, CTA, tabs, slider, timeline, how-to, review, countdown, article-content, **google-maps, modal, marketing-buttons, table-of-contents, forms, post-display (grid/masonry/carousel/timeline), image-gallery, icon-list, inline-notice, social-share, price-list, popup-builder, lottie, star-rating**
- 8 templates blueprints (page-accueil, page-tarifs, page-contact, page-a-propos, blog-editorial, e-commerce-produit, landing-saas, page-agence)
- **14 scripts PHP utilitaires**, dont `pre-flight-check.php` (validateur bloqueur 22 quirks + i18n, exit 1 si BLOCKED), `update-page-meta-css.php` (tag-aware), `regen-spectra.php` (4 stratégies cascadées)
- 4 workflows validés (new-page-from-brief avec pre-flight obligatoire, refonte-page-existante, visual-validation-loop, deploy-template)
- Mu-plugin compagnon avec endpoints REST custom (setup, upload-image, regen-spectra, inspect-faq, cleanup, enable-on-page-css)
- Validateur roundtrip parse/serialize anti-crash Gutenberg
- Audit visuel 10 checks (block_id unique, WCAG walker contraste, hardcoded color avec whitelist contextuelle, alternance bg sections)
- Baselines screenshots validées sur Astra 4.13.1 + Spectra 2.19.25 (palette par défaut + palette saturée chaude)

🚧 **À venir (v1.0 stable)**

- Validation finale par reviewer indépendant (re-test régression sur stack production réel : Apache mutualisé + LiteSpeed)
- Baselines additionnelles sur 2-3 palettes additionnelles pour `status: stable` officiel par pattern
- Variantes i18n par pattern (fr-FR.json, en-US.json, de-DE.json, es-ES.json)
- Workflow GitHub Actions de régression visuelle automatisée
- Endpoint `/skill-test/v1/inspect-icon/{name}` pour auto-validation icônes Spectra
- PDF lead magnet 32 pages compilé (Pandoc/Typst)

---

## Pour aller au-delà du skill

### 🎓 Formation WordPress + IA avec Claude Code

Si ce skill te parle et que tu veux **industrialiser ton WordPress avec Claude Code** au-delà de la génération de pages — créer ton propre fichier CLAUDE.md, tes propres skills, tes propres routines cloud, ton stack MCP, automatiser tout ce qui est répétitif sur ton site — j'enseigne ça en formation sur-mesure.

- 20 à 60 heures, en visio ou en présentiel
- Organisme certifié Qualiopi, financement OPCO possible
- Pour développeurs, agences, freelances, formateurs

👉 **[Découvrir la formation WordPress + IA](https://wpformation.com/formation-wordpress/)**

### 📚 Article complet : Piloter WordPress avec Claude Code

Le contexte, le stack, les skills WPF en prod, le fichier CLAUDE.md commenté ligne par ligne, les MCP utilisés au quotidien, les routines cloud Anthropic.

👉 **[Lire « Piloter WordPress avec Claude Code »](https://wpformation.com/claude-code-wordpress/)**

---

## Mes deux derniers plugins (2026, GPL forever)

Pas de version premium, pas de dépendance, pas de SaaS qui meurt l'année prochaine. Tout est local, tout est libre.

### Login Armor — 8 couches de sécurité, un seul plugin léger

Cache `wp-login.php`, brute-force protection en cascade, hardening 13 toggles, 2FA (TOTP + email + backup codes), détection d'incidents temps réel, activity log signé HMAC, headers de sécurité, breach check Have I Been Pwned. Sub-megabyte ZIP, PHP 8.1+, multisite-ready, suite WP-CLI complète. Pour les agences et freelances qui livrent des sites prêts à passer un audit.

👉 **[wpformation.com/login-armor/](https://wpformation.com/login-armor/)** · *Gratuit · sur [WordPress.org](https://wordpress.org/plugins/login-armor/) · publication récente, encore en early adopters*

### OGEEAT — l'extension post-SEO pour l'ère de l'IA

Pas un remplaçant de Yoast ou Rank Math, **un complément.** OGEEAT ajoute la couche que les SEO plugins classiques n'ont pas : E-E-A-T (Person + Organization + Article schemas), Score GEO (citabilité IA en 12 critères par article), `llms.txt` + `llms-full.txt` enrichis automatiquement, AI Crawler Firewall (bloque GPTBot/CCBot, autorise PerplexityBot/ChatGPT-User), Share with AI (6 moteurs : ChatGPT, Perplexity, Claude, Mistral, Gemini, Grok), Reviewed By pour le YMYL, Trust Signals, audit en masse. 14 modules, GPL forever, zéro premium.

👉 **[wpformation.com/ogeeat/](https://wpformation.com/ogeeat/)** · *Gratuit · sur [WordPress.org](https://wordpress.org/plugins/ogeeat/)*

---

## L'auteur

**Fabrice Ducarme** — formateur WordPress depuis 2012, fondateur de **[WPFormation](https://wpformation.com)**.

- Speaker WordCamp Paris 2013, Paris 2015, Marseille 2017, Lyon 2022
- Plugins publiés sur WordPress.org (2.1M+ téléchargements cumulés)
- Co-créateur du Meetup WordPress Montpellier
- Enseignant Pôle Sup (Nîmes)
- Qualiopi · 14 ans d'expertise WordPress

👉 **[wpformation.com/formateur-wordpress/](https://wpformation.com/formateur-wordpress/)**

---

## Contribuer

Issues et pull requests bienvenus. Les domaines où ton aide a le plus de valeur :

- **Nouveaux patterns** : tu utilises Spectra et tu as une composition récurrente non documentée ? Propose un pattern au format « comment construire » (cf `patterns/stats-bar-editorial.md` comme référence)
- **Nouveau piège Spectra détecté** : ouvre une issue avec Symptôme / Cause / Fix / Détection. Si validé, j'ajoute à `references/spectra-attributes-quirks.md`
- **Whitelist icônes** : tu as testé une icône hors `references/spectra-icons-list.md` qui marche / ne marche pas ? Document
- **Bug reports** : si un block markup échoue à parser sur ton site, ouvre une issue avec le markup en question
- **Nouveaux templates** : sites clients réussis → templates partageables (anonymisés)
- **Compatibilité multi-thèmes** : tests sur GeneratePress, Kadence, Hello Elementor, Twenty Twenty-Five → si ça marche, dis-le. Si ça ne marche pas, dis-le aussi.
- **Doc, traductions, exemples** : toujours bienvenu

---

## License

MIT — voir [LICENSE](LICENSE).

Tu peux le forker, le modifier, l'utiliser commercialement, en faire un produit. Si tu construis quelque chose dessus, je serais ravi de le voir : ping-moi sur [LinkedIn](https://www.linkedin.com/in/fabriceducarme/).

---

## Liens

- 🌐 [WPFormation.com](https://wpformation.com)
- 🎓 [Formation WordPress + IA](https://wpformation.com/formation-wordpress/)
- 📚 [Article Claude Code + WordPress](https://wpformation.com/claude-code-wordpress/)
- 🛡️ [Plugin Login Armor](https://wpformation.com/login-armor/)
- 🤖 [Plugin OGEEAT](https://wpformation.com/ogeeat/)
- 💼 [LinkedIn Fabrice Ducarme](https://www.linkedin.com/in/fabriceducarme/)
- 🐦 [Twitter @WPFormation](https://x.com/wpformation)

---

**Made in France · Du WordPress, rien que du WordPress.**
