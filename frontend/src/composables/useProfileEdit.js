// src/composables/useProfileEdit.js
import { ref } from 'vue';
import { apiEditProfile, apiChangePassword } from '@/api/me';
import { useProfile } from '@/composables/useProfile';
import { useValidation } from '@/composables/useValidation';

export function useProfileEdit() {
  const savingProfile = ref(false);
  const savingPassword = ref(false);
  const profileError = ref('');
  const passwordError = ref('');
  const passwordSuccess = ref(false);
  const profileSuccess = ref(false);

  const { load: reloadProfile } = useProfile();
  const { isStrongPassword } = useValidation();

  async function saveProfile({ name, userId = null }) {
    profileError.value = '';
    profileSuccess.value = false;
    if (!name || !name.trim()) {
      profileError.value = 'Name is required.';
      return;
    }
    savingProfile.value = true;
    try {
      await apiEditProfile({ name: name.trim(), userId });
      profileSuccess.value = true;
      await reloadProfile(); // refresh profilová data v celé app
    } catch (e) {
      profileError.value = e?.message || 'Failed to update profile.';
    } finally {
      savingProfile.value = false;
    }
  }

  async function changePassword(newPassword) {
    passwordError.value = '';
    passwordSuccess.value = false;
    if (!newPassword) {
      passwordError.value = 'Password is required.';
      return;
    }
    if (!isStrongPassword(newPassword)) {
      passwordError.value = 'Password must be at least 6 chars, include a capital letter and a number.';
      return;
    }
    savingPassword.value = true;
    try {
      await apiChangePassword(newPassword);
      passwordSuccess.value = true;
    } catch (e) {
      passwordError.value = e?.message || 'Failed to change password.';
    } finally {
      savingPassword.value = false;
    }
  }

  return {
    savingProfile,
    profileError,
    profileSuccess,
    saveProfile,

    savingPassword,
    passwordError,
    passwordSuccess,
    changePassword,
  };
}