import Header from "./components/Header";
import Footer from "./components/Footer";
import ProductCard from "./components/ProductCard";
import Image from "next/image";
import Link from "next/link";
import {
  Truck,
  Award,
  Layers,
  FileText,
  Phone,
  ArrowRight,
  BookOpen,
  FolderHeart,
  Grid,
  Zap,
} from "lucide-react";

export default function Home() {
  // 6 Representative featured B2B products
  const featuredProducts = [
    {
      id: 1,
      name: "Leather 2-in-1 Premium Combo Pad",
      category: "Combo Photo Pads",
      description: "Includes custom cover leather pad, matching photo bag, LED frame, and table calendar.",
      price: 1550,
      sizes: ["12x18", "12x36"],
      features: ["Fine Italian Leather Cover", "Custom Carry Bag Included", "Gold Accented Embossed Bindings"],
      tag: "Best Seller",
      image: "/images/combos/leather-2in1-bag.jpg",
    },
    {
      id: 2,
      name: "Wooden LAWood 4-in-1 Combo Package",
      category: "Combo Photo Pads",
      description: "Premium wooden cover pad, matching leather briefcase box, mini table LED frame, and calendar.",
      price: 1850,
      sizes: ["12x18", "12x24"],
      features: ["Solid Teakwood Finished Cover", "Premium Velvet Lined Storage Box", "All-In-One Display Desk Kit"],
      tag: "Premium",
      image: "/images/combos/wooden-4in1.jpg",
    },
    {
      id: 3,
      name: "Superior Platinum 6-in-1 Royal Set",
      category: "Combo Photo Pads",
      description: "Ultra luxurious set comprising full leather cover album, square briefcase box, presentation bag, mini book, LED desk frame, and photo calendar.",
      price: 3150,
      sizes: ["12x18", "15x20"],
      features: ["Handcrafted Dual-Tone Leather", "Plush Presentation Briefcase", "Matching Pocket Mini Book Included"],
      tag: "Royal Choice",
      image: "/images/combos/superior-platinum-6in1.jpg",
    },
    {
      id: 4,
      name: "Acrylic 2-in-1 Premium Photo Album",
      category: "Combo Photo Pads",
      description: "Sleek full acrylic glass front layout combined with luxury leather cover pad back bindings.",
      price: 1250,
      sizes: ["12x18", "12x36"],
      features: ["High Gloss Shatterproof Acrylic", "Ultra HD Matte Print Sheet Pages", "Stitchless Flat lay Binding"],
      tag: "Modern",
      image: "/images/combos/acrylic-2in1.jpg",
    },
    {
      id: 5,
      name: "Ultra HD NTR Velvet Wedding Album",
      category: "Wedding Albums",
      description: "Non-Tearable Slim sheets layered with premium velvet finishing to produce deep blacks and rich colors.",
      price: 2160, // based on NTR Heavy Velvet rates
      sizes: ["12x18", "12x36"],
      features: ["Velvety Soft Touch Sheets", "Water & Fingerprint Resistant", "Perfect Lay-Flat Presentation"],
      tag: "Photographers Fav",
      image: "/images/combos/superior-silver-3in1.jpg",
    },
    {
      id: 6,
      name: "Acrylic Wall Frame (12x18)",
      category: "Wall Acrylics & Canvas",
      description: "High impact wall art printed directly behind 5mm crystal clear acrylic glass with finished polished edges.",
      price: 750,
      sizes: ["8x12", "12x18", "24x36"],
      features: ["Waterproof Back Panel Plates", "Direct Ultra HD UV Flatbed Ink", "Sturdy Wall Mount Studs Included"],
      tag: "Wall Art",
      image: "/images/combos/inluxury-proluxury-5in1.jpg",
    },
  ];

  const categories = [
    {
      name: "Wedding Albums",
      description: "Regular, NTR Slim, Velvet & Sparkle Lay-Flat Sheets",
      icon: BookOpen,
      href: "/products#albums",
    },
    {
      name: "Combo Photo Pads",
      description: "Premium Presentation Sets (briefcase, frames & bags)",
      icon: FolderHeart,
      href: "/products#combos",
    },
    {
      name: "LED Frames",
      description: "Backlit Glowing Desk and Wall Photo Displays",
      icon: Zap,
      href: "/products#led-frames",
    },
    {
      name: "Wall Canvas & Acrylics",
      description: "Large format prints, mounting plaques & wraps",
      icon: Grid,
      href: "/products#canvas",
    },
  ];

  return (
    <>
      <Header />
      <main className="flex-grow pt-16">
        
        {/* HERO SECTION */}
        <section className="relative min-h-[90vh] bg-secondary flex items-center overflow-hidden">
          {/* Subtle Background Accent Pattern */}
          <div className="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-zinc-800/30 via-zinc-950 to-black pointer-events-none" />
          
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10 w-full">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
              
              {/* Text Area */}
              <div className="lg:col-span-7 space-y-8 text-center lg:text-left">
                <div className="inline-flex items-center gap-2 border border-primary/30 bg-primary/5 rounded-full px-4 py-1.5 text-xs text-primary font-semibold tracking-wider uppercase animate-pulse">
                  <span>Professional B2B Photobook Printing</span>
                </div>
                
                <h1 className="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-white tracking-tight leading-[1.1]">
                  Creativity <span className="text-gradient">Photobook Company</span> in India
                </h1>
                
                <p className="text-zinc-300 text-base sm:text-lg leading-relaxed max-w-xl mx-auto lg:mx-0">
                  Your Fast & Professional Printing Partner. Handcrafting stunning wedding albums, premium leather combos, and wall frames using advanced print engineering.
                </p>

                <div className="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-4">
                  <a
                    href="https://wa.me/918895838987?text=Hi%20SD%20Colours%20Lab!%20I%2527m%20a%20photographer%20and%20I%20want%20to%20place%20an%20order."
                    target="_blank"
                    rel="noopener noreferrer"
                    className="bg-primary text-secondary font-extrabold text-sm py-4 px-8 rounded-full hover:bg-primary-dark transition-all text-center shadow-lg shadow-primary/20"
                  >
                    Start Order (WhatsApp)
                  </a>
                  
                  <Link
                    href="/pricing"
                    className="border border-white/30 text-white font-semibold text-sm py-4 px-8 rounded-full hover:bg-white/10 hover:border-white transition-all text-center flex items-center justify-center gap-2"
                  >
                    <FileText className="w-4 h-4 text-primary" />
                    Calculator & Rates
                  </Link>

                  <a
                    href="tel:+918895838987"
                    className="border border-primary/40 bg-white/5 text-primary font-bold text-sm py-4 px-8 rounded-full hover:bg-primary/10 transition-all text-center flex items-center justify-center gap-2"
                  >
                    <Phone className="w-4 h-4" />
                    Call Lab
                  </a>
                </div>
              </div>

              {/* Monogram Monolith Graphics */}
              <div className="hidden lg:flex lg:col-span-5 justify-center relative">
                <div className="absolute inset-0 bg-primary/10 rounded-full blur-[100px] pointer-events-none scale-75" />
                <div className="relative border-4 border-white/5 bg-white/[0.02] p-8 rounded-full shadow-2xl transition-transform duration-700 hover:rotate-6">
                  <Image
                    src="/images/monogram.png"
                    alt="SD Colours Monogram Logo"
                    width={380}
                    height={380}
                    className="opacity-90 brightness-200 drop-shadow-[0_15px_35px_rgba(204,163,83,0.35)]"
                    priority
                  />
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* TRUST BADGING SECTION */}
        <section className="bg-zinc-50 border-y border-zinc-200/50 py-12">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              
              <div className="bg-white border border-zinc-200/60 p-6 rounded-2xl flex items-center gap-4 shadow-sm">
                <div className="bg-primary/10 p-3.5 rounded-xl shrink-0">
                  <Truck className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <h3 className="font-bold text-secondary text-sm">Shipping All Over India</h3>
                  <p className="text-zinc-500 text-xs mt-0.5">Reliable logistical transport direct to your studio.</p>
                </div>
              </div>

              <div className="bg-white border border-zinc-200/60 p-6 rounded-2xl flex items-center gap-4 shadow-sm">
                <div className="bg-primary/10 p-3.5 rounded-xl shrink-0">
                  <Award className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <h3 className="font-bold text-secondary text-sm">High-Definition Quality</h3>
                  <p className="text-zinc-500 text-xs mt-0.5">Stitchless lay-flat binding and rich pigments.</p>
                </div>
              </div>

              <div className="bg-white border border-zinc-200/60 p-6 rounded-2xl flex items-center gap-4 shadow-sm">
                <div className="bg-primary/10 p-3.5 rounded-xl shrink-0">
                  <Layers className="w-6 h-6 text-primary" />
                </div>
                <div>
                  <h3 className="font-bold text-secondary text-sm">Premium Wedding Combo Packages</h3>
                  <p className="text-zinc-500 text-xs mt-0.5">Complete presentations with bags, panels and mini books.</p>
                </div>
              </div>

            </div>
          </div>
        </section>

        {/* CORE OFFERINGS CATEGORIES */}
        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-12">
            <div className="space-y-3">
              <span className="text-xs uppercase font-extrabold tracking-widest text-primary">
                What We Do
              </span>
              <h2 className="font-serif text-3xl sm:text-4xl font-bold text-secondary tracking-tight">
                Our Core B2B Offerings
              </h2>
              <div className="w-16 h-1 bg-primary mx-auto rounded-full" />
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
              {categories.map((cat, idx) => {
                const IconComponent = cat.icon;
                return (
                  <Link
                    key={idx}
                    href={cat.href}
                    className="group border border-zinc-200/70 p-8 rounded-2xl shadow-sm hover:shadow-md hover:border-primary/50 transition-all flex flex-col items-center gap-4 text-center bg-zinc-50/50 hover:bg-white"
                  >
                    <div className="bg-secondary text-white border-4 border-white shadow-xl rounded-full p-4 group-hover:scale-105 group-hover:border-primary transition-all duration-300">
                      <IconComponent className="w-6 h-6 text-primary" />
                    </div>
                    <div className="space-y-1">
                      <h3 className="font-bold text-secondary group-hover:text-primary transition-colors text-base">
                        {cat.name}
                      </h3>
                      <p className="text-zinc-500 text-xs leading-relaxed">
                        {cat.description}
                      </p>
                    </div>
                  </Link>
                );
              })}
            </div>
          </div>
        </section>

        {/* FEATURED COLLECTIONS */}
        <section className="py-20 bg-zinc-50 border-t border-zinc-200/50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div className="flex flex-col sm:flex-row items-center justify-between gap-4">
              <div className="space-y-2 text-center sm:text-left">
                <span className="text-xs uppercase font-extrabold tracking-widest text-primary">
                  Spotlight
                </span>
                <h2 className="font-serif text-3xl font-bold text-secondary tracking-tight">
                  Featured B2B Printing Packages
                </h2>
              </div>
              <Link
                href="/products"
                className="flex items-center gap-1.5 text-sm font-bold text-secondary hover:text-primary transition-colors"
              >
                View Full Catalog
                <ArrowRight className="w-4 h-4" />
              </Link>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {featuredProducts.map((prod) => (
                <ProductCard key={prod.id} {...prod} />
              ))}
            </div>
          </div>
        </section>

        {/* BOTTOM CALL TO ACTION */}
        <section className="relative bg-secondary text-white py-24 overflow-hidden border-t border-white/10">
          {/* Background image overlay */}
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,_var(--tw-gradient-stops))] from-zinc-800/40 via-zinc-950 to-black pointer-events-none" />
          
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-8">
            <h2 className="font-serif text-4xl sm:text-5xl font-bold text-white tracking-tight leading-tight">
              Ready to Craft Your <br />
              <span className="text-gradient">Next Premium Wedding Album?</span>
            </h2>
            <p className="text-zinc-300 text-base max-w-xl mx-auto">
              Get direct B2B pricing, immediate support, and express shipping by contacting our Rourkela dispatch desk on WhatsApp.
            </p>
            <div className="flex justify-center pt-2">
              <a
                href="https://wa.me/918895838987?text=Hi%20SD%20Colours%20Lab!%20I%20have%20an%20album%20design%20ready.%20I%27d%20like%20to%20discuss%20printing%20rates%20and%20shipping."
                target="_blank"
                rel="noopener noreferrer"
                className="bg-primary text-secondary font-black text-sm py-4 px-10 rounded-full hover:bg-primary-dark transition-all shadow-xl shadow-primary/20 tracking-wider uppercase"
              >
                Start Album Order Now
              </a>
            </div>
          </div>
        </section>

      </main>
      <Footer />
    </>
  );
}
