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

    if (!name.trim()) errors.name = 'Name is compulsory.';
    if (!email.trim()) errors.email = 'E-mail is compulsory.';
    else if (!isValidEmail(email)) errors.email = 'Invalid e-mail.';

    if (!password) errors.password = 'Password is compulsory.';
    else if (!isStrongPassword(password))
      errors.password = 'Min. 6 characters, one capital letter and number.';

    if (!confirm) errors.confirm = 'Confirm password is compolsary.';
    else if (password !== confirm) errors.confirm = "Password doesn't match.";

    return errors;
  }

  // Login → vrací JEDEN řetězec se souhrnnou chybou (jak už používáš)
  function validateLoginForm({ email = '', password = '' }) {
    if (!email.trim()) return 'E-mail is compulsory.';
    if (!isValidEmail(email)) return 'Invalid e-mail.';
    if (!String(password).trim()) return 'Password is compulsory.';
    return '';
  }

  // Forgot password (jen e-mail)
  function validateForgotEmail(email = '') {
    if (!email.trim()) return 'insert e-mail.';
    if (!isValidEmail(email)) return 'Invalid e-mail.';
    return '';
  }

  // Reset password (nové heslo)
  function validateResetPassword(newPassword = '') {
    if (!newPassword) return 'Insert new password.';
    if (!isStrongPassword(newPassword)) {
      return 'Min. 6 characters, one capital letter and number.';
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