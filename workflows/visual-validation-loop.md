# Workflow — Boucle de validation visuelle (auto-retries)

> 🔴 **GATE NON-NÉGOCIABLE** : ce workflow est **OBLIGATOIRE** avant de qualifier une page de WOW / impeccable / propre / éditorial / beau / réussi (cf règle 1 SKILL.md). Sans screenshot validé, l'instance Claude **n'a pas le droit** d'utiliser ces qualificatifs dans sa réponse au user.

> **Killer feature cachée** : génération from brief → screenshot → audit `/impeccable` → re-POST si correction nécessaire → screenshot → … jusqu'à validation. Le tout sans intervention manuelle.

## Objectif

Garantir qu'une page générée par le skill n'est pas seulement **markup-valide** mais aussi **visuellement correcte** (hiérarchie, spacing, contraste, alignements, design tokens).

> **Origine retour reviewer 02/05/2026** : 3 pages contact générées « techniquement OK » (pre-flight 0 P0/P1, 24 quirks verts) mais qualifiées « moches, niveau débutant » par le user après visualisation. Cause : ce workflow n'était **pas obligatoire** → l'instance Claude a livré sans screenshot.

## Le gate visuel BLOQUANT

Avant TOUTE réponse au user qui annonce un succès design, l'instance Claude DOIT avoir :

1. ✅ Screenshot capturé (cf [`screenshot-options.md`](screenshot-options.md) — 5 options)
2. ✅ Checklist visuelle minimum passée (10 points dans `screenshot-options.md`)
3. ✅ Si défaut détecté visuellement : retry markup OU avertir le user explicitement

Si l'un des 3 est ❌, l'instance Claude :

- **Ne qualifie PAS** la composition de WOW / impeccable / propre / éditorial / beau / réussi
- **Qualifie** la composition de **« non vérifiée visuellement, à juger par le user »**
- **Demande** au user de screenshotter via Option E de `screenshot-options.md`

Cette règle n'a **pas d'exception**. Aucune.

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

Sauvegarder le screenshot dans un répertoire temporaire numéroté (`/tmp/astra-spectra-skill-validate-<id>/screenshot-N.png`).

### 2. Audit `/impeccable` (ou fallback design system)

Si `/impeccable` est installé, l'invoquer avec :

```
Cible : <draft_url>
Mode : audit + suggestions de correction concrètes (pas de polish destructif)
Verrouillage : couleurs Astra global-color-* respectées (ne JAMAIS proposer de changer la palette)
Output : liste de problèmes priorisés (P0 critique → P3 cosmétique)
```

**Si `/impeccable` absent**, le script `scripts/visual-audit.php` applique ces 8 checks intégrés (statiques, sur le markup, sans rendu visuel) :

| # | Check | Critère | Sévérité |
|---|-------|---------|----------|
| 1 | Hiérarchie titres | exactement 1 H1, comptage H2/H3 | P0 (multiple) / P1 (absent) |
| 2 | block_id Spectra | tous les `uagb/*` ont un `block_id` unique | P0 (manquant ou dupliqué) |
| 3 | Couleur hardcodée | détecte hex/rgb/rgba/hsl au lieu de `var(--ast-global-color-X)` | P1 (général) / P3 (box-shadow neutre rgba(0,0,0,X) accepté) |
| 4 | Image alt | `core/image` ou `uagb/image` sans alt text | P1 |
| 5 | Container width | `contentWidth > 1400` (lecture désconfortable) | P2 |
| 6 | Padding responsive sur container racine | container racine avec padding desktop mais sans tablet/mobile | P1 |
| 7 | CTA présent | au moins un `uagb/buttons` ou `core/buttons` sur la page | P1 |
| 8 | H1 présent | exactement 1 H1 sur la page | P1 (absent) |

> **Note honnêteté** : ces 8 checks sont les plus utiles parce qu'ils sont mesurables sur le markup statique sans rendu navigateur. Les checks visuels avancés (contraste WCAG mesuré sur DOM rendu, font-size minimum, spacing rhythm, weight image, ordre sémantique a11y, micro-interactions) **nécessitent un vrai rendu navigateur** et sont délégués à `/impeccable` qui pilote Chrome via Playwright. Si `/impeccable` est installé, l'audit est plus profond. S'il ne l'est pas, ces 8 checks couvrent ~80 % des problèmes critiques.

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

