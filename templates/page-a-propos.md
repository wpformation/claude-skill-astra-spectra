# Template : Page À Propos

> **Use case** : page « À propos » / « Notre histoire ». Hero + storytelling + valeurs + équipe + timeline + stats + CTA. Pour bâtir l'autorité E-E-A-T (Expertise, Experience, Authoritativeness, Trustworthiness).

## Composition de patterns

```
1. patterns/hero-image-overlay.md         (hero éditorial avec photo équipe)
2. patterns/about-story-split.md          (« Notre approche / Notre histoire » image + texte)
3. patterns/features-numbered.md          (3 valeurs : 01 Excellence / 02 Pédagogie / 03 Innovation)
4. patterns/timeline-vertical.md          (timeline 4-6 events de la création à aujourd'hui)
5. patterns/stats-bar-editorial.md        (4 chiffres clés : années / clients / produits / awards)
6. patterns/team-grid.md                  (grille 4-8 membres équipe)
7. patterns/testimonials-cards.md         (3 clients qui nous font confiance)
8. patterns/cta-banner-fullwidth.md       (CTA « Travaillons ensemble »)
```

## Variables d'entrée

| Variable | Description |
|---|---|
| `{{HERO_HEADING}}` | « Notre histoire » ou « Qui sommes-nous » |
| `{{HERO_SUBHEADING}}` | Tagline éditoriale |
| `{{HERO_IMAGE}}` | Photo équipe ou lieu (16:9 ou 16:7) |
| `{{STORY_HEADING}}` | « Le BTS NDRC expliqué comme un ami... » |
| `{{STORY_DESC}}` | Storytelling 1-2 paragraphes |
| `{{STORY_IMAGE}}` | Photo fondateur ou bureau |
| `{{VALUES[3]}}` | 3 valeurs (label uppercase + heading + desc) |
| `{{TIMELINE_EVENTS[]}}` | 4-6 events historiques |
| `{{STATS[4]}}` | 4 chiffres clés |
| `{{TEAM_MEMBERS[]}}` | 4-8 membres avec photo + nom + role + bio |
| `{{TESTIMONIALS[3]}}` | 3 témoignages clients |
| `{{CTA_FINAL_HEADING}}` | « Prêt à travailler avec nous ? » |

## Sections clés

### 1. Hero éditorial

Pattern `hero-image-overlay.md` :
- Photo équipe au travail (ratio 16:9, ambiance bureau / réunion)
- Eyebrow « Depuis 2012 »
- Heading « Notre histoire »
- Subheading 2-line tagline
- Pas de CTA hero (la page = la story, pas la conversion)

### 2. Story split — « Notre approche »

Pattern `about-story-split.md` :
- Image éditoriale 16:5 du fondateur ou du bureau
- Heading H2 « Le BTS NDRC expliqué comme un ami qui l'a déjà eu »
- Desc storytelling 3-4 phrases
- Bonus : 3 mini-cards 3/2/5 « 3 idées clés / 2 exemples / 5 erreurs » (cf v0.9.3)

### 3. 3 valeurs — features-numbered

Pattern `features-numbered.md` :
- 01 — EXCELLENCE « Chaque cours est relu par 3 anciens étudiants avant publication. »
- 02 — PÉDAGOGIE « Pas de jargon. Les concepts expliqués comme tu les entendrais en cafèt. »
- 03 — INNOVATION « QCM générés par AI, corrigés humainement. Best of both worlds. »

### 4. Timeline historique

Pattern `timeline-vertical.md`. 4-6 events :
- 2012 — Lancement du site
- 2015 — 100 000 utilisateurs
- 2018 — Refonte UX
- 2020 — Lancement des QCM
- 2023 — 1 million d'utilisateurs annuels
- 2026 — Refonte IA

### 5. Stats clés

Pattern `stats-bar-editorial.md`. 4 chiffres :
- 13 — ANNÉES D'EXISTENCE
- 250K — ÉTUDIANTS AIDÉS
- 227 — COURS RÉDIGÉS
- 87% — TAUX DE RÉUSSITE

### 6. Team grid

Pattern `team-grid.md` (existant). Grille 4 membres avec photo + nom + role + bio courte + social links.

### 7. Testimonials

Pattern `testimonials-cards.md`. 3 clients différents qui ont fait confiance.

### 8. CTA final

Pattern `cta-banner-fullwidth.md` :
- Heading « Prêt à rejoindre 250 000 étudiants ? »
- 2 CTAs : Voir le programme (primary) / Contact (secondary)

## CSS overrides minimum

Hérités de chaque pattern utilisé. Pas d'overrides spécifiques au template À Propos.

## Schema SEO Organization

```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "{{COMPANY_NAME}}",
  "founder": {"@type": "Person", "name": "{{FOUNDER_NAME}}"},
  "foundingDate": "2012",
  "url": "https://{{DOMAIN}}",
  "logo": "https://{{DOMAIN}}/logo.png",
  "description": "{{TAGLINE}}",
  "sameAs": [
    "https://linkedin.com/company/...",
    "https://twitter.com/..."
  ]
}
```

## Configuration Astra

Standard page builder + no-sidebar + no-title.

## Importance E-E-A-T

Cette page est **critique** pour le SEO. Google évalue ta crédibilité notamment via :
- **Experience** : timeline montrant ton expérience longue
- **Expertise** : team grid avec photos + roles + bio detailing crédibilité
- **Authoritativeness** : stats + testimonials + awards mentionnés
- **Trustworthiness** : politique de confidentialité accessible, infos contact claires

Inclure systématiquement :
- Photo réelle de l'équipe (pas stock)
- Bio courte mais factuelle de chaque membre
- Liens vers profils LinkedIn / Twitter pour vérification
- Mention awards / certifications / partenariats si pertinents

## Variantes par secteur

- **Agence créative** : focus team + portfolio, photos tendance
- **Cabinet pro** (avocat, médecin) : focus diplômes + barreau / ordre, photos formelles
- **Startup tech** : focus founders + investors + mission, photos modernes
- **Association** : focus mission + bénéficiaires + bénévoles, photos terrain

## Workflow

1. Brief : `« crée la page à propos de cours-ndrc.fr »`
2. Récupérer storytelling (Mission / Histoire / Valeurs)
3. Récupérer team (4-8 membres avec photos)
4. Récupérer chiffres clés validés
5. Récupérer testimonials clients
6. Composer 8 sections
7. Générer CSS overrides (hérités de chaque pattern)
8. Injecter schema Organization
9. POST + meta + regen
10. Test :
    - Tous les liens sociaux fonctionnent
    - Photos team chargent (eager loading forcé)
    - Timeline responsive
    - Stats horizontales 4-cols desktop / 2x2 tablet / 1x4 mobile
