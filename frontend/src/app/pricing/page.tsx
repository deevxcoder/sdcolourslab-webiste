"use client";

import { useState } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
import { Download, Calculator, PhoneCall, Plus, Minus, ArrowRight } from "lucide-react";

type Category = "albums" | "acrylics" | "leds" | "combos";

export default function Pricing() {
  const [activeTab, setActiveTab] = useState<Category>("albums");

  // State values for calculations
  const [albumPaper, setAlbumPaper] = useState("regular-glossy");
  const [albumPages, setAlbumPages] = useState(30);
  const [albumQty, setAlbumQty] = useState(1);

  const [acrylicSize, setAcrylicSize] = useState("12x18");
  const [acrylicQty, setAcrylicQty] = useState(1);

  const [ledSize, setLedSize] = useState("12x18");
  const [ledQty, setLedQty] = useState(1);

  const [comboPackage, setComboPackage] = useState("leather-2in1");
  const [comboQty, setComboQty] = useState(1);

  // 1. ALBUM PAPER TYPE CONSTANTS
  const albumPapers = [
    { key: "regular-glossy", name: "Regular Glossy (₹38/page)", rate: 38 },
    { key: "regular-heavy-glossy", name: "Regular Heavy Glossy (₹46/page)", rate: 46 },
    { key: "regular-matt", name: "Regular Matt (₹51/page)", rate: 51 },
    { key: "regular-heavy-matt", name: "Regular Heavy Matt (₹61/page)", rate: 61 },
    { key: "ntr-glossy-slim", name: "NTR Glossy Slim (₹52/page)", rate: 52 },
    { key: "ntr-heavy-glossy", name: "NTR Heavy Glossy (₹62/page)", rate: 62 },
    { key: "ntr-matt-slim", name: "NTR Matt Slim (₹62/page)", rate: 62 },
    { key: "ntr-heavy-matt", name: "NTR Heavy Matt (₹66/page)", rate: 66 },
    { key: "luster", name: "Luster (₹70/page)", rate: 70 },
    { key: "velvet-sheet", name: "Regular Velvet Sheet (₹60/page)", rate: 60 },
    { key: "ntr-velvet-sheet", name: "NTR Velvet Sheet (₹72/page)", rate: 72 },
    { key: "transparent-sheet", name: "Transparent Sheet (₹90/page)", rate: 90 },
    { key: "silky-metallic", name: "Silky Metallic (₹90/page)", rate: 90 },
    { key: "ultra-metallic", name: "Ultra Metallic (₹90/page)", rate: 90 },
    { key: "sparkle", name: "Sparkle (₹90/page)", rate: 90 },
    { key: "pearl-metallic", name: "Pearl Metallic (₹110/page)", rate: 110 },
    { key: "3d", name: "3D (₹110/page)", rate: 110 },
  ];

  // 2. WALL ACRYLIC CONSTANTS
  const acrylicSizes = [
    { size: "5x7", price: 350 },
    { size: "6x8", price: 500 },
    { size: "8x12", price: 650 },
    { size: "12x18", price: 750 },
    { size: "16x20", price: 1550 },
    { size: "20x24", price: 2250 },
    { size: "20x30", price: 2750 },
    { size: "24x36", price: 3150 },
  ];

  // 3. LED FRAMES CONSTANTS (Tiers)
  const ledSizes = [
    {
      size: "6x8",
      getRate: (q: number) => (q >= 50 ? 190 : q >= 25 ? 230 : q >= 15 ? 295 : 380),
    },
    {
      size: "8x12",
      getRate: (q: number) => (q >= 50 ? 310 : q >= 25 ? 360 : q >= 15 ? 380 : 412),
    },
    {
      size: "12x18",
      getRate: (q: number) => (q >= 50 ? 452 : q >= 25 ? 480 : q >= 15 ? 530 : 570),
    },
    {
      size: "12x36",
      getRate: (q: number) => (q >= 50 ? 750 : q >= 25 ? 895 : q >= 15 ? 1015 : 1050),
    },
    {
      size: "16x20",
      getRate: (q: number) => (q >= 50 ? 923 : q >= 25 ? 1010 : q >= 15 ? 1052 : 1115),
    },
    {
      size: "18x24",
      getRate: (q: number) => (q >= 50 ? 1050 : q >= 25 ? 1170 : q >= 15 ? 1210 : 1290),
    },
    {
      size: "24x36",
      getRate: (q: number) => (q >= 50 ? 1540 : q >= 25 ? 1650 : q >= 15 ? 1830 : 1910),
    },
  ];

  // 4. COMBO PACKS CONSTANTS
  const comboPackages = [
    { key: "leather-2in1", name: "Leather 2-in-1 Combo (Cover, Bag, LED, Calendar)", price: 1550 },
    { key: "acrylic-2in1", name: "Acrylic 2-in-1 Combo (Leather cover, Acrylic layout)", price: 1250 },
    { key: "wooden-4in1", name: "Wooden LAWood 4-in-1 Combo (Wooden cover, Bag, LED, Calendar)", price: 1850 },
    { key: "royal-4in1", name: "Royal 4-in-1 Combo (Leather cover, Briefcase box, LED)", price: 2250 },
    { key: "superior-silver-3in1", name: "Superior Silver (3 in 1) (Acrylic cover pad, Bag, Calendar)", price: 1750 },
    { key: "superior-silver-4in1", name: "Superior Silver (4 in 1) (Wood/Acrylic cover, Bag, LED)", price: 2100 },
    { key: "superior-gold-6in1", name: "Superior Gold+ (6 in 1) (Leather cover, Briefcase, Bag, LED)", price: 2550 },
    { key: "superior-platinum-6in1", name: "Superior Platinum (6 in 1) (Premium leather, Box, Bag, LED)", price: 3150 },
    { key: "inluxury-5in1", name: "Inluxury Combo (5 in 1) (Square Briefcase, Window Acrylic Cover)", price: 4100 },
    { key: "leatherism-7in1", name: "Leatherism Combo (7 in 1) (Furio Double Door Briefcase, 2 Pads)", price: 4500 },
  ];

  // REAL-TIME PRICING MATHEMATICS
  const calculateTotal = (): { total: number; detailString: string } => {
    switch (activeTab) {
      case "albums": {
        const paper = albumPapers.find((p) => p.key === albumPaper);
        const rate = paper ? paper.rate : 0;
        const total = rate * albumPages * albumQty;
        const detailString = `Wedding Album:\n- Paper: ${paper?.name}\n- Pages (Sides): ${albumPages}\n- Qty: ${albumQty}\n- Formula: ₹${rate} rate x ${albumPages} pages x ${albumQty} items`;
        return { total, detailString };
      }
      case "acrylics": {
        const item = acrylicSizes.find((a) => a.size === acrylicSize);
        const price = item ? item.price : 0;
        const total = price * acrylicQty;
        const detailString = `Wall Acrylic/Canvas:\n- Size: ${acrylicSize}\n- Qty: ${acrylicQty}\n- Rate: ₹${price} per item`;
        return { total, detailString };
      }
      case "leds": {
        const item = ledSizes.find((l) => l.size === ledSize);
        const rate = item ? item.getRate(ledQty) : 0;
        const total = rate * ledQty;
        const detailString = `LED Backlit Frame:\n- Size: ${ledSize}\n- Qty: ${ledQty}\n- Applied Rate: ₹${rate} (Volume break applied)`;
        return { total, detailString };
      }
      case "combos": {
        const pkg = comboPackages.find((c) => c.key === comboPackage);
        const price = pkg ? pkg.price : 0;
        const total = price * comboQty;
        const detailString = `Premium Combo Pack:\n- Package: ${pkg?.name}\n- Qty: ${comboQty}\n- Rate: ₹${price} per package`;
        return { total, detailString };
      }
      default:
        return { total: 0, detailString: "" };
    }
  };

  const { total, detailString } = calculateTotal();

  // COMPILE CUSTOM WHATSAPP MESSAGE LINK
  const getWhatsAppLink = () => {
    const textMessage = `Hi SD Colours Lab! I calculated an estimate on your B2B calculator:

${detailString}

Estimated Net Total: ₹${total} B2B Net
Please verify my order specs and confirm dispatch time.`;

    return `https://wa.me/918895838987?text=${encodeURIComponent(textMessage)}`;
  };

  return (
    <>
      <Header />
      <main className="flex-grow pt-24 bg-zinc-50">
        
        {/* Intro */}
        <section className="bg-secondary text-white py-16 text-center space-y-4">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span className="text-xs uppercase font-extrabold tracking-widest text-primary">
              Interactive Estimator
            </span>
            <h1 className="font-serif text-3xl sm:text-4xl font-bold tracking-tight">
              B2B Cost <span className="text-gradient">Estimator Tool</span>
            </h1>
            <p className="text-zinc-400 text-sm max-w-xl mx-auto mt-2">
              Select category details below to compute real-time wholesale printing charges based on layout styles, sheet sizes, and quantities.
            </p>
          </div>
        </section>

        {/* CALCULATOR INTERACTION CONTAINER */}
        <section className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {/* LEFT COLUMN: CONFIG CONTROLS */}
            <div className="lg:col-span-7 bg-white border border-zinc-200/80 p-6 sm:p-8 rounded-3xl shadow-sm space-y-8">
              
              {/* Category tabs */}
              <div className="border-b border-zinc-200">
                <div className="flex flex-wrap -mb-px gap-4">
                  {(["albums", "acrylics", "leds", "combos"] as Category[]).map((tab) => (
                    <button
                      key={tab}
                      onClick={() => setActiveTab(tab)}
                      className={`pb-4 text-xs font-bold uppercase tracking-wider border-b-2 transition-all cursor-pointer ${
                        activeTab === tab
                          ? "border-primary text-primary"
                          : "border-transparent text-zinc-400 hover:text-secondary"
                      }`}
                    >
                      {tab === "albums" && "Wedding Albums"}
                      {tab === "acrylics" && "Wall Acrylics"}
                      {tab === "leds" && "LED Frames"}
                      {tab === "combos" && "Combo Pads"}
                    </button>
                  ))}
                </div>
              </div>

              {/* Dynamic Tabs Fields */}
              <div className="space-y-6">
                
                {/* 1. Wedding Albums Fields */}
                {activeTab === "albums" && (
                  <div className="space-y-6">
                    <div className="space-y-2">
                      <label className="block text-xs font-bold uppercase text-secondary">
                        Select Album Paper & Style
                      </label>
                      <select
                        value={albumPaper}
                        onChange={(e) => setAlbumPaper(e.target.value)}
                        className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary"
                      >
                        {albumPapers.map((paper) => (
                          <option key={paper.key} value={paper.key}>
                            {paper.name}
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="space-y-2">
                      <label className="block text-xs font-bold uppercase text-secondary">
                        Number of Pages (Sides): <span className="text-primary font-black">{albumPages}</span>
                      </label>
                      <input
                        type="range"
                        min="20"
                        max="100"
                        step="2"
                        value={albumPages}
                        onChange={(e) => setAlbumPages(parseInt(e.target.value))}
                        className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-primary"
                      />
                      <div className="flex justify-between text-[10px] font-bold text-zinc-400">
                        <span>Min: 20 Pages</span>
                        <span>Max: 100 Pages</span>
                      </div>
                    </div>

                    <div className="space-y-2">
                      <label className="block text-xs font-bold uppercase text-secondary">
                        Quantity
                      </label>
                      <div className="flex items-center gap-3">
                        <button
                          onClick={() => setAlbumQty(Math.max(1, albumQty - 1))}
                          className="bg-zinc-100 hover:bg-zinc-200 p-2.5 rounded-xl transition-all cursor-pointer"
                        >
                          <Minus className="w-4 h-4 text-secondary" />
                        </button>
                        <span className="font-extrabold text-lg w-12 text-center">{albumQty}</span>
                        <button
                          onClick={() => setAlbumQty(albumQty + 1)}
                          className="bg-zinc-100 hover:bg-zinc-200 p-2.5 rounded-xl transition-all cursor-pointer"
                        >
                          <Plus className="w-4 h-4 text-secondary" />
                        </button>
                      </div>
                    </div>
                  </div>
                )}

                {/* 2. Acrylics Fields */}
                {activeTab === "acrylics" && (
                  <div className="space-y-6">
                    <div className="space-y-2">
                      <label className="block text-xs font-bold uppercase text-secondary">
                        Select Frame Size
                      </label>
                      <select
                        value={acrylicSize}
                        onChange={(e) => setAcrylicSize(e.target.value)}
                        className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary"
                      >
                        {acrylicSizes.map((a) => (
                          <option key={a.size} value={a.size}>
                            {a.size} Size (₹{a.price})
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="space-y-2">
                      <label className="block text-xs font-bold uppercase text-secondary">
                        Quantity
                      </label>
                      <div className="flex items-center gap-3">
                        <button
                          onClick={() => setAcrylicQty(Math.max(1, acrylicQty - 1))}
                          className="bg-zinc-100 hover:bg-zinc-200 p-2.5 rounded-xl transition-all cursor-pointer"
                        >
                          <Minus className="w-4 h-4 text-secondary" />
                        </button>
                        <span className="font-extrabold text-lg w-12 text-center">{acrylicQty}</span>
                        <button
                          onClick={() => setAcrylicQty(acrylicQty + 1)}
                          className="bg-zinc-100 hover:bg-zinc-200 p-2.5 rounded-xl transition-all cursor-pointer"
                        >
                          <Plus className="w-4 h-4 text-secondary" />
                        </button>
                      </div>
                    </div>
                  </div>
                )}

                {/* 3. LED Frames Fields */}
                {activeTab === "leds" && (
                  <div className="space-y-6">
                    <div className="space-y-2">
                      <label className="block text-xs font-bold uppercase text-secondary">
                        Select Frame Size
                      </label>
                      <select
                        value={ledSize}
                        onChange={(e) => setLedSize(e.target.value)}
                        className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary"
                      >
                        {ledSizes.map((l) => (
                          <option key={l.size} value={l.size}>
                            {l.size} LED Frame
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="space-y-2">
                      <label className="block text-xs font-bold uppercase text-secondary">
                        Quantity (Wholesale breaks applied automatically)
                      </label>
                      <div className="flex items-center gap-3">
                        <button
                          onClick={() => setLedQty(Math.max(1, ledQty - 1))}
                          className="bg-zinc-100 hover:bg-zinc-200 p-2.5 rounded-xl transition-all cursor-pointer"
                        >
                          <Minus className="w-4 h-4 text-secondary" />
                        </button>
                        <span className="font-extrabold text-lg w-12 text-center">{ledQty}</span>
                        <button
                          onClick={() => setLedQty(ledQty + 1)}
                          className="bg-zinc-100 hover:bg-zinc-200 p-2.5 rounded-xl transition-all cursor-pointer"
                        >
                          <Plus className="w-4 h-4 text-secondary" />
                        </button>
                      </div>
                      <div className="text-[10px] text-zinc-500 font-medium">
                        Volume guide: 15-24 pcs (Discount), 25-49 pcs (Bulk), 50+ pcs (Super Bulk)
                      </div>
                    </div>
                  </div>
                )}

                {/* 4. Combos Fields */}
                {activeTab === "combos" && (
                  <div className="space-y-6">
                    <div className="space-y-2">
                      <label className="block text-xs font-bold uppercase text-secondary">
                        Select Combo Set Pack
                      </label>
                      <select
                        value={comboPackage}
                        onChange={(e) => setComboPackage(e.target.value)}
                        className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary"
                      >
                        {comboPackages.map((c) => (
                          <option key={c.key} value={c.key}>
                            {c.name}
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="space-y-2">
                      <label className="block text-xs font-bold uppercase text-secondary">
                        Quantity
                      </label>
                      <div className="flex items-center gap-3">
                        <button
                          onClick={() => setComboQty(Math.max(1, comboQty - 1))}
                          className="bg-zinc-100 hover:bg-zinc-200 p-2.5 rounded-xl transition-all cursor-pointer"
                        >
                          <Minus className="w-4 h-4 text-secondary" />
                        </button>
                        <span className="font-extrabold text-lg w-12 text-center">{comboQty}</span>
                        <button
                          onClick={() => setComboQty(comboQty + 1)}
                          className="bg-zinc-100 hover:bg-zinc-200 p-2.5 rounded-xl transition-all cursor-pointer"
                        >
                          <Plus className="w-4 h-4 text-secondary" />
                        </button>
                      </div>
                    </div>
                  </div>
                )}

              </div>
            </div>

            {/* RIGHT COLUMN: CALCULATION SUMMARY */}
            <div className="lg:col-span-5 bg-secondary text-white p-8 rounded-3xl shadow-xl flex flex-col justify-between h-full min-h-[420px]">
              <div className="space-y-6">
                <div className="flex items-center gap-2 border-b border-white/10 pb-4">
                  <Calculator className="w-5 h-5 text-primary" />
                  <h3 className="font-bold text-xs uppercase tracking-widest text-primary">
                    Order Summary
                  </h3>
                </div>

                <div className="space-y-3">
                  <span className="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">
                    Selected Specs
                  </span>
                  <pre className="font-sans text-xs text-zinc-300 bg-white/5 border border-white/10 p-4 rounded-2xl whitespace-pre-wrap leading-relaxed">
                    {detailString}
                  </pre>
                </div>
              </div>

              <div className="space-y-6 mt-8">
                <div className="bg-white/5 border border-white/10 rounded-2xl p-6 flex flex-col items-center justify-center gap-1">
                  <span className="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">
                    Estimated B2B Net Cost
                  </span>
                  <span className="text-3xl font-black text-gradient">₹{total}</span>
                </div>

                <a
                  href={getWhatsAppLink()}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center justify-center gap-2 w-full bg-primary hover:bg-primary-dark text-secondary py-4 px-6 rounded-full text-xs font-black uppercase transition-all shadow-lg hover:shadow-primary/10 tracking-wider"
                >
                  <PhoneCall className="w-4 h-4" />
                  Send Order Estimate
                </a>
              </div>
            </div>

          </div>
        </section>

        {/* PDF DOWNLOAD CARDS */}
        <section className="bg-white border-t border-zinc-200/50 py-20">
          <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            <div className="text-center space-y-3">
              <span className="text-xs uppercase font-extrabold tracking-widest text-primary">
                Reference PDFs
              </span>
              <h2 className="font-serif text-2xl sm:text-3xl font-bold text-secondary">
                Download Complete B2B Catalog Sheets
              </h2>
              <div className="w-12 h-1 bg-primary mx-auto rounded-full" />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              
              <div className="bg-zinc-50 border border-zinc-200/80 p-6 sm:p-8 rounded-2xl flex flex-col justify-between items-start gap-6 hover:border-primary/45 transition-colors">
                <div className="space-y-2">
                  <span className="bg-primary/10 text-primary font-bold text-[10px] px-3 py-1 rounded-full uppercase tracking-wider">
                    Full Price List
                  </span>
                  <h3 className="font-serif text-lg font-bold text-secondary">
                    B2B Wedding Albums & General Printing Rates
                  </h3>
                  <p className="text-zinc-500 text-xs leading-relaxed">
                    Download the official layout catalog covering standard glossy sheets, velvet sheets, NTR synthetic pages, custom box embossings, and foil prints.
                  </p>
                </div>
                <a
                  href="https://backend.sdcolourslab.in/price_list.pdf"
                  download
                  className="flex items-center gap-2 text-xs font-bold text-secondary hover:text-primary transition-colors border-b border-secondary hover:border-primary pb-0.5"
                >
                  <Download className="w-4 h-4" />
                  Download Price List PDF (8 MB)
                </a>
              </div>

              <div className="bg-zinc-50 border border-zinc-200/80 p-6 sm:p-8 rounded-2xl flex flex-col justify-between items-start gap-6 hover:border-primary/45 transition-colors">
                <div className="space-y-2">
                  <span className="bg-secondary text-white font-bold text-[10px] px-3 py-1 rounded-full uppercase tracking-wider">
                    Combo Catalog
                  </span>
                  <h3 className="font-serif text-lg font-bold text-secondary">
                    Premium Presentation Combo Pads Brochure
                  </h3>
                  <p className="text-zinc-500 text-xs leading-relaxed">
                    Download the comprehensive brochure containing photorealistic renderings of all 19 combo sets, briefcases, velvet carrybags, and layout configurations.
                  </p>
                </div>
                <a
                  href="https://backend.sdcolourslab.in/combo_price_list.pdf"
                  download
                  className="flex items-center gap-2 text-xs font-bold text-secondary hover:text-primary transition-colors border-b border-secondary hover:border-primary pb-0.5"
                >
                  <Download className="w-4 h-4" />
                  Download Combo Brochure PDF (62 MB)
                </a>
              </div>

            </div>
          </div>
        </section>

      </main>
      <Footer />
    </>
  );
}
