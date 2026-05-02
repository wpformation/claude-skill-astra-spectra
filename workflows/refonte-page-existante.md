# Workflow : Refonte d'une page existante (Killer Feature 2)

> **Promesse** : l'utilisateur pointe une URL ou un ID de page existante, le skill récupère le contenu, l'analyse, et propose une reconstruction Spectra moderne en respectant le contenu original.

## Exemple de prompt

```
Modernise /a-propos/ de mon site
Site : https://monsite.com
Application Password : xxxx xxxx xxxx xxxx
Style cible : moderne avec glassmorphism, palette WPF orange
```

## Étapes du workflow

### Étape 1 — Détection environnement

Idem que `new-page-from-brief.md`. Vérifier Spectra présent + Astra optionnel.

### Étape 2 — Snapshot de la page existante

Exécuter `scripts/snapshot-page.php` avec l'URL ou l'ID :

```bash
# Par ID
curl 'https://{{SITE_URL}}/wp-json/astra-spectra/v1/snapshot/123' \
  -u 'admin:{{APP_PASSWORD}}'

# Par slug → résolution ID via REST API standard
curl 'https://{{SITE_URL}}/wp-json/wp/v2/pages?slug=a-propos' \
  -u 'admin:{{APP_PASSWORD}}'
```

Résultat :

```json
{
  "id": 123,
  "title": "À propos",
  "slug": "a-propos",
  "content_raw": "<!-- wp:paragraph -->...<!-- /wp:paragraph -->",
  "block_count": 8,
  "block_inventory": { "core/paragraph": 5, "core/heading": 3 },
  "uses_spectra": false,
  "featured_image": { ... }
}
```

### Étape 3 — Analyse sémantique du contenu

Parser le `content_raw` pour extraire :

1. **Sections** identifiées par les H2 (titre = nom de section)
2. **Sous-sections** identifiées par les H3
3. **Paragraphes** par section
4. **Médias** (images, embeds vidéo)
5. **Listes** (ul/ol)
6. **Citations** (blockquotes)
7. **Tableaux**

Construire un arbre logique :

```yaml
page_structure:
  hero:
    headline: "À propos de notre agence"
    subline: "Découvrez notre équipe et notre histoire"
  sections:
    - title: "Notre histoire"
      content: ["paragraphe 1", "paragraphe 2"]
      media: ["url-image.jpg"]
    - title: "Notre équipe"
      content: ["bio Jean", "bio Marie", "bio Paul"]
      type: "team"  # détecté par mot-clé "équipe"
    - title: "Nos valeurs"
      content: ["valeur 1", "valeur 2", "valeur 3"]
      type: "features"  # détecté par enum + court
```

### Étape 4 — Mapping sections → patterns Spectra

Pour chaque section, mapper vers le pattern adapté :

| Section détectée | Pattern recommandé |
|------------------|-------------------|
| Headline + lead | `patterns/hero-cta-split.md` ou variante centered |
| Liste de N items courts | `patterns/features-3-cols.md` (si N=3) ou variante 4 cols |
| Présentation équipe | `patterns/team-grid.md` |
| Témoignages clients | `patterns/testimonials-grid.md` |
| Stats / chiffres | `patterns/stats-counters.md` |
| Timeline / chronologie | pattern `timeline-vertical.md` (futur) |
| FAQ | `patterns/faq-accordion.md` |
| Texte long éditorial | `patterns/article-content-rich.md` |
| CTA conversion | `patterns/cta-banner-fullwidth.md` |

### Étape 5 — Reconstruction du markup

Pour chaque section, remplir le pattern avec le contenu original :

- Le texte source est conservé tel quel (pas de réécriture sans demande explicite)
- Les médias (images) sont conservés (URLs identiques)
- Les liens externes sont préservés
- La structure des H2/H3 est respectée (mais peut être enrichie)

### Étape 6 — POST en mode CLONE (pas overwrite)

⚠️ **NE JAMAIS écraser** la page originale. Toujours créer un clone draft :

