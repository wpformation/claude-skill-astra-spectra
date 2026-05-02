# Référence : visual pitfalls — moves design qui sonnent créatifs mais foirent en pratique

> **LECTURE OBLIGATOIRE** avant d'oser un move design « créatif » (watermark, drop cap, asymetric layout, mono fonts, neon glow, etc.). Ce fichier liste les patterns qui paraissent malins dans l'abstrait mais qui foirent visuellement quand combinés à Spectra + un thème WP standard.

> **Origine** : retour reviewer 02/05/2026 après 3 pages contact pour cours-ndrc.fr toutes qualifiées « moches, niveau débutant qui n'a jamais touché WordPress » par le user. Cause : moves design « créatifs » accumulés sans validation visuelle.

## Comment utiliser ce fichier

Pour chaque pattern listé : **Symptôme du désastre / Pourquoi ça foire / Quand c'est OK / Alternative recommandée**.

Si tu envisages un move design qui ressemble à un de ces patterns, **lis l'entrée** avant de décider. Si tu veux quand même le faire, demande au user d'abord.

---

## Pitfall #1 — Watermark numérique géant en deco isolée

### Symptôme

Tu mets un grand chiffre `247` ou `12+` à 480px de font-size en watermark transparent (`rgba(0,0,0,0.05)`) en background d'une section. Tu trouves ça « éditorial premium ».

### Pourquoi ça foire

- Sans le **reste du design global** déjà committed (typo extreme, palette restreinte, alignements millimetrés), le watermark paraît être un **bug d'affichage**
- Sur mobile, le watermark déborde ou écrase le contenu
- Si la section a déjà un H1 + un eyebrow + un accent line orange + un CTA fort, le watermark devient un **5e élément graphique** = saturation visuelle
- En général, **le user ne demande pas** ça — c'est l'instance Claude qui se croit créative

### Quand c'est OK

- Design global **déjà validé** comme éditorial extreme (par screenshot)
- Section où le contenu est **minimaliste** (juste 1 H2 + 1 paragraphe), le watermark devient le focal
- User a **explicitement demandé** un effet « numéro géant » ou « editorial typo »

### Alternative recommandée

- **Numéros 01/02/03** dans des features cards (pattern `features-numbered.md`) — effet éditorial sans le risque watermark
- **Stats bar avec chiffres 56-72px** alignés en row — clarté + impact, baseline éprouvée

---

## Pitfall #2 — Stats asymetric 40/30/20/10 sans calibration

### Symptôme

Tu fais 4 stats en row mais tu mets `widthDesktop: 40 / 30 / 20 / 10` pour un effet « hiérarchie visuelle ». La 1re stat est dramatique, les autres rétrécissent.

### Pourquoi ça foire

- Demande une **calibration typo extrême** (la stat 1 doit avoir font-size 80px+ vs stat 4 à 36px) sinon ça paraît cassé
- Sur tablet et mobile, l'asymétrie devient incompréhensible — les colonnes se réorganisent et la hiérarchie disparaît
- L'œil cherche une grille régulière par défaut, l'asymétrie demande un effort cognitif que la majorité des sites ne mérite pas

### Quand c'est OK

- Brand **committed dramatic** (luxe, art, magazine éditorial)
- Calibration typo + spacing déjà validée par screenshot sur 3 breakpoints
- User a **explicitement demandé** une hiérarchie asymétrique

### Alternative recommandée

- **Row equal-width 4 cols** (`widthDesktop:22` chacune, gap 24px) — 99 % des cas
- Si vraiment besoin de hiérarchie, **3 cols égales avec un H1 stat dominant au-dessus** : le H1 fait la hiérarchie, pas la grid

---

## Pitfall #3 — Drop cap orange `::first-letter` sur paragraphe

### Symptôme

Tu mets dans `_uag_custom_page_level_css` :

```css
.uagb-block-{slug}-story-text p::first-letter {
  font-size: 88px;
  color: #FF8C00;
  float: left;
  margin-right: 8px;
}
```

Effet « magazine éditorial ». Tu trouves ça beau dans ta tête.

