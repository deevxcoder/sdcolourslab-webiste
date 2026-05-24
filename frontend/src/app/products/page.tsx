"use client";

import { useState, useEffect } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
import ProductCard from "../components/ProductCard";
import { BookOpen, FolderHeart, Zap, Grid, FileText, CheckCircle2 } from "lucide-react";
import Link from "next/link";

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

export default function Products() {
  const [products, setProducts] = useState<DbProduct[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const getBackendUrl = (path: string) => {
    const isDev = typeof window !== "undefined" && (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1");
    const base = isDev ? "http://127.0.0.1:8000" : "https://backend.sdcolourslab.in";
    return `${base}${path}`;
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

  // Filter and map Albums
  const albums = products
    .filter((p) => p.category === "album")
    .map((p) => ({
      id: p.id,
      name: p.name,
      category: "Wedding Albums",
      description: p.description || "Premium wedding album printing with custom binding options.",
      price: p.price,
      priceAlt: p.price_alt,
      sizes: p.sizes,
      features: p.features,
      tag: p.tag || undefined,
      image: p.image || "/images/combos/superior-silver-3in1.jpg",
    }));

  // Filter and map Combos
  const combos = products
    .filter((p) => p.category === "combo")
    .map((p) => ({
      id: p.id,
      name: p.name,
      category: "Combo Photo Pads",
      description: p.description || "Handcrafted cover album combo set.",
      price: p.price,
      priceAlt: p.price_alt,
      sizes: p.sizes,
      features: p.features,
      tag: p.tag || undefined,
      image: p.image || "/images/combos/leather-2in1-bag.jpg",
    }));

  // Filter and map LED Frames
  const ledFrames = products
    .filter((p) => p.category === "led_frame")
    .map((p) => {
      const sizeStr = p.sizes[0] || p.name.replace(/led\s*frame/i, "").trim() || "Standard";
      return {
        size: sizeStr,
        rates: p.features.length > 0 ? p.features : [`Qty 1+: ₹${p.price}`],
      };
    });

  // Filter and map Wall Acrylics
  const acrylics = products
    .filter((p) => p.category === "wall_acrylic")
    .map((p) => {
      const sizeStr = p.sizes[0] || p.name.replace(/acrylic/i, "").trim() || "Standard";
      return {
        size: sizeStr,
        price: p.price,
      };
    })
    .sort((a, b) => a.price - b.price);

  return (
    <>
      <Header />
      <main className="flex-grow pt-24 bg-zinc-50">
        
        {/* Intro */}
        <section className="bg-white border-b border-zinc-200/50 py-16">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <span className="text-xs uppercase font-extrabold tracking-widest text-primary animate-pulse">
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
                href={getBackendUrl("/price_list.pdf")}
                download
                className="border border-zinc-300 text-secondary bg-white px-6 py-3 rounded-full text-xs font-bold hover:bg-zinc-50 transition-all flex items-center gap-2"
              >
                <FileText className="w-4 h-4 text-primary" />
                Download Price List PDF
              </a>
            </div>
          </div>
        </section>

        {loading ? (
          /* Premium Skeleton Loader */
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 space-y-20">
            {/* Albums skeleton */}
            <div className="space-y-8">
              <div className="h-8 w-48 bg-zinc-200 rounded-lg animate-pulse" />
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                {[1, 2, 3, 4].map((i) => (
                  <div key={i} className="rounded-2xl border border-zinc-200 bg-white p-6 space-y-4 animate-pulse h-96">
                    <div className="aspect-[4/3] bg-zinc-200 rounded-xl" />
                    <div className="h-6 bg-zinc-200 rounded w-3/4" />
                    <div className="h-4 bg-zinc-200 rounded w-1/2" />
                    <div className="space-y-2 pt-2">
                      <div className="h-3 bg-zinc-200 rounded w-full" />
                      <div className="h-3 bg-zinc-200 rounded w-5/6" />
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        ) : error ? (
          /* Error feedback */
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
            <div className="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl max-w-lg mx-auto shadow-sm">
              <p className="font-bold">Could not load catalog details</p>
              <p className="text-xs text-red-500 mt-1">{error}</p>
              <button
                onClick={() => window.location.reload()}
                className="mt-4 bg-red-700 text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-red-800 transition-colors"
              >
                Retry Request
              </button>
            </div>
          </div>
        ) : (
          <>
            {/* SECTION A: WEDDING ALBUMS */}
            {albums.length > 0 && (
              <section id="albums" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 scroll-mt-24 space-y-12 animate-fade-in">
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
            )}

            {/* SECTION B: COMBO PHOTO PADS */}
            {combos.length > 0 && (
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

                  <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-in">
                    {combos.map((item) => (
                      <ProductCard key={item.id} {...item} />
                    ))}
                  </div>
                </div>
              </section>
            )}

            {/* SECTION C: LED FRAMES */}
            {ledFrames.length > 0 && (
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

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 animate-fade-in">
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
            )}

            {/* SECTION D: WALL ACRYLIC & CANVAS */}
            {acrylics.length > 0 && (
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
                  <div className="bg-white border border-zinc-200 rounded-2xl overflow-hidden shadow-sm max-w-3xl animate-fade-in">
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
            )}
          </>
        )}

      </main>
      <Footer />
    </>
  );
}
