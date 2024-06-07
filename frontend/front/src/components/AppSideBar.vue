<template>
    <div class="sidebar bg-white text-gray-800 w-64 h-full fixed shadow-lg">
      <div class="p-4">
        <h2 class="text-2xl font-bold mb-4">Menu</h2>
        <ul>
          <li class="mb-2">
            <router-link to="/Groupe1" class="block py-2 px-4 hover:bg-gray-200 rounded">Groupe1</router-link>
          </li>
          <li class="mb-2">
            <router-link to="/Groupe1" class="block py-2 px-4 hover:bg-gray-200 rounded">Groupe1</router-link>
          </li>
          <li class="mb-2">
            <router-link to="/Groupe1" class="block py-2 px-4 hover:bg-gray-200 rounded">Groupe1</router-link>
          </li>
          <li class="mb-2">
            <router-link to="/Groupe1" class="block py-2 px-4 hover:bg-gray-200 rounded">Groupe1</router-link>
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'AppSidebar'
  };
  </script>
  
  <style scoped>
  .sidebar {
    top: 0;
    left: 0;
    z-index: 1000;
  }
  </style>