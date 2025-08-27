import { API_BASE_URL } from '@/api/constants';

/**
 * Vrátí absolutní URL pro obrázek challenge.
 * - Pokud je to absolutní URL, použije ji přímo.
 * - Pokud začíná na "/", přilepí API_BASE_URL.
 * - Jinak vrátí null.
 */
export function resolvedImage(c) {
  const src = c?.image;
  if (!src) return null;
  try {
    new URL(src); // už je absolutní URL
    return src;
  } catch {
    if (src.startsWith('/') && API_BASE_URL) {
      return `${API_BASE_URL.replace(/\/+$/, '')}${src}`;
    }
    return src || null;
  }
}

/**
 * Handler pro chybu načtení obrázku – skryje element <img>.
 */
export function onImgError(e) {
  e.target.style.display = 'none';
}