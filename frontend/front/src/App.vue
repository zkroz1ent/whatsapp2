<template>
  <div>
    <AppNavbar />
    <router-view />
  </div>
</template>

<script>
import AppNavbar from './components/AppNavBar.vue';

export default {
  name: 'App',
  components: {
    AppNavbar
  }
};
</script>

<style>
/* Ajoutez ici des styles globaux si nécessaire */
</style>