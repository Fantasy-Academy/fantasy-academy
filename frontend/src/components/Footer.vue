<template>
  <footer class="bg-blue-black text-dark-white mt-12 shadow-main">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between px-4 py-6 gap-4">
      <!-- Brand -->
      <p class="font-bebas-neue text-lg tracking-wide">
        © {{ new Date().getFullYear() }} Fantasy Academy
      </p>

      <!-- Links -->
      <ul class="flex gap-6 text-sm font-alexandria">
        <li>
          <router-link
            to="/"
            class="hover:text-vibrant-coral transition"
          >
            About
          </router-link>
        </li>
        <li>
          <router-link
            to="/"
            class="hover:text-vibrant-coral transition"
          >
            Privacy
          </router-link>
        </li>
        <li>
          <router-link
            to="/"
            class="hover:text-vibrant-coral transition"
          >
            Terms
          </router-link>
        </li>
      </ul>

      <!-- Socials -->
      <div class="flex gap-4 text-xl">
        <a href="#" class="hover:text-golden-yellow transition" aria-label="Twitter">
          <i class="fa-brands fa-x-twitter"></i>
        </a>
        <a href="#" class="hover:text-golden-yellow transition" aria-label="Discord">
          <i class="fa-brands fa-discord"></i>
        </a>
        <a href="#" class="hover:text-golden-yellow transition" aria-label="GitHub">
          <i class="fa-brands fa-github"></i>
        </a>
      </div>
    </div>
  </footer>
</template>

<script setup>
</script>