---

## Pipeline pratique testé v0.9.1 (02/05/2026)

> Cette section documente le pipeline EXACTEMENT exécuté pour produire la baseline `screenshots/loginarmor-dev-palette3/v091-iter3-WOW-fullpage.png`. Reproduire ces commandes garantit un rendu visuel identique pour tester un nouveau pattern ou une régression.

### Setup environnement (une fois)

```bash
# 1. Vérifier WP local Astra+Spectra accessible
curl -s -u "admin:APP_PASS" http://loginarmor-dev.local/wp-json/wp/v2/users/me?context=edit \
  | python -c "import json,sys;print([k for k,v in json.load(sys.stdin).get('capabilities',{}).items() if v][:5])"
# Doit retourner ['switch_themes', 'edit_themes', 'activate_plugins', 'edit_plugins', ...]

# 2. Installer le mu-plugin compagnon (endpoints custom skill-test/v1)
# Voir mu-plugin-companion.md pour le code complet
cp scripts/mu-plugin-skill-test.php "C:/Users/USER/Local Sites/SITE/app/public/wp-content/mu-plugins/"

# 3. Configurer la palette test (palette_3 cours-ndrc.fr orange WPF)
curl -X POST -u "admin:APP_PASS" \
  http://loginarmor-dev.local/wp-json/skill-test/v1/setup -H "Content-Type: application/json" -d '{}'

# 4. Uploader 6 images placeholders (Unsplash via skill-test/v1/upload-image avec name custom .jpg)
for i in 1..6; do
  curl -X POST -u "admin:APP_PASS" \
    http://loginarmor-dev.local/wp-json/skill-test/v1/upload-image \
    -H "Content-Type: application/json" \
    -d "{\"url\":\"https://images.unsplash.com/photo-XXXX?w=1920&h=1080&fit=crop&q=80\",\"name\":\"hero-img-$i.jpg\"}"
done
```

### Pipeline screenshot (chaque test)

```bash
# 5. Publier la page test
python -c "
import json
content=open('examples/landing-formation-complete-markup.html').read()
open('payload.json','w').write(json.dumps({'title':'DEMO Skill','content':content,'status':'publish','slug':'demo-skill'}))
"
curl -X POST -u "admin:APP_PASS" \
  http://loginarmor-dev.local/wp-json/wp/v2/pages \
  -H "Content-Type: application/json; charset=utf-8" --data-binary "@payload.json"

# 6. Régénérer assets Spectra (force CSS file generation)
curl -X POST -u "admin:APP_PASS" \
  http://loginarmor-dev.local/wp-json/skill-test/v1/regen-spectra \
  -H "Content-Type: application/json" -d '{"post_id":<ID>}'

# 7. Setup viewport agent-browser desktop
agent-browser set viewport 1440 900

# 8. Open + force eager + fullpage screenshot
agent-browser open http://loginarmor-dev.local/demo-skill/
sleep 3
agent-browser eval "document.querySelectorAll('img[loading=lazy]').forEach(i=>i.loading='eager'); window.scrollTo(0,document.body.scrollHeight); 'ok'"
sleep 2
agent-browser eval "window.scrollTo(0,0); 'reset'"
sleep 1
agent-browser screenshot --full screenshots/<context>/<test-name>-fullpage.png

# 9. Screenshots zoomés par section
for sect in v3-hero v3-stats v3-features v3-story v3-testimonials v3-faq-section v3-cta-final; do
  agent-browser eval "document.querySelector('.uagb-block-${sect}').scrollIntoView({behavior:'instant',block:'start'}); window.scrollBy(0,-50); 'ok'"
  sleep 1
  agent-browser screenshot screenshots/<context>/<test-name>-zoom-${sect}.png
done
```

## 4 pièges critiques détectés et fixés v0.9.1

