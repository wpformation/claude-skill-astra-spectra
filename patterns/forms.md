# Pattern : Forms (uagb/forms + cf7-designer + gf-designer)

> **Use case** : formulaire de contact, devis, inscription newsletter, demande d'information. 3 options selon ce que le site cible a installé : `uagb/forms` (natif Spectra), `uagb/cf7-designer` (Contact Form 7), `uagb/gf-designer` (Gravity Forms).

> **Décision** : si le site a déjà CF7 ou GF installé, utiliser le designer correspondant (préserve les fonctionnalités du plugin existant : confirmations, anti-spam, intégrations Mailchimp/etc.). Sinon, `uagb/forms` natif suffit pour cas simple.

## Variables d'entrée

| Variable | Description | Exemple |
|---|---|---|
| `{{FORM_HEADING}}` | Titre H2 de la section formulaire | `Demande de devis` |
| `{{FORM_DESC}}` | Sous-titre / description | `Réponse sous 24 h ouvrées` |
| `{{FORM_FIELDS}}` | Liste des champs | (voir tableau ci-dessous) |
| `{{FORM_SUBMIT_LABEL}}` | Texte du bouton submit | `Envoyer ma demande` |
| `{{FORM_SUCCESS_MESSAGE}}` | Message de confirmation | `Merci, on revient vers toi rapidement.` |
| `{{FORM_TARGET_EMAIL}}` | Email de réception (uagb/forms) | `contact@monsite.com` |

### Champs typiques par cas d'usage

| Cas d'usage | Champs |
|---|---|
| Contact simple | `name` (text req), `email` (email req), `message` (textarea req) |
| Demande de devis | `name` (req), `email` (req), `phone` (tel), `company` (text), `project` (textarea req), `budget` (select : <5K / 5-15K / 15-50K / 50K+) |
| Inscription newsletter | `email` (req), `firstname` (text), `consent_rgpd` (checkbox req) |
| Réservation restaurant | `name` (req), `phone` (req), `email`, `date` (date), `time` (time), `guests` (number 1-20), `notes` (textarea) |

## Block markup — Variante A : `uagb/forms` natif Spectra

```html
<!-- wp:uagb/forms {"block_id":"{slug}-form","formLabel":"{{FORM_HEADING}}","formstyling":"boxed","afterSubmitToEmail":"{{FORM_TARGET_EMAIL}}","successMessage":"{{FORM_SUCCESS_MESSAGE}}","afterSubmitBehaviour":"message","buttonAlign":"center","buttonText":"{{FORM_SUBMIT_LABEL}}","buttonAlignment":"left","submitColor":"#FFFFFF","submitBgColor":"var(--ast-global-color-0)","submitTextHoverColor":"#FFFFFF","submitBgHoverColor":"var(--ast-global-color-1)","submitFontSize":16,"submitFontWeight":"700","submitPaddingTopBottom":16,"submitPaddingLeftRight":36,"submitBorderRadius":8,"hPaddingField":16,"vPaddingField":14,"borderRadiusField":8,"borderColorField":"#e5e7eb"} -->
<div class="wp-block-uagb-forms uagb-block-{slug}-form">
  <form class="uagb-forms__form" action="" method="post">

    <!-- wp:uagb/forms-name {"block_id":"{slug}-name","label":"Nom","required":true} -->
    <div class="uagb-forms-name-wrap"><label>Nom *</label><input type="text" name="name" required></div>
    <!-- /wp:uagb/forms-name -->

    <!-- wp:uagb/forms-email {"block_id":"{slug}-email","label":"Email","required":true} -->
    <div class="uagb-forms-email-wrap"><label>Email *</label><input type="email" name="email" required></div>
    <!-- /wp:uagb/forms-email -->

    <!-- wp:uagb/forms-textarea {"block_id":"{slug}-message","label":"Message","required":true,"rows":5} -->
    <div class="uagb-forms-textarea-wrap"><label>Message *</label><textarea name="message" rows="5" required></textarea></div>
    <!-- /wp:uagb/forms-textarea -->

    <!-- Champ honeypot anti-spam (invisible) -->
    <!-- wp:uagb/forms-hidden {"block_id":"{slug}-honey","name":"website","value":""} -->
    <input type="text" name="website" value="" style="position:absolute;left:-9999px" tabindex="-1" autocomplete="off">
    <!-- /wp:uagb/forms-hidden -->

    <button type="submit" class="uagb-forms__submit-button">{{FORM_SUBMIT_LABEL}}</button>
  </form>
</div>
<!-- /wp:uagb/forms -->
```

## Block markup — Variante B : Contact Form 7 designer

