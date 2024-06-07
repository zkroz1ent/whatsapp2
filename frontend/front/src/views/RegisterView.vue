<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-8 space-y-8 bg-white rounded-lg shadow-md">
      <h2 class="text-3xl font-bold text-center text-gray-900">Inscription</h2>
      <form @submit.prevent="handleRegister" class="space-y-6">
        <div class="relative">
          <input
            type="text"
            v-model="username"
            required
            class="w-full px-3 py-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200"
          />
          <label class="absolute top-0 left-0 px-3 py-2 text-gray-500 transform -translate-y-1/2 pointer-events-none">
            Nom d'utilisateur
          </label>
        </div>
        <div class="relative">
          <input
            type="email"
            v-model="email"
            required
            class="w-full px-3 py-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200"
          />
          <label class="absolute top-0 left-0 px-3 py-2 text-gray-500 transform -translate-y-1/2 pointer-events-none">
            Email
          </label>
        </div>
        <div class="relative">
          <input
            type="password"
            v-model="password"
            required
            class="w-full px-3 py-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200"
          />
          <label class="absolute top-0 left-0 px-3 py-2 text-gray-500 transform -translate-y-1/2 pointer-events-none">
            Mot de passe
          </label>
        </div>
        <div class="relative">
          <input
            type="password"
            v-model="confirmPassword"
            required
            class="w-full px-3 py-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200"
          />
          <label class="absolute top-0 left-0 px-3 py-2 text-gray-500 transform -translate-y-1/2 pointer-events-none">
            Confirmez le mot de passe
          </label>
        </div>
        <button
          type="submit"
          class="w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          S'inscrire
        </button>
      </form>
      <div class="text-center">
        <p class="text-sm text-gray-600">Vous avez déjà un compte ? <a href="#" class="text-indigo-600 hover:underline">Connexion</a></p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  name: 'RegisterPage',
  data() {
    return {
      username: '',
      email: '',
      password: '',
      confirmPassword: ''
    };
  },
  methods: {
    async handleRegister() {
      // Vérifiez si les mots de passe correspondent
      if (this.password !== this.confirmPassword) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Les mots de passe ne correspondent pas!",
          footer: '<a href="#">Pourquoi ai-je ce problème?</a>'
        });
        return;
      }

      // Vérifiez si le mot de passe respecte la regex
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
      if (!passwordRegex.test(this.password)) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.",
          footer: '<a href="#">Pourquoi ai-je ce problème?</a>'
        });
        return;
      }

      // Préparez les données pour l'inscription
      const userData = {
        username: this.username,
        email: this.email,
        password: this.password
      };

      try {
        // Faites un appel POST à votre API d'inscription
        const response = await axios.post('https://votre-api.com/register', userData);
        
        // Gérez la réponse de l'API
        console.log('Réponse de l\'API:', response.data);

        // Redirigez l'utilisateur vers la page de connexion ou autre
        this.$router.push('/login');
      } catch (error) {
        // Gérez les erreurs éventuelles
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Une erreur est survenue lors de l'inscription. Veuillez réessayer.",
          footer: '<a href="#">Pourquoi ai-je ce problème?</a>'
        });
        console.error('Erreur lors de l\'inscription:', error);
      }
    }
  }
};
</script>

<style scoped>
/* Ajoutez ici des styles spécifiques à ce composant si nécessaire */
</style>