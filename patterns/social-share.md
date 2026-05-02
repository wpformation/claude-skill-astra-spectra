# Pattern : Social Share (uagb/social-share)

> **Use case** : boutons de partage sur réseaux sociaux à la fin d'un article ou en sticky sidebar. Génère les URL canonical de partage (Twitter, Facebook, LinkedIn, Reddit, WhatsApp, Email, etc.) avec encoding correct du title + URL.

> **Bloc Spectra** : `uagb/social-share` (parent) + `uagb/social-share-child` (chaque réseau). Layouts : horizontal (par défaut), vertical (pour sidebar sticky).

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{SHARE_LAYOUT}}` | `horizontal` / `vertical` | `horizontal` |
| `{{SHARE_NETWORKS[]}}` | Array de réseaux à inclure | `["twitter","facebook","linkedin","whatsapp","email"]` |
| `{{SHARE_STYLE}}` | `circle` / `square` / `rounded` / `text` | `circle` |
| `{{SHARE_SIZE}}` | Taille en px | `40` |
| `{{SHARE_BG_HOVER}}` | `brand-color` (Twitter bleu, FB bleu) ou `accent` (palette) | `brand-color` |
| `{{SHARE_LABEL}}` | Texte d'invitation au-dessus | `Partager cet article` |

## Block markup

```html
<!-- wp:uagb/social-share {"block_id":"{slug}-share","gap":12,"size":{{SHARE_SIZE}},"sizeUnit":"px","social_layout":"{{SHARE_LAYOUT}}","alignment":"left","stack":"none","shape":"{{SHARE_STYLE}}","borderWidth":0} -->
<div class="wp-block-uagb-social-share uagb-block-{slug}-share uagb-social-share__layout-{{SHARE_LAYOUT}}">
  <div class="uagb-social-share__wrap">

    <!-- wp:uagb/social-share-child {"block_id":"{slug}-share-tw","type":"twitter","icon":"twitter","color":"#FFFFFF","background":"#000000","hoverColor":"#FFFFFF","hoverBackground":"#1DA1F2"} -->
    <div class="wp-block-uagb-social-share-child uagb-block-{slug}-share-tw">
      <a href="https://twitter.com/intent/tweet?text={{POST_TITLE_ENC}}&url={{POST_URL_ENC}}" target="_blank" rel="noopener" aria-label="Partager sur Twitter">
        <i class="fab fa-twitter" aria-hidden="true"></i>
      </a>
    </div>
    <!-- /wp:uagb/social-share-child -->

    <!-- wp:uagb/social-share-child {"block_id":"{slug}-share-fb","type":"facebook","icon":"facebook-f","color":"#FFFFFF","background":"#1877F2","hoverColor":"#FFFFFF","hoverBackground":"#0E5FCE"} -->
    <div class="wp-block-uagb-social-share-child uagb-block-{slug}-share-fb">
      <a href="https://www.facebook.com/sharer/sharer.php?u={{POST_URL_ENC}}" target="_blank" rel="noopener" aria-label="Partager sur Facebook">
        <i class="fab fa-facebook-f" aria-hidden="true"></i>
      </a>
    </div>
    <!-- /wp:uagb/social-share-child -->

    <!-- wp:uagb/social-share-child {"block_id":"{slug}-share-li","type":"linkedin","icon":"linkedin-in","color":"#FFFFFF","background":"#0A66C2","hoverColor":"#FFFFFF","hoverBackground":"#084d92"} -->
    <div class="wp-block-uagb-social-share-child uagb-block-{slug}-share-li">
      <a href="https://www.linkedin.com/sharing/share-offsite/?url={{POST_URL_ENC}}" target="_blank" rel="noopener" aria-label="Partager sur LinkedIn">
        <i class="fab fa-linkedin-in" aria-hidden="true"></i>
      </a>
    </div>
    <!-- /wp:uagb/social-share-child -->

    <!-- wp:uagb/social-share-child {"block_id":"{slug}-share-wa","type":"whatsapp","icon":"whatsapp","color":"#FFFFFF","background":"#25D366","hoverColor":"#FFFFFF","hoverBackground":"#1EAE52"} -->
    <div class="wp-block-uagb-social-share-child uagb-block-{slug}-share-wa">
      <a href="https://api.whatsapp.com/send?text={{POST_TITLE_ENC}}%20{{POST_URL_ENC}}" target="_blank" rel="noopener" aria-label="Partager sur WhatsApp">
        <i class="fab fa-whatsapp" aria-hidden="true"></i>
      </a>
    </div>
    <!-- /wp:uagb/social-share-child -->

    <!-- wp:uagb/social-share-child {"block_id":"{slug}-share-em","type":"email","icon":"envelope","color":"#FFFFFF","background":"#454F5E","hoverColor":"#FFFFFF","hoverBackground":"var(--ast-global-color-0)"} -->
    <div class="wp-block-uagb-social-share-child uagb-block-{slug}-share-em">
      <a href="mailto:?subject={{POST_TITLE_ENC}}&body={{POST_URL_ENC}}" aria-label="Partager par email">
        <i class="fas fa-envelope" aria-hidden="true"></i>
      </a>
    </div>
    <!-- /wp:uagb/social-share-child -->

  </div>
