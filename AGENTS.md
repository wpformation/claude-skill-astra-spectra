# AGENTS.md

**À faire** : [A-FAIRE.md](A-FAIRE.md). Au premier échange de chaque session, lire ce fichier et commencer la réponse par ses points échus ou à 7 jours et le nombre de points qui attendent Fabrice (3 lignes au plus, rien s'il n'y a rien), puis traiter la demande. En fin de tâche, le tenir à jour : point fait retiré, point nouveau ajouté avec sa date et son lien, date de mise à jour changée.

> Fichier lu par Codex (et par tout agent respectant la convention `AGENTS.md`). Le bloc ci-dessous est propagé et maintenu depuis `META-AUDIT-CLAUDE`. Ne pas dupliquer les marqueurs. Les instructions métier propres au projet vivent en dehors des marqueurs et restent prioritaires.

<!-- META-AUDIT:BI-AGENT:START -->
## Gouvernance bi-agent Claude Code / Codex

Ce projet peut être travaillé par **deux agents** : Claude Code et Codex. Source de gouvernance : `META-AUDIT-CLAUDE/rapports/DECISION-GOUVERNANCE-CLAUDE-CODEX-2026-07-14.md`.

- **Checkouts séparés** : Claude Code travaille sous `G:\CLAUDE-PROJETS\` ; Codex travaille sous `G:\CODEX\`. Aucun agent ne modifie le checkout de l'autre.
- **Ne jamais supposer** qu'un checkout de l'autre agent existe : le vérifier, ne rien inventer.
- **Remote arbitre** : le dépôt distant tranche entre les clones. Tout travail destiné à durer est poussé.
- **Branches** : Claude utilise `claude/<sujet>`, Codex utilise `codex/<sujet>`. Jamais la même branche des deux côtés en même temps.
- **Début de session** : `git fetch` puis `git pull --ff-only` sur arbre propre ; si l'arbre est sale, s'arrêter et identifier le propriétaire des changements.
- **Fin de session** : diff relu, `git add` ciblé (jamais `-A` aveugle), commit clair, push sur la branche courante.
- **Aucun secret** dans Git (credentials hors dépôt, gitignorés).
- Les **instructions métier de ce projet restent prioritaires** sur ce bloc générique.
<!-- META-AUDIT:BI-AGENT:END -->
