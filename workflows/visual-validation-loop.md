# Workflow — Boucle de validation visuelle (auto-retries)

> **Killer feature cachée** : génération from brief → screenshot → audit `/impeccable` → re-POST si correction nécessaire → screenshot → … jusqu'à validation. Le tout sans intervention manuelle.

## Objectif

Garantir qu'une page générée par le skill n'est pas seulement **markup-valide** mais aussi **visuellement correcte** (hiérarchie, spacing, contraste, alignements, design tokens).

## Quand l'utiliser

Workflow appelé automatiquement par les 3 killer features :

1. `new-page-from-brief.md` → après POST initial
2. `refonte-page-existante.md` → après reconstruction
3. `deploy-template.md` → après adaptation contenu

L'utilisateur peut aussi le déclencher seul : « audit visuel de /ma-page/ et corrige si besoin ».

## Pré-requis

- Page draft postée et accessible via URL (ID retourné par `createPost()`)
- Au moins **un** des outils suivants :
  - MCP `wordpress-playground` (preview interne sans serveur)
  - CLI `agent-browser` (snapshot accessibility + screenshot annoté)
  - Playwright local (fallback)
- Skill `/impeccable` installé (ou règles de design system locales)

## Étapes

### 1. Capture visuelle initiale

Selon l'environnement disponible, dans cet ordre de préférence :

```
Si MCP wordpress-playground actif :
  → playground_navigate(url=draft_url)
  → playground_get_website_url() pour récupérer URL temporaire
  → screenshot via DevTools du Playground

Sinon si agent-browser dispo :
  → agent-browser screenshot --url=<draft_url> --annotate --output=/tmp/screenshot-1.png

Sinon Playwright :
  → npx playwright screenshot <draft_url> /tmp/screenshot-1.png
```

Sauvegarder le screenshot dans un répertoire temporaire numéroté (`/tmp/wpf-skill-validate-<id>/screenshot-N.png`).

### 2. Audit `/impeccable` (ou fallback design system)

Si `/impeccable` est installé, l'invoquer avec :

```
Cible : <draft_url>
Mode : audit + suggestions de correction concrètes (pas de polish destructif)
Verrouillage : couleurs Astra global-color-* respectées (ne JAMAIS proposer de changer la palette)
Output : liste de problèmes priorisés (P0 critique → P3 cosmétique)
```

**Si `/impeccable` absent**, appliquer ces 12 checks intégrés :

| Check | Critère | Sévérité |
|-------|---------|----------|
| 1. Hiérarchie titres | un seul H1, H2/H3 cohérents, pas de saut H1→H4 | P0 |
| 2. Contraste texte/fond | ratio WCAG AA ≥ 4.5 sur tous les containers | P0 |
| 3. Hex hardcodé | détecter `color:#XXXXXX` au lieu de `var(--ast-global-color-*)` | P1 |
| 4. block_id manquant | tous les `uagb/*` doivent avoir un `block_id` unique | P0 |
| 5. Padding incohérent | `paddingTop`/`paddingBottom` cohérents entre sections (60-80-100) | P2 |
| 6. Boutons sans CTA texte | pas de bouton sans label clair | P1 |
| 7. Images sans alt | `<img alt="">` vide → P1 |
| 8. Largeur container | `contentWidth` ≤ 1200 sur écrans desktop | P2 |
| 9. Responsive breakpoints | au moins `tablet` + `mobile` définis sur containers | P1 |
| 10. Typo cohérente | mêmes `headingFontFamily` dans toute la page | P3 |
| 11. Espace vertical | gap minimum 40px entre sections | P2 |
| 12. CTA primaire visible | au moins un CTA `var(--ast-global-color-0)` au-dessus de la ligne de flottaison | P1 |

### 3. Décision : retry ou done

```
Si tous les checks P0/P1 OK → DONE, retourner draft_url
Si ≥1 check P0 échoue → RETRY (max 3 tentatives)
Si seuls des P2/P3 échouent → WARNING + DONE
```

### 4. Retry intelligent (max 3 itérations)

Pour chaque problème P0/P1 détecté, appliquer la correction adéquate dans le markup :

| Problème détecté | Correction appliquée |
|------------------|---------------------|
| H1 multiple | Garder le 1er, dégrader les suivants en H2 |
| Hex hardcodé | Remplacer par token Astra le plus proche |
| block_id dupliqué | Régénérer un UUID v4 unique |
| Contraste faible | Inverser fond/texte ou wrapper avec container `--ast-global-color-7` (off-white) |
| Padding incohérent | Normaliser sur grille 8pt (40/60/80/100) |
| Image alt vide | Générer alt depuis le contexte du heading le plus proche |

Re-POST le markup corrigé via `updatePost()` (PUT REST API), invalider le cache, recapturer le screenshot, repasser l'audit.

### 5. Compteur de retries

```
attempt=1 → screenshot-1 + audit-1.json
attempt=2 → screenshot-2 + audit-2.json
attempt=3 → screenshot-3 + audit-3.json
attempt=4 → ÉCHEC, retourner le diff entre audit-1 et audit-3 + recommandation manuelle
```

Si après 3 retries des P0 persistent : retourner un rapport détaillé à l'utilisateur (« voici ce que j'ai tenté de corriger 3 fois, voici ce qui résiste, voici ce que je suggère manuellement »).

### 6. Output final

```json
{
  "draft_url": "https://exemple.fr/ma-page-draft/",
  "draft_id": 12345,
  "validation": {
    "status": "OK | WARNING | FAILED",
    "attempts": 2,
    "p0_remaining": 0,
    "p1_remaining": 0,
    "p2_warnings": ["Padding section 3 et 5 incohérents (60 vs 80)"],
    "p3_warnings": []
  },
  "screenshots": ["/tmp/.../screenshot-1.png", "/tmp/.../screenshot-2.png"],
  "audit_logs": ["/tmp/.../audit-1.json", "/tmp/.../audit-2.json"]
}
```

## Couplage avec `/screenshot-loop` natif

Si l'utilisateur a déjà le skill `/screenshot-loop` (frontend Next.js), le skill astra-spectra **délègue** plutôt que dupliquer :

```
Si SKILL_SCREENSHOT_LOOP_AVAILABLE :
  → invoquer /screenshot-loop avec target=<draft_url> et iterations=3
  → récupérer le verdict
Sinon :
  → utiliser la boucle interne décrite ci-dessus
```

## Anti-patterns à éviter

- ❌ Boucle infinie : toujours capper à 3 retries max
- ❌ Modifier le contenu textuel pendant un retry visuel (uniquement les attributs structurels)
- ❌ Toucher à la palette Astra (verrouillée par design system)
- ❌ Re-POST si le diff de markup est nul (gaspillage)
- ❌ Ignorer les warnings P2/P3 sans les loguer (l'utilisateur veut savoir)

## Métriques de succès

- 80 %+ des pages générées passent au 1er essai (screenshot-1 = OK)
- 95 %+ passent en ≤ 2 retries
- < 5 % nécessitent intervention manuelle

Ces seuils sont mesurés dans la suite d'évals (`evals/visual-validation.json`).
