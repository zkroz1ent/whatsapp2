<template>
    <div>
      <p class="text-lg">Prénom: {{ user.firstName }}</p>
      <p class="text-lg">Nom: {{ user.name }}</p>
      <p class="text-lg">Email: {{ user.email }}</p>
    </div>
  </template>
  
  
  <script>
  export default {
    props: ['user'],
  };
  </script>
  