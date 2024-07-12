<template>
  <div class="container mx-auto p-4 md:p-8 grid place-items-center h-screen">
    <div class="bg-slate-200 dark:bg-slate-700 shadow-md rounded px-4 md:px-8 pt-6 pb-8 mb-4 md:w-96 w-full">
      <h1 class="text-2xl md:text-3xl dark:text-white font-bold mb-4 text-center">Mon Profil</h1>

      <div v-if="!editing">
        <div>
          <p class="text-lg">Pseudo: {{ username }}</p>
          <p class="text-lg">Email: {{ email }}</p>
        </div>
        <button @click="openModal"
          class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4 w-full">
          Modifier
        </button>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="editing" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
      <div class="bg-white dark:bg-slate-800 rounded-lg p-6 w-full md:w-1/3">
        <h2 class="text-lg font-bold mb-4">Modifier Profil</h2>
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label class="block text-sm font-bold mb-2">Pseudo:</label>
            <input v-model="form.username" type="text" class="form-input mt-1 block w-full" />
          </div>
          <div class="mb-4">
            <label class="block text-sm font-bold mb-2">Email:</label>
            <input v-model="form.email" type="email" class="form-input mt-1 block w-full" />
          </div>
          <button type="submit"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4 w-full">
            Enregistrer
          </button>
          <button @click="closeModal"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4 w-full">
            Annuler
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      editing: false,
      username: '',
      email: '',
      form: {
        username: '',
        email: ''
      }
    };
  },
  async mounted() {
    await this.handleLogin()
  },
  methods: {
    async handleLogin() {
      try {
        let userId = localStorage.getItem('userId');
        if (userId != null) {
          const response = await axios.get('http://127.0.0.1:8000/api/user-info/' + userId);
          const { username, email } = response.data;
          this.username = username;
          this.email = email;
          this.form.username = username;
          this.form.email = email;
        }
      } catch (error) {
        console.error('Erreur lors de la recuperation des donnees ', error);
      }
    },
    openModal() {
      this.editing = true;
    },
    async closeModal() {
      this.editing = false;
      await this.handleLogin()
    },
    async submitForm() {
      try {
        const updatedUser = {
          username: this.form.username,
          email: this.form.email
        };
        let userId = localStorage.getItem('userId');
        if (userId != null) {
          const response = await axios.patch('http://127.0.0.1:8000/api/update-user/' + userId, updatedUser);
          this.username = response.data.username;
          this.email = response.data.email;
          this.closeModal();

        }
      } catch (error) {
        console.error('Erreur lors de la soumission des modifications', error);
      }
    }
  }
};
</script>
