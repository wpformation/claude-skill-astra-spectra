# Workflow : screenshot-options — 5 options concrètes pour capturer un visuel

> **LECTURE OBLIGATOIRE** par l'instance Claude qui s'apprête à valider visuellement une page (cf règle 1 SKILL.md). Sans screenshot, tu **n'as pas le droit** de qualifier une composition de « WOW / impeccable / studio editorial / propre / éditorial / beau ».

> **Origine** : retour reviewer 02/05/2026 — sur Windows + Chrome installé + hooks bloquant le publish, l'instance Claude a galéré à screenshotter et a fini par claim aveugle. Doc concrète manquante.

## Décision tree

```
1. Tu as agent-browser installé ?
   OUI → Option A (recommandée)
   NON → 2

2. Tu as chrome-headless installé (Chrome ou Chromium dispo en CLI) ?
   OUI → Option B (CLI direct)
   NON → 3

3. Tu as Playwright installé ?
   OUI → Option C
   NON → 4

4. Le site est sur WP Playground (jetable, public) ?
   OUI → Option D (capture URL Playground)
   NON → 5

5. Demande au user (Option E) : c'est lui qui screenshotte
```

---

## Option A — `agent-browser` (recommandée)

### Pré-requis

```bash
which agent-browser
# /c/Users/Fabrice/AppData/Roaming/npm/agent-browser
```

Si non installé : `npm install -g agent-browser` (CLI Rust + Chromium embedded).

### Capture screenshot fullpage

```bash
# Naviguer + screenshot fullpage
agent-browser navigate "https://site.com/slug/" --viewport 1440,900
agent-browser screenshot --full "./screenshots/page-fullpage.png"
```

### Capture screenshot viewport (above-the-fold)

```bash
agent-browser navigate "https://site.com/slug/" --viewport 1440,900
agent-browser screenshot "./screenshots/page-hero.png"
```

### Capture viewport mobile

```bash
agent-browser navigate "https://site.com/slug/" --viewport 375,812
agent-browser screenshot --full "./screenshots/page-mobile.png"
```

### Capture avec annotations (refs interactifs)

```bash
agent-browser screenshot --annotate "./screenshots/page-annotated.png"
# Affiche un legend mappant les refs @e1, @e2... aux éléments interactifs
```

### Capture site protégé par auth

`agent-browser` peut utiliser un session existant. Pour un WP draft/preview, il faut être loggé :

```bash
# Setup session une fois (Chrome ouvert connecté à WP admin)
agent-browser login "https://site.com/wp-admin/" --session=wp-admin

# Réutiliser
agent-browser --session=wp-admin navigate "https://site.com/?p=42&preview=true"
agent-browser --session=wp-admin screenshot --full "./preview.png"
```

---

## Option B — Chrome headless CLI

### Pré-requis

Chrome ou Chromium installé. Tester :

```bash
chrome --version
# Google Chrome 130.0.6723.118
```

Sur Windows, le binaire est typiquement à `C:\Program Files\Google\Chrome\Application\chrome.exe`.

### Capture screenshot fullpage

```bash
chrome \
  --headless=new \
  --disable-gpu \
  --hide-scrollbars \
  --window-size=1440,900 \
  --screenshot="$PWD/page.png" \
  "https://site.com/slug/"
```

⚠️ `--screenshot` capture **viewport seulement**, pas fullpage. Pour fullpage, utiliser un script Puppeteer / Playwright (Option C).

### Capture page WP draft (auth required)

WordPress drafts demandent un nonce de preview, plus délicat. 2 sous-options :

#### Sous-option B.1 — Page publish temporaire

```bash
# 1. Publier la page temporairement
curl -X POST -u "admin:APP_PASS" \
  "https://site.com/wp-json/wp/v2/pages/{ID}" \
  -H "Content-Type: application/json" \
  -d '{"status":"publish"}'

# 2. Hit le slug (cache + render full)
curl -s "https://site.com/{slug}/" -o /tmp/page.html

# 3. Screenshot via chrome
chrome --headless=new --window-size=1440,900 \
  --screenshot="$PWD/page.png" \
  "https://site.com/{slug}/"

# 4. Re-passer en draft
curl -X POST -u "admin:APP_PASS" \
  "https://site.com/wp-json/wp/v2/pages/{ID}" \
  -d '{"status":"draft"}'
```

⚠️ Si site SEO indexé : la page est **indexable pendant le temps publish**. Si tu veux éviter ça, ajouter `meta robots noindex` via Yoast/RankMath avant de publish, OU utiliser sous-option B.2.

