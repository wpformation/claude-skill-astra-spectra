# Référence : ratios images attendus par pattern

> **Lecture obligatoire avant de demander à l'utilisateur d'uploader des images.** Chaque pattern attend une image avec un ratio spécifique. Mismatch ratio + objectFit:cover = image mal cadrée (visage zoom max, fruits coupés en deux, etc.).

## Pourquoi c'est critique

Quand tu mets une image portrait 600×900 dans un container qui attend 1200×400 (ratio 16:5), Spectra applique `object-fit: cover` qui :
1. Préserve l'aspect ratio de l'image
2. La crop pour qu'elle remplisse le container
3. **Centre par défaut**

Résultat : si l'image est un visage en plan rapproché (600×900), le crop 1200×400 = on garde uniquement la partie centrale horizontale du visage. C'est moche.

Solution : documenter pour chaque pattern le ratio attendu, et le communiquer à l'utilisateur avant qu'il upload.

## Tableau des ratios par pattern

| Pattern | Ratio | Dimensions recommandées | Use case |
|---|---|---|---|
| `hero-image-overlay` (image bg) | 16:9 ou 16:7 | 1920×1080 ou 1920×850 | Photo paysage, équipe au travail, lieu |
| `cta-banner-fullwidth` (image bg) | 16:7 | 1920×800 | Photo équipe, lieu, ambiance |
| `about-story-split` (image éditoriale) | 16:5 ou 3:1 | 1200×400 ou 1200×400 | Photo paysage éditoriale |
| `about-story-side` (image side) | 4:5 ou 3:4 | 600×750 ou 600×800 | Photo portrait, produit vertical |
| `testimonials-cards` (avatar) | 1:1 | 400×400 | Portrait recadré |
| `team-grid` (membre) | 4:5 | 400×500 | Portrait équipe |
| `features-numbered` (illustration optionnelle) | 1:1 | 600×600 | Illustration carrée |
| `pricing-3-tiers` | pas d'image | — | — |
| `faq-accordion` | pas d'image | — | — |
| `stats-bar-editorial` | pas d'image | — | — |
| `services-cards-with-images` | 4:3 | 600×450 | Photo service |
| `blog-post-grid` (featured image) | 16:9 | 1200×675 | Image article cover |
| `tabs-section` | optionnel 16:9 | 800×450 par tab | — |
| `slider-carousel` | 16:9 ou 1:1 | 1200×675 ou 800×800 | — |
| `timeline-vertical` (étape image) | 1:1 | 200×200 | Mini icône/photo |
| `how-to-steps` (illustration step) | 4:3 ou 1:1 | 600×450 ou 400×400 | Photo de l'étape |
| `review-product` (cover) | 1:1 | 800×800 | Produit sur fond uni |
| `countdown-launch` (image bg) | 16:7 | 1920×800 | Photo ambiance évènement |

## Comment communiquer le ratio à l'utilisateur

Au moment du brief, le skill doit demander :

> Pour le hero, j'ai besoin d'une image **landscape format 16:9** (recommandé 1920×1080 px). Idéalement une photo qui montre l'ambiance de ton site (étudiants en révision, équipe au travail, lieu de cours). Évite les portraits gros plan car ils seront mal cadrés.

Pour les avatars testimonials :

> Pour chaque témoignage, j'ai besoin d'une **photo carrée 1:1** (400×400 px minimum). Un portrait recadré sur le visage.

## Stratégies si l'utilisateur n'a pas la bonne image

### Stratégie A — Adapter le pattern au ratio fourni

Si l'utilisateur ne peut fournir qu'une image portrait pour le hero, adapter le pattern :
- Hero avec image side (au lieu de hero overlay full-width)
- Layout 60/40 texte | image au lieu de hero alignfull

### Stratégie B — Image stock par défaut

Maintenir une bibliothèque d'images stock libres de droits (Unsplash, Pexels) catégorisées par thème + ratio :

```
images/stock/
├── hero-16-9-business/        (10 images business 1920×1080)
├── hero-16-9-education/       (10 images étudiants/cours 1920×1080)
├── hero-16-9-tech/            (10 images dev/tech 1920×1080)
├── about-16-5-team/           (10 images équipe paysage 1200×400)
├── avatar-1-1-portrait/       (15 portraits divers 400×400)
└── cta-16-7-ambiance/         (10 images ambiance 1920×800)
```

Suggestions à l'utilisateur :
> Pour le hero, je peux utiliser une image stock business landscape par défaut (depuis Unsplash). Tu pourras la remplacer après par ta propre photo.

### Stratégie C — Génération AI

Pour les patterns nécessitant une image très spécifique (e.g. illustration de feature), générer via DALL-E / Midjourney / Stable Diffusion avec un prompt qui force le bon ratio :

```
Prompt : "Étudiants en révision sur ordinateurs, ambiance bibliothèque moderne, photographie réaliste, 16:9 aspect ratio"
```

Puis upload via mu-plugin endpoint `/skill-test/v1/upload-image`.

## Pièges connus

### Image ratio correct mais sujet mal positionné

Symptôme : image 1920×1080 (16:9 OK) mais le sujet (visage) est en bas à droite. `object-fit: cover` centre le crop = sujet sorti du cadrage.

Fix : utiliser `object-position` CSS dans `_uag_custom_page_level_css` :

```css
.uagb-block-{block_id} img {
  object-position: bottom right !important;
}
```

### Image trop grosse → page lente

Symptôme : l'utilisateur uploade une photo de 8 MB en 4000×3000. Page first contentful paint dépasse 5s.

Fix :
- Imposer max upload size dans `wp-config.php` ou `.user.ini` : `upload_max_filesize = 2M`
- Optimiser les images uploadées via WP-CLI ou plugin (WebP Express, ShortPixel, EWWW)
- Utiliser `srcset` natif WP qui sert des tailles adaptées

### Image transparente PNG sur fond clair

Symptôme : l'utilisateur uploade un logo PNG transparent. Sur un container avec backgroundColor:#ffffff, le logo disparaît visuellement.

Fix : utiliser un container avec backgroundColor neutre (#fafafa) ou un dark accent pour faire ressortir le PNG transparent.

## Comment vérifier le ratio d'une image

### Via wp-cli

```bash
wp media list --field=ID,file,sizes
```

### Via REST API

```bash
curl /wp-json/wp/v2/media/{id} | jq .media_details.sizes
```

Retourne tous les size variants disponibles. Pour trouver le ratio :

```bash
curl /wp-json/wp/v2/media/{id} | jq '{w:.media_details.width,h:.media_details.height,ratio:(.media_details.width/.media_details.height)}'
```

Si ratio = 1.778 → 16:9 OK pour hero. Si ratio = 0.667 → 2:3 portrait, ne PAS mettre dans hero.

## TODO v1.1+

- [ ] `scripts/validate-image-ratio.php` qui prend un media_id + un pattern_id, vérifie ratio compatible, suggère alternative
- [ ] Bibliothèque d'images stock auto-uploadable via `/skill-test/v1/upload-stock-image?theme=education&ratio=16:9`
- [ ] Crop intelligent avant upload (smart crop avec face detection si visage à préserver)
- [ ] Génération AI optionnelle si l'utilisateur ne fournit pas d'image
