"use client";

import { useState } from "react";
import { getBackendUrl } from "@/lib/backend";
import Link from "next/link";
import Image from "next/image";
import {
  User,
  Mail,
  Phone,
  Lock,
  Building,
  MapPin,
  Eye,
  EyeOff,
  Loader2,
  CheckCircle,
  AlertCircle,
  ArrowLeft,
  ChevronRight,
  ShieldAlert,
} from "lucide-react";

export default function Register() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [studio, setStudio] = useState("");
  const [city, setCity] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");


  const handleRegisterSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    if (password !== confirmPassword) {
      setError("Passwords do not match.");
      return;
    }

    if (password.length < 6) {
      setError("Password must be at least 6 characters.");
      return;
    }

    setLoading(true);

    try {
      const response = await fetch(getBackendUrl("/api/auth/register"), {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({
          name,
          email,
          phone,
          studio_name: studio,
          city,
          password,
        }),
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        setError(data.message || "Registration failed. Please try again.");
      } else {
        setSuccess(data.message || "Registration successful!");
      }
    } catch (err) {
      setError("Unable to connect to the registration server. Please check your internet connection.");
    } finally {
      setLoading(false);
    }
  };

  const getWhatsAppApproveLink = () => {
    const textMsg = `Hi SD Colours Lab! I just submitted my B2B photographer registration on the website:
- Name: ${name}
- Email: ${email}
- Studio Name: ${studio}
- City: ${city}

Please review and approve my account login status. Thanks!`;
    return `https://wa.me/918895838987?text=${encodeURIComponent(textMsg)}`;
  };

  return (
    <main className="min-h-screen bg-[#0d0d0f] relative flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden font-sans">
      
      {/* Background visual glowing meshes */}
      <div className="absolute top-0 left-1/4 w-[350px] h-[350px] bg-primary/10 rounded-full blur-[120px] pointer-events-none scale-75" />
      <div className="absolute bottom-0 right-1/4 w-[350px] h-[350px] bg-primary/5 rounded-full blur-[120px] pointer-events-none scale-75" />
      
      <div className="max-w-md w-full relative z-10 space-y-6">
        
        {/* Logo and Back button */}
        <div className="flex flex-col items-center gap-4">
          <Link href="/" className="inline-flex items-center gap-1.5 text-xs text-zinc-400 hover:text-white transition-colors self-start">
            <ArrowLeft className="w-3.5 h-3.5" />
            Back to home
          </Link>
          <Image
            src="/images/logo.png"
            alt="SD Colours Logo"
            width={180}
            height={65}
            className="brightness-110"
            priority
          />
        </div>

        {/* Dynamic Card Area */}
        <div className="glass-dark border border-primary/30 rounded-3xl p-6 sm:p-8 shadow-2xl relative">
          
          {success ? (
            /* SUCCESS PANEL VIEW */
            <div className="space-y-6 animate-in fade-in duration-300">
              <div className="text-center space-y-3">
                <div className="inline-flex bg-primary/15 p-3 rounded-full text-primary mx-auto">
                  <CheckCircle className="w-10 h-10" />
                </div>
                <h2 className="font-serif text-2xl font-bold text-white tracking-tight">
                  Registration Received
                </h2>
                <p className="text-zinc-400 text-xs leading-relaxed">
                  Your photographer B2B account has been created successfully and is currently set to <span className="text-primary font-bold">Pending Approval</span>.
                </p>
              </div>

              {/* Status checklist */}
              <div className="bg-white/5 border border-white/10 rounded-2xl p-5 space-y-3.5 text-xs text-zinc-300">
                <div className="flex items-center gap-2">
                  <CheckCircle className="w-4 h-4 text-primary shrink-0" />
                  <span className="line-through text-zinc-500">Account credentials generated</span>
                </div>
                <div className="flex items-center gap-2">
                  <Loader2 className="w-4 h-4 text-primary shrink-0 animate-spin" />
                  <span className="font-semibold text-white">Awaiting administrator profile review</span>
                </div>
                <div className="flex items-start gap-2 pt-1 border-t border-white/5 text-[10px] text-zinc-400 leading-normal">
                  <ShieldAlert className="w-3.5 h-3.5 text-primary shrink-0 mt-0.5" />
                  <span>Standard approval takes 2-4 business hours. Approved users receive dashboard access automatically.</span>
                </div>
              </div>

              <div className="space-y-3">
                <a
                  href={getWhatsAppApproveLink()}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center justify-center gap-2 w-full bg-primary text-secondary font-black text-xs uppercase py-3.5 rounded-xl transition-all hover:bg-primary-dark shadow-lg shadow-primary/10 tracking-wider"
                >
                  Expedite via WhatsApp
                  <ChevronRight className="w-4 h-4" />
                </a>

                <Link
                  href={getBackendUrl("/login.php")}
                  className="block text-center border border-white/20 text-white hover:bg-white/10 text-xs font-semibold py-3.5 rounded-xl transition-all"
                >
                  Go to Login Portal
                </Link>
              </div>
            </div>
          ) : (
            /* REGISTRATION FORM VIEW */
            <div className="space-y-6 animate-in fade-in duration-300">
              <div className="text-center space-y-1">
                <h2 className="font-serif text-2xl font-bold text-white tracking-tight">
                  Join Photographer Network
                </h2>
                <p className="text-zinc-400 text-xs">
                  Create a B2B partner account to check active pricing, place orders, and track logs.
                </p>
              </div>

              {/* Error Alert */}
              {error && (
                <div className="bg-red-500/10 border border-red-500/30 text-red-400 text-xs p-4 rounded-xl flex items-start gap-2.5 leading-normal">
                  <AlertCircle className="w-4 h-4 shrink-0 mt-0.5 text-red-500" />
                  <span>{error}</span>
                </div>
              )}

              <form onSubmit={handleRegisterSubmit} className="space-y-4">
                
                {/* Full name input */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase text-zinc-400 tracking-wider">
                    Full Name *
                  </label>
                  <div className="relative">
                    <User className="absolute left-3.5 top-3.5 w-4 h-4 text-zinc-500" />
                    <input
                      type="text"
                      required
                      placeholder="Your full name"
                      value={name}
                      onChange={(e) => setName(e.target.value)}
                      className="w-full bg-[#1b1b1f] border border-white/10 text-white text-xs rounded-xl pl-10 pr-4 py-3.5 focus:outline-none focus:border-primary transition-all"
                    />
                  </div>
                </div>

                {/* Email input */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase text-zinc-400 tracking-wider">
                    Email Address *
                  </label>
                  <div className="relative">
                    <Mail className="absolute left-3.5 top-3.5 w-4 h-4 text-zinc-500" />
                    <input
                      type="email"
                      required
                      placeholder="photographer@studio.com"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      className="w-full bg-[#1b1b1f] border border-white/10 text-white text-xs rounded-xl pl-10 pr-4 py-3.5 focus:outline-none focus:border-primary transition-all"
                    />
                  </div>
                </div>

                {/* Phone & City dual row */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div className="space-y-1.5">
                    <label className="block text-[10px] font-bold uppercase text-zinc-400 tracking-wider">
                      Phone Number *
                    </label>
                    <div className="relative">
                      <Phone className="absolute left-3.5 top-3.5 w-4 h-4 text-zinc-500" />
                      <input
                        type="tel"
                        required
                        placeholder="+91 Phone"
                        value={phone}
                        onChange={(e) => setPhone(e.target.value)}
                        className="w-full bg-[#1b1b1f] border border-white/10 text-white text-xs rounded-xl pl-10 pr-4 py-3.5 focus:outline-none focus:border-primary transition-all"
                      />
                    </div>
                  </div>
                  <div className="space-y-1.5">
                    <label className="block text-[10px] font-bold uppercase text-zinc-400 tracking-wider">
                      City / Location
                    </label>
                    <div className="relative">
                      <MapPin className="absolute left-3.5 top-3.5 w-4 h-4 text-zinc-500" />
                      <input
                        type="text"
                        placeholder="Your city"
                        value={city}
                        onChange={(e) => setCity(e.target.value)}
                        className="w-full bg-[#1b1b1f] border border-white/10 text-white text-xs rounded-xl pl-10 pr-4 py-3.5 focus:outline-none focus:border-primary transition-all"
                      />
                    </div>
                  </div>
                </div>

                {/* Studio name input */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase text-zinc-400 tracking-wider">
                    Studio / Business Name
                  </label>
                  <div className="relative">
                    <Building className="absolute left-3.5 top-3.5 w-4 h-4 text-zinc-500" />
                    <input
                      type="text"
                      placeholder="e.g. Royal Photography Studio"
                      value={studio}
                      onChange={(e) => setStudio(e.target.value)}
                      className="w-full bg-[#1b1b1f] border border-white/10 text-white text-xs rounded-xl pl-10 pr-4 py-3.5 focus:outline-none focus:border-primary transition-all"
                    />
                  </div>
                </div>

                {/* Password input */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase text-zinc-400 tracking-wider">
                    Portal Password *
                  </label>
                  <div className="relative">
                    <Lock className="absolute left-3.5 top-3.5 w-4 h-4 text-zinc-500" />
                    <input
                      type={showPassword ? "text" : "password"}
                      required
                      placeholder="Min. 6 characters"
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      className="w-full bg-[#1b1b1f] border border-white/10 text-white text-xs rounded-xl pl-10 pr-10 py-3.5 focus:outline-none focus:border-primary transition-all"
                    />
                    <button
                      type="button"
                      onClick={() => setShowPassword(!showPassword)}
                      className="absolute right-3.5 top-3.5 text-zinc-500 hover:text-white transition-colors cursor-pointer"
                    >
                      {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                    </button>
                  </div>
                </div>

                {/* Confirm password input */}
                <div className="space-y-1.5">
                  <label className="block text-[10px] font-bold uppercase text-zinc-400 tracking-wider">
                    Confirm Password *
                  </label>
                  <div className="relative">
                    <Lock className="absolute left-3.5 top-3.5 w-4 h-4 text-zinc-500" />
                    <input
                      type={showConfirmPassword ? "text" : "password"}
                      required
                      placeholder="Repeat password"
                      value={confirmPassword}
                      onChange={(e) => setConfirmPassword(e.target.value)}
                      className="w-full bg-[#1b1b1f] border border-white/10 text-white text-xs rounded-xl pl-10 pr-10 py-3.5 focus:outline-none focus:border-primary transition-all"
                    />
                    <button
                      type="button"
                      onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                      className="absolute right-3.5 top-3.5 text-zinc-500 hover:text-white transition-colors cursor-pointer"
                    >
                      {showConfirmPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                    </button>
                  </div>
                </div>

                {/* Submit button */}
                <button
                  type="submit"
                  disabled={loading}
                  className="flex items-center justify-center gap-2 w-full bg-primary text-secondary font-black text-xs uppercase py-4 rounded-xl transition-all hover:bg-primary-dark shadow-lg shadow-primary/10 tracking-wider disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                >
                  {loading ? (
                    <>
                      <Loader2 className="w-4 h-4 animate-spin text-secondary" />
                      Creating Account...
                    </>
                  ) : (
                    "Submit Application"
                  )}
                </button>
              </form>

              <div className="text-center text-xs text-zinc-400 pt-2 border-t border-white/5">
                Already registered?{" "}
                <Link
                  href={getBackendUrl("/login.php")}
                  className="text-primary font-bold hover:underline transition-all"
                >
                  Log In Portal
                </Link>
              </div>
            </div>
          )}

        </div>

      </div>
    </main>
  );
}
