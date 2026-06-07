"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import Image from "next/image";
import { usePathname } from "next/navigation";
import { Menu, X, ChevronDown, LogIn, UserPlus, PhoneCall } from "lucide-react";
import { getBackendUrl } from "@/lib/backend";

export default function Header() {
  const [scrolled, setScrolled] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const pathname = usePathname();


  useEffect(() => {
    const handleScroll = () => {
      if (window.scrollY > 20) {
        setScrolled(true);
      } else {
        setScrolled(false);
      }
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const navLinks = [
    { name: "Home", href: "/" },
    { name: "Products", href: "/products" },
    { name: "Pricing", href: "/pricing" },
    { name: "About", href: "/about" },
    { name: "Contact", href: "/contact" },
  ];

  return (
    <header
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        scrolled
          ? "glass-dark py-2 shadow-lg"
          : "bg-secondary py-4"
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <div className="flex-shrink-0">
            <Link href="/" className="flex items-center">
              <Image
                src="/images/logo.png"
                alt="SD Colours Logo"
                width={160}
                height={60}
                className="h-10 sm:h-12 w-auto brightness-110"
                priority
              />
            </Link>
          </div>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex space-x-8">
            {navLinks.map((link) => {
              const isActive = pathname === link.href;
              return (
                <Link
                  key={link.name}
                  href={link.href}
                  className={`text-sm font-semibold tracking-wide transition-colors duration-200 ${
                    isActive
                      ? "text-primary border-b-2 border-primary pb-1"
                      : "text-white/90 hover:text-primary pb-1"
                  }`}
                >
                  {link.name}
                </Link>
              );
            })}
          </nav>

          {/* Right Section CTAs */}
          <div className="hidden md:flex items-center space-x-4">
            {/* Photographer Portal Dropdown */}
            <div className="relative">
              <button
                onClick={() => setDropdownOpen(!dropdownOpen)}
                onBlur={() => setTimeout(() => setDropdownOpen(false), 200)}
                className="flex items-center gap-1.5 border border-primary/40 bg-white/5 backdrop-blur-md px-4 py-2 rounded-full text-white text-xs font-semibold hover:bg-primary/15 hover:border-primary transition-all cursor-pointer"
              >
                Photographer Portal
                <ChevronDown className="w-3.5 h-3.5" />
              </button>

              {dropdownOpen && (
                <div className="absolute right-0 mt-2 w-48 rounded-xl bg-[#1f1f23] border border-white/10 shadow-2xl py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                  <Link
                    href={getBackendUrl("/login.php")}
                    className="flex items-center gap-2 px-4 py-2.5 text-xs text-white/90 hover:bg-primary/20 hover:text-white transition-colors"
                  >
                    <LogIn className="w-3.5 h-3.5 text-primary" />
                    Portal Login
                  </Link>
                  <Link
                    href="/register"
                    className="flex items-center gap-2 px-4 py-2.5 text-xs text-white/90 hover:bg-primary/20 hover:text-white transition-colors"
                  >
                    <UserPlus className="w-3.5 h-3.5 text-primary" />
                    Register (Sign Up)
                  </Link>
                </div>
              )}
            </div>

            {/* Order Now WhatsApp */}
            <a
              href="https://wa.me/918895838987?text=Hi%20SD%20Colours%20Lab!%20I'd%20like%20to%20inquire%20about%20ordering%20a%20photobook%20album."
              target="_blank"
              rel="noopener noreferrer"
              className="flex items-center gap-1.5 bg-primary text-secondary px-5 py-2 rounded-full text-xs font-bold hover:bg-primary-dark transition-all shadow-md hover:shadow-primary/20"
            >
              <PhoneCall className="w-3.5 h-3.5" />
              Order Now
            </a>
          </div>

          {/* Mobile Menu Button */}
          <div className="flex md:hidden">
            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="text-white hover:text-primary p-2 focus:outline-none cursor-pointer"
            >
              {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Sliding Drawer Overlay */}
      {mobileMenuOpen && (
        <div className="fixed inset-0 top-[72px] z-40 bg-black/60 backdrop-blur-sm md:hidden animate-in fade-in duration-200">
          <div className="bg-secondary w-full px-6 py-8 border-t border-white/10 flex flex-col gap-6 shadow-2xl animate-in slide-in-from-top duration-300">
            <nav className="flex flex-col gap-4">
              {navLinks.map((link) => {
                const isActive = pathname === link.href;
                return (
                  <Link
                    key={link.name}
                    href={link.href}
                    onClick={() => setMobileMenuOpen(false)}
                    className={`text-base font-semibold py-2 transition-colors ${
                      isActive ? "text-primary border-l-2 border-primary pl-3" : "text-white/80 hover:text-primary pl-3"
                    }`}
                  >
                    {link.name}
                  </Link>
                );
              })}
            </nav>

            <div className="border-t border-white/10 pt-6 flex flex-col gap-4">
              <div className="text-white/50 text-xs font-semibold uppercase tracking-wider pl-3">
                Photographer Portal
              </div>
              <Link
                href={getBackendUrl("/login.php")}
                onClick={() => setMobileMenuOpen(false)}
                className="flex items-center gap-3 text-white hover:text-primary text-sm font-semibold pl-3 py-1"
              >
                <LogIn className="w-4 h-4 text-primary" />
                Portal Login
              </Link>
              <Link
                href="/register"
                onClick={() => setMobileMenuOpen(false)}
                className="flex items-center gap-3 text-white hover:text-primary text-sm font-semibold pl-3 py-1"
              >
                <UserPlus className="w-4 h-4 text-primary" />
                Register (Sign Up)
              </Link>
              <a
                href="https://wa.me/918895838987?text=Hi%20SD%20Colours%20Lab!%20I'd%20like%20to%20inquire%20about%20ordering%20a%20photobook%20album."
                target="_blank"
                rel="noopener noreferrer"
                onClick={() => setMobileMenuOpen(false)}
                className="flex items-center justify-center gap-2 bg-primary text-secondary py-3 px-5 rounded-full text-sm font-bold hover:bg-primary-dark transition-all mt-2"
              >
                <PhoneCall className="w-4 h-4" />
                Order Now via WhatsApp
              </a>
            </div>
          </div>
        </div>
      )}
    </header>
  );
}
