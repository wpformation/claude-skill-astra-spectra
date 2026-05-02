# Pattern : Inline Notice (uagb/inline-notice)

> **Use case** : alerte / callout / note importante intercalée dans un article éditorial. 5 variantes : `info` (bleu), `success` (vert), `warning` (orange), `error` (rouge), `neutral` (gris). Inclut option « dismissible » (croix de fermeture avec cookie de mémorisation).

> **Bloc Spectra** : `uagb/inline-notice`. Plus stylisé qu'une simple `core/group` avec classe CSS custom. Schema attendu pour les contenus pédagogiques (Note de l'auteur, Avertissement, Astuce).

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{NOTICE_TYPE}}` | `info` / `success` / `warning` / `error` / `neutral` | `info` |
| `{{NOTICE_TITLE}}` | Titre court (optionnel) | `Astuce` |
| `{{NOTICE_BODY}}` | Contenu (HTML autorisé) | `Pour gagner du temps, utilise...` |
| `{{NOTICE_DISMISSIBLE}}` | Afficher croix de fermeture | `false` |
| `{{NOTICE_ICON}}` | Icône custom (optionnel, override default par type) | `lightbulb` |

## Block markup

```html
<!-- wp:uagb/inline-notice {"block_id":"{slug}-notice","noticeAlignment":"left","noticeTitle":"{{NOTICE_TITLE}}","noticeContent":"{{NOTICE_BODY}}","noticeDismiss":{{NOTICE_DISMISSIBLE}},"layout":"modern","cookies":{{NOTICE_DISMISSIBLE}},"closeIcon":"times","mainTitleColor":"#0F172A","mainDescColor":"#454F5E","mainTitleFontSizeDesktop":18,"mainTitleFontWeight":"800","mainDescFontSizeDesktop":15,"mainDescLineHeightDesktop":1.6,"contentBgColor":"#EFF6FF","titleBorderHColor":"#3B82F6","mainContentVrPadding":20,"mainContentHrPadding":24,"borderRadius":12,"borderRadiusUnit":"px","borderTopWidth":0,"borderRightWidth":0,"borderBottomWidth":0,"borderLeftWidth":4,"borderStyle":"solid","borderColor":"#3B82F6"} -->
<div class="wp-block-uagb-inline-notice uagb-block-{slug}-notice uagb-inline-notice-{{NOTICE_TYPE}}">
  <div class="uagb-notice-title-wrap">
    <div class="uagb-notice-icon"><i class="fas fa-info-circle" aria-hidden="true"></i></div>
    <div class="uagb-notice-title">{{NOTICE_TITLE}}</div>
  </div>
  <div class="uagb-notice-text"><p>{{NOTICE_BODY}}</p></div>
</div>
<!-- /wp:uagb/inline-notice -->
```

## CSS overrides recommandés (5 variantes par type)

```css
/* Container — radius + border-left accent */
.uagb-block-{slug}-notice {
  border-radius: 12px !important;
  border-left: 4px solid !important;
  padding: 20px 24px !important;
  display: flex !important;
  gap: 16px !important;
  align-items: flex-start !important;
  margin: 24px 0 !important;
}

/* Icône — taille + couleur synchro avec type */
.uagb-block-{slug}-notice .uagb-notice-icon {
  flex-shrink: 0 !important;
  font-size: 24px !important;
  line-height: 1 !important;
  margin-top: 2px !important;
}

/* Title — bold uppercase tracking petit */
.uagb-block-{slug}-notice .uagb-notice-title {
  font-size: 14px !important;
  font-weight: 800 !important;
  text-transform: uppercase !important;
  letter-spacing: 1.5px !important;
  margin: 0 0 6px !important;
}

/* Body — readable, line-height généreux */
.uagb-block-{slug}-notice .uagb-notice-text p {
  font-size: 15px !important;
  line-height: 1.6 !important;
  margin: 0 !important;
}

/* === Variantes par type === */

