# Pattern : How-To Steps (avec schema HowTo SEO)

> **Use case** : tutoriel pas-à-pas, processus en N étapes, recipe-like content. Spectra `uagb/how-to` génère **automatiquement le schema HowTo JSON-LD** pour le SEO + des étapes numérotées avec image et description.

> **Avantage SEO** : Google peut afficher tes étapes en rich result avec images. Très visible dans la SERP.

## Bloc Spectra utilisé : `uagb/how-to`

## Structure

```
uagb/container#howto-section (root, alignfull, padding 120px, bg #ffffff)
  ├─ uagb/info-box#howto-title (eyebrow + H2 + desc center)
  └─ uagb/how-to#howto (mainHeading, mainDesc, totalTime, supplies[], tools[], steps[])
```

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{HOWTO_EYEBROW}}` | Kicker | `Comment faire` |
| `{{HOWTO_TITLE}}` | Titre principal H2 | `Comment r&eacute;ussir l&rsquo;&eacute;preuve E5A en 5 &eacute;tapes` |
| `{{HOWTO_DESC}}` | Description introductive | `M&eacute;thode test&eacute;e par 3000 anciens &eacute;tudiants...` |
| `{{TOTAL_TIME}}` | Temps total estimé (PT2H format ISO 8601) | `PT2H` (= 2 heures) |
| `{{TOTAL_COST}}` | Coût (optionnel) | `0` (gratuit) |
| `{{CURRENCY}}` | Devise | `EUR` |
| `{{SUPPLIES_LIST}}` | Liste fournitures | `Stylo`, `Cahier`, `R&eacute;f&eacute;rentiel BTS` |
| `{{TOOLS_LIST}}` | Liste outils | `Cours sur cours-ndrc.fr`, `QCM en ligne` |
| `{{STEP_N_TITLE}}` | Titre étape | `Lis le cours en entier` |
| `{{STEP_N_DESC}}` | Description étape | `Prends 15 minutes pour lire le cours...` |
| `{{STEP_N_IMAGE_URL}}` | Image étape (optionnel) | ... |

## Block markup (squelette)

```html
<!-- wp:uagb/how-to {"block_id":"{slug}-howto","headingTitle":"Comment r&eacute;ussir l&rsquo;&eacute;preuve E5A","headingDesc":"M&eacute;thode test&eacute;e par 3000 anciens &eacute;tudiants","headingTag":"h2","mainImage":"","timeNeeded":"2 heures","timeISO":"PT2H","estCost":"0","currency":"EUR","showTotaltime":true,"showEstcost":true,"showSupplies":true,"showTools":true,"suppliesHeading":"Mat&eacute;riel n&eacute;cessaire","toolsHeading":"Outils","stepsHeading":"&Eacute;tapes","supplies":[{"name":"Stylo"},{"name":"Cahier"},{"name":"R&eacute;f&eacute;rentiel BTS"}],"tools":[{"name":"Cours sur cours-ndrc.fr"},{"name":"QCM en ligne"}],"steps":[{"name":"Lis le cours en entier","description":"Prends 15 minutes...","image":""},{"name":"Fais l&rsquo;exercice associ&eacute;","description":"Sans regarder la correction...","image":""},{"name":"Compare avec la correction","description":"Note les points o&ugrave; tu as fait des erreurs..."},{"name":"Refais l&rsquo;exercice corrig&eacute;","description":"Pour ancrer la m&eacute;thode..."},{"name":"Valide avec le QCM","description":"Si tu obtiens 80%+, tu es pr&ecirc;t pour le suivant"}],"resultHeading":"Tu es pr&ecirc;t pour l&rsquo;&eacute;preuve","resultDesc":"Tu as r&eacute;vis&eacute; un chapitre complet en 30 minutes."} -->
<div class="wp-block-uagb-how-to uagb-block-{slug}-howto">
  <!-- Spectra render auto -->
</div>
<!-- /wp:uagb/how-to -->
```

## CSS overrides (`_uag_custom_page_level_css`)

```css
/* Steps numbers en orange massif */
.uagb-block-{slug}-howto .uagb-howto__step-wrap .uagb-howto__step-no {
  background-color: {{ACCENT_COLOR}} !important;
  color: #ffffff !important;
  font-size: 24px !important;
  font-weight: 800 !important;
  width: 48px !important;
  height: 48px !important;
}

/* Step heading */
.uagb-block-{slug}-howto .uagb-howto__step-heading {
  font-size: 22px !important;
  font-weight: 700 !important;
  color: #0F172A !important;
}

/* Step description */
.uagb-block-{slug}-howto .uagb-howto__step-desc {
  font-size: 16px !important;
  line-height: 1.7 !important;
  color: #454F5E !important;
}
```

## Schema SEO automatique

Spectra ajoute automatiquement dans le `<head>` un JSON-LD :

```json
{
  "@context": "https://schema.org",
  "@type": "HowTo",
  "name": "Comment réussir l'épreuve E5A...",
  "totalTime": "PT2H",
  "estimatedCost": {"@type": "MonetaryAmount", "currency": "EUR", "value": "0"},
  "supply": [{"@type": "HowToSupply", "name": "Stylo"}, ...],
  "tool": [{"@type": "HowToTool", "name": "Cours..."}, ...],
  "step": [{"@type": "HowToStep", "name": "Lis le cours...", "text": "Prends 15 minutes..."}, ...]
}
```

Validation : test avec [Google Rich Results Test](https://search.google.com/test/rich-results) après publication.

## Variantes

### Variante 1 — Sans supplies/tools (recipe simple)

`showSupplies: false`, `showTools: false`. Juste les étapes.

### Variante 2 — Steps avec images

Chaque step.image = URL d'une mini-illustration de l'étape. Layout devient plus visuel mais nécessite des images.

### Variante 3 — Avec result (résultat final)

Activer `showResult: true` avec `resultHeading` + `resultDesc`. Spectra ajoute une carte « Tu es prêt » à la fin.

## Pièges

- **timeISO format** : doit être en ISO 8601 (`PT2H`, `PT30M`, `P1DT2H` pour 1 jour 2h). Sinon Google rejette le schema.
- **totalCost** : doit être un nombre, pas un texte (`"0"` OK, `"gratuit"` ko).
- **steps array** : limite testée 10 étapes max sans dégradation visuelle. Plus = découper en sous-tutoriels.
- **mainImage** : si absent, utiliser une chaîne vide `""` (pas null), sinon Spectra peut crasher au render.

## Test post-génération

1. Screenshot → étapes numérotées 1, 2, 3, 4, 5 avec headings + descs
2. Vérifier supplies + tools listés
3. View-source → grep `application/ld+json` → JSON-LD HowTo présent
4. [Rich Results Test](https://search.google.com/test/rich-results) → schema valide
5. Test responsive 768 → étapes stack vertical avec numéros toujours visibles
