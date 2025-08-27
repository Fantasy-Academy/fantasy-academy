// src/composables/useValidation.js
export function useValidation() {
  function isValidEmail(email = '') {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email).trim());
  }

  function isStrongPassword(password = '') {
    // min. 6 znaků, aspoň jedno velké písmeno a číslo
    return /^(?=.*[A-Z])(?=.*\d).{6,}$/.test(String(password));
  }

  // Signup → vrací objekt chyb pro jednotlivá pole
  function validateSignup({ name = '', email = '', password = '', confirm = '' }) {
    const errors = { name: '', email: '', password: '', confirm: '' };

    if (!name.trim()) errors.name = 'Jméno je povinné.';
    if (!email.trim()) errors.email = 'E-mail je povinný.';
    else if (!isValidEmail(email)) errors.email = 'Neplatný e-mail.';

    if (!password) errors.password = 'Heslo je povinné.';
    else if (!isStrongPassword(password))
      errors.password = 'Min. 6 znaků, jedno velké písmeno a číslo.';

    if (!confirm) errors.confirm = 'Potvrzení hesla je povinné.';
    else if (password !== confirm) errors.confirm = 'Hesla se neshodují.';

    return errors;
  }

  // Login → vrací JEDEN řetězec se souhrnnou chybou (jak už používáš)
  function validateLoginForm({ email = '', password = '' }) {
    if (!email.trim()) return 'E-mail je povinný.';
    if (!isValidEmail(email)) return 'Neplatný e-mail.';
    if (!String(password).trim()) return 'Heslo je povinné.';
    return '';
  }

  // Forgot password (jen e-mail)
  function validateForgotEmail(email = '') {
    if (!email.trim()) return 'Zadej e-mail.';
    if (!isValidEmail(email)) return 'Neplatný e-mail.';
    return '';
  }

  // Reset password (nové heslo)
  function validateResetPassword(newPassword = '') {
    if (!newPassword) return 'Zadej nové heslo.';
    if (!isStrongPassword(newPassword)) {
      return 'Heslo musí mít min. 6 znaků, obsahovat velké písmeno a číslo.';
    }
    return '';
  }

  return {
    isValidEmail,
    isStrongPassword,
    validateSignup,
    validateLoginForm,
    validateForgotEmail,
    validateResetPassword,
  };
}