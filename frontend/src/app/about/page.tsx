import Header from "../components/Header";
import Footer from "../components/Footer";
import Image from "next/image";
import { CheckCircle2, Award, Users, ShieldCheck, HelpCircle } from "lucide-react";
import Link from "next/link";

export default function About() {
  const credentials = [
    {
      icon: Award,
      title: "State-Of-The-Art Lab",
      desc: "Operating advanced digital presses and thermal laminators.",
    },
    {
      icon: Users,
      title: "Preferred B2B Vendor",
      desc: "Partnered with over 5,000 professional wedding photographers.",
    },
    {
      icon: ShieldCheck,
      title: "Guaranteed Durability",
      desc: "Waterproof bindings, non-tear sheets, and fingerprint resistance.",
    },
  ];

  return (
    <>
      <Header />
      <main className="flex-grow pt-24 bg-zinc-50">
        
        {/* Page Intro */}
        <section className="bg-white border-b border-zinc-200/50 py-16 text-center">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <span className="text-xs uppercase font-extrabold tracking-widest text-primary">
              Our Story
            </span>
            <h1 className="font-serif text-4xl font-bold tracking-tight text-secondary">
              About <span className="text-gradient">SD Colours Lab</span>
            </h1>
            <p className="text-zinc-500 text-sm max-w-xl mx-auto mt-2">
              Learn about our technology, our commitment to quality, and why wedding photographers across the country choose us as their printing press.
            </p>
          </div>
        </section>

        {/* TWO-COLUMN DETAILS GRID */}
        <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            {/* Left Column: Text & Credentials */}
            <div className="lg:col-span-7 space-y-8">
              <div className="space-y-4">
                <span className="text-xs font-bold uppercase tracking-wider text-primary">
                  Industrial Print Standards
                </span>
                <h2 className="font-serif text-3xl font-bold text-secondary leading-snug">
                  Most Rated Wedding Album <br />
                  Printing Press in Odisha
                </h2>
                <div className="w-16 h-1 bg-primary rounded-full" />
              </div>

              <p className="text-zinc-600 text-sm leading-relaxed">
                SD Colours Photobook Lab has been at the forefront of photobook printing technology for over a decade. We combine advanced machinery with handcraftsmanship to deliver albums that stand the test of time. Every sheet is processed to capture the exact color gamuts, tones, and highlights captured by your camera sensor.
              </p>

              {/* Checklist Items */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {[
                  "Lay-flat binding for complete spread view",
                  "High-definition non-tear slim pages",
                  "Exquisite velvet, sparkle, and metallic finishes",
                  "Matching presentation bags and briefcases",
                  "Direct courier logistics support",
                  "Client-side estimate compilers",
                ].map((item, idx) => (
                  <div key={idx} className="flex items-start gap-2.5 text-xs text-zinc-700">
                    <CheckCircle2 className="w-4 h-4 text-primary shrink-0 mt-0.5" />
                    <span>{item}</span>
                  </div>
                ))}
              </div>

              <div className="flex gap-4 pt-4">
                <Link
                  href="/products"
                  className="bg-secondary hover:bg-black text-white px-6 py-3 rounded-full text-xs font-bold transition-all shadow-md"
                >
                  Explore Products
                </Link>
                <Link
                  href="/contact"
                  className="border border-zinc-300 bg-white hover:bg-zinc-50 text-secondary px-6 py-3 rounded-full text-xs font-bold transition-all"
                >
                  Contact Desk
                </Link>
              </div>
            </div>

            {/* Right Column: Monogram Layout */}
            <div className="lg:col-span-5 flex justify-center">
              <div className="w-full aspect-[4/3] bg-secondary rounded-3xl border-4 border-white/5 shadow-2xl flex items-center justify-center p-8 relative overflow-hidden group">
                <div className="absolute inset-0 bg-gradient-to-tr from-black via-zinc-950 to-zinc-900 pointer-events-none" />
                <div className="absolute inset-0 bg-primary/5 blur-3xl pointer-events-none scale-75 group-hover:scale-100 transition-transform duration-700" />
                
                <Image
                  src="/images/monogram.png"
                  alt="SD Colours Monogram"
                  width={220}
                  height={220}
                  className="opacity-80 brightness-150 drop-shadow-[0_10px_25px_rgba(204,163,83,0.25)] relative z-10 transition-transform duration-500 group-hover:scale-105"
                />
              </div>
            </div>

          </div>
        </section>

        {/* REPUTATION AND CREDENTIALS */}
        <section className="bg-white border-t border-zinc-200/50 py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {credentials.map((cred, idx) => {
                const IconComponent = cred.icon;
                return (
                  <div
                    key={idx}
                    className="border border-zinc-200/80 p-8 rounded-2xl flex flex-col gap-4 bg-zinc-50/30"
                  >
                    <div className="bg-primary/10 text-primary p-3 rounded-xl w-fit">
                      <IconComponent className="w-6 h-6" />
                    </div>
                    <div className="space-y-1">
                      <h3 className="font-bold text-secondary text-base">{cred.title}</h3>
                      <p className="text-zinc-500 text-xs leading-relaxed">{cred.desc}</p>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        </section>

        {/* BOTTOM VISIT OUR LAB BANNER */}
        <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
          <div className="bg-secondary text-white rounded-3xl p-8 sm:p-12 relative overflow-hidden shadow-2xl border border-white/5">
            {/* Background elements */}
            <div className="absolute inset-0 bg-gradient-to-r from-zinc-950 to-zinc-900 pointer-events-none" />
            <div className="absolute top-0 right-0 w-80 h-80 bg-primary/5 rounded-full blur-[100px] pointer-events-none translate-x-1/3 -translate-y-1/3" />
            
            <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
              <div className="lg:col-span-8 space-y-4">
                <h3 className="font-serif text-2xl sm:text-3xl font-bold">
                  Are You in Rourkela or Bhubaneswar? <br />
                  <span className="text-gradient">Come Visit Our Printing Lab!</span>
                </h3>
                <p className="text-zinc-400 text-xs max-w-xl leading-relaxed">
                  We invite professional photographers to schedule a lab tour. Come see our digital presses, feel the texture of our papers (NTR, Velvet, Metallic), and review physical samples of all our combo pads.
                </p>
              </div>
              <div className="lg:col-span-4 lg:text-right">
                <Link
                  href="/contact"
                  className="inline-flex bg-primary text-secondary px-8 py-4 rounded-full text-xs font-black uppercase hover:bg-primary-dark transition-all tracking-wider shadow-lg shadow-primary/15"
                >
                  Get Directions & Call
                </Link>
              </div>
            </div>
          </div>
        </section>

      </main>
      <Footer />
    </>
  );
}