Si CF7 est installé, créer le formulaire dans CF7 admin (récupère un shortcode `[contact-form-7 id="123" title="..."]`), puis :

```html
<!-- wp:uagb/cf7-designer {"block_id":"{slug}-cf7","formId":"123","fieldStyle":"box","msgFontSize":14,"buttonAlign":"center","buttonAlignment":"flex-start","buttonText":"{{FORM_SUBMIT_LABEL}}","buttonTextColor":"#FFFFFF","buttonBgColor":"var(--ast-global-color-0)","buttonHoverColor":"#FFFFFF","buttonBgHoverColor":"var(--ast-global-color-1)","fieldBorderRadius":8,"fieldHrPadding":16,"fieldVrPadding":14,"buttonPaddingTopBottom":16,"buttonPaddingLeftRight":36,"buttonBorderRadius":8,"successMsgColor":"#16A34A","errorMsgColor":"#DC2626"} -->
<div class="wp-block-uagb-cf7-designer uagb-block-{slug}-cf7">[contact-form-7 id="123"]</div>
<!-- /wp:uagb/cf7-designer -->
```

## Block markup — Variante C : Gravity Forms designer

Si GF est installé, créer le formulaire dans GF admin (récupère un ID), puis :

```html
<!-- wp:uagb/gf-designer {"block_id":"{slug}-gf","formId":"5","fieldStyle":"box","buttonText":"{{FORM_SUBMIT_LABEL}}","buttonTextColor":"#FFFFFF","buttonBgColor":"var(--ast-global-color-0)","fieldBorderRadius":8,"buttonBorderRadius":8} -->
<div class="wp-block-uagb-gf-designer uagb-block-{slug}-gf">[gravityform id="5" title="false" description="false"]</div>
<!-- /wp:uagb/gf-designer -->
```

## CSS overrides recommandés

```css
/* Container formulaire — fond blanc, padding éditorial, shadow */
.uagb-block-{slug}-form,
.uagb-block-{slug}-cf7,
.uagb-block-{slug}-gf {
  background: #ffffff !important;
  padding: 48px !important;
  border-radius: 16px !important;
  box-shadow: 0 8px 32px rgba(15,23,42,0.08) !important;
  border: 1px solid #e5e7eb !important;
}

/* Labels — uppercase tracking petit */
.uagb-block-{slug}-form label,
.uagb-block-{slug}-cf7 label,
.uagb-block-{slug}-gf label {
  font-size: 12px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 1.5px !important;
  color: #454F5E !important;
  margin-bottom: 6px !important;
  display: block !important;
}

/* Inputs — bordure subtile, focus orange */
.uagb-block-{slug}-form input[type="text"],
.uagb-block-{slug}-form input[type="email"],
.uagb-block-{slug}-form input[type="tel"],
.uagb-block-{slug}-form input[type="number"],
.uagb-block-{slug}-form select,
.uagb-block-{slug}-form textarea,
.uagb-block-{slug}-cf7 input,
.uagb-block-{slug}-cf7 select,
.uagb-block-{slug}-cf7 textarea,
.uagb-block-{slug}-gf input,
.uagb-block-{slug}-gf select,
.uagb-block-{slug}-gf textarea {
  border: 1px solid #e5e7eb !important;
  border-radius: 8px !important;
  padding: 14px 16px !important;
  font-size: 16px !important;
  width: 100% !important;
  transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}

.uagb-block-{slug}-form input:focus,
.uagb-block-{slug}-cf7 input:focus,
.uagb-block-{slug}-gf input:focus,
.uagb-block-{slug}-form textarea:focus,
.uagb-block-{slug}-cf7 textarea:focus,
.uagb-block-{slug}-gf textarea:focus {
  outline: none !important;
  border-color: var(--ast-global-color-0) !important;
  box-shadow: 0 0 0 3px rgba(253,152,0,0.15) !important;
}

/* Submit button — primary CTA */
.uagb-block-{slug}-form .uagb-forms__submit-button,
.uagb-block-{slug}-cf7 input[type="submit"],
.uagb-block-{slug}-gf input[type="submit"] {
  background: var(--ast-global-color-0) !important;
  color: #FFFFFF !important;
  padding: 16px 36px !important;
  border-radius: 8px !important;
  font-weight: 700 !important;
  font-size: 16px !important;
  letter-spacing: 0.3px !important;
  border: none !important;
  cursor: pointer !important;
  transition: background-color 0.15s ease, transform 0.1s ease !important;
}

.uagb-block-{slug}-form .uagb-forms__submit-button:hover,
.uagb-block-{slug}-cf7 input[type="submit"]:hover,
.uagb-block-{slug}-gf input[type="submit"]:hover {
  background: var(--ast-global-color-1) !important;
  transform: translateY(-1px) !important;
}

/* Required asterisk — rouge subtil */
.uagb-block-{slug}-form .required-mark,
.uagb-block-{slug}-cf7 .wpcf7-not-valid-tip {
  color: #DC2626 !important;
}

/* Success / error messages */
.uagb-forms__success-msg, .wpcf7-mail-sent-ok { color: #16A34A !important; padding: 16px !important; background: #F0FDF4 !important; border-radius: 8px !important; border: 1px solid #16A34A !important; }
.uagb-forms__error-msg, .wpcf7-validation-errors { color: #DC2626 !important; padding: 16px !important; background: #FEF2F2 !important; border-radius: 8px !important; border: 1px solid #DC2626 !important; }
```

