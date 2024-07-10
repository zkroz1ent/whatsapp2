<template>
  <div class="container mx-auto p-4 md:p-8 grid place-items-center h-screen">
    <div class="bg-slate-200 dark:bg-slate-700 shadow-md rounded px-4 md:px-8 pt-6 pb-8 mb-4 md:w-96 w-full">
      <h1 class="text-2xl md:text-3xl dark:text-white font-bold mb-4 text-center">Mon Profil</h1>

      <div v-if="!editing">
        <ProfileInfo :user="user" class="dark:text-white" />
        <button @click="editing = true" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-4 w-full">
          Modifier
        </button>
      </div>

      <div v-else>
        <ProfileForm :user="user" @save="saveChanges" @cancel="editing = false" />
      </div>
    </div>
  </div>
</template>

<script>
import ProfileInfo from './ProfileInfo.vue';
import ProfileForm from './ProfileForm.vue';

export default {
  components: { ProfileInfo, ProfileForm },
  data() {
    return {
      editing: false,
      user: {
        username: 'Cakou Cakou',
        name: 'Votre Nom',
        email: 'votre@email.com',
      },
    };
  },
  methods: {
    saveChanges(updatedUser) {
      this.user = updatedUser;
      this.editing = false;
    },
  },
};
</script>
