# Installation — claude-skill-astra-spectra

Guide pas-à-pas pour installer le skill et générer ta première page WordPress en moins de 5 minutes.

## Pré-requis (vérifie avant)

### Côté Claude Code

- [Claude Code](https://docs.claude.com/en/docs/agents-and-tools/claude-code) installé sur ton ordi (CLI ou extension VS Code)
- Compte Anthropic Pro ou Max (recommandé pour des sessions longues)

### Côté site WordPress cible

- WordPress **6.0+** installé (vérifie : Tableau de bord > Mises à jour)
- PHP **7.4+** (vérifie : Outils > Santé du site > Info > Serveur)
- **Spectra plugin** installé et activé : [wordpress.org/plugins/ultimate-addons-for-gutenberg](https://wordpress.org/plugins/ultimate-addons-for-gutenberg/)
- (Optionnel) **Astra theme** activé pour bénéficier du module Customizer
- Permaliens en `/%postname%/` (Réglages > Permaliens)

## Étape 1 — Installer le skill (2 minutes)

### Option A : via git clone (recommandé)

```bash
# Identifie ton dossier skills Claude Code
# Sur Windows :
cd %USERPROFILE%\.claude\skills\

# Sur Mac/Linux :
cd ~/.claude/skills/

# Clone ce repo
git clone https://github.com/wpformation/claude-skill-astra-spectra.git astra-spectra
```

### Option B : via téléchargement ZIP

1. Télécharge le ZIP depuis [GitHub](https://github.com/wpformation/claude-skill-astra-spectra/archive/refs/heads/main.zip)
2. Extrais dans `~/.claude/skills/astra-spectra/` (le dossier doit s'appeler exactement `astra-spectra`)
3. Vérifie que `~/.claude/skills/astra-spectra/SKILL.md` existe

### Option C : symlink depuis un repo local

Si tu as déjà cloné le repo ailleurs :

```bash
# Mac/Linux
ln -s /path/to/your/clone ~/.claude/skills/astra-spectra

# Windows (PowerShell admin)
New-Item -ItemType SymbolicLink -Path "$env:USERPROFILE\.claude\skills\astra-spectra" -Target "C:\path\to\your\clone"
```

## Étape 2 — Générer une Application Password (1 minute)

L'Application Password permet au skill de communiquer avec ton site sans utiliser ton mot de passe principal.

1. Connecte-toi à ton WordPress en admin
2. Va dans **Utilisateurs > Profil**
3. Tout en bas : section **Mots de passe d'application**
4. Nom : `claude-skill-astra-spectra`
5. Clique **Ajouter un nouveau mot de passe d'application**
6. **Copie immédiatement** le password généré (format : `abcd 1234 efgh 5678` avec espaces)

⚠️ Cette valeur ne sera plus visible après la fermeture de la fenêtre. Note-la dans ton gestionnaire de password.

## Étape 3 — Tester la connexion (30 secondes)

Lance Claude Code et invoque le skill avec ces 3 paramètres :

```
> Invoque le skill astra-spectra : détecte mon site https://monsite.com,
> user admin, app password "abcd 1234 efgh 5678".
```

Sous le capot, Claude Code lance `scripts/detect-environment.php` (via `wp eval-file` si tu as WP-CLI local, ou via un endpoint REST si tu as installé le mu-plugin compagnon, ou en téléchargeant le script et en l'exécutant côté hébergeur).

Le skill devrait répondre avec :

```
✅ Profil détecté :
- WordPress 6.9.4
- PHP 8.3.30
- Spectra 2.19.25 ✓
- Astra 4.13.1 ✓
- Theme : Astra
- Verdict : GO

Tu peux générer ta première page.
```

Si tu obtiens **`Verdict : BLOCKED`** :

- **`Spectra not activated`** → Installe-le : WP admin > Extensions > Ajouter > recherche "Spectra"
- **`WordPress < 6.0`** → Mets à jour WordPress
- **`PHP < 7.4`** → Mets à jour PHP côté hébergeur

Si tu obtiens **HTTP 401** :

- Vérifie ton Application Password (recopie-le exactement, espaces compris)
- Vérifie le username (souvent ton email ou ton nom d'utilisateur admin)

## Étape 4 — Générer ta première page (2 minutes)

```
> Crée-moi une page de test simple avec :
> - Hero "Bienvenue sur mon site" + bouton "Découvrir"
> - 3 features (rapidité, qualité, support)
> - FAQ avec 3 questions courantes
> - CTA final "Contactez-nous"
> Site : https://monsite.com (App Password déjà mémorisé)
```

Le skill devrait répondre en 30-90 secondes :

```
✅ Page créée

ID : 42
Title : Bienvenue
Slug : /bienvenue/
Status : draft

URL d'édition : https://monsite.com/wp-admin/post.php?post=42&action=edit
URL de prévisualisation : https://monsite.com/?page_id=42&preview=true

Composition : 12 blocs (8 uagb + 4 core), 0 erreur de parsing.
```

## Étape 5 — Vérifier dans Gutenberg (1 minute)

1. Ouvre l'URL d'édition fournie
2. Tu devrais voir la page complète dans Gutenberg
3. **Aucun warning** « this block contains unexpected or invalid content » ne devrait apparaître
4. Si tout est OK : clique **Publier** pour activer

🎉 **Bravo !** Tu viens de générer ta première page WordPress avec ce skill.

## Troubleshooting

### Le skill n'est pas détecté par Claude Code

- Vérifie que le dossier s'appelle exactement `astra-spectra` (pas `claude-skill-astra-spectra`)
- Vérifie que `SKILL.md` existe à la racine du dossier
- Redémarre Claude Code (CLI ou VS Code extension)

### REST API retourne 404

- Vérifie que les permaliens ne sont pas en `?p=ID` (mode par défaut). Va dans Réglages > Permaliens et choisis `/%postname%/`.

### Le skill génère un block markup invalide

- Vérifie que Spectra est à jour (≥ 2.10) : `wp plugin update ultimate-addons-for-gutenberg --allow-root`
- Ouvre une issue sur le repo avec le prompt + le markup généré

### Cohérence design absente (couleurs hardcoded)

- Si tu n'as pas Astra activé, le skill injecte un CSS fallback. Vérifie qu'il est bien chargé (`wp-head` doit contenir `astra-spectra-skill-design-tokens`)
- Si Astra activé, vérifie que la palette est bien appliquée : `astra-color-palettes` option en BDD doit avoir ta palette

### Performance lente (génération > 3 min)

- Vérifie ta connexion réseau au site cible
- Si gros site (10K+ pages), évite la génération multi-pages ; préfère les générations one-shot
- Désactive temporairement les plugins de cache pendant les tests

## Sécurité

- **Application Password** : ne le partage jamais, ne le commit jamais dans Git, ne l'écris pas dans des fichiers publics
- Crée un **utilisateur dédié** au skill avec un rôle `editor` (pas `administrator`) pour limiter les permissions
- Révoque l'Application Password depuis WP admin si tu n'utilises plus le skill

## Pour aller plus loin

- 📚 [Guide PDF premium](https://wpformation.com/skill-astra-spectra/) — 25-40 pages avec recettes avancées
- 🎨 [Patterns disponibles](patterns/)
- 🎨 [Templates de pages complètes](templates/)
- 💬 Discord WPFormation (lien dans le PDF premium)

---

**Bonne génération !** Si tu rencontres un problème, ouvre une issue sur GitHub.