### Pourquoi ça foire

- `::first-letter` se comporte différemment selon le navigateur ET selon que le `<p>` est dans un `uagb-ifb-desc` ou un `core/paragraph` ou un `uagb-ifb-content`
- Spectra wrap parfois le texte dans des `<span>` internes, ce qui casse `::first-letter`
- Sur mobile, la drop cap déborde la ligne ou crée un wrap moche
- Le drop cap orange + un eyebrow orange + un CTA orange = **3 accents orange** = saturation (cf règle 6 SKILL.md)

### Quand c'est OK

- Pattern dédié **`article-content-rich.md`** qui a une variante drop cap testée + screenshot validé
- User a explicitement demandé un effet magazine

### Alternative recommandée

- **Eyebrow uppercase** au-dessus du paragraphe pour signaler l'intro éditoriale (pattern proven, no risk)
- **Pull quote** (`uagb/blockquote`) au milieu du texte si tu veux un effet visuel fort

---

## Pitfall #4 — Mono fonts isolés (timestamps, badges, codes)

### Symptôme

Tu mets `font-family: 'JetBrains Mono', 'Fira Code', monospace` sur un timestamp `12:34` dans un design global qui n'a pas de thème terminal/dev.

### Pourquoi ça foire

- Le mono font dépend des fonts **système ou téléchargées** sur le navigateur visiteur. Si la font n'est pas dispo, fallback `monospace` générique = Courier moche par défaut
- Dans un design global qui a Manrope/Inter/sans-serif élégant, le mono font isolé paraît être une **erreur de copie-coller**
- Le user qui n'est pas dev se dit « pourquoi ce texte ressemble à du code de hacker »

### Quand c'est OK

- Site **dev/tech committed** (terminal aesthetic, code blocks dominants, palette neon)
- Font mono est **chargée explicitement** via `@font-face` ou Google Fonts dans le thème
- User a explicitement demandé un effet « terminal »

### Alternative recommandée

- Font système cohérente avec le reste de la page + **font-variant-numeric: tabular-nums** pour aligner les chiffres dans les tables
- Pour code source affiché : `core/code` (rendu en `<pre><code>` avec mono natif géré par le thème)

---

## Pitfall #5 — 3+ accents couleur identiques dans la même section (saturation orange)

### Symptôme

Tu fais une section avec :
- **Watermark** orange `#FF8C00` 480px en background
- **Accent line** orange `#FF8C00` 64×4px sous le H2
- **Eyebrow** avec barre `::before` orange `#FF8C00` 32×3px
- **CTA primary** orange `#FF8C00` background

= **4 accents identiques** dans la même section, perception « overdose orange ».

### Pourquoi ça foire

L'œil humain cherche des **points de focus**. S'il y a 4 accents identiques, **aucun** n'est focal. Le message visuel est diffus, le CTA n'attire plus l'attention.

### Quand c'est OK

- Brand monochromatique **délibérée** (ex: site marque « 100% orange » comme orange.fr)
- Section très simple (1 H1 + 1 CTA) avec 1 seul accent dominant

### Alternative recommandée

- **1 accent primary dominant** par section + **1 accent secondaire** max (de hue différente, ex: orange + vert success ou orange + bleu info)
- Le CTA garde toujours l'accent primary (le focus le plus important)
- Watermark/accent line : **choisir un seul** des deux, pas les deux

### Règle quantitative

- **Max 2 occurrences du même accent couleur** par section root
- Si tu veux signaler une variation, utiliser une **hue différente** ou une **saturation/luminosité différente** (orange clair vs orange saturé)

---

## Pitfall #6 — Padding « éditorial extrême » 220/220 sur toutes les sections

### Symptôme

Pour faire « rendu studio editorial », tu mets `topPadding/bottomPadding: 220` sur toutes les sections root. La page fait 6000px de scroll.

### Pourquoi ça foire

- L'utilisateur scrolle 5 fois pour atteindre la 2e section
- Le contenu est **noyé** dans le whitespace
- Mobile : padding 220 = écran 80% blanc, contenu 20%
- Le « éditorial » ne vient PAS du padding mais de la qualité typo + alignement

