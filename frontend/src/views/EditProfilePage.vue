<template>
  <section class="mx-auto max-w-3xl px-4 py-8">
    <h1 class="mb-6 font-bebas-neue text-3xl tracking-wide text-blue-black">Edit Profile</h1>

    <!-- Profile card -->
    <div class="rounded-2xl border border-charcoal/10 bg-white p-6 shadow-sm">
      <h2 class="font-alexandria text-xl font-semibold text-blue-black">Profile info</h2>
      <p class="mt-1 text-sm text-cool-gray">Update your public name. Your email is not editable here.</p>

      <div class="mt-4 grid gap-4 grid-cols-1">
        <div>
          <label class="mb-1 block text-sm font-medium text-blue-black">Name</label>
          <input
            v-model.trim="name"
            type="text"
            placeholder="Your display name"
            class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-golden-yellow"
            :class="profileError ? 'border-vibrant-coral' : 'border-charcoal/20'"
            autocomplete="name"
          />
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-blue-black">E-mail</label>
          <input
            :value="profile.email"
            type="email"
            disabled
            class="w-full cursor-not-allowed rounded-lg border border-charcoal/10 bg-dark-white/40 px-3 py-2 text-cool-gray"
          />
        </div>
      </div>

      <p v-if="profileError" class="mt-3 rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-3 text-sm text-vibrant-coral">
        {{ profileError }}
      </p>
      <p v-if="profileSuccess" class="mt-3 rounded-xl border border-pistachio/30 bg-pistachio/10 p-3 text-sm text-pistachio">
        Profile has been updated.
      </p>

      <div class="mt-4 flex gap-3">
        <button
          @click="onSaveProfile"
          :disabled="savingProfile"
          class="rounded-lg bg-blue-black px-4 py-2 font-alexandria font-semibold text-white shadow-main hover:opacity-90 disabled:opacity-60"
        >
          {{ savingProfile ? 'Saving…' : 'Save changes' }}
        </button>
        <router-link
          to="/profile"
          class="rounded-lg border border-charcoal/20 bg-white px-4 py-2 font-alexandria font-semibold text-blue-black hover:bg-dark-white"
        >
          Cancel
        </router-link>
      </div>
    </div>

    <!-- Divider -->
    <div class="my-8 h-px w-full bg-dark-white"></div>
  </section>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useProfile } from '@/composables/useProfile';
import { useAuth } from '@/composables/useAuth';
import { useProfileEdit } from '@/composables/useProfileEdit';

document.title = 'Fantasy Academy | Edit Profile';

const { user } = useAuth();
const { me } = useProfile();

const profile = computed(() => ({
  id: me.value?.id ?? user.value?.id ?? null,
  name: me.value?.name ?? user.value?.name ?? '',
  email: me.value?.email ?? user.value?.email ?? '',
  userId: me.value?.userId ?? null,
}));

const name = ref(profile.value.name);

const {
  savingProfile, profileError, profileSuccess, saveProfile,
} = useProfileEdit();

async function onSaveProfile() {
  await saveProfile({ name: name.value, userId: profile.value.userId });
}
</script>
