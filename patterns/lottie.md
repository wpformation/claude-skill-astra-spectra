# Pattern : Lottie (uagb/lottie)

> **Use case** : intégrer une animation vectorielle JSON Lottie (After Effects → Bodymovin export). Idéal pour hero animé, illustration explicative, mascotte, success animation post-formulaire.

> **Bloc Spectra** : `uagb/lottie`. Wrapper le player Lottie-web avec contrôles autoplay / loop / hover-to-play / scroll-trigger. Format `.json` ou `.lottie` (compressé).

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{LOTTIE_URL}}` | URL du fichier .json (depuis lottiefiles.com ou auto-hosté) | `https://assets.lottiefiles.com/.../data.json` |
| `{{LOTTIE_WIDTH}}` | Largeur container en px | `400` |
| `{{LOTTIE_HEIGHT}}` | Hauteur container en px | `400` |
| `{{LOTTIE_AUTOPLAY}}` | Démarrer au chargement | `true` |
| `{{LOTTIE_LOOP}}` | Boucler indéfiniment | `true` |
| `{{LOTTIE_TRIGGER}}` | `none` / `hover` / `click` / `scroll` | `none` (autoplay loop) |
| `{{LOTTIE_SPEED}}` | Multiplicateur vitesse | `1.0` (default) |

## Block markup

```html
<!-- wp:uagb/lottie {"block_id":"{slug}-lottie","url":"{{LOTTIE_URL}}","heightDesktop":{{LOTTIE_HEIGHT}},"heightTablet":{{LOTTIE_HEIGHT}},"heightMobile":280,"widthDesktop":{{LOTTIE_WIDTH}},"widthTablet":{{LOTTIE_WIDTH}},"widthMobile":280,"loop":{{LOTTIE_LOOP}},"autoplay":{{LOTTIE_AUTOPLAY}},"playOnHover":false,"playOnViewport":false,"speed":{{LOTTIE_SPEED}},"reverse":false,"backgroundColor":"transparent","alignDesktop":"center"} -->
<div class="wp-block-uagb-lottie uagb-block-{slug}-lottie" align="center">
  <div class="uagb-lottie__inner-wrap" style="width:{{LOTTIE_WIDTH}}px;height:{{LOTTIE_HEIGHT}}px">
    <!-- Player JS injecté par Spectra au render -->
  </div>
</div>
<!-- /wp:uagb/lottie -->
```

## CSS overrides recommandés

```css
/* Lottie container — centré, padding optionnel */
.uagb-block-{slug}-lottie {
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  margin: 24px 0 !important;
}

.uagb-block-{slug}-lottie .uagb-lottie__inner-wrap {
  max-width: 100% !important;
}

/* Mobile : largeur réduite proportionnellement */
@media (max-width: 600px) {
  .uagb-block-{slug}-lottie .uagb-lottie__inner-wrap {
    width: 280px !important;
    height: 280px !important;
  }
}
```

## Pièges

| # | Quirk |
|---|---|
| **Format URL** | URL `.json` directe (lottiefiles.com → bouton « Use Lottie URL »). PAS l'URL de la page lottiefiles. Ex valide : `https://assets10.lottiefiles.com/packages/lf20_xyz/data.json` |
| **Format `.lottie`** | Le format `.lottie` (binaire compressé, depuis 2023) est plus léger (-70%) mais demande le player `dotlottie-player` (différent de `lottie-web`). Spectra utilise `lottie-web` par défaut → préférer `.json` pour compatibilité |
| **Performance** | Lottie peut être lourd (200-800 KB JSON). Si > 500 KB, considérer une vidéo MP4 transparente (smaller) ou un GIF (low quality). Lighthouse pénalise les Lottie > 1 MB |
| **Auto-host** | Préférer auto-hoster le `.json` sur ton serveur (média library WP) pour vitesse + privacy. CDN lottiefiles.com peut être lent / bloqué selon géo |
| **Autoplay sans interaction** | Sur mobile, certains browsers (iOS Safari) bloquent l'autoplay des Lottie animées. Combiner avec `playOnViewport: true` (déclenche au scroll-into-view) |
| **Accessibilité** | Animation décorative → `role="presentation"` ou `aria-hidden="true"`. Animation porteuse de sens → ajouter une description textuelle équivalente |
| **`prefers-reduced-motion`** | Respecter la préférence user. CSS : `@media (prefers-reduced-motion: reduce) { .uagb-lottie { display: none; } }` ou freeze le player au premier frame |

## Sources d'animations Lottie

| Source | Notes |
|---|---|
| [LottieFiles.com](https://lottiefiles.com) | Le plus large catalogue. Gratuit + premium. Filtre par catégorie, couleur, durée |
| [IconScout](https://iconscout.com/lotties) | Très bon pour business / SaaS. Premium |
| [LordIcon](https://lordicon.com) | Mini-icônes animées (16-128px). Premium |
| Custom (After Effects + Bodymovin) | Pour brand identity unique. Designer requis |

## Variantes

### Variante 1 — Hero illustration animée (autoplay loop)

Animation full largeur dans un hero, à côté du texte H1 + CTA. Format 16:10 ou 1:1, taille 500×500 desktop. Autoplay + loop infini.

Cas d'usage : SaaS landing avec illustration produit, formation avec mascotte explicative.

### Variante 2 — Success animation post-form (autoplay no-loop)

Après soumission de formulaire, afficher une animation succès (cocher animé, fusée qui décolle, confettis). 2-3 secondes, ne boucle pas.

```json
{
  "autoplay": true,
  "loop": false,
  "speed": 1.2
}
```

### Variante 3 — Hover-to-play icon (hover trigger)

Icône statique au repos, animation au hover. Pour cards features.

```json
{
  "autoplay": false,
  "loop": false,
  "playOnHover": true
}
```

Composer 3-4 icon Lottie côte à côte dans un container row pour effet « features 3-cols animées ».

### Variante 4 — Scroll-triggered (au scroll-into-view)

Animation déclenchée quand le bloc entre dans le viewport. Utile pour explainer scroll-driven.

```json
{
  "autoplay": false,
  "loop": false,
  "playOnViewport": true
}
```

### Variante 5 — Background ambient loop

Animation ambient en background d'une section (particules, vagues, gradient animé). Trop coûteux ? Préférer un MP4 transparent (`.webm` avec alpha) ou un CSS gradient animé.

Cf `modules/spectra/container-wow-recipes.md` pour alternatives CSS sans JS.

## Test post-génération

1. Vérifier que l'animation charge bien (pas d'erreur 404 sur le `.json`)
2. Vérifier l'autoplay si `autoplay: true`
3. Tester le loop si `loop: true`
4. Mobile : vérifier que l'animation tient dans 280×280 et reste fluide
5. Lighthouse : vérifier l'impact sur LCP / TBT (si > 100ms, considérer alternative)
6. a11y : si décoratif, `aria-hidden="true"`. Si porteur de sens, alternative texte.
7. `prefers-reduced-motion` : mettre une CSS qui freeze l'animation pour les users sensibles

## Pour aller plus loin

- Container hero avec Lottie + texte : composer hero-cta-split + uagb/lottie
- Vidéo MP4 transparente comme alternative : utiliser `core/video` avec ratio
- Animations CSS only : voir `modules/spectra/container-wow-recipes.md`
- Skill compagnon HyperFrames pour vidéos sociales animées : voir https://github.com/wpformation
