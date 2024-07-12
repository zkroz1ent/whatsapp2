<template>
  <nav class="bg-green-600 p-4">
    <div class="container mx-auto flex justify-between items-center">
      <div class="text-white text-xl font-bold">WhatsApp2.0</div>
      <div class="flex space-x-4">
        <router-link v-if="!isLoggedIn" to="/login" class="text-white hover:text-gray-300">Login</router-link>
        <router-link v-if="!isLoggedIn" to="/register" class="text-white hover:text-gray-300">Register</router-link>
        <router-link v-if="isLoggedIn" to="/chat" class="text-white hover:text-gray-300">Chat</router-link>
        <div v-if="isLoggedIn" class="relative">
          <button @click="toggleDropdown" class="flex items-center text-white hover:text-gray-300 focus:outline-none">
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
              <!--
              On peut par exemple utiliser le premier caractère du nom d'utilisateur pour l'avatar, 
              mais ici je l'ai laissé P pour la simplicité
              -->
              <span class="text-green-600 font-bold">{{ avatarInitials }}</span>
            </div>
          </button>
          <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2">
            <router-link to="/profile" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profil</router-link>
            <router-link to="/settings" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Paramètres</router-link>
            <button @click="logout" class="block w-full text-left px-4 py-2 text-gray-800 hover:bg-gray-200">Déconnexion</button>
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
      dropdownOpen: false,
      isLoggedIn: false,
      avatarInitials: 'P' // Initial par défaut
    };
  },
  methods: {
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },
    checkLoginStatus() {
      const userId = localStorage.getItem('userId');
      const username = localStorage.getItem('username');
      this.isLoggedIn = userId !== null;
      if (username) {
        this.avatarInitials = username.charAt(0).toUpperCase();
      }
    },
    logout() {
      localStorage.removeItem('userId');
      localStorage.removeItem('username');
      this.isLoggedIn = false;
      this.$router.push('/login');
    }
  },
  created() {
    this.checkLoginStatus();
  },
  watch: {
    $route() {
      this.dropdownOpen = false; // Fermer le menu déroulant lors de la navigation
      this.checkLoginStatus();   // Vérifiez le statut de connexion lors de chaque changement de route
    }
  }
};
</script>