// src/composables/useValidation.js
export function useValidation() {
  function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  function isStrongPassword(password) {
    // min. 6 znaků, aspoň jedno velké písmeno a číslo
    return /^(?=.*[A-Z])(?=.*\d).{6,}$/.test(password);
  }

  function validateSignup({ name, email, password, confirm }) {
    const errors = { name: '', email: '', password: '', confirm: '' };

    if (!name.trim()) errors.name = 'Jméno je povinné.';
    if (!email.trim()) errors.email = 'E-mail je povinný.';
    else if (!isValidEmail(email)) errors.email = 'Neplatný e-mail.';

    if (!password) errors.password = 'Heslo je povinné.';
    else if (!isStrongPassword(password))
      errors.password = 'Min. 6 znaků, velké písmeno a číslo.';

    if (!confirm) errors.confirm = 'Potvrzení hesla je povinné.';
    else if (password !== confirm) errors.confirm = 'Hesla se neshodují.';

    return errors;
  }

  return {
    isValidEmail,
    isStrongPassword,
    validateSignup
  };
}