```bash
curl -X POST 'https://{{SITE_URL}}/wp-json/wp/v2/pages' \
  -u 'admin:{{APP_PASSWORD}}' \
  -H 'Content-Type: application/json' \
  -d '{
    "title": "À propos (refonte)",
    "slug": "a-propos-refonte",
    "status": "draft",
    "content": "{{NEW_MARKUP}}",
    "parent": 123
  }'
```

Le clone hérite du parent (relation page-builder) et est en `status: draft` pour validation.

### Étape 7 — Diff & récap

Output structuré pour l'utilisateur :

```markdown
✅ Refonte créée en draft

**Original** : `/a-propos/` (ID 123)
**Refonte** : `/a-propos-refonte/` (ID 124, draft)

**Changements appliqués** :
- 5 paragraphes core → conservés tels quels
- 1 section « Notre équipe » → remplacée par `uagb/team` (3 cards avec photos)
- 1 section « Nos valeurs » → remplacée par `uagb/info-box` (3 cards)
- 1 hero section → ajouté avec gradient `uagb/container`
- 1 CTA fin de page → ajouté

**Comparaison visuelle** :
- Original : https://{{SITE_URL}}/a-propos/
- Refonte : https://{{SITE_URL}}/?page_id=124&preview=true

**Étapes suivantes** :
1. Compare les deux URLs
2. Si OK : remplace l'original (publier le clone + 301 redirect ou simplement update content du original)
3. Si à ajuster : reprends la conversation pour faire évoluer la refonte
```

### Étape 8 (optionnelle) — Migration vers original

Si l'utilisateur valide la refonte et veut écraser l'original :

```
Tu confirmes que je remplace le contenu de /a-propos/ (ID 123) par celui de la refonte ?
[Oui, remplace] [Non, garde les deux] [Annule]
```

Si oui :

```bash
# 1. Backup de l'original (au cas où)
curl 'https://{{SITE_URL}}/wp-json/wp/v2/pages/123' > backup-a-propos-{{DATE}}.json

# 2. Update du content de l'original
curl -X POST 'https://{{SITE_URL}}/wp-json/wp/v2/pages/123' \
  -u 'admin:{{APP_PASSWORD}}' \
  -H 'Content-Type: application/json' \
  -d '{ "content": "{{NEW_MARKUP}}" }'

# 3. Suppression du clone
curl -X DELETE 'https://{{SITE_URL}}/wp-json/wp/v2/pages/124?force=true' \
  -u 'admin:{{APP_PASSWORD}}'
```

## Cas d'erreur

### Page introuvable

→ Vérifier que `slug` ou `id` existe. L'utilisateur peut s'être trompé.

### Page contient du HTML legacy non Gutenberg

→ Page créée avant Gutenberg ou avec un autre page builder (Elementor, Divi, Beaver). Le snapshot extraira le HTML mais l'analyse sera plus basique. Proposer une reconstruction « from scratch » en utilisant les éléments textuels comme inputs.

### Page a déjà des blocs Spectra

→ Le snapshot le détecte (`uses_spectra: true`). Ne pas tout refaire — analyser quelles sections peuvent être améliorées (ex: hero pas contemporain, CTA absent, etc.) et proposer un diff ciblé.

### Page a 50+ blocs (très longue)

→ Demander confirmation avant refonte (« cette page fait X mots, ça va prendre N minutes, on continue ? »). Préférer le mode incrémental (refondre section par section).

## Règles strictes

- ⚠️ **JAMAIS d'écrasement direct** sans confirmation explicite
- ✅ **Toujours en clone draft** avec `parent: {{ORIGINAL_ID}}` pour traçabilité
- ✅ **Backup JSON** systématique de l'original avant écrasement final
- ✅ **Préserver les URLs externes** (liens vers source)
- ✅ **Préserver les médias** (URLs images, IDs WordPress)
- ⚠️ **Demander avant de réécrire le texte** (par défaut on conserve)

## Pour aller plus loin

- Killer feature 1 : `new-page-from-brief.md`
- Killer feature 3 : `deploy-template.md`
- Snapshot script : `../scripts/snapshot-page.php`
- Patterns disponibles : `../patterns/`