</div>
<!-- /wp:uagb/social-share -->
```

## CSS overrides recommandés

```css
/* Container — gap entre boutons */
.uagb-block-{slug}-share .uagb-social-share__wrap {
  display: flex !important;
  gap: 12px !important;
  align-items: center !important;
}

.uagb-block-{slug}-share.uagb-social-share__layout-vertical .uagb-social-share__wrap {
  flex-direction: column !important;
}

/* Bouton — circle, hover lift */
.uagb-block-{slug}-share a {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  width: 40px !important;
  height: 40px !important;
  border-radius: 50% !important;
  text-decoration: none !important;
  transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease !important;
  font-size: 16px !important;
}

.uagb-block-{slug}-share a:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 8px 16px rgba(0,0,0,0.15) !important;
}

/* Variante vertical sticky (sidebar article) */
.uagb-block-{slug}-share.uagb-social-share__layout-vertical {
  position: sticky !important;
  top: 100px !important;
  z-index: 10 !important;
}

/* Mobile : compacter */
@media (max-width: 600px) {
  .uagb-block-{slug}-share a {
    width: 36px !important;
    height: 36px !important;
    font-size: 14px !important;
  }
}
```

## Pièges

| # | Quirk |
|---|---|
| **URL encoding** | `{{POST_TITLE_ENC}}` et `{{POST_URL_ENC}}` doivent être encodés URL (`encodeURIComponent`). Sinon les `?`, `&`, espaces cassent les liens. Le skill encode automatiquement à la génération |
| **Twitter / X branding** | Twitter est devenu X. L'icône `twitter` (oiseau) est encore acceptée mais l'icône `x-twitter` (logo X moderne) est plus à jour. Spectra peut ne pas l'avoir → utiliser `twitter` pour stabilité |
| **WhatsApp share** | URL différente entre desktop (`api.whatsapp.com/send`) et mobile (`whatsapp://send`). Spectra utilise l'URL universelle `api.whatsapp.com` qui marche sur les deux |
| **Email share** | `mailto:?subject=...&body=...` ouvre le client email par défaut. Si l'utilisateur n'en a pas configuré, navigateur peut afficher une erreur. Considérer Forward (URL share-by-email tiers) |
| **Pinterest** | Demande explicitement une image (`media=`). Si pas d'image disponible, retirer Pinterest de la liste |
| **Reddit** | URL : `https://reddit.com/submit?url=...&title=...`. Penser à proposer aux audiences tech/dev |
| **Native sharing API** | Sur mobile, Web Share API natif (`navigator.share()`) est plus joli. Pour fallback à Spectra, JS minimal : si supporté, ouvrir le sheet natif au clic |

## Variantes

### Variante 1 — Bottom-of-article (recommandé)

Layout `horizontal`, position : à la fin de l'article avant la FAQ. Label « Partager cet article » + 4-5 boutons circle.

### Variante 2 — Sticky sidebar (desktop only)

Layout `vertical`, position `sticky`, `top: 100px`. Caché sur mobile (`display: none` < 1024px). Effet médium/Substack.

### Variante 3 — Footer page contact

Layout `horizontal`, label « Suivez-nous » (pas « Partagez »). Liens vers les profils, pas vers les share intent.

```html
<a href="https://twitter.com/wpformation" target="_blank" aria-label="Twitter">
```

### Variante 4 — Share AI engines (cf wpformation)

Évolution moderne : ajouter des liens « partager avec ChatGPT / Perplexity / Claude / Mistral / Grok » qui pré-remplissent un prompt résumé. Format des URL :

```
https://chatgpt.com/?q=Lis%20cet%20article%3A%20{{URL}}
https://www.perplexity.ai/?q=Analyse%20cet%20article%3A%20{{URL}}
https://chat.mistral.ai/chat?q=R%C3%A9sume%20{{URL}}
```

Cf plugin OGEEAT pour implémentation complète (out of scope skill mais cas d'usage moderne).

### Variante 5 — Copy URL button

Ajouter un bouton « Copier le lien » qui copie l'URL canonique dans le presse-papiers (Clipboard API). Plus pratique que share intent pour Slack/Discord.

```html
<button type="button" class="copy-url-btn" data-url="{{POST_URL}}" aria-label="Copier l'URL">
  <i class="fas fa-link"></i>
</button>
<script>
document.querySelector('.copy-url-btn').addEventListener('click', e => {
  navigator.clipboard.writeText(e.currentTarget.dataset.url);
  e.currentTarget.classList.add('copied');
  setTimeout(()=> e.currentTarget.classList.remove('copied'), 1500);
});
</script>
```

## Test post-génération

1. Cliquer sur Twitter → s'ouvre dans un nouvel onglet avec le tweet pré-rempli (title + URL)
2. Cliquer sur LinkedIn → s'ouvre la modal de partage LinkedIn
3. Cliquer sur Email → ouvre le client email avec subject + body pré-remplis
4. Mobile : chaque bouton est tappable (44×44 minimum)
5. a11y : `aria-label` sur chaque lien (« Partager sur Twitter »), `aria-hidden="true"` sur les icônes décoratives

## Pour aller plus loin

- Liste icon-list horizontale (alternative simple) : `patterns/icon-list.md`
- Article éditorial avec share buttons : `patterns/article-content-rich.md`
- Plugin OGEEAT pour Share with AI : voir [wpformation.com/ogeeat/](https://wpformation.com/ogeeat/)
