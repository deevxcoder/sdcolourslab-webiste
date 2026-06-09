"use client";

import { useState, useEffect } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
import { Download, Calculator, PhoneCall, Plus, Minus, FileText } from "lucide-react";
import { getBackendUrl } from "@/lib/backend";

type Category = "albums" | "acrylics" | "leds" | "combos";

interface DbProduct {
  id: number;
  name: string;
  category: string;
  description: string;
  price: number;
  price_alt: number | null;
  sizes: string[];
  features: string[];
  tag: string | null;
  image: string | null;
}

interface AlbumPaperOption {
  name: string;
  rate: number;
}

export default function Pricing() {
  const [activeTab, setActiveTab] = useState<Category>("albums");
  const [products, setProducts] = useState<DbProduct[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Dynamic selection states
  const [selectedAlbumId, setSelectedAlbumId] = useState<number | "">("");
  const [selectedPaperName, setSelectedPaperName] = useState<string>("");
  const [albumPages, setAlbumPages] = useState(30);
  const [albumQty, setAlbumQty] = useState(1);

  const [selectedAcrylicId, setSelectedAcrylicId] = useState<number | "">("");
  const [acrylicQty, setAcrylicQty] = useState(1);

  const [selectedLedId, setSelectedLedId] = useState<number | "">("");
  const [ledQty, setLedQty] = useState(1);

  const [selectedComboId, setSelectedComboId] = useState<number | "">("");
  const [comboQty, setComboQty] = useState(1);


  const parseAlbumPapers = (features: string[]): AlbumPaperOption[] => {
    const list: AlbumPaperOption[] = [];
    features.forEach((f) => {
      const match = f.match(/^(.*?)\s*–\s*₹?\s*(\d+)/);
      if (match) {
        list.push({
          name: match[1].trim(),
          rate: parseFloat(match[2]),
        });
      }
    });
    return list;
  };

  const calculateLedDiscountPrice = (qty: number, features: string[], basePrice: number): number => {
    let activePrice = basePrice;
    let maxQtyThreshold = 0;
    features.forEach((f) => {
      const match = f.match(/Qty\s+(\d+)\+:\s*₹?\s*(\d+)/i);
      if (match) {
        const threshold = parseInt(match[1]);
        const price = parseFloat(match[2]);
        if (qty >= threshold && threshold > maxQtyThreshold) {
          maxQtyThreshold = threshold;
          activePrice = price;
        }
      }
    });
    return activePrice;
  };

  useEffect(() => {
    async function fetchProducts() {
      try {
        const res = await fetch(getBackendUrl("/api/products"));
        if (!res.ok) throw new Error("Failed to fetch products");
        const json = await res.json();
        if (json.success && Array.isArray(json.data)) {
          setProducts(json.data);
        } else {
          throw new Error("Invalid API response format");
        }
      } catch (err: any) {
        setError(err.message || "An error occurred");
      } finally {
        setLoading(false);
      }
    }
    fetchProducts();
  }, []);

  // Initialize selection states when products load
  useEffect(() => {
    if (products.length > 0) {
      const albumList = products.filter((p) => p.category === "album");
      if (albumList.length > 0) {
        setSelectedAlbumId(albumList[0].id);
        const papers = parseAlbumPapers(albumList[0].features);
        if (papers.length > 0) {
          setSelectedPaperName(papers[0].name);
        }
      }
      
      const acrylicList = products.filter((p) => p.category === "wall_acrylic");
      if (acrylicList.length > 0) {
        setSelectedAcrylicId(acrylicList[0].id);
      }
      
      const ledList = products.filter((p) => p.category === "led_frame");
      if (ledList.length > 0) {
        setSelectedLedId(ledList[0].id);
      }
      
      const comboList = products.filter((p) => p.category === "combo");
      if (comboList.length > 0) {
        setSelectedComboId(comboList[0].id);
      }
    }
  }, [products]);

  const handleAlbumChange = (id: number) => {
    setSelectedAlbumId(id);
    const alb = products.find((p) => p.id === id);
    if (alb) {
      const papers = parseAlbumPapers(alb.features);
      if (papers.length > 0) {
        setSelectedPaperName(papers[0].name);
      } else {
        setSelectedPaperName("");
      }
    }
  };

  // Filtered lists
  const dbAlbums = products.filter((p) => p.category === "album");
  const dbAcrylics = products.filter((p) => p.category === "wall_acrylic").sort((a, b) => a.price - b.price);
  const dbLeds = products.filter((p) => p.category === "led_frame");
  const dbCombos = products.filter((p) => p.category === "combo");

  // Selected object references
  const currentAlbum = products.find((p) => p.id === selectedAlbumId);
  const albumPapers = currentAlbum ? parseAlbumPapers(currentAlbum.features) : [];
  const currentAcrylic = products.find((p) => p.id === selectedAcrylicId);
  const currentLed = products.find((p) => p.id === selectedLedId);
  const currentCombo = products.find((p) => p.id === selectedComboId);

  // REAL-TIME PRICING MATHEMATICS
  const calculateTotal = (): { total: number; detailString: string } => {
    if (loading) {
      return { total: 0, detailString: "Loading B2B catalog data..." };
    }
    if (products.length === 0) {
      return { total: 0, detailString: "No products available." };
    }

    switch (activeTab) {
      case "albums": {
        if (!currentAlbum) return { total: 0, detailString: "Select an album product" };
        const paper = albumPapers.find((p) => p.name === selectedPaperName) || (albumPapers.length > 0 ? albumPapers[0] : null);
        const rate = paper ? paper.rate : currentAlbum.price;
        const total = rate * albumPages * albumQty;
        const detailString = `Wedding Album:\n- Album: ${currentAlbum.name}\n- Finish/Paper: ${paper ? paper.name : "Default Finish"}\n- Pages (Sides): ${albumPages}\n- Qty: ${albumQty}\n- Formula: ₹${rate} rate x ${albumPages} pages x ${albumQty} items`;
        return { total, detailString };
      }
      case "acrylics": {
        if (!currentAcrylic) return { total: 0, detailString: "Select an acrylic size" };
        const sizeStr = currentAcrylic.sizes[0] || "Standard";
        const price = currentAcrylic.price;
        const total = price * acrylicQty;
        const detailString = `Wall Acrylic/Canvas:\n- Product: ${currentAcrylic.name} (${sizeStr})\n- Qty: ${acrylicQty}\n- Rate: ₹${price} per item`;
        return { total, detailString };
      }
      case "leds": {
        if (!currentLed) return { total: 0, detailString: "Select an LED size" };
        const sizeStr = currentLed.sizes[0] || "Standard";
        const rate = calculateLedDiscountPrice(ledQty, currentLed.features, currentLed.price);
        const total = rate * ledQty;
        const detailString = `LED Backlit Frame:\n- Size: ${sizeStr}\n- Qty: ${ledQty}\n- Applied Rate: ₹${rate} (Volume break applied)`;
        return { total, detailString };
      }
      case "combos": {
        if (!currentCombo) return { total: 0, detailString: "Select a combo set" };
        const price = currentCombo.price;
        const total = price * comboQty;
        const detailString = `Premium Combo Pack:\n- Package: ${currentCombo.name}\n- Qty: ${comboQty}\n- Rate: ₹${price} per package`;
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
            <span className="text-xs uppercase font-extrabold tracking-widest text-primary animate-pulse">
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

              {loading ? (
                <div className="space-y-4 animate-pulse py-8">
                  <div className="h-4 bg-zinc-200 rounded w-1/4" />
                  <div className="h-10 bg-zinc-200 rounded-xl w-full" />
                  <div className="h-4 bg-zinc-200 rounded w-1/3 mt-6" />
                  <div className="h-10 bg-zinc-200 rounded-xl w-full" />
                </div>
              ) : error ? (
                <div className="bg-red-50 text-red-700 p-4 rounded-xl text-xs font-bold border border-red-200 text-center">
                  Could not load products. Please check connection.
                  <p className="text-[10px] font-normal text-red-500 mt-1">{error}</p>
                </div>
              ) : (
                /* Dynamic Tabs Fields */
                <div className="space-y-6">
                  
                  {/* 1. Wedding Albums Fields */}
                  {activeTab === "albums" && (
                    <div className="space-y-6">
                      <div className="space-y-2">
                        <label className="block text-xs font-bold uppercase text-secondary">
                          Select Album Product
                        </label>
                        <select
                          value={selectedAlbumId}
                          onChange={(e) => handleAlbumChange(Number(e.target.value))}
                          className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary font-semibold text-secondary"
                        >
                          {dbAlbums.map((alb) => (
                            <option key={alb.id} value={alb.id}>
                              {alb.name}
                            </option>
                          ))}
                        </select>
                      </div>

                      {albumPapers.length > 0 && (
                        <div className="space-y-2">
                          <label className="block text-xs font-bold uppercase text-secondary">
                            Select Paper Type & Style
                          </label>
                          <select
                            value={selectedPaperName}
                            onChange={(e) => setSelectedPaperName(e.target.value)}
                            className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary text-secondary"
                          >
                            {albumPapers.map((paper) => (
                              <option key={paper.name} value={paper.name}>
                                {paper.name} (₹{paper.rate}/page)
                              </option>
                            ))}
                          </select>
                        </div>
                      )}

                      <div className="space-y-2">
                        <label className="block text-xs font-bold uppercase text-secondary">
                          Number of Pages (Sides): <span className="text-primary font-black">{albumPages}</span>
                        </label>
                        <input
                          type="range"
                          min="2"
                          max="100"
                          step="2"
                          value={albumPages}
                          onChange={(e) => setAlbumPages(parseInt(e.target.value))}
                          className="w-full h-2 bg-zinc-200 rounded-lg appearance-none cursor-pointer accent-primary"
                        />
                        <div className="flex justify-between text-[10px] font-bold text-zinc-400">
                          <span>Min: 2 Pages</span>
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
                          value={selectedAcrylicId}
                          onChange={(e) => setSelectedAcrylicId(Number(e.target.value))}
                          className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary font-semibold text-secondary"
                        >
                          {dbAcrylics.map((a) => (
                            <option key={a.id} value={a.id}>
                              {a.sizes[0] || a.name} size (₹{a.price})
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
                          value={selectedLedId}
                          onChange={(e) => setSelectedLedId(Number(e.target.value))}
                          className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary font-semibold text-secondary"
                        >
                          {dbLeds.map((l) => (
                            <option key={l.id} value={l.id}>
                              {l.sizes[0] || l.name} LED Frame
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
                        {currentLed && currentLed.features.length > 0 && (
                          <div className="text-[10px] text-zinc-500 font-medium space-y-0.5 mt-2 bg-zinc-50 p-3 rounded-lg border border-zinc-100">
                            <span className="block font-bold text-secondary uppercase tracking-wider mb-1">Volume Pricing Guide:</span>
                            {currentLed.features.map((feat, idx) => (
                              <span key={idx} className="block">• {feat}</span>
                            ))}
                          </div>
                        )}
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
                          value={selectedComboId}
                          onChange={(e) => setSelectedComboId(Number(e.target.value))}
                          className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary font-semibold text-secondary"
                        >
                          {dbCombos.map((c) => (
                            <option key={c.id} value={c.id}>
                              {c.name} (₹{c.price})
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
              )}
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

              <div className="space-y-6 mt-8 animate-fade-in">
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
              
              <div className="bg-zinc-50 border border-zinc-200/80 p-6 sm:p-8 rounded-2xl flex flex-col justify-between items-start gap-6 hover:border-primary/45 transition-colors animate-fade-in">
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
                  href={getBackendUrl("/price_list.pdf")}
                  download
                  className="flex items-center gap-2 text-xs font-bold text-secondary hover:text-primary transition-colors border-b border-secondary hover:border-primary pb-0.5"
                >
                  <Download className="w-4 h-4" />
                  Download Price List PDF (8 MB)
                </a>
              </div>

              <div className="bg-zinc-50 border border-zinc-200/80 p-6 sm:p-8 rounded-2xl flex flex-col justify-between items-start gap-6 hover:border-primary/45 transition-colors animate-fade-in">
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
                  href={getBackendUrl("/combo_price_list.pdf")}
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
