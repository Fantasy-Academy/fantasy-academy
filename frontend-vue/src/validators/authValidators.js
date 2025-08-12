export function validateLoginForm({ email, password }) {
  if (!email.trim()) {
    return 'Please fill email';
  }
  if (!password.trim()) {
    return 'Please fill password';
  }
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(email)) {
    return 'Invalid email format';
  }
  return null;
}