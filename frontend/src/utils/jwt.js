export function decodeJwt(token) {
  if (!token) return null;

  try {
    const base64 = token.split('.')[1];
    if (!base64) return null;

    const payload = JSON.parse(atob(base64.replace(/-/g, '+').replace(/_/g, '/')));
    return payload;
  } catch (e) {
    console.warn("[decodeJwt] Failed to decode token:", e);
    return null;
  }
}