### Quand c'est OK

- 1 ou 2 sections **délibérément** sur-padded pour servir de **respiration** entre 2 zones denses
- Brand luxe/art committed avec validation screenshot

### Alternative recommandée

- **140/140 desktop** standard partout
- Pour une section qui doit respirer plus : **160/160 ou 180/180** max
- Si tu veux du `editorial dramatic`, joue plutôt sur la **typo** (H1 88px+, line-height 1.05, letter-spacing -3px) que sur le padding

---

## Pitfall #7 — `border-radius` extrêmes (0px ou 32px+)

### Symptôme

- `border-radius: 0` partout pour un effet « brutal » → paraît juste **pas fini**, comme si tu avais oublié les radius
- `border-radius: 32px+` partout → toutes les cards en pills, design **qui se prend trop au sérieux**

### Pourquoi ça foire

- Les conventions web modernes attendent **8-18px** de radius par défaut
- Trop peu = brutalist incompris ou bug d'affichage
- Trop = baby toy aesthetic ou fintech app casual qui ne va pas avec le secteur

### Quand c'est OK

- Brand **brutalist committed** (e.g. magazine punk, art gallery dark)
- Brand **playful committed** (e.g. SaaS friendly Duolingo-style)

### Alternative recommandée

- **8px** boutons, **12-16px** cards features, **18-24px** cards testimonials
- Si tu veux un effet sharp : **2-4px** plutôt que 0 (subtil)

---

## Pitfall #8 — Box-shadows over-the-top

### Symptôme

```css
.card {
  box-shadow:
    0 16px 32px rgba(0,0,0,0.20),
    0 8px 16px rgba(255,140,0,0.15),
    inset 0 -2px 0 rgba(255,255,255,0.5);
}
```

Triple shadow + colored shadow + inset highlight. Tu te crois designer Stripe.

### Pourquoi ça foire

- Le browser doit calculer 3 shadows à chaque scroll = **performance** dégradée
- Multi-shadows demandent une **calibration extrêmement fine** sinon ça fait toy
- Sur fond dark, les shadows blanches deviennent invisibles ; sur fond clair, les shadows colorées paraissent du blur cheap

### Quand c'est OK

- Card hero unique avec attention design + validation screenshot 3 breakpoints
- Brand premium committed

### Alternative recommandée

- **Shadow simple** : `rgba(15,23,42,0.06-0.12) 0 4-12px 24-48px`
- 1 seule couche, pas de couleur, pas d'inset

---

## Pitfall #9 — Animations CSS « subtiles » (hover lift, floating, pulse)

### Symptôme

```css
.cta-button {
  animation: float 3s ease-in-out infinite;
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.cta-button:hover {
  transform: translateY(-4px) scale(1.05) rotate(-1deg);
  box-shadow: 0 16px 48px rgba(255,140,0,0.4);
}
```

Tu testes sur ton dev local, ça fait wow. En prod, le user trouve ça « épileptique ».

### Pourquoi ça foire

- **`prefers-reduced-motion`** ignoré = a11y violation
- L'animation `float infinite` distrait du contenu vrai (le CTA)
- `cubic-bezier overshoot` (`(0.34, 1.56, 0.64, 1)` = bounce) paraît toy si pas brand committed
- Sur mobile, les animations consomment batterie + CPU

### Quand c'est OK

- Animation **single-trigger** sur hover desktop only (pas mobile, pas autoplay)
- `@media (prefers-reduced-motion: reduce) { animation: none; }` toujours respecté
- Validation screenshot frame-by-frame

### Alternative recommandée

- **Hover simple** : `transform: translateY(-2px); transition: transform 0.15s ease;` + box-shadow shift léger
- Pas d'`animation infinite` sur les éléments interactifs

---

## Pitfall #10 — Hero overlay opacity 0.92 (image background invisible)

### Symptôme

