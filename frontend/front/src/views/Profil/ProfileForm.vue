<template>
    <form @submit.prevent="save">
    <div class="grid">
      <div>
        <label for="pseudo" class="block text-gray-700 dark:text-white text-sm font-bold">Pseudo:</label>
        <input type="text" id="username" v-model="userCopy.username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2" />
      </div>
      <div>
        <label for="firstName" class="block text-gray-700 dark:text-white text-sm font-bold">Prénom:</label>
        <input type="text" id="firstName" v-model="userCopy.firstName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-2" />
      </div>
      <div>
        <label for="name" class="block text-gray-700 dark:text-white text-sm font-bold">Nom:</label>
        <input type="text" id="name" v-model="userCopy.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline mb-4" />
      </div>
      <div class="mb-2">
        <button @click="this.save()" type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">Enregistrer</button>
        <button @click="$emit('cancel')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Annuler</button>
      </div>
    </div>
    </form>
  </template>
  
  <script>
  import Swal from 'sweetalert2'
  export default {
    props: ['user'],
    data() {
      return {
        userCopy: { ...this.user },
      };
    },
    methods: {
      save() {
        this.$emit('save', this.userCopy);
        Swal.fire({
            position: "top",
            timerProgressBar: true,
            icon: "success",
            title: "Modification Enregistrées avec Succés",
            showConfirmButton: false,
            timer: 1500,
        });
      },
    },
  };
  </script>
  