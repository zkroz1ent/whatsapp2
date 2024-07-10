<template>
  <div class="dark:bg-slate-900 flex items-center justify-center min-h-screen bg-gray-100">
    <div class="bg-slate-200 dark:bg-slate-700 w-full max-w-md p-8 space-y-8 rounded-lg shadow-md">
      <h1 class="dark:text-white text-2xl font-bold text-center text-gray-900">Connexion</h1>
      <form @submit.prevent="handleLogin" class="space-y-6">
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
            Mot de passe
          </label>
          <input
            type="password"
            v-model="password"
            required
            class="w-full px-3 py-4 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-200"
          />
        </div>
        <!--<div class="flex items-center justify-between">
          <a href="#" class="dark:text-green-500 text-sm text-indigo-600 hover:underline">Mot de passe oublié ?</a>
        </div>-->
        <button
          type="submit"
          class="w-full px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          Se connecter
        </button>
      </form>
      <div class="text-center">
        <p class="dark:text-white text-sm text-gray-600">Pas encore de compte ? <a href="#/register" class="dark:text-green-500 text-indigo-600 hover:underline">Inscription</a></p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'LoginPage',
  data() {
    return {
      username: '',
      password: ''
    };
  },
  methods: {
    async handleLogin() {
      try {
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
          username: this.username,
          password: this.password
        });
        const { token } = response.data;
        localStorage.setItem('username', this.username);
        localStorage.setItem('token', token);

        console.log('Connexion réussie:', response.data);
        this.$router.push('/chat');
      } catch (error) {
        console.error('Erreur lors de la connexion:', error);
      }
    }
  }
};
</script>