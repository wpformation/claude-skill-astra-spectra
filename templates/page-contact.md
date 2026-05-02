# Template : Page Contact

> **Use case** : page contact pro avec hero + infos contact 4-cols (adresse / téléphone / email / horaires) + formulaire de contact + map Google + FAQ.

## Composition de patterns

```
1. patterns/hero-image-overlay.md         (variante hero court 120px, headline « Contact »)
2. Contact info 4-cols (composition custom ci-dessous)
3. Formulaire + map split (uagb/forms ou cf7-designer + uagb/google-map)
4. patterns/faq-accordion.md              (FAQ contact : délais réponse, langues, etc.)
5. patterns/cta-banner-fullwidth.md       (CTA backup « Pas trouvé ? Discord »)
```

## Variables d'entrée

| Variable | Description |
|---|---|
| `{{HERO_HEADING}}` | « Parlons de ton projet » |
| `{{HERO_SUBHEADING}}` | Tagline contact |
| `{{ADDRESS}}` | Adresse postale |
| `{{PHONE}}` | Numéro téléphone |
| `{{EMAIL}}` | Email contact |
| `{{HOURS}}` | Horaires d'ouverture |
| `{{MAP_LATITUDE}}` `{{MAP_LONGITUDE}}` | Coordonnées Google Maps |
| `{{FORM_TYPE}}` | `uagb-forms` / `cf7` / `wpforms` / `gravity` |
| `{{FORM_ID}}` | ID du formulaire si CF7/WPForms |
| `{{SOCIAL_LINKS[]}}` | LinkedIn, Twitter, Instagram |

## Sections clés

### 1. Hero court contact

Pattern `hero-image-overlay.md` court :
- Padding 120
- Image bg ambiance bureau / accueil
- Heading « Parlons de ton projet »
- Subheading « On répond sous 24h ouvrées »
- 1 CTA scroll vers form (smooth scroll #contact-form)

### 2. Contact info 4-cols numérotés

Inspiré du démo Spectra Natures contact page. 4 cards en row avec numéros prefix « 01 / 02 / 03 / 04 » :

```
container#contact-info (alignfull, padding 100, bg #fafafa)
  └─ container row 4-cols
      ├─ card 01 : ADRESSE
      │     ├─ « 01 » prefix orange
      │     ├─ heading « Notre adresse »
      │     └─ desc « 12 rue de la Paix, 75002 Paris »
      ├─ card 02 : TÉLÉPHONE
      │     └─ heading « Téléphone » + desc cliquable « 01 23 45 67 89 »
      ├─ card 03 : EMAIL
      │     └─ heading « Email » + desc lien mailto:
      └─ card 04 : HORAIRES
            └─ heading « Horaires » + desc « Lun-Ven 9h-18h »
```

### 3. Form + Map split

Container row 50/50 :
- Colonne gauche (60%) : `uagb/forms` (form natif Spectra) avec champs : nom, email, sujet, message, envoyer
- Colonne droite (40%) : `uagb/google-map` (lat/long, zoom 15, marker custom)

Markup :

```html
<!-- wp:uagb/container {"block_id":"{slug}-contact-form","alignfull","directionDesktop":"row","columnGapDesktop":40,"isBlockRootParent":true} -->
<div class="wp-block-uagb-container uagb-block-{slug}-contact-form alignfull">

  <!-- Form col (60%) -->
  <!-- wp:uagb/forms {"block_id":"{slug}-form","fieldName":[{"label":"Nom","required":true},{"label":"Email","type":"email","required":true},{"label":"Message","type":"textarea","required":true}],"submitButtonText":"Envoyer","successMessage":"Merci ! Nous te r&eacute;pondons sous 24h.","errorMessage":"Une erreur est survenue, r&eacute;essaie."} -->
  <!-- /wp:uagb/forms -->

  <!-- Map col (40%) -->
  <!-- wp:uagb/google-map {"block_id":"{slug}-map","address":"{{ADDRESS}}","zoom":15,"height":480,"language":"fr"} -->
  <!-- /wp:uagb/google-map -->

</div>
<!-- /wp:uagb/container -->
```

### 4. FAQ contact

Pattern `faq-accordion.md`. Questions :
- Quels sont vos délais de réponse ?
- Parlez-vous anglais / autre langue ?
- Acceptez-vous les rendez-vous en présentiel ?
- Comment annuler un rendez-vous ?
- Avez-vous un Discord / Slack pour échanger ?

### 5. CTA backup « Pas trouvé ? »

Pattern `cta-banner-fullwidth.md` :
- Heading « Tu préfères qu'on en parle de vive voix ? »
- 2 CTAs : Réserver un appel Calendly (primary) / Discord WPF (secondary)

## CSS overrides minimum

```css
/* Contact info cards numérotés */
.uagb-block-{slug}-contact-info-1 .number-prefix,
.uagb-block-{slug}-contact-info-2 .number-prefix,
.uagb-block-{slug}-contact-info-3 .number-prefix,
.uagb-block-{slug}-contact-info-4 .number-prefix {
  font-size: 56px !important;
  font-weight: 800 !important;
  color: var(--ast-global-color-0) !important;
  letter-spacing: -2px !important;
}

/* Form input styling */
.uagb-block-{slug}-form input[type="text"],
.uagb-block-{slug}-form input[type="email"],
.uagb-block-{slug}-form textarea {
  border-radius: 8px !important;
  border: 1px solid #e5e7eb !important;
  padding: 14px 18px !important;
  font-size: 16px !important;
}

/* Map iframe border-radius */
.uagb-block-{slug}-map iframe {
  border-radius: 16px !important;
}
```

## Schema SEO LocalBusiness / Organization

```json
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "{{COMPANY_NAME}}",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "12 rue de la Paix",
    "postalCode": "75002",
    "addressLocality": "Paris",
    "addressCountry": "FR"
  },
  "telephone": "{{PHONE}}",
  "email": "{{EMAIL}}",
  "openingHours": "Mo-Fr 09:00-18:00",
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": "{{MAP_LATITUDE}}",
    "longitude": "{{MAP_LONGITUDE}}"
  }
}
```

## Configuration Astra

Standard page builder + no-sidebar + no-title.

## Variantes par type d'organisation

- **Cabinet pro** (avocat, médecin, conseiller) : focus form + booking Calendly
- **Boutique physique** : focus map + horaires + photos lieu
- **SaaS / agence dématérialisée** : pas de map, juste form + Discord/Slack
- **Restaurant** : form réservation + horaires + menu lien

## Workflow

1. Brief : `« crée ma page contact avec form, infos, map Paris »`
2. Récupérer infos contact (adresse, tel, email, horaires)
3. Si form : choisir `uagb/forms` (default) ou plugin existant (CF7, WPForms)
4. Si map : récupérer lat/long via Google Maps
5. Composer 5 sections
6. Générer CSS overrides
7. Injecter schema LocalBusiness
8. POST + meta + regen
9. Test :
   - Form submit → email reçu
   - Click téléphone mobile → ouvre app phone
   - Click email → ouvre client mail
   - Map zoom + marker correct