### Piège 1 — FAQ avec Lorem Ipsum
**Symptôme observé** : la FAQ accordéon s'expand mais affiche `Lorem ipsum dolor sit amet, consectetur...` au lieu de la vraie réponse.
**Cause** : l'attribut Spectra s'appelle **`answer`**, pas `description`. Vérifié via `register_rest_route('/inspect-faq')` qui inspecte `WP_Block_Type_Registry`.
**Fix** : `<!-- wp:uagb/faq-child {"question":"...", "answer":"<reponse complète>"} -->`. Le inner content (`<p class="uagb-faq-content">`) est secondaire mais doit être présent pour la rendition initiale.
**Validation** : `screenshots/loginarmor-dev-palette3/v091-iter2-faq-fixed.png` — la première question affiche maintenant la vraie réponse.

### Piège 2 — Image about-story qui n'apparaît pas en screenshot fullpage
**Symptôme** : screenshot fullpage montre une zone blanche à la place de l'image about-story (mais l'image est présente dans le HTML rendu).
**Cause** : `loading="lazy"` sur `<img>` + agent-browser screenshot --full ne déclenche pas toujours le lazy loading des images en bas de page.
**Fix** : avant le screenshot, exécuter `document.querySelectorAll('img[loading=lazy]').forEach(i=>i.loading='eager')` puis scroll bottom + scroll top pour déclencher tous les lazy loaders.
**Validation** : `screenshots/loginarmor-dev-palette3/v091-iter1-1440-fullpage-eager.png` — l'image apparaît correctement.

### Piège 3 — CSS Spectra absent en draft preview
**Symptôme** : preview anonyme `?preview=true` d'un draft → page rendue sans styles (pas de flex-grid, pas de border-radius, pas de shadow). Le `_uag_page_assets` post_meta existe avec un `css` de 17K+ chars mais le HTML <head> n'a aucun `<style id="uagb-style-frontend-X">`.
**Cause** : sur Apache mutu (o2switch) + LiteSpeed Cache + plugins de sécurité, le hook `wp_head` n'injecte pas le CSS Spectra inline pour les drafts en preview anonyme. Spectra a un check `is_user_logged_in()` ou `current_user_can('edit_post')` qui filtre.
**Fix** : `wpf_skill_temp_publish_trick()` dans `scripts/post-page-via-rest.php` v0.9.1 — publish temporaire (~1s) → hit URL frontend (force pipeline complète Astra+Spectra) → revert au statut original. Active par défaut, désactivable via `--no-temp-publish` sur sites live.

### Piège 4 — Slot color-7 = noir massif sur palette_3
**Symptôme** : section testimonials/FAQ apparaît avec **bord noir massif** sur palette_3 (cours-ndrc.fr, palette orange WPF).
**Cause** : pattern utilise `borderColor: var(--ast-global-color-7)`. Sur palette_3, color-7 vaut `#141006` (presque noir). Sur palette default, color-7 vaut le secondary gris.
**Fix** : utiliser **hex direct neutre** `borderColor: "#e5e7eb"` ou résoudre via `wpf_skill_resolve_color('border_subtle', $palette)`. Voir `references/semantic-color-roles.md` pour la table GUARANTEED vs VARIABLE slots Astra.

## Baselines de référence (mises à jour 02/05/2026)

| Palette | Pattern | Baseline screenshot |
|---------|---------|---------------------|
| palette_3 (orange WPF) | `landing-formation-complete` | `screenshots/loginarmor-dev-palette3/v091-iter3-WOW-fullpage.png` (~7 sections) |
| palette_3 | hero-image-overlay (variante gradient) | `screenshots/loginarmor-dev-palette3/v091-FINAL-zoom-v3-hero.png` |
| palette_3 | features-3-cols | `screenshots/loginarmor-dev-palette3/v091-FINAL-zoom-v3-features.png` |
| palette_3 | testimonials-grid | `screenshots/loginarmor-dev-palette3/v091-FINAL-zoom-v3-testimonials.png` |
| palette_3 | faq-accordion | `screenshots/loginarmor-dev-palette3/v091-iter2-faq-fixed.png` |
| palette_3 | cta-banner-fullwidth | `screenshots/loginarmor-dev-palette3/v091-FINAL-zoom-v3-cta-final.png` |

À ajouter en v1.0 : palette default + preset_8 (orange gourmand piège). Voir `screenshots/README.md` pour le process complet.
