import Link from "next/link";
import Image from "next/image";
import { Mail, Phone, MapPin, Printer } from "lucide-react";

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-secondary text-white border-t border-white/10">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-12">
          
          {/* Column 1: Brand & Tagline */}
          <div className="space-y-6">
            <Link href="/" className="inline-block">
              <Image
                src="/images/logo.png"
                alt="SD Colours Logo"
                width={165}
                height={60}
                className="h-12 w-auto brightness-110"
              />
            </Link>
            <p className="text-zinc-400 text-sm leading-relaxed max-w-sm">
              SD Colours Photobook Lab is India's fast & professional B2B printing partner. We specialize in producing premium wedding albums, leather combo photo pads, personalized LED frames, and high-impact canvas prints.
            </p>
            <div className="flex items-center gap-2 pt-2 text-primary font-semibold text-xs tracking-wider uppercase">
              <Printer className="w-4 h-4" />
              State-Of-The-Art Printing Lab
            </div>
          </div>

          {/* Column 2: Solutions & Navigation */}
          <div className="grid grid-cols-2 gap-6">
            <div>
              <h3 className="text-xs font-bold uppercase tracking-widest text-primary mb-4">
                Products
              </h3>
              <ul className="space-y-2.5 text-sm text-zinc-400">
                <li>
                  <Link href="/products" className="hover:text-primary transition-colors">
                    Wedding Albums
                  </Link>
                </li>
                <li>
                  <Link href="/products" className="hover:text-primary transition-colors">
                    Combo Photo Pads
                  </Link>
                </li>
                <li>
                  <Link href="/products" className="hover:text-primary transition-colors">
                    LED Frames
                  </Link>
                </li>
                <li>
                  <Link href="/products" className="hover:text-primary transition-colors">
                    Wall Canvas
                  </Link>
                </li>
              </ul>
            </div>
            
            <div>
              <h3 className="text-xs font-bold uppercase tracking-widest text-primary mb-4">
                Company
              </h3>
              <ul className="space-y-2.5 text-sm text-zinc-400">
                <li>
                  <Link href="/" className="hover:text-primary transition-colors">
                    Home
                  </Link>
                </li>
                <li>
                  <Link href="/pricing" className="hover:text-primary transition-colors">
                    Pricing
                  </Link>
                </li>
                <li>
                  <Link href="/about" className="hover:text-primary transition-colors">
                    About Us
                  </Link>
                </li>
                <li>
                  <Link href="/contact" className="hover:text-primary transition-colors">
                    Contact Us
                  </Link>
                </li>
              </ul>
            </div>
          </div>

          {/* Column 3: Contact Details */}
          <div className="space-y-6">
            <h3 className="text-xs font-bold uppercase tracking-widest text-primary">
              Contact Our Desk
            </h3>
            
            <div className="space-y-4 text-sm text-zinc-300">
              {/* Rourkela HQ */}
              <div className="flex items-start gap-3">
                <MapPin className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                <div>
                  <h4 className="font-bold text-white text-xs">Rourkela HQ Lab (Main Branch)</h4>
                  <p className="text-zinc-400 text-xs">
                    Shanti Complex, Panposh Road, Civil Township, Rourkela, Odisha - 769004
                  </p>
                  <p className="flex items-center gap-1.5 mt-1 text-primary text-xs font-semibold">
                    <Phone className="w-3.5 h-3.5" />
                    +91 8895838987
                  </p>
                </div>
              </div>

              {/* Bhubaneswar Office */}
              <div className="flex items-start gap-3 pt-2">
                <MapPin className="w-5 h-5 text-primary shrink-0 mt-0.5" />
                <div>
                  <h4 className="font-bold text-white text-xs">Bhubaneswar Branch Desk</h4>
                  <p className="text-zinc-400 text-xs">
                    Plot No. 129, Saheed Nagar, Bhubaneswar, Odisha - 751007
                  </p>
                  <p className="flex items-center gap-1.5 mt-1 text-primary text-xs font-semibold">
                    <Phone className="w-3.5 h-3.5" />
                    +91 9437255987
                  </p>
                </div>
              </div>

              {/* Email Desk */}
              <div className="flex items-center gap-3 pt-2 border-t border-white/5">
                <Mail className="w-4 h-4 text-primary shrink-0" />
                <a href="mailto:info@sdcolourslab.in" className="hover:text-primary text-xs transition-colors">
                  info@sdcolourslab.in
                </a>
              </div>
            </div>
          </div>
        </div>

        {/* Bottom copyright bar */}
        <div className="border-t border-white/5 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-zinc-500 gap-4">
          <p>© {currentYear} SD Colours Photobook Lab. All rights reserved.</p>
          <div className="flex gap-6">
            <Link href="https://backend.sdcolourslab.in/login.php" className="hover:text-primary transition-colors">
              Photographer Dashboard
            </Link>
            <Link href="/register" className="hover:text-primary transition-colors">
              B2B Register
            </Link>
          </div>
        </div>
      </div>
    </footer>
  );
}
