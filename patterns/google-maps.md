# Pattern : Google Maps (uagb/google-map)

> **Use case** : intégrer une carte Google Maps embarquée sur une page contact, page à propos, page commerce physique. Affiche un emplacement géographique avec zoom configurable, sans clé API requise (mode iframe public).

> **Bloc Spectra** : `uagb/google-map`. Repose sur l'iframe Google Maps publique (pas l'API JS payante). Idéal pour un POI unique. Pour plusieurs POI, voir variante 3.

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{LOCATION}}` | Adresse ou nom du lieu | `15 rue de la République, 13002 Marseille` |
| `{{ZOOM}}` | Niveau de zoom 1-20 (12 ville, 16 rue, 19 immeuble) | `16` |
| `{{HEIGHT}}` | Hauteur en px (responsive auto sur mobile) | `420` |

## Block markup

```html
<!-- wp:uagb/google-map {"block_id":"{slug}-map","address":"{{LOCATION}}","zoom":{{ZOOM}},"height":{{HEIGHT}},"heightTablet":{{HEIGHT}},"heightMobile":320,"language":"fr","mapType":"roadmap"} -->
<div class="wp-block-uagb-google-map uagb-block-{slug}-map">
  <div class="uagb-google-map__wrap">
    <iframe
      src="https://www.google.com/maps?q={{LOCATION_URL_ENCODED}}&t=&z={{ZOOM}}&ie=UTF8&iwloc=&output=embed&hl=fr"
      title="{{LOCATION}}"
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
    ></iframe>
  </div>
</div>
<!-- /wp:uagb/google-map -->
```

## CSS overrides recommandés (`_uag_custom_page_level_css`)

```css
/* Map — radius + shadow pour intégration éditoriale */
.uagb-block-{slug}-map .uagb-google-map__wrap {
  border-radius: 16px !important;
  overflow: hidden !important;
  box-shadow: 0 8px 32px rgba(15,23,42,0.10) !important;
}

/* Mobile : hauteur réduite + radius réduit */
@media (max-width: 600px) {
  .uagb-block-{slug}-map iframe {
    height: 320px !important;
  }
  .uagb-block-{slug}-map .uagb-google-map__wrap {
    border-radius: 12px !important;
  }
}
```

## Pièges

| # | Quirk |
|---|---|
| **Adresse** | Si l'adresse contient des accents/espaces, l'attribut `address` Spectra peut faillir. Pour fiabilité, encoder l'URL : `15%20rue%20de%20la%20R%C3%A9publique` |
| **Pas d'API key** | L'iframe `output=embed` fonctionne sans clé Google API. Pour features avancées (markers custom, layers), il faut la version JS API + clé (out of scope du skill) |
| **GDPR/RGPD** | Google Maps charge des cookies tiers. Si site sous obligation RGPD : wrapper la map dans un `consent-required` div + invitation au clic. Voir variante 4 |
| `loading="lazy"` | Toujours forcer `loading=lazy` sur l'iframe pour éviter le LCP penalty (la map peut être 800-1500ms à charger) |
| **Mobile UX** | iframe Google Maps mobile capture le scroll. Hauteur ≤ 320px + désactivation du touch scrolling via `pointer-events:none` puis activation au focus si nécessaire |

## Variantes

### Variante 1 — Map en split avec infos contact (recommandé)

Container `direction:row` avec :
- Colonne gauche (`widthDesktop:50`) : adresse + horaires + téléphone + email (uagb/icon-list)
- Colonne droite (`widthDesktop:50`) : la map

```
uagb/container#contact-row (row, alignItemsDesktop:stretch)
  ├─ uagb/container#contact-info (50% width)
  │     └─ infos texte
  └─ uagb/container#contact-map (50% width)
        └─ uagb/google-map
```

### Variante 2 — Map alignfull pleine largeur (page contact dramatic)

Hauteur 600px, alignfull, pas de container parent. Effet « immersion » au-dessus du footer.

### Variante 3 — Plusieurs POI (multi-locations)

Spectra `uagb/google-map` ne supporte qu'un POI. Pour 2+ POI :

- **Option A** : multiplier les `uagb/google-map` blocs dans un container row (1 par lieu)
- **Option B** : utiliser un embed Google My Maps custom (URL `mymaps.google.com/.../embed`) collé dans un `core/html` ou `uagb/html`

### Variante 4 — Avec gate consentement RGPD

Wrapper la map dans un container avec un placeholder texte + bouton « Charger la carte ». Au clic, JS injecte l'iframe.

```html
<!-- wp:uagb/container {"block_id":"{slug}-map-consent",...} -->
<div class="wp-block-uagb-container uagb-block-{slug}-map-consent">
  <div class="map-consent-placeholder" data-map-url="https://www.google.com/maps/embed?...">
    <p>Cette carte est fournie par Google. En la chargeant, vous acceptez les <a href="https://policies.google.com/privacy">conditions de Google</a>.</p>
    <button type="button" class="map-consent-btn">Charger la carte</button>
  </div>
</div>
<!-- /wp:uagb/container -->
```

JS minimal à ajouter dans le footer (via `core/html` ou Yoast):

```html
<script>
document.querySelectorAll('.map-consent-btn').forEach(btn=>{
  btn.addEventListener('click', e => {
    const wrap = e.target.closest('.map-consent-placeholder');
    const url = wrap.dataset.mapUrl;
    wrap.innerHTML = `<iframe src="${url}" loading="lazy" style="width:100%;height:420px;border:0"></iframe>`;
  });
});
</script>
```

## Test post-génération

1. Vérifier que l'iframe charge bien la map (zoom + emplacement corrects)
2. Vérifier le rendu mobile (hauteur ≤ 320px, pas de scroll capture)
3. Vérifier `loading=lazy` présent (devtools Network → la map ne se charge qu'au scroll)
4. Si RGPD : vérifier qu'aucun cookie `_ga` Google n'est posé avant le clic utilisateur

## Pour aller plus loin

- Variante 1 (split contact) : voir `templates/page-contact.md`
- Forms intégrés sur la page contact : voir `patterns/forms.md`
- Apostrophes accents dans l'adresse : voir `references/i18n-rules.md` (encoding URL)