## Pièges

| # | Quirk |
|---|---|
| **Honeypot anti-spam** | Toujours ajouter un champ `<input type="text" name="website" style="position:absolute;left:-9999px" tabindex="-1">`. Les bots remplissent, les humains non. Si le champ est non-vide à la submission → spam, à filtrer côté serveur |
| **reCAPTCHA / Turnstile** | `uagb/forms` natif ne supporte pas reCAPTCHA out-of-box. Pour anti-spam sérieux : préférer CF7 + plugin reCAPTCHA, ou GF + reCAPTCHA Enterprise |
| **Validation côté client** | HTML5 `required`, `type="email"`, `type="tel"`, `pattern="..."` font le boulot pour 80% des cas. Pour validation custom, JS dédié |
| **Email envoi** | `uagb/forms` natif envoie via `wp_mail()`. Sur Apache mutu, `wp_mail()` peut échouer silencieusement (pas de SPF/DKIM). Forcer SMTP avec un plugin comme **WP Mail SMTP** ou **Post SMTP** |
| **RGPD checkbox** | Pour formulaire de contact, ajouter une checkbox `consent_rgpd` (required) avec lien vers la politique de confidentialité |
| **Accessibilité** | Chaque `<input>` DOIT avoir un `<label for="...">` associé. Spectra le génère par défaut, vérifier dans devtools |
| **Tab order** | Vérifier que TAB navigue de champ en champ dans l'ordre logique (pas en sautant) |

## Variantes

### Variante 1 — Contact split avec map (recommandé pour page contact)

Container `direction:row` :
- Colonne gauche (`widthDesktop:50`) : le formulaire
- Colonne droite (`widthDesktop:50`) : `uagb/google-map` + horaires + téléphone

Cf `templates/page-contact.md`.

### Variante 2 — Newsletter inline minimaliste (footer / hero CTA)

Pour newsletter, on ne fait pas un formulaire avec heading + 5 champs. On fait un simple `email + bouton` inline :

```html
<form class="newsletter-inline">
  <input type="email" placeholder="Ton email" required>
  <button type="submit">M'inscrire</button>
</form>
```

CSS minimal pour effet inline pill. Voir `patterns/newsletter-inline.md` (à créer si fréquemment utilisé) ou simplement composer en flat.

### Variante 3 — Devis multi-étapes (wizard)

Pour formulaire long (10+ champs), découper en 3 étapes :
1. Coordonnées (nom, email, tel)
2. Projet (description, type, budget)
3. Validation + RGPD

Spectra `uagb/forms` natif ne supporte pas le wizard. Utiliser **Gravity Forms** ou **Forminator** (multi-step natif).

### Variante 4 — Embed dans modal

Wrapper le formulaire dans un `uagb/modal` (cf `patterns/modal.md`). Trigger « Demander un devis » → modal avec le formulaire compact.

## Test post-génération

1. Soumettre le formulaire avec tous les champs valides → message de succès s'affiche
2. Soumettre vide → erreurs de validation HTML5 sur les champs `required`
3. Soumettre un email invalide (`foo@bar`) → erreur HTML5 `type=email`
4. Vérifier la réception sur l'email cible (préférer un email pro, pas Gmail qui peut spam-folder)
5. Vérifier l'absence de soumissions spam (honeypot, reCAPTCHA si activé)
6. a11y : navigation TAB, labels visibles, contraste focus ≥ 3:1
7. Mobile : champs `width:100%`, font-size ≥ 16px (sinon iOS zoome au focus)

## Pour aller plus loin

- Map à côté du formulaire : `patterns/google-maps.md`
- Modal contenant le formulaire : `patterns/modal.md`
- Template page contact complet : `templates/page-contact.md`
- Plugins SMTP recommandés : Post SMTP (300K+), WP Mail SMTP (3M+), FluentSMTP
