<template>
    <div class="sidebar bg-gray-800 text-white w-64 h-full fixed">
      <div class="p-4">
        <h2 class="text-2xl font-bold mb-4">Menu</h2>
        <ul>
          <li class="mb-2">
            <router-link to="/groupe A" class="block py-2 px-4 hover:bg-gray-700 rounded">groupe A</router-link>
          </li>
          <li class="mb-2">
            <router-link to="/groupe B" class="block py-2 px-4 hover:bg-gray-700 rounded">groupe B</router-link>
          </li>
          <li class="mb-2">
            <router-link to="/groupe C" class="block py-2 px-4 hover:bg-gray-700 rounded">groupe C</router-link>
          </li>
          <li class="mb-2">
            <router-link to="/logout" class="block py-2 px-4 hover:bg-gray-700 rounded">Logout</router-link>
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