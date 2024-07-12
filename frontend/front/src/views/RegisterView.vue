<template>
  <div class="dark:bg-slate-900 flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-slate-200 dark:bg-slate-700 w-full max-w-md p-8 space-y-8 rounded-lg shadow-md">
      <h1 class="dark:text-white text-2xl font-bold text-center text-gray-900">Inscription</h1>
      <form @submit.prevent="handleRegister" class="space-y-6">
        <div class="relative">
          <label class="font-bold dark:text-white p-2 text-gray-500">
            Nom d'utilisateur
          </label>
          <input
            type="text"
            v-model="username"
            required
            class="w-full px-3 py-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200"
          />
        </div>
        <div class="relative">
          <label class="font-bold dark:text-white p-2 text-gray-500">
            Email
          </label>
          <input
            type="email"
            v-model="email"
            required
            class="w-full px-3 py-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200"
          />
        </div>
        <div class="relative">
          <label class="font-bold dark:text-white p-2 text-gray-500">
            Mot de passe
          </label>
          <input
            type="password"
            v-model="password"
            required
            class="w-full px-3 py-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200"
          />
        </div>
        <div class="relative">
          <label class="font-bold dark:text-white p-2 text-gray-500">
            Confirmez le mot de passe
          </label>
          <input
            type="password"
            v-model="confirmPassword"
            required
            class="w-full px-3 py-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200"
          />
        </div>
        <button
          type="submit"
          class="font-bold w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          S'inscrire
        </button>
      </form>
      <div class="text-center">
        <p class="dark:text-white  text-sm text-gray-600">Vous avez déjà un compte ? <a href="#/login" class="dark:text-green-500 text-indigo-600 hover:underline">Connexion</a></p>
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
      if (this.password !== this.confirmPassword) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Les mots de passe ne correspondent pas!",
        });
        return;
      }
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/;
      if (!passwordRegex.test(this.password)) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.",
        });
        return;
      }
      const userData = {
        username: this.username,
        email: this.email,
        password: this.password
      };

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/register', userData);

        console.log('Réponse de l\'API:', response.data);

        console.log('Inscription réussie:', response.data);

        console.table(userData);

        this.$router.push('/login');


      } catch (error) {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: "Une erreur est survenue lors de l'inscription. Veuillez réessayer.",
        });
        console.error('Erreur lors de l\'inscription:', error);
      }
    }
  }
};
</script>