/* INFO — bleu */
.uagb-inline-notice-info {
  background: #EFF6FF !important;
  border-left-color: #3B82F6 !important;
  color: #1E40AF !important;
}
.uagb-inline-notice-info .uagb-notice-icon { color: #3B82F6 !important; }

/* SUCCESS — vert */
.uagb-inline-notice-success {
  background: #F0FDF4 !important;
  border-left-color: #16A34A !important;
  color: #14532D !important;
}
.uagb-inline-notice-success .uagb-notice-icon { color: #16A34A !important; }

/* WARNING — orange */
.uagb-inline-notice-warning {
  background: #FFFBEB !important;
  border-left-color: #F59E0B !important;
  color: #78350F !important;
}
.uagb-inline-notice-warning .uagb-notice-icon { color: #F59E0B !important; }

/* ERROR — rouge */
.uagb-inline-notice-error {
  background: #FEF2F2 !important;
  border-left-color: #DC2626 !important;
  color: #7F1D1D !important;
}
.uagb-inline-notice-error .uagb-notice-icon { color: #DC2626 !important; }

/* NEUTRAL — gris */
.uagb-inline-notice-neutral {
  background: #F9FAFB !important;
  border-left-color: #9CA3AF !important;
  color: #374151 !important;
}
.uagb-inline-notice-neutral .uagb-notice-icon { color: #6B7280 !important; }

/* Title color override par type (vs color body) */
.uagb-inline-notice-info .uagb-notice-title { color: #1E40AF !important; }
.uagb-inline-notice-success .uagb-notice-title { color: #14532D !important; }
.uagb-inline-notice-warning .uagb-notice-title { color: #78350F !important; }
.uagb-inline-notice-error .uagb-notice-title { color: #7F1D1D !important; }
.uagb-inline-notice-neutral .uagb-notice-title { color: #111827 !important; }
```

## Mapping icône recommandée par type

| Type | Icône Spectra |
|---|---|
| `info` | `info-circle` |
| `success` | `check-circle` |
| `warning` | `exclamation-triangle` |
| `error` | `times-circle` |
| `neutral` | `comment-alt` ou `lightbulb` |

## Pièges

| # | Quirk |
|---|---|
| **Type sans CSS** | Le `noticeType` dans les attrs Spectra ne change pas automatiquement la couleur. Tu DOIS écrire le CSS de chaque variante (cf section précédente) sinon toutes les notices ont le même style |
| **Couleurs hardcodées** | Les couleurs des notices (bleu info, vert success, etc.) sont des **conventions universelles** — pas de palette Astra. Garder `#3B82F6` blue-500 / `#16A34A` green-600 / `#F59E0B` amber-500 / `#DC2626` red-600 partout |
| **Dismissible cookie** | Si `noticeDismiss: true`, Spectra pose un cookie pour mémoriser que l'utilisateur a fermé la notice. Sur GDPR strict (sans consentement préalable), c'est illégal. Soit ne pas activer, soit attendre le consent |
| **Inline dans paragraphe** | `uagb/inline-notice` est un bloc full-width par défaut. Pas pour insérer en milieu de paragraphe. Pour highlight inline, utiliser `<mark>` ou un `<span class="highlight">` |
| **Markdown body** | Le body accepte du HTML mais pas du markdown. Si le contenu source est en markdown (`**bold**`), le convertir en HTML (`<strong>bold</strong>`) avant injection |

## Variantes

### Variante 1 — Astuce pédagogique (info)

```
ℹ️ ASTUCE
Tu peux gagner 30 minutes par semaine en automatisant la sauvegarde
de tes brouillons avec un cron Vercel.
```

### Variante 2 — Avertissement sécurité (warning)

```
⚠️ ATTENTION
Cette commande supprime définitivement la base de données. Toujours
faire un backup avant.
```

### Variante 3 — Confirmation succès (success)

```
✅ MIS À JOUR LE 02/05/2026
Cet article a été refondu avec les dernières informations Spectra
v2.19.25.
```

### Variante 4 — Erreur connue (error)

```
❌ BUG CONNU
Cette technique ne fonctionne pas avec Spectra < 2.10. Vérifie ta
version dans WP Admin > Plugins.
```

### Variante 5 — Note de l'auteur (neutral)

```
✏️ NOTE DE L'AUTEUR
J'ai testé cette approche sur 8 sites clients en 2025-2026. Sur 7
elle marche, sur 1 elle a planté pour cette raison.
```

## Test post-génération

1. Vérifier que la notice se rend avec la bonne couleur (info bleu / success vert / etc.)
2. Vérifier l'icône correspondante (info-circle, check-circle, etc.)
3. Si dismissible : cliquer croix → notice disparaît, recharger la page → notice reste cachée (cookie OK)
4. Mobile : padding réduit, mais lisibilité OK
5. a11y : couleur + icône (pas seulement couleur). Lecteurs d'écran lisent « Information : ... »

## Pour aller plus loin

- Modal full-screen au lieu de notice inline : `patterns/modal.md`
- Article éditorial avec notices intercalées : `patterns/article-content-rich.md`
- Toast notifications (auto-dismiss) : nécessite plugin tiers (out of scope)