Tu mets une image hero magnifique. Tu ajoutes `overlayOpacity: 0.92` pour que le texte blanc soit lisible. Résultat : on ne voit plus du tout l'image, c'est juste un bloc sombre uniforme.

### Pourquoi ça foire

Cf quirk #9 dans `references/spectra-attributes-quirks.md`. Au-delà de 0.85, l'image perd 100 % de son rôle éditorial.

### Quand c'est OK

- Tu n'as pas besoin de l'image, autant la retirer

### Alternative recommandée

- Overlay **gradient** : color1 `rgba(0,0,0,0.7)` haut-gauche → color2 `rgba(0,0,0,0.20)` bas-droite, 110-135deg
- Texte hero en haut-gauche (où l'overlay est dense), image visible en bas-droite

---

## Pitfall #11 — Réutiliser la même image entre 2 pages du même site

### Symptôme

Tu as utilisé l'image stock `coffee-shop-hero.jpg` pour la page `/` (accueil), tu la réutilises pour la page `/contact/`. Le user voit immédiatement le doublon.

### Pourquoi ça foire

- Le user qui navigue sur le site voit que c'est **la même image** = signe de paresse, de manque de soin
- L'image perd son rôle éditorial (elle n'illustre plus rien de spécifique à la page)

### Quand c'est OK

- **Logo / brand asset** (logo, favicon, Open Graph image) — réutilisation OK
- Image héro brand committed identique sur toutes les pages (cas rare, brand monolithique)

### Alternative recommandée

- **Demander au user** de fournir des images différenciées par page
- Si pas dispo, propose 3 sources d'images génériques (Unsplash / Pexels / Pixabay) avec des concepts visuels différents par page

---

## Pitfall #12 — Trop de sections (8+) en 1 livraison

### Symptôme

Tu livres une page avec hero + stats + features + about + testimonials + pricing + FAQ + CTA = 8 sections d'un coup. Le user trouve la 2e section moche, doit te re-briefer pour les 8 sections.

### Pourquoi ça foire

- Si la 1re section a un défaut design, **les 7 autres ont le même défaut** (typo/spacing/accent) — tu accumules le défaut sur toute la page
- Le user doit donner des feedbacks sur 8 sections d'un coup, c'est cognitivement lourd
- Si la page est rejetée, **8 sections de travail perdues**

### Quand c'est OK

- Tu déploies un **template committed** avec baselines screenshots validées (`templates/` + `screenshots/`)
- Le user a explicitement demandé une page complète d'un coup

### Alternative recommandée

- **Max 3 sections** par itération
- Hero + Stats + 1 feature ou 1 testimonial → screenshot → user valide → ajoute le reste
- Aller vite quand c'est validé, ralentir quand c'est nouveau

---

## Pitfall #13 — Auto-claim « WOW / impeccable / éditorial » sans screenshot

### Symptôme

Tu finis ta génération, tu écris au user :

> ✨ Page composée avec succès. Rendu studio editorial impeccable, hero
> dramatique, hiérarchie typo cohérente, accent line orange élégant.

Le user va voir : c'est moche.

### Pourquoi ça foire

- Tu **n'as pas vu** la page rendue. Tu projettes ce que tu as voulu faire, pas ce qui est rendu
- Le user te fait confiance sur ton claim → déception × 10 quand il découvre la réalité
- Tu **brûles ta crédibilité** auprès du user pour les sessions suivantes

### Quand c'est OK

- Tu as un **screenshot validé** que tu peux montrer au user dans la même réponse

### Alternative recommandée

Voir **règle 1 SKILL.md**. Sans screenshot, tu qualifies de **« composition non vérifiée visuellement »** et tu demandes au user de valider.

---

## Comment cette doc évolue

À chaque session où l'instance Claude fait un move design qui foire, **ajouter** un pitfall ici avec les 4 sections **Symptôme / Pourquoi / Quand OK / Alternative**. La doc est vivante.

Goal v1.1+ : 90 % des « moves créatifs » qui foirent sont documentés ici. L'instance Claude lit cette doc avant d'oser, et choisit l'**alternative recommandée** par défaut.