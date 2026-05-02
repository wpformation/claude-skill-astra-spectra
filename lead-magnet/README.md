# Lead magnet — PDF premium

## Contenu

- `pdf-source.md` : source markdown du guide (32-44 pages cible)
- `template.tex` : template LaTeX/Pandoc (à créer en session de production)
- `screenshots/` : 25 captures à produire (à créer en session de production)
- `guide-claude-code-wordpress-spectra-v1.pdf` : output final (à compiler)

## Production

Voir section « Workflow de production » à la fin de `pdf-source.md`.

Outils nécessaires :
- Pandoc 3.x
- XeLaTeX (TeX Live ou MikTeX sur Windows)
- Polices Inter Tight, Instrument Sans, Inter Display installées

Alternative plus simple : Typst (single binary, syntaxe lisible, pas de LaTeX).

## Distribution

Le PDF peut être distribué via le canal de ton choix : page front statique, capture email via le service email transactionnel que tu utilises déjà, ou simple lien direct sur ton site.

Côté WPFormation, l'intégration côté front (page de capture + route API) est gérée hors de ce repo public.

## Mise à jour

À chaque release majeure du skill (v1.0, v1.5, v2.0), regénérer le PDF avec :

- nouveaux patterns ajoutés
- nouvelles recettes WOW
- nouveaux templates
- mise à jour des captures (Playground refresh)
- mise à jour du chapitre « À propos » si bio change

## Mesure de succès

- 500+ téléchargements premier mois
- 30 %+ taux de conversion newsletter (email collecté)
- 5 %+ conversion vers formation payante (149 EUR/h ou 1 900 EUR)
- 50+ étoiles GitHub sur le repo public sur 90 jours
