// frontend/src/app/api/skill-astra-spectra/route.ts
//
// Route API qui collecte un email + prénom, inscrit le contact à la newsletter Brevo (liste 5),
// et envoie le PDF skill Astra+Spectra par email transactionnel.
//
// Cloné de /api/guide-ia/ avec adaptations : sujet email, contenu, source d'inscription.

import { NextRequest, NextResponse } from "next/server";
import { env } from "@/lib/env";
import { createRateLimiter, getClientIp, rateLimitResponse, isWhitelistedIp } from "@/lib/rate-limiter";
import { isDisposableEmail, DISPOSABLE_EMAIL_ERROR } from "@/lib/email-validation";

const BREVO_API_KEY = env("BREVO_API_KEY");
const BREVO_MAIN_LIST_ID = 5;
const TURNSTILE_SECRET_KEY = env("TURNSTILE_SECRET_KEY");

const PDF_URL = "https://wpformation.com/guides/skill-astra-spectra-v1.pdf";

const RATE_LIMIT = 3;
const checkRateLimit = createRateLimiter({ limit: RATE_LIMIT, windowMs: 15 * 60 * 1000 });

async function verifyTurnstile(token: string): Promise<boolean> {
  if (!TURNSTILE_SECRET_KEY) return true;
  try {
    const res = await fetch("https://challenges.cloudflare.com/turnstile/v0/siteverify", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ secret: TURNSTILE_SECRET_KEY, response: token }),
    });
    const data = await res.json();
    return data.success === true;
  } catch {
    return false;
  }
}

function buildEmailHtml(firstName: string): string {
  const displayName = firstName || "WordPress fan";
  const UTM = "?utm_source=brevo&utm_medium=email&utm_campaign=skill-astra-spectra";
  return `<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Votre guide skill Astra+Spectra est pr&ecirc;t</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f0f0;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f0f0f0;">
<tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;width:100%;">
<tr><td style="background-color:#1a1a1a;border-radius:12px 12px 0 0;padding:32px 40px 24px;" align="center">
  <img src="https://img.mailinblue.com/2766702/images/content_library/original/69a86024bba4b49309873d5a.png" alt="WPFormation" width="240" height="45" style="display:block;border:0;margin-bottom:14px;">
  <p style="margin:0;font-size:11px;font-weight:600;color:#FF8C00;text-transform:uppercase;letter-spacing:3px;">Skill Claude Code &middot; Astra + Spectra</p>
</td></tr>
<tr><td style="background-color:#FF8C00;height:4px;font-size:0;line-height:0;">&nbsp;</td></tr>
<tr><td style="background-color:#ffffff;padding:40px 40px 36px;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#1a1a1a;border-radius:12px;overflow:hidden;">
  <tr><td style="background-color:#FF8C00;height:3px;font-size:0;line-height:0;">&nbsp;</td></tr>
  <tr><td style="padding:32px 28px 28px;">
    <p style="margin:0 0 6px;font-size:11px;font-weight:700;color:#FF8C00;text-transform:uppercase;letter-spacing:3px;">Le guide de 32 pages</p>
    <h1 style="margin:0 0 16px;font-size:24px;color:#ffffff;line-height:1.3;font-weight:800;">${displayName}, votre PDF est pr&ecirc;t.</h1>
    <p style="margin:0 0 8px;font-size:14px;line-height:1.65;color:#b0b0b0;">Le skill Astra+Spectra+Gutenberg core pour Claude Code, accompagn&eacute; de 32 pages de recettes, de templates et d'effets WOW pr&ecirc;ts &agrave; reproduire.</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:12px 0 20px;">
      <tr><td style="padding:4px 0;font-size:13px;color:#b0b0b0;line-height:1.5;">&#10003;&nbsp; 12 recettes WOW avec uagb/container (gradient mesh, glassmorphism, dividers...)</td></tr>
      <tr><td style="padding:4px 0;font-size:13px;color:#b0b0b0;line-height:1.5;">&#10003;&nbsp; 8 patterns hybrides Spectra + core</td></tr>
      <tr><td style="padding:4px 0;font-size:13px;color:#b0b0b0;line-height:1.5;">&#10003;&nbsp; 3 templates de pages (formation, SaaS, agence)</td></tr>
      <tr><td style="padding:4px 0;font-size:13px;color:#b0b0b0;line-height:1.5;">&#10003;&nbsp; Pilotage Astra Customizer complet (palette, typo, header, footer)</td></tr>
      <tr><td style="padding:4px 0;font-size:13px;color:#b0b0b0;line-height:1.5;">&#10003;&nbsp; 15 prompts optimis&eacute;s &agrave; copier-coller</td></tr>
    </table>
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr><td align="center">
      <a href="${PDF_URL}" style="display:inline-block;padding:14px 32px;background-color:#FF8C00;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;border-radius:8px;letter-spacing:0.3px;">T&eacute;l&eacute;charger le guide PDF</a>
    </td></tr>
    </table>
    <p style="margin:16px 0 0;font-size:12px;color:#666;text-align:center;">Repo GitHub&nbsp;: <a href="https://github.com/wpformation/claude-skill-astra-spectra" style="color:#FF8C00;text-decoration:underline;font-size:12px;">github.com/wpformation/claude-skill-astra-spectra</a></p>
  </td></tr>
  </table>
</td></tr>
<tr><td style="background-color:#000000;border-radius:0 0 12px 12px;padding:0;overflow:hidden;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td width="180" style="vertical-align:top;padding:0;margin:0;line-height:0;font-size:0;">
      <img src="https://img.mailinblue.com/2766702/images/content_library/original/69a860f96622324b7f74246f.jpeg" alt="" width="180" height="220" style="display:block;border:0;width:180px;height:220px;object-fit:cover;object-position:center top;">
    </td>
    <td style="vertical-align:middle;padding:20px 24px;background-color:#1a1a1a;">
      <p style="margin:0 0 2px;font-size:16px;font-weight:800;color:#ffffff;">Fabrice Ducarme</p>
      <p style="margin:0 0 12px;font-size:12px;color:#FF8C00;font-weight:600;">Formateur WordPress &amp; IA</p>
      <p style="margin:12px 0 4px;font-size:12px;color:#FF8C00;font-style:italic;">WordPressement,<br/>Fabrice de wpformation.com</p>
      <p style="margin:10px 0 0;font-size:10px;color:#555;line-height:1.4;"><a href="{{ unsubscribe }}" style="color:#888;text-decoration:underline;">Se d&eacute;sabonner</a></p>
    </td>
  </tr>
  </table>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>`;
}