#### Sous-option B.2 — Preview avec session cookie

```bash
# 1. Login admin pour récupérer cookies
chrome --user-data-dir="/tmp/wp-session" \
  "https://site.com/wp-admin/"
# (login manuel une fois)

# 2. Récupérer le preview link via REST API
curl -s -u "admin:APP_PASS" \
  "https://site.com/wp-json/wp/v2/pages/{ID}?context=edit&_fields=preview_link" \
  | python -c "import json,sys;d=json.load(sys.stdin);print(d['preview_link'])"
# → https://site.com/?p=42&preview_id=42&preview_nonce=abc&preview=true

# 3. Screenshot avec session cookie
chrome --user-data-dir="/tmp/wp-session" \
  --headless=new --window-size=1440,900 \
  --screenshot="$PWD/preview.png" \
  "https://site.com/?p=42&preview_nonce=abc&preview=true"
```

---

## Option C — Playwright

### Pré-requis

```bash
npx playwright --version
# Version 1.59.1
```

Si non installé : `npx playwright install chromium`.

### Capture screenshot fullpage avec script Node

Créer un fichier `screenshot.mjs` :

```javascript
import { chromium } from 'playwright';

const browser = await chromium.launch();
const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
const page = await context.newPage();
await page.goto(process.argv[2]);
await page.waitForLoadState('networkidle');
await page.screenshot({ path: process.argv[3], fullPage: true });
await browser.close();
console.log('Saved:', process.argv[3]);
```

```bash
node screenshot.mjs "https://site.com/slug/" "./page-fullpage.png"
```

### Avec auth (Application Password)

```javascript
import { chromium } from 'playwright';

const browser = await chromium.launch();
const context = await browser.newContext({
  viewport: { width: 1440, height: 900 },
  httpCredentials: { username: 'admin', password: 'APP PASS' }
});
const page = await context.newPage();
await page.goto(process.argv[2]);
await page.waitForLoadState('networkidle');
await page.screenshot({ path: process.argv[3], fullPage: true });
await browser.close();
```

---

## Option D — WP Playground (jetable)

Si le site est **un Playground public** (URL `https://playground.wordpress.net/?id=...`), tu peux le capturer directement comme une URL publique.

```bash
agent-browser navigate "https://playground.wordpress.net/?id=xyz123"
agent-browser screenshot --full "./playground-page.png"
```

Avantage : pas d'auth requise, page publique. Inconvénient : cache navigateur Playground variable, parfois la page met 5-10s à fully load → ajouter `--wait 5000` ou similaire si l'option est dispo.

---

## Option E — DEMANDER AU USER (fallback)

Si **aucune** des options A-D n'est disponible (pas de tooling installé, pas de Playground, restrictions environnement), tu DOIS demander au user.

### Format de demande recommandé

```markdown
Page créée et publiée en draft. ID 42, slug `/contact/`.

Je n'ai pas pu capturer un screenshot moi-même (pas de tooling visuel disponible
dans mon environnement actuel). **Avant de te confirmer que la page est OK
visuellement**, j'ai besoin que tu m'aides :

**Option 1 — Screenshot via aperçu Gutenberg (le plus simple)** :
1. Ouvre l'URL d'édition : http://site.com/wp-admin/post.php?post=42&action=edit
2. Clique sur le bouton « Aperçu » (ou Cmd/Ctrl+P)
3. Capture la page complète (Win+Shift+S sur Windows, Cmd+Shift+5 sur Mac)
4. Drag-drop l'image dans cette conversation

**Option 2 — Screenshot via DevTools fullpage (Chrome)** :
1. Ouvre l'URL : http://site.com/?p=42&preview=true
2. F12 (DevTools), Ctrl+Shift+P, tape `Capture full size screenshot`, Entrée
3. Drag-drop l'image dans cette conversation

**Option 3 — Tu publies la page et tu me partages l'URL** :
1. Tu publies la page en frontend
2. Tu me donnes l'URL et je peux fetcher le HTML pour vérifier la structure
   (mais pas la qualité visuelle)

Sans un de ces 3, je peux te confirmer que **techniquement** la page est OK
(pre-flight 0 P0/P1, 24 quirks verts, 50+ blocs validés), mais je ne peux pas
te garantir que **visuellement** c'est WOW. Si tu veux, je peux quand même
te livrer la page comme « composition non vérifiée visuellement, à juger par
toi ».
```

⚠️ **Cette demande est NON-NÉGOCIABLE** si tu n'as aucun tooling. Cf règle 1 SKILL.md : tu **ne peux pas** qualifier une page de WOW sans preuve visuelle.

