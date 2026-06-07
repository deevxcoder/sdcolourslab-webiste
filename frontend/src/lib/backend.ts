/**
 * Returns the absolute URL for a backend path.
 *
 * Uses NEXT_PUBLIC_BACKEND_URL so both the SSR pass and the client
 * hydration receive the exact same string — avoiding React hydration
 * attribute mismatches caused by `typeof window` checks.
 *
 * Set NEXT_PUBLIC_BACKEND_URL in:
 *   .env.local  → local dev  (e.g. http://127.0.0.1:8000)
 *   .env        → production (e.g. https://backend.sdcolourslab.in)
 */
const BACKEND_BASE =
  process.env.NEXT_PUBLIC_BACKEND_URL ?? "https://backend.sdcolourslab.in";

export function getBackendUrl(path: string): string {
  return `${BACKEND_BASE}${path}`;
}