function buildEmailText(firstName: string): string {
  const displayName = firstName || "WordPress fan";
  return `WPFORMATION - Skill Claude Code Astra+Spectra

${displayName}, votre guide de 32 pages est pret.

Le skill Astra+Spectra+Gutenberg core pour Claude Code, accompagne de 32 pages de recettes, templates et effets WOW prets a reproduire.

Au sommaire :
- 12 recettes WOW avec uagb/container
- 8 patterns hybrides Spectra + core
- 3 templates de pages (formation, SaaS, agence)
- Pilotage Astra Customizer complet
- 15 prompts optimises a copier-coller

Telecharger le PDF : ${PDF_URL}

Repo GitHub : https://github.com/wpformation/claude-skill-astra-spectra

WordPressement, Fabrice.

--
WPFormation - Fabrice Ducarme - EI
SIRET 478 478 332 00032
Mentions legales : https://wpformation.com/mentions-legales/
Se desinscrire : {{ unsubscribe }}`;
}

export async function POST(request: NextRequest) {
  try {
    const ip = getClientIp(request);
    if (!isWhitelistedIp(ip)) {
      const rateCheck = await checkRateLimit(ip);
      if (rateCheck.limited) {
        return rateLimitResponse(rateCheck, RATE_LIMIT);
      }
    }

    const { email, firstName, turnstileToken } = await request.json();

    if (TURNSTILE_SECRET_KEY) {
      if (!turnstileToken) {
        return NextResponse.json(
          { success: false, error: "Vérification anti-spam requise. Si vous utilisez un bloqueur de publicités, désactivez-le puis rechargez la page." },
          { status: 403 },
        );
      }
      const valid = await verifyTurnstile(turnstileToken);
      if (!valid) {
        return NextResponse.json(
          { success: false, error: "Vérification anti-spam échouée." },
          { status: 403 },
        );
      }
    }

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      return NextResponse.json(
        { success: false, error: "Adresse email invalide." },
        { status: 400 },
      );
    }

    if (isDisposableEmail(email)) {
      return NextResponse.json(
        { success: false, error: DISPOSABLE_EMAIL_ERROR },
        { status: 422 },
      );
    }

    if (!BREVO_API_KEY) {
      console.warn("[SkillAstraSpectra] BREVO_API_KEY not configured — dev mode");
      return NextResponse.json({ success: true, dev: true });
    }

    let existing = false;
    const contactRes = await fetch("https://api.brevo.com/v3/contacts", {
      method: "POST",
      headers: { "api-key": BREVO_API_KEY, "Content-Type": "application/json" },
      body: JSON.stringify({
        email,
        listIds: [BREVO_MAIN_LIST_ID],
        updateEnabled: true,
        attributes: {
          PRENOM: firstName || "",
          FIRSTNAME: firstName || "",
          SOURCE_INSCRIPTION: "skill-astra-spectra-v1",
        },
      }),
    });

    if (contactRes.status !== 201 && contactRes.status !== 204) {
      const contactData = await contactRes.json().catch(() => ({}));
      if (contactData.code === "duplicate_parameter") {
        existing = true;
      } else {
        console.error("[SkillAstraSpectra] Brevo contact error:", contactRes.status, contactData);
        return NextResponse.json(
          { success: false, error: "Erreur lors de l'inscription." },
          { status: 500 },
        );
      }
    }

    const emailRes = await fetch("https://api.brevo.com/v3/smtp/email", {
      method: "POST",
      headers: { "api-key": BREVO_API_KEY, "Content-Type": "application/json" },
      body: JSON.stringify({
        sender: { name: "Fabrice Ducarme", email: "fabrice@wpformation.com" },
        to: [{ email, name: firstName || email.split("@")[0] }],
        subject: "Votre guide skill Astra+Spectra est prêt",
        htmlContent: buildEmailHtml(firstName || ""),
        textContent: buildEmailText(firstName || ""),
      }),
    });

    if (!emailRes.ok) {
      const emailData = await emailRes.json().catch(() => ({}));
      console.error("[SkillAstraSpectra] Brevo email error:", emailRes.status, emailData);
    }

    return NextResponse.json({ success: true, existing });
  } catch {
    return NextResponse.json(
      { success: false, error: "Erreur serveur." },
      { status: 500 },
    );
  }
}
