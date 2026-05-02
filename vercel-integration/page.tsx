// frontend/src/app/skill-astra-spectra/page.tsx
//
// Page front du lead magnet : pitch + capture email + lien GitHub.
// Pattern cloné de /guide-ia-wordpress/ (page existante en prod).

import type { Metadata } from "next";
import LeadMagnetForm from "@/components/LeadMagnetForm";

export const metadata: Metadata = {
  title: "Skill Claude Code Astra+Spectra — Guide PDF gratuit | WPFormation",
  description:
    "Pilotez WordPress avec Claude Code. Génération de pages from brief, refonte intelligente et templates clic-bouton. Le skill open source + 32 pages de recettes prêtes à reproduire.",
  alternates: { canonical: "https://wpformation.com/skill-astra-spectra/" },
  openGraph: {
    title: "Skill Claude Code Astra+Spectra — Guide PDF gratuit",
    description:
      "Pilotez WordPress avec Claude Code. Skill open source + 32 pages de recettes.",
    url: "https://wpformation.com/skill-astra-spectra/",
    type: "article",
  },
};

export default function SkillAstraSpectraPage() {
  return (
    <main className="min-h-screen bg-[#0a0a0a] text-white">
      <section className="relative overflow-hidden py-20 px-4">
        <div
          className="absolute inset-0 opacity-30"
          style={{
            background:
              "radial-gradient(circle at 20% 30%, #FF8C00 0%, transparent 50%), radial-gradient(circle at 80% 70%, #0274be 0%, transparent 50%)",
          }}
        />
        <div className="relative max-w-5xl mx-auto">
          <p className="text-[#FF8C00] text-xs font-bold uppercase tracking-[0.3em] mb-4">
            Skill Claude Code · Open source MIT
          </p>
          <h1 className="text-4xl md:text-6xl font-extrabold leading-tight mb-6">
            Pilotez WordPress
            <br />
            <span className="text-[#FF8C00]">avec Claude Code.</span>
          </h1>
          <p className="text-xl text-gray-300 mb-10 max-w-3xl leading-relaxed">
            Le skill Astra+Spectra+Gutenberg core qui transforme un brief en page WordPress
            complète en moins de 2 minutes. 49 blocs Spectra + 30 blocs core + pilotage
            Astra Customizer. Open source MIT.
          </p>
          <div className="flex flex-wrap gap-4 mb-12">
            <a
              href="https://github.com/wpformation/claude-skill-astra-spectra"
              target="_blank"
              rel="noopener"
              className="inline-block px-8 py-4 bg-white text-black font-bold rounded-lg hover:bg-gray-200 transition"
            >
              Voir le repo GitHub →
            </a>
            <a
              href="#telecharger"
              className="inline-block px-8 py-4 bg-[#FF8C00] text-white font-bold rounded-lg hover:bg-orange-600 transition"
            >
              Télécharger le guide PDF gratuit
            </a>
          </div>
        </div>
      </section>

      <section className="py-20 px-4 bg-[#1a1a1a]">
        <div className="max-w-5xl mx-auto grid md:grid-cols-3 gap-8">
          <div>
            <div className="text-5xl mb-4">⚡</div>
            <h2 className="text-2xl font-bold mb-3">Génération from brief</h2>
            <p className="text-gray-400 leading-relaxed">
              Décrivez ce que vous voulez en langage naturel. Le skill assemble le markup
              Gutenberg complet (Spectra + core) avec design system Astra cohérent.
            </p>
          </div>
          <div>
            <div className="text-5xl mb-4">🔄</div>
            <h2 className="text-2xl font-bold mb-3">Refonte intelligente</h2>
            <p className="text-gray-400 leading-relaxed">
              Une page legacy WordPress 5 ? Le skill snapshot, analyse, reconstruit en
              Spectra moderne tout en préservant chaque mot du contenu original.
            </p>
          </div>
          <div>
            <div className="text-5xl mb-4">🎨</div>
            <h2 className="text-2xl font-bold mb-3">Templates clic-bouton</h2>
            <p className="text-gray-400 leading-relaxed">
              8 templates prêts (page formation, landing SaaS, page agence) qui
              s&apos;adaptent automatiquement à vos couleurs, typo et contenu.
            </p>
          </div>
        </div>
      </section>

      <section id="telecharger" className="py-20 px-4 bg-[#0a0a0a]">
        <div className="max-w-3xl mx-auto">
          <p className="text-[#FF8C00] text-xs font-bold uppercase tracking-[0.3em] mb-4 text-center">
            32 pages de recettes
          </p>
          <h2 className="text-3xl md:text-5xl font-extrabold text-center mb-6">
            Recevez le guide PDF
            <br />
            par email
          </h2>
          <p className="text-xl text-gray-300 text-center mb-12 max-w-2xl mx-auto">
            12 effets WOW avec uagb/container · 3 templates · pilotage Astra Customizer ·
            15 prompts optimisés. Gratuit, en échange de votre email.
          </p>
          <LeadMagnetForm
            apiEndpoint="/api/skill-astra-spectra/"
            ctaLabel="Recevoir le guide PDF"
            successMessage="C&apos;est parti ! Le guide arrive dans votre boîte mail dans quelques secondes."
            campaignId="skill-astra-spectra-v1"
          />
        </div>
      </section>

      <section className="py-20 px-4 bg-[#1a1a1a]">
        <div className="max-w-5xl mx-auto">
          <h2 className="text-3xl md:text-4xl font-extrabold mb-12 text-center">
            Au sommaire du PDF
          </h2>
          <div className="grid md:grid-cols-2 gap-8">
            <ul className="space-y-3 text-gray-300">
              <li>✓ Routing automatique : 45 entrées intent → bloc</li>
              <li>✓ 12 recettes WOW avec uagb/container</li>
              <li>✓ Hero pleine page avec gradient mesh</li>
              <li>✓ Glassmorphism cards</li>
              <li>✓ Background vidéo en boucle</li>
              <li>✓ Sticky sidebar layout 70/30</li>
              <li>✓ Conic gradient (rotation 1deg)</li>
              <li>✓ FAQ avec schema FAQPage automatique</li>
            </ul>
            <ul className="space-y-3 text-gray-300">
              <li>✓ 3 templates prêts à déployer</li>
              <li>✓ Workflow complet en 8 étapes</li>
              <li>✓ Workflow refonte avec préservation contenu</li>
              <li>✓ Pilotage Astra (palette, typo, header, footer)</li>
              <li>✓ 15 anti-patterns à éviter</li>
              <li>✓ 10 erreurs résolues (troubleshooting)</li>
              <li>✓ 15 prompts optimisés à copier-coller</li>
              <li>✓ Cas d&apos;usage agence (ROI estimé)</li>
            </ul>
          </div>
        </div>
      </section>

      <section className="py-20 px-4 bg-[#0a0a0a]">
        <div className="max-w-3xl mx-auto text-center">
          <h2 className="text-3xl md:text-4xl font-extrabold mb-6">
            Vous voulez aller plus loin ?
          </h2>
          <p className="text-xl text-gray-300 mb-10">
            Formation WordPress + IA sur-mesure : 20 à 60 heures, en visio ou présentiel,
            financement OPCO. Apprenez à industrialiser votre WordPress avec Claude Code.
          </p>
          <a
            href="/formation-wordpress/"
            className="inline-block px-8 py-4 bg-[#FF8C00] text-white font-bold rounded-lg hover:bg-orange-600 transition"
          >
            Découvrir la formation →
          </a>
        </div>
      </section>
    </main>
  );
}
