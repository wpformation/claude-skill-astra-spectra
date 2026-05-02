# Intégration Vercel — Lead magnet skill Astra+Spectra

Ce dossier contient les fichiers **prêts à déployer** sur le site Next.js de WPFormation (ou tout site Vercel similaire) pour distribuer le PDF skill avec capture email.

## Pattern cloné

Cette intégration clone le pattern existant `/api/guide-ia/` (guide IA WordPress, déjà en prod sur wpformation.com) avec :

- Route API POST avec validation email (anti-domaines jetables)
- Rate limiter (3 requêtes / 15 min / IP)
- Turnstile Cloudflare (anti-bot)
- Brevo : ajout liste newsletter + envoi email transactionnel HTML+text
- Page front avec formulaire + témoignages + features

## Fichiers à déployer

```
frontend/src/app/api/skill-astra-spectra/route.ts
frontend/src/app/skill-astra-spectra/page.tsx
frontend/src/components/SkillAstraSpectraHero.tsx
public/guides/skill-astra-spectra-v1.pdf  ← uploader le PDF compilé
```

## Étapes d'intégration (à faire en session dédiée)

```bash
# 1. Copier les fichiers dans le repo WPFORMATION
cp vercel-integration/api-route.ts        wpformation/frontend/src/app/api/skill-astra-spectra/route.ts
cp vercel-integration/page.tsx             wpformation/frontend/src/app/skill-astra-spectra/page.tsx

# 2. Compiler le PDF (voir lead-magnet/README.md) puis uploader
cp lead-magnet/guide-claude-code-wordpress-spectra-v1.pdf wpformation/frontend/public/guides/

# 3. Tester en local
cd wpformation/frontend && pnpm dev
# Ouvrir http://localhost:3000/skill-astra-spectra/

# 4. Build + check TypeScript
pnpm build

# 5. Push (1 commit dédié)
git add frontend/src/app/api/skill-astra-spectra/ frontend/src/app/skill-astra-spectra/ frontend/public/guides/skill-astra-spectra-v1.pdf
git commit -m "feat(lead-magnet): page + route /skill-astra-spectra/ avec capture email Brevo"
git push
```

## Variables d'environnement requises

Toutes déjà configurées sur Vercel (utilisées par `/api/guide-ia/`) :

- `BREVO_API_KEY`
- `TURNSTILE_SECRET_KEY`
- `NEXT_PUBLIC_TURNSTILE_SITE_KEY`

## Tracking GA4 / Brevo

Source d'inscription identifiable :

```
attributes.SOURCE_INSCRIPTION = "skill-astra-spectra-v1"
```

Permet de filtrer les inscrits dans Brevo et de mesurer la conversion newsletter → formation payante par cohorte de lead magnet.

## Métriques cibles (3 mois post-lancement)

- 500+ téléchargements
- 30 %+ taux conversion email collecté
- 5 %+ conversion vers formation payante (149 EUR/h ou 1 900 EUR HT)
- 50+ étoiles GitHub repo public
