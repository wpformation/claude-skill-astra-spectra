# Convention rhythm sections — alternance bg + dividers

> **Origine** : rapport visuel cours-ndrc.fr du 02/05/2026. Sur la page démo, 5 sections `alignwide` enchaînées avaient toutes leur padding interne mais aucune marge externe → mur de blocs sans hiérarchie. Pour respirer visuellement entre sections, le skill applique une convention de rhythm.

## Règle 1 — Alternance background section ↔ section

Deux sections consécutives ne doivent **JAMAIS avoir le même `backgroundColor`**. Sinon elles se collent visuellement et l'utilisateur ne perçoit pas le découpage.

Convention par défaut (claire) :

| # | Section | `backgroundColor` |
|---|---------|--------------------|
| 1 | Hero | `#ffffff` (white pur) |
| 2 | Features | `#fafafa` (off-white) |
| 3 | Testimonials | `#ffffff` |
| 4 | Pricing | `#fafafa` |
| 5 | FAQ | `#ffffff` |
| 6 | CTA banner | gradient `var(--ast-global-color-0)` → `var(--ast-global-color-1)` (primary) |
| 7 | Footer | thème par défaut |

L'œil perçoit immédiatement les transitions sans avoir besoin de marge externe.

Convention dark mode (variante) :

| # | Section | `backgroundColor` |
|---|---------|--------------------|
| 1 | Hero | `#0f172a` (dark slate) |
| 2 | Features | `#1e293b` (dark slate lighter) |
| 3 | Testimonials | `#0f172a` |
| 4 | Pricing | `#1e293b` |
| 5 | FAQ | `#0f172a` |

## Règle 2 — Padding interne généreux + responsive

Tous les root containers ont :

- `topPaddingDesktop` / `bottomPaddingDesktop` : 80-100 px (large)
- `topPaddingTablet` / `bottomPaddingTablet` : 60 px
- `topPaddingMobile` / `bottomPaddingMobile` : 48 px
- `leftPaddingTablet` / `rightPaddingTablet` : 24 px
- `leftPaddingMobile` / `rightPaddingMobile` : 16 px

Ces valeurs sont déjà appliquées dans tous les patterns v0.8.3+. La respiration vient du padding interne **ET** de l'alternance bg, pas de la marge externe (qui peut casser le `alignfull`).

## Règle 3 — Dividers Spectra optionnels (effet WOW)

Pour transitions élégantes entre sections (use case landing page premium), utiliser un divider Spectra :

```json
{
  "topDividerStyle": "tilt",
  "topDividerColor": "#fafafa",
  "topDividerWidth": 100,
  "topDividerHeight": 60
}
```

Le `topDividerColor` doit matcher le `backgroundColor` de la **section précédente** pour donner l'illusion de continuité visuelle.

Voir `modules/spectra/container-wow-recipes.md` recette 4 (« Dividers diagonaux »).

## Règle 4 — Pas de marge externe sur `alignfull`

Pour les sections `alignfull` (CTA banner, hero gradient mesh), ne **jamais** ajouter `marginTopDesktop` ou `marginBottomDesktop` externes positifs. La section doit toucher la précédente/suivante pour respecter l'alignement full-width. La respiration vient du padding interne ET du contraste de bg.

Pour les sections `alignwide`, idem — on évite les marges externes pour ne pas créer de gap blanc visible avec le body bg.

## Pattern d'usage côté workflow

`workflows/new-page-from-brief.md` étape 4 (assemblage) doit :

1. Récupérer la liste ordonnée de patterns à assembler
2. Pour chaque pattern, déterminer son **backgroundColor par défaut** (lu depuis le pattern ou résolu via `wpf_skill_resolve_color`)
3. Vérifier que la séquence respecte l'alternance (pas 2 sections consécutives avec même bg)
4. Si conflit détecté : **changer le bg de la section sur palindrome** (sect_n_bg = sect_(n-1)_bg → forcer à l'alternative)
5. Optionnellement : injecter un `uagb/separator` entre sections si la transition est jugée trop dure

## Anti-patterns

- ❌ 2 sections consécutives avec `backgroundColor: #ffffff` → mur de blocs invisibles
- ❌ Margin externe `marginTopDesktop: 80` sur section `alignfull` → casse l'alignment
- ❌ Pas de padding bottom sur la dernière section avant footer → footer collé
- ❌ Divider color qui ne matche pas le bg de la section adjacente → effet de bord visible

## Vérification visuelle

Le check 6 du `visual-audit.php` v0.9.0+ vérifie que les sections root consécutives ont un `backgroundColor` différent. Flag P2 si 2 sections collées ont même bg.
