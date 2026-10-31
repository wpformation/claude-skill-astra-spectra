# À faire : claude-skill-astra-spectra

Mis à jour le 31/10/2026 par Claude Code (test sur cours-ndrc.fr, avis de Fabrice reçu). Ce fichier est chargé à l'ouverture de chaque session : les points datés échus ou à 7 jours sont signalés dans la première réponse. Un point par ligne ; le détail reste dans l'issue, la pull request ou le document lié.

## Attend Fabrice


## Travail des agents

- [ ] Intégrer les 3 pièges relevés sur cours-ndrc.fr le 31/10/2026 : `site-post-title = disabled` manque au quirk #13, `inheritFromTheme: false` aux boutons quand le site hérite du thème (padding des boutons seulement par CSS de page en Spectra 2.20.5), et le fond d'une section sombre pris dans la couleur de l'en-tête du site, pas dans le slot des titres. Lien : `screenshots/cours-ndrc-palette3/README.md`.
- [ ] Liste « TODO avant v1.0 », 5 cases encore ouvertes : 27 captures des 9 patterns sur 3 palettes, 9 captures des 3 templates, fixtures `_palettes/*.json`, régression visuelle en GitHub Actions, `tested-on-palettes` dans les frontmatters des patterns. Pas d'étiquette v1.0 avant qu'elles soient toutes cochées (version actuelle : 1.0-rc6 du 02/05/2026). Lien : `screenshots/README.md`.

## Sans échéance

- [ ] Contenus prévus pour la v1.0 finale : PDF du lead magnet (25 captures, gabarit LaTeX à créer), page et route API sur wpformation.com, 6 patterns et 5 templates supplémentaires, liste des icônes. Lien : `CHANGELOG.md`, section « [Unreleased] » ; `lead-magnet/README.md`.
- [ ] Évaluations sans intégration continue (proposition seulement) et pistes « TODO v1.1+ » dans 9 fichiers de `references/`. Lien : `evals/README.md`, section « En CI (proposition) ».
