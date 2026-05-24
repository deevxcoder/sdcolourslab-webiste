import Header from "../components/Header";
import Footer from "../components/Footer";
import ProductCard from "../components/ProductCard";
import { BookOpen, FolderHeart, Zap, Grid, FileText, CheckCircle2 } from "lucide-react";
import Link from "next/link";

export default function Products() {
  const albums = [
    {
      id: 101,
      name: "Regular Glossy & Matt Albums",
      category: "Wedding Albums",
      description: "Standard professional B2B wedding albums with glossy or matte thermal lamination.",
      price: 38, // price per page
      sizes: ["12x18", "12x36"],
      features: ["Glossy: ₹38/page", "Matt: ₹51/page", "Lay-flat flush mount pages", "Hard-bound standard cover"],
      tag: "Standard",
      image: "/images/combos/superior-silver-3in1.jpg",
    },
    {
      id: 102,
      name: "NTR Slim & Heavy Albums",
      category: "Wedding Albums",
      description: "Non-Tearable synthetic sheets with slim or thick options for extra durability and layout flexibility.",
      price: 52, // price per page
      sizes: ["12x18", "12x24", "12x36"],
      features: ["Slim Sheet: ₹52/page", "Heavy Sheet: ₹62/page", "100% Tear-resistant synthetic core", "Stain & dust proof coating"],
      tag: "Durability Plus",
      image: "/images/combos/superior-silver-4in1.jpg",
    },
    {
      id: 103,
      name: "Velvet Metallic & Sparkle Album",
      category: "Wedding Albums",
      description: "Luxe velvet textures combined with transparent metallic highlight layers for high-fashion photo books.",
      price: 60, // price per page
      sizes: ["12x18", "15x20", "12x36"],
      features: ["Velvet: ₹60/page", "Silky Metallic: ₹90/page", "Pearl Metallic: ₹110/page", "Rich deep color gamut"],
      tag: "Luxury Finish",
      image: "/images/combos/inluxury-premiumster-6in1.jpg",
    },
    {
      id: 104,
      name: "Mini B2B Pocket Albums",
      category: "Wedding Albums",
      description: "Cute identical miniature replicas of the main wedding album, perfect for parents and relatives.",
      price: 28, // price per page
      sizes: ["6x8", "8x10"],
      features: ["Mini Glossy: ₹28/page", "Mini Matt: ₹30/page", "Mini NTR: ₹38/page", "Easy to carry layout"],
      tag: "Parent Replica",
      image: "/images/combos/superior-gold-6in1.jpg",
    },
  ];

  const combos = [
    {
      id: 201,
      name: "Leather 2-in-1 Presentation Combo",
      category: "Combo Photo Pads",
      price: 1550,
      sizes: ["12x18"],
      features: ["Cover Leather Pad", "Photo Bag", "LED Frame", "Calendar"],
      tag: "Best Value",
      image: "/images/combos/leather-2in1-bag.jpg",
    },
    {
      id: 202,
      name: "Acrylic 2-in-1 Premium Combo",
      category: "Combo Photo Pads",
      price: 1250,
      sizes: ["12x18"],
      features: ["Leather Cover Pad", "Full Acrylic Front Cover Layout"],
      tag: "Minimalist",
      image: "/images/combos/acrylic-2in1.jpg",
    },
    {
      id: 203,
      name: "Wooden LAWood 4-in-1 Combo",
      category: "Combo Photo Pads",
      price: 1850,
      sizes: ["12x18"],
      features: ["Teakwood Finished Cover", "Leather Bag", "LED Frame", "Calendar"],
      tag: "Elite Wood",
      image: "/images/combos/wooden-4in1.jpg",
    },
    {
      id: 204,
      name: "Royal 4-in-1 Presentation Set",
      category: "Combo Photo Pads",
      price: 2250,
      sizes: ["12x18"],
      features: ["Premium Leather Cover", "Leather Briefcase Box", "LED Frame", "Calendar"],
      tag: "Royal Class",
      image: "/images/combos/royal-4in1.jpg",
    },
    {
      id: 205,
      name: "Superior Silver 3-in-1 Combo",
      category: "Combo Photo Pads",
      price: 1750,
      sizes: ["12x18"],
      features: ["Acrylic Cover Pad", "Leather Bag", "Calendar"],
      tag: "Sleek Silver",
      image: "/images/combos/superior-silver-3in1.jpg",
    },
    {
      id: 206,
      name: "Superior Silver 4-in-1 Combo",
      category: "Combo Photo Pads",
      price: 2100,
      sizes: ["12x18"],
      features: ["Wooden + Acrylic Cover Pad", "Leather Bag", "LED Frame", "Calendar"],
      tag: "Popular Choice",
      image: "/images/combos/superior-silver-4in1.jpg",
    },
    {
      id: 207,
      name: "Superior Gold+ 6-in-1 Presentation Set",
      category: "Combo Photo Pads",
      price: 2550,
      sizes: ["12x18"],
      features: ["Leather Cover Album", "Matching Briefcase", "Bag", "LED Frame", "Calendar", "Replica Mini Book"],
      tag: "All-Inclusive",
      image: "/images/combos/superior-gold-6in1.jpg",
    },
    {
      id: 208,
      name: "Superior Platinum 6-in-1 Royal Combo",
      category: "Combo Photo Pads",
      price: 3150,
      sizes: ["12x18"],
      features: ["Exquisite Heavy Leather Album", "Briefcase Box", "Bespoke Bag", "LED Frame", "Calendar", "Mini Book"],
      tag: "Luxe Platinum",
      image: "/images/combos/superior-platinum-6in1.jpg",
    },
    {
      id: 209,
      name: "Proluxury 5-in-1 Inluxury Combo",
      category: "Combo Photo Pads",
      price: 4100,
      sizes: ["12x18"],
      features: ["Square Briefcase", "Window Acrylic Cover Album", "Luxe Matching Tote Bag"],
      tag: "Ultra Luxury",
      image: "/images/combos/inluxury-proluxury-5in1.jpg",
    },
  ];

  const ledFrames = [
    {
      size: "6x8",
      rates: ["Qty 1-14: ₹380", "Qty 15-24: ₹295", "Qty 25-49: ₹230", "Qty 50+: ₹190"],
    },
    {
      size: "8x12",
      rates: ["Qty 1-14: ₹412", "Qty 15-24: ₹380", "Qty 25-49: ₹360", "Qty 50+: ₹310"],
    },
    {
      size: "12x18",
      rates: ["Qty 1-14: ₹570", "Qty 15-24: ₹530", "Qty 25-49: ₹480", "Qty 50+: ₹452"],
    },
    {
      size: "12x36",
      rates: ["Qty 1-14: ₹1050", "Qty 15-24: ₹1015", "Qty 25-49: ₹895", "Qty 50+: ₹750"],
    },
    {
      size: "16x20",
      rates: ["Qty 1-14: ₹1115", "Qty 15-24: ₹1052", "Qty 25-49: ₹1010", "Qty 50+: ₹923"],
    },
    {
      size: "18x24",
      rates: ["Qty 1-14: ₹1290", "Qty 15-24: ₹1210", "Qty 25-49: ₹1170", "Qty 50+: ₹1050"],
    },
    {
      size: "24x36",
      rates: ["Qty 1-14: ₹1910", "Qty 15-24: ₹1830", "Qty 25-49: ₹1650", "Qty 50+: ₹1540"],
    },
  ];

  const acrylics = [
    { size: "5x7", price: 350 },
    { size: "6x8", price: 500 },
    { size: "8x12", price: 650 },
    { size: "12x18", price: 750 },
    { size: "16x20", price: 1550 },
    { size: "20x24", price: 2250 },
    { size: "20x30", price: 2750 },
    { size: "24x36", price: 3150 },
  ];

  return (
    <>
      <Header />
      <main className="flex-grow pt-24 bg-zinc-50">
        
        {/* Intro */}
        <section className="bg-white border-b border-zinc-200/50 py-16">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <span className="text-xs uppercase font-extrabold tracking-widest text-primary">
              Pricing & Catalog
            </span>
            <h1 className="font-serif text-4xl sm:text-5xl font-bold tracking-tight text-secondary">
              Our <span className="text-gradient">B2B Product Range</span>
            </h1>
            <p className="text-zinc-500 text-sm max-w-2xl mx-auto">
              Professional printing rates structured for photographers, design studios, and laboratories. Explore wedding albums, complete sets, and glowing backlit frames.
            </p>
            <div className="flex justify-center gap-4 pt-4">
              <Link
                href="/pricing"
                className="bg-primary text-secondary px-6 py-3 rounded-full text-xs font-bold hover:bg-primary-dark transition-all flex items-center gap-2 shadow-md shadow-primary/10"
              >
                Cost Estimator Calculator
              </Link>
              <a
                href="https://backend.sdcolourslab.in/price_list.pdf"
                download
                className="border border-zinc-300 text-secondary bg-white px-6 py-3 rounded-full text-xs font-bold hover:bg-zinc-50 transition-all flex items-center gap-2"
              >
                <FileText className="w-4 h-4 text-primary" />
                Download Price List PDF
              </a>
            </div>
          </div>
        </section>

        {/* SECTION A: WEDDING ALBUMS */}
        <section id="albums" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 scroll-mt-24 space-y-12">
          <div className="space-y-3">
            <div className="flex items-center gap-2 text-primary">
              <BookOpen className="w-5 h-5" />
              <span className="text-xs uppercase font-bold tracking-widest">Section A</span>
            </div>
            <h2 className="font-serif text-2xl sm:text-3xl font-bold text-secondary">
              Photo Album Printing
            </h2>
            <div className="border-b-2 border-primary w-24" />
            <p className="text-zinc-500 text-xs max-w-2xl">
              *Calculated on rates per page (side) of print sheet. We support lay-flat flush bindings, hot thermal laminations, and premium water-resistant seals.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {albums.map((item) => (
              <ProductCard key={item.id} {...item} />
            ))}
          </div>
        </section>

        {/* SECTION B: COMBO PHOTO PADS */}
        <section id="combos" className="bg-white border-y border-zinc-200/50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 scroll-mt-24 space-y-12">
            <div className="space-y-3">
              <div className="flex items-center gap-2 text-primary">
                <FolderHeart className="w-5 h-5" />
                <span className="text-xs uppercase font-bold tracking-widest">Section B</span>
              </div>
              <h2 className="font-serif text-2xl sm:text-3xl font-bold text-secondary">
                Combo Photo Pad Products
              </h2>
              <div className="border-b-2 border-primary w-24" />
              <p className="text-zinc-500 text-xs max-w-2xl">
                Ready-to-deliver wedding packages that include handcrafted cover albums, matched protection bags or presentation briefcases, backlit LED frames, and pocket calendar items.
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {combos.map((item) => (
                <ProductCard key={item.id} {...item} />
              ))}
            </div>
          </div>
        </section>

        {/* SECTION C: LED FRAMES */}
        <section id="led-frames" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 scroll-mt-24 space-y-12">
          <div className="space-y-3">
            <div className="flex items-center gap-2 text-primary">
              <Zap className="w-5 h-5" />
              <span className="text-xs uppercase font-bold tracking-widest">Section C</span>
            </div>
            <h2 className="font-serif text-2xl sm:text-3xl font-bold text-secondary">
              LED Backlit Photo Frames
            </h2>
            <div className="border-b-2 border-primary w-24" />
            <p className="text-zinc-500 text-xs max-w-2xl">
              Tiered wholesale rates structured based on the volume count ordered in a single batch. Perfect for portrait packages and commercial studio display collections.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {ledFrames.map((frame, index) => (
              <div
                key={index}
                className="bg-white border border-zinc-200/80 rounded-2xl p-6 shadow-sm space-y-4 hover:border-primary/45 transition-colors"
              >
                <div className="flex items-center justify-between border-b border-zinc-100 pb-3">
                  <span className="font-serif text-lg font-bold text-secondary">{frame.size} Size</span>
                  <span className="bg-accent text-secondary text-[10px] font-bold px-2 py-0.5 rounded-full">
                    LED Frame
                  </span>
                </div>
                <ul className="space-y-2 text-xs text-zinc-600">
                  {frame.rates.map((rate, rIdx) => (
                    <li key={rIdx} className="flex items-center gap-2">
                      <CheckCircle2 className="w-3.5 h-3.5 text-primary shrink-0" />
                      <span>{rate}</span>
                    </li>
                  ))}
                </ul>
                <a
                  href={`https://wa.me/918895838987?text=Hi%20SD%20Colours%20Lab!%20I'd%20like%20to%20place%20an%20order%20for%20the%20${frame.size}%20LED%20Frames.`}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="block text-center bg-secondary hover:bg-black text-white text-[10px] font-bold py-2 rounded-lg transition-colors w-full"
                >
                  Order via WhatsApp
                </a>
              </div>
            ))}
          </div>
        </section>

        {/* SECTION D: WALL ACRYLIC & CANVAS */}
        <section id="canvas" className="bg-white border-t border-zinc-200/50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 scroll-mt-24 space-y-12">
            <div className="space-y-3">
              <div className="flex items-center gap-2 text-primary">
                <Grid className="w-5 h-5" />
                <span className="text-xs uppercase font-bold tracking-widest">Section D</span>
              </div>
              <h2 className="font-serif text-2xl sm:text-3xl font-bold text-secondary">
                Wall Acrylics & Canvas Prints
              </h2>
              <div className="border-b-2 border-primary w-24" />
              <p className="text-zinc-500 text-xs max-w-2xl">
                Luxury wall panels. Printed directly behind 5mm plexiglass sheets or wrapped onto robust gallery wooden stretcher frames.
              </p>
            </div>

            {/* Price List Table */}
            <div className="bg-white border border-zinc-200 rounded-2xl overflow-hidden shadow-sm max-w-3xl">
              <table className="w-full text-left border-collapse">
                <thead>
                  <tr className="bg-secondary text-white text-xs uppercase tracking-wider font-semibold">
                    <th className="p-4 sm:p-5">Plaque size (Inches)</th>
                    <th className="p-4 sm:p-5">Acrylic / Canvas B2B Price</th>
                    <th className="p-4 sm:p-5 text-right">Inquiry</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-200 text-xs sm:text-sm">
                  {acrylics.map((item, idx) => (
                    <tr key={idx} className="hover:bg-zinc-50 transition-colors">
                      <td className="p-4 sm:p-5 font-bold text-secondary">{item.size} Size</td>
                      <td className="p-4 sm:p-5 font-semibold text-primary">₹{item.price} net</td>
                      <td className="p-4 sm:p-5 text-right">
                        <a
                          href={`https://wa.me/918895838987?text=Hi%20SD%20Colours%20Lab!%20I'd%20like%20to%20order%20the%20${item.size}%20Wall%20Acrylic/Canvas%20at%2520%E2%82%B9${item.price}.`}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="inline-flex bg-zinc-100 hover:bg-primary hover:text-secondary text-secondary font-bold text-[10px] px-3 py-1.5 rounded-lg transition-colors border border-zinc-200"
                        >
                          Order
                        </a>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </section>

      </main>
      <Footer />
    </>
  );
}
