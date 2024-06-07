<template>
  <nav class="bg-green-600 p-4">
    <div class="container mx-auto flex justify-between items-center">
      <div class="text-white text-xl font-bold">WhatsApp</div>
      <div class="flex space-x-4">
        <router-link to="/login" class="text-white hover:text-gray-300">Login</router-link>
        <router-link to="/register" class="text-white hover:text-gray-300">Register</router-link>
        <router-link to="/chat" class="text-white hover:text-gray-300">Chat</router-link>
        <div class="relative">
          <button @click="toggleDropdown" class="flex items-center text-white hover:text-gray-300 focus:outline-none">
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
              <span class="text-green-600 font-bold">P</span>
            </div>
          </button>
          <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2">
            <router-link to="/profile" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profil</router-link>
            <router-link to="/settings" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Paramètres</router-link>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'AppNavbar',
  data() {
    return {
      dropdownOpen: false
    };
  },
  methods: {
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    }
  },
  watch: {
    $route() {
      this.dropdownOpen = false; // Fermer le menu déroulant lors de la navigation
    }
  }
};
</script>

<style scoped>
/* Ajoutez ici des styles spécifiques à ce composant si nécessaire */
</style>