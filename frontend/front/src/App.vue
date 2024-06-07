<template>
      <AppNavbar />
      <router-view />
</template>

<script>
import AppNavbar from './components/AppNavBar.vue';
//import AppSidebar from './components/AppSideBar.vue';
//<div class="app-container flex">
//    <AppSidebar />
export default {
  name: 'App',
  components: {
    AppNavbar,
   // AppSidebar
  }
};
</script>

<style>
.app-container {
  display: flex;
}
.main-content {
  margin-left: 16rem; /* Ajustez en fonction de la largeur de votre barre latérale */
}
</style>