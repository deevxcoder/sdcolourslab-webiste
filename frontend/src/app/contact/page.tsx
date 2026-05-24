"use client";

import { useState } from "react";
import Header from "../components/Header";
import Footer from "../components/Footer";
import { Phone, MapPin, Mail, Clock, MessageSquare, Map, Maximize2 } from "lucide-react";

export default function Contact() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [studio, setStudio] = useState("");
  const [message, setMessage] = useState("");

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    const textMessage = `Hi SD Colours Lab! I submitted an inquiry from the website contact form:
- Name: ${name}
- Email: ${email}
- Phone: ${phone}
- Studio Name: ${studio}
- Message: ${message}`;

    const waLink = `https://wa.me/918895838987?text=${encodeURIComponent(textMessage)}`;
    window.open(waLink, "_blank");
  };

  const contactCards = [
    {
      icon: Phone,
      title: "Rourkela HQ Desk",
      details: "+91 8895838987",
      sub: "General & dispatch queries",
    },
    {
      icon: Phone,
      title: "Bhubaneswar Branch",
      details: "+91 9437255987",
      sub: "Local sample pick-ups",
    },
    {
      icon: Mail,
      title: "Email Desk",
      details: "info@sdcolourslab.in",
      sub: "B2B partnership inquiries",
    },
    {
      icon: Clock,
      title: "Lab Hours",
      details: "10:00 AM - 8:00 PM",
      sub: "Monday to Saturday (Sunday off)",
    },
  ];

  return (
    <>
      <Header />
      <main className="flex-grow pt-24 bg-zinc-50">
        
        {/* Intro */}
        <section className="bg-white border-b border-zinc-200/50 py-16 text-center">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <span className="text-xs uppercase font-extrabold tracking-widest text-primary">
              Get In Touch
            </span>
            <h1 className="font-serif text-4xl font-bold tracking-tight text-secondary">
              Contact <span className="text-gradient">Our Desk</span>
            </h1>
            <p className="text-zinc-500 text-sm max-w-xl mx-auto mt-2">
              Have a question about a custom size album, leather colors, or freight transit? Shoot us a message or call our branch offices directly.
            </p>
          </div>
        </section>

        {/* 4 COLUMN CARDS GRID */}
        <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {contactCards.map((card, idx) => {
              const IconComp = card.icon;
              return (
                <div
                  key={idx}
                  className="bg-white border border-zinc-200/80 p-6 rounded-2xl shadow-sm flex flex-col items-start gap-4 hover:border-primary/45 transition-colors"
                >
                  <div className="bg-primary/10 text-primary p-2.5 rounded-xl">
                    <IconComp className="w-5 h-5" />
                  </div>
                  <div>
                    <h3 className="font-bold text-secondary text-sm">{card.title}</h3>
                    <p className="text-primary font-bold text-sm mt-1">{card.details}</p>
                    <p className="text-zinc-400 text-xs mt-0.5">{card.sub}</p>
                  </div>
                </div>
              );
            })}
          </div>
        </section>

        {/* FORM AND MAP SECTION */}
        <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pb-24">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {/* Form Column */}
            <div className="lg:col-span-6 bg-white border border-zinc-200/80 p-8 rounded-3xl shadow-sm space-y-6">
              <div className="space-y-2">
                <h2 className="font-serif text-xl font-bold text-secondary">Inquiry Form</h2>
                <p className="text-zinc-500 text-xs">
                  Fill out this form and click submit to send a detailed inquiry to our dispatch team via WhatsApp.
                </p>
              </div>

              <form onSubmit={handleSubmit} className="space-y-4">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div className="space-y-1.5">
                    <label className="block text-xs font-bold uppercase text-secondary">Full Name</label>
                    <input
                      type="text"
                      required
                      placeholder="Your name"
                      value={name}
                      onChange={(e) => setName(e.target.value)}
                      className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                    />
                  </div>
                  <div className="space-y-1.5">
                    <label className="block text-xs font-bold uppercase text-secondary">Phone Number</label>
                    <input
                      type="tel"
                      required
                      placeholder="Your phone"
                      value={phone}
                      onChange={(e) => setPhone(e.target.value)}
                      className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div className="space-y-1.5">
                    <label className="block text-xs font-bold uppercase text-secondary">Email Address</label>
                    <input
                      type="email"
                      placeholder="Your email"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                    />
                  </div>
                  <div className="space-y-1.5">
                    <label className="block text-xs font-bold uppercase text-secondary">Photography Studio</label>
                    <input
                      type="text"
                      placeholder="Your studio name"
                      value={studio}
                      onChange={(e) => setStudio(e.target.value)}
                      className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
                    />
                  </div>
                </div>

                <div className="space-y-1.5">
                  <label className="block text-xs font-bold uppercase text-secondary">Your Message</label>
                  <textarea
                    rows={4}
                    required
                    placeholder="Describe your inquiry (e.g. 10 leather album combos for wedding portrait season)"
                    value={message}
                    onChange={(e) => setMessage(e.target.value)}
                    className="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none"
                  />
                </div>

                <button
                  type="submit"
                  className="flex items-center justify-center gap-2 w-full bg-secondary hover:bg-black text-white py-3.5 px-6 rounded-xl text-xs font-bold transition-all shadow-sm"
                >
                  <MessageSquare className="w-4 h-4 text-primary" />
                  Submit Inquiry via WhatsApp
                </button>
              </form>
            </div>

            {/* Interactive Map Column */}
            <div className="lg:col-span-6 bg-white border border-zinc-200/80 p-8 rounded-3xl shadow-sm space-y-6">
              <div className="flex items-center justify-between border-b border-zinc-100 pb-4">
                <div className="space-y-1">
                  <div className="flex items-center gap-1.5 text-primary">
                    <Map className="w-4 h-4" />
                    <span className="text-[10px] font-bold uppercase tracking-widest">Maps Hub</span>
                  </div>
                  <h2 className="font-serif text-lg font-bold text-secondary">Locate Our Lab</h2>
                </div>
              </div>

              {/* Styled Maps Embed */}
              <div className="relative rounded-2xl overflow-hidden border border-zinc-200 h-[280px]">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3691.874987747804!2d84.85195447596645!3d22.244795944900767!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a201b87fcf309b5%3A0xe679294e09cb9f64!2sSD%20Colours%20Photobook%20Lab!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin"
                  width="100%"
                  height="100%"
                  style={{ border: 0 }}
                  allowFullScreen
                  loading="lazy"
                  referrerPolicy="no-referrer-when-downgrade"
                  className="grayscale contrast-125 opacity-70 hover:grayscale-0 hover:opacity-100 transition-all duration-700 ease-in-out"
                />
              </div>

              <div className="space-y-4 text-xs">
                <div className="flex items-start gap-2.5">
                  <MapPin className="w-4 h-4 text-primary shrink-0 mt-0.5" />
                  <div>
                    <h4 className="font-bold text-secondary">Rourkela HQ Complex Location</h4>
                    <p className="text-zinc-500 mt-0.5">
                      Civil Township near Shanti Complex. Easy parking available. Follow Panposh Road.
                    </p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </section>

      </main>
      <Footer />
    </>
  );
}
