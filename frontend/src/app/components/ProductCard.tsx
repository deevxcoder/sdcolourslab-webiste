import Image from "next/image";
import { CheckCircle2, MessageSquare } from "lucide-react";

export interface ProductProps {
  id: number;
  name: string;
  category: string;
  description?: string;
  price: number;
  priceAlt?: number | null;
  sizes: string[];
  features: string[];
  tag?: string;
  image: string;
}

export default function ProductCard({
  name,
  category,
  price,
  sizes,
  features,
  tag,
  image,
}: ProductProps) {
  // Setup the WhatsApp message link
  const messageText = `Hi SD Colours Lab! I would like to inquire/order:
- Product: ${name}
- Category: ${category}
- Price: ₹${price}
- Available Sizes: ${sizes.join(", ")}`;
  const waLink = `https://wa.me/918895838987?text=${encodeURIComponent(messageText)}`;

  return (
    <div className="group rounded-2xl bg-white shadow-md border border-zinc-200/80 transition-all duration-300 hover:shadow-xl hover:border-zinc-300/80 overflow-hidden flex flex-col h-full">
      {/* Upper half: Image wrapper */}
      <div className="relative aspect-[4/3] bg-zinc-100 overflow-hidden shrink-0">
        {tag && (
          <span
            className={`absolute top-3 right-3 z-10 rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider shadow-sm ${
              tag.toLowerCase() === "premium"
                ? "bg-secondary text-white"
                : "bg-primary text-secondary"
            }`}
          >
            {tag}
          </span>
        )}
        <Image
          src={image}
          alt={name}
          fill
          sizes="(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw"
          className="object-cover transition-transform duration-500 group-hover:scale-105"
        />
      </div>

      {/* Lower half: Content */}
      <div className="p-6 flex flex-col justify-between flex-grow gap-4">
        <div className="space-y-3">
          <span className="text-[10px] uppercase font-bold tracking-widest text-primary">
            {category}
          </span>
          <h3 className="font-serif text-lg font-bold text-secondary tracking-tight leading-snug group-hover:text-primary transition-colors">
            {name}
          </h3>
          
          <div className="flex items-baseline gap-1">
            <span className="text-2xl font-bold text-secondary">₹{price}</span>
            <span className="text-xs text-zinc-500 font-medium">onwards</span>
          </div>

          {/* Sizes */}
          {sizes.length > 0 && (
            <div className="flex flex-wrap gap-1.5 pt-1">
              {sizes.map((sz) => (
                <span
                  key={sz}
                  className="bg-accent text-secondary text-[10px] font-bold px-2 py-0.5 rounded-full ring-1 ring-primary/20 shrink-0"
                >
                  {sz}
                </span>
              ))}
            </div>
          )}

          {/* Features */}
          {features.length > 0 && (
            <ul className="space-y-1.5 pt-2 border-t border-zinc-100">
              {features.slice(0, 3).map((feat, index) => (
                <li key={index} className="flex items-start gap-2 text-xs text-zinc-600">
                  <CheckCircle2 className="w-3.5 h-3.5 text-primary shrink-0 mt-0.5" />
                  <span className="leading-normal">{feat}</span>
                </li>
              ))}
            </ul>
          )}
        </div>

        {/* Action Button */}
        <a
          href={waLink}
          target="_blank"
          rel="noopener noreferrer"
          className="flex items-center justify-center gap-2 w-full bg-secondary hover:bg-black text-white text-center text-xs font-bold rounded-xl py-3 transition-colors shadow-sm"
        >
          <MessageSquare className="w-3.5 h-3.5 text-primary" />
          Order via WhatsApp
        </a>
      </div>
    </div>
  );
}
