<template>
  <div class="bg-dark container mx-auto p-4 md:p-8 grid place-items-center h-screen">
    <div class="bg-slate-200 dark:bg-slate-700 shadow-md rounded px-4 md:px-8 pt-6 pb-8 mb-4 md:w-96 w-full">
      <h1 class="dark:text-white text-2xl md:text-3xl font-bold mb-4 text-center">Paramètres</h1>
      <div class="flex items-center mb-4">
        <input id="default-checkbox" type="checkbox" value="" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
        <label for="default-checkbox" class="ms-2 text-xl font-medium text-gray-900 dark:text-gray-300">Notifications</label>
      </div>
      <div>
        <button @click="toggleDark()"
          class="text-white bg-black dark:bg-white dark:text-black font-bold py-2 rounded w-full">
         {{ isDark ? 'Dark Mode :  ON 🐵' : 'Dark Mode :  OFF ☀️' }}
        </button>
      </div>
      <div>
        <button class="text-white bg-yellow-500 hover:bg-yellow-700 font-bold py-2 rounded mt-4 w-full">
          Supprimer l'historique
        </button>
      </div>
      <div>
        <button class="text-white bg-red-500 hover:bg-red-700 font-bold py-2 rounded mt-4 w-full">
          Désactiver le compte
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { useDark , useToggle } from '@vueuse/core';
import { watch } from 'vue';
import { mapMutations } from 'vuex';

export default {
  data() {
    const isDark = useDark();
    const toggleDark = useToggle(isDark); 

    watch(isDark, (newIsDark) => {
      this.setDarkMode('SET_DARK_MODE', newIsDark);
    });

    return {
      isDark,
      toggleDark
    }
  },
  methods: {
    ...mapMutations({
      setDarkMode: 'SET_DARK_MODE'
    })
  },
  unmounted() {
    console.log('Component unmounted');
  }
}
</script>