---

## Validation post-screenshot

Une fois le screenshot obtenu (option A/B/C/D ou via user en E), **avant** de claim succès :

### Checklist visuelle minimum (à examiner sur le screenshot)

- [ ] **Pas de Lorem Ipsum** dans la FAQ ou autre section (cf quirk #3)
- [ ] **Pas de `201C` littéral** dans les watermarks (cf quirk #21)
- [ ] **Pas de double H1** visible (1 seul H1 visuel — cf quirks #13/#24)
- [ ] **Pas de stats empilées** verticalement quand attendu en row (cf quirk #2)
- [ ] **Pas de 3 cards avec même icône** (cf quirk #8)
- [ ] **Pas d'overlay opacity > 0.85** rendant l'image invisible (cf quirk #9)
- [ ] **Hierarchy typo cohérente** : H1 le plus grand, eyebrow plus petit que H1, body lisible
- [ ] **Pas de saturation 3+ accents identiques** dans une section (cf `visual-pitfalls.md` #5)
- [ ] **Mobile** : pas de débordement horizontal, padding latéral OK
- [ ] **Couleurs** : aucune section avec contraste < 4.5:1 (WCAG AA)

Si **un seul** check ❌ → corriger AVANT de claim succès.

### Comparer avec une baseline

Si la page suit un template committed (e.g. `templates/page-accueil.md`), comparer le screenshot avec les `screenshots/` baselines associés :

```bash
diff <(stat -c%s screenshots/page-accueil-baseline.png) <(stat -c%s ./current.png)
# Si écart > 30% → quelque chose a changé visuellement, investiguer
```

---

## Quand tu n'as DÉFINITIVEMENT pas de screenshot

Si après tentative options A-E, tu n'as toujours pas de screenshot, tu DOIS dans ta réponse au user :

1. **Ne pas qualifier** la page de WOW / impeccable / propre / éditorial / beau
2. **Lister** ce que tu as fait (markup généré, CSS overrides, blocs créés, pre-flight OK)
3. **Lister** ce que tu n'as PAS pu vérifier (rendu visuel, hiérarchie typo perçue, saturation accents)
4. **Demander explicitement** au user de valider visuellement

Format de réponse :

```markdown
✅ **Tâches techniques accomplies**
- 54 blocs uagb générés et validés (pre-flight STATUS OK, 24 quirks verts)
- CSS overrides scopé sur slug `{slug}` (8.5 KB)
- POST page draft réussi (ID {ID})
- Mu-plugin compagnon vérifié, workarounds Quirks #23 + #24 actifs

⚠️ **Composition NON VÉRIFIÉE VISUELLEMENT**
Je n'ai pas pu screenshotter la page. Je ne peux donc pas te confirmer que la
hiérarchie typo, les accents couleur, le rendu mobile et les détails design
sont WOW.

**Action requise de ta part** :
👉 Ouvre l'URL : http://site.com/{slug}/
👉 Si quelque chose ne va pas (typo trop petite, accent saturé, layout cassé,
   image absente, etc.), dis-le moi et on itère.
👉 Si tu vois un défaut, je préfère que tu screenshottes avant qu'on continue —
   évite-moi de partir dans une 2e direction sans validation.
```

---

## Logs / debug screenshot

Si screenshot foire :

```bash
# Vérifier que la page existe bien
curl -I "https://site.com/{slug}/"
# 200 OK attendu

# Vérifier que la page contient les classes attendues
curl -s "https://site.com/{slug}/" | grep -c "uagb-block-{slug}-"
# > 0 attendu

# Vérifier que <style id="uagb-style-frontend-X"> est dans <head>
curl -s "https://site.com/{slug}/" | grep -c "uagb-style-frontend-{post_id}"
# 1 attendu (sinon Quirk #23 actif → installer mu-plugin)
```

Si pre-flight + curl checks OK mais le screenshot rate quand même, c'est probablement :
- Tooling pas correctement installé (relancer `npm install -g agent-browser`)
- Permissions firewall/proxy local (whitelister localhost:80, 443)
- WP local (Local by Flywheel) endpoint pas démarré (vérifier `localhost.local` ping)

---

## Résumé tldr

1. **Toujours** screenshot avant de claim WOW (règle 1 SKILL.md)
2. Préférence : `agent-browser` > Chrome headless > Playwright > Playground > demander au user
3. Si rien ne marche : qualifier de **« composition non vérifiée visuellement »** + demander au user de valider
4. **Jamais** claim WOW sans preuve visuelle. Jamais.