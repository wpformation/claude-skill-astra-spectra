# Template : Page Agence

> **Use case** : Site vitrine d'une agence digitale, marketing, design, dev, ou freelance solo positionné en agence. Conversion via prise de RDV ou demande de devis.

## Structure

```
1. Hero impact (full-screen, dark mode)
   - Background image full ou gradient mesh
   - Headline punchy : "On crée des sites qui convertissent. Point."
   - Subline : positionnement clair
   - CTA primary : "Démarrer un projet"
   - CTA secondary : "Voir nos cas clients"

2. Logos clients (8-12 logos prestigieux)
   - Container alignfull background sombre, logos en blanc transparent
   - Effet hover : passage en couleur

3. Services 4 ou 6 Cards (uagb/info-box hoverables)
   - Web design / Dev / SEO / Marketing / etc.
   - Icônes ou pictos métier

4. Section "Pourquoi nous" — 3 piliers de valeur
   - Container split avec image illustrative
   - 3 features uagb/info-box minimaliste

5. Case Studies (3-6 projets)
   - Grid uagb/post personnalisé ou cards manuelles
   - Photo avant/après ou hero du projet
   - Hover avec lien vers étude de cas détaillée

6. Process en 5 étapes (uagb/timeline horizontale ou verticale)
   - Brief → Audit → Design → Dev → Lancement
   - Pictos par étape

7. Team Grid (3-6 membres clés)
   - Photo + nom + rôle + bio courte + LinkedIn

8. Testimonials Grid (3 témoignages clients)
   - Photo + témoignage + nom + rôle + logo entreprise

9. Stats Counters (preuve)
   - "X projets livrés", "Y années d'expérience", "Z clients fidèles"

10. CTA Section avec form de contact rapide
    - "Démarrons votre projet ensemble"
    - Form simple : nom, email, message court
    - Ou CTA vers calendly/cal.com pour RDV
```

## Variables minimales

```yaml
AGENCY_NAME: "Studio X"
HERO_HEADLINE: "On crée des sites qui convertissent. Point."
HERO_SUBLINE: "Agence webdesign + dev WordPress depuis 2018."
SERVICES: [
  { name: "Web design", icon: "fa-paint-brush", desc: "..." },
  { name: "Développement WordPress", icon: "fa-code", desc: "..." },
  ...
]
CASE_STUDIES_URLS: [...]
TEAM: [...]
TESTIMONIALS: [...]
STATS: { projects: 120, years: 6, clients: 80 }
CONTACT_FORM_URL: "/contact/"
```

## Palette suggérée

- **wpf-minimal** (#18181B noir + accents) — agences premium / luxe
- **wpf-creative** (#8B5CF6 violet) — agences créatives / design studios
- **preset_8** (#FD9800 orange) — agences friendly / startup
- **wpf-dark** (background #0F172A + accent #FF8C00) — tech / bold positioning

## Effet wow recommandé

Hero avec **background image fixed parallax** + overlay dark 65% (recette 1) + **scramble text effect** sur la headline (custom CSS).

Section Process en **timeline horizontale** avec animations stagger au scroll (recette 10 dans `container-wow-recipes.md`).

Case Studies en **grid hoverables** avec scale 1.05 + box shadow grosse au hover.

CTA final avec **glassmorphism card** (recette 3) flottante au-dessus d'un background image.

## Compatibilité

- Spectra ≥ 2.10
- Astra optionnel mais recommandé pour le pilotage Customizer (header transparent fixed scrollé)
- Schema SEO : Organization + LocalBusiness (manuel via Yoast) + Service
- Responsive critique car cible probablement viewport mobile (60-70%+ trafic)
