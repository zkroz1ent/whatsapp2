<template>
  <div :class="['w-1/4 bg-gray-100 dark:bg-slate-900 border-r border-gray-300 transition-transform transform', { '-translate-x-full': !isSidebarOpen }]">
    <div class="p-4 border-b border-gray-300 flex justify-between items-center">
      <input v-model="searchQuery" type="text" placeholder="Rechercher..." class="w-full p-2 border rounded" />
      <button @click="toggleSidebar" class="ml-2 bg-green-500 text-white p-2 rounded">⇦</button>
    </div>
    <div class="overflow-y-auto h-full">
      <div class="p-4">
        <h2 class="text-xl dark:text-white font-bold mb-2">Personnelles</h2>
        <ul>
          <li v-for="conversation in filteredPersonalConversations" :key="conversation.id" 
              @click="selectConversation(conversation.id, 'personal')"
              class="cursor-pointer p-4 border-b border-gray-300 hover:scale-110">
            <div class="font-bold dark:text-white">{{ conversation.name }}</div>
            <div class="text-sm dark:text-white text-gray-600">{{ conversation.lastMessage }}</div>
          </li>
        </ul>
      </div>
      <div class="p-4">
        <h2 class="text-xl dark:text-white font-bold mb-2">Groupes</h2>
        <ul>
          <li v-for="conversation in filteredGroupConversations" :key="conversation.id"
              @click="selectConversation(conversation.id, 'group')"
              class="cursor-pointer dark:text-white p-4 border-b border-gray-300 hover:scale-110">
            <div class="font-bold">{{ conversation.name }}</div>
            <div class="text-sm dark:text-white text-gray-600">{{ conversation.lastMessage }}</div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      personalConversations: [
      { id: 1, name: 'Alice', lastMessage: 'Bonjour !' },
      { id: 2, name: 'Bob', lastMessage: 'Tu es là ?' }
    ],
    groupConversations: [
      { id: 3, name: 'Projet X', lastMessage: 'Réunion demain' },
      { id: 4, name: 'Famille', lastMessage: 'Bonne soirée !' }
    ]
  };
  },
  computed: {
    // ...autres propriétés calculées
    filteredPersonalConversations() {
    return this.personalConversations
      .sort((a, b) => a.name.localeCompare(b.name)); // Tri alphabétique
  },
  filteredGroupConversations() {
    return this.groupConversations
      .sort((a, b) => a.name.localeCompare(b.name)); // Tri alphabétique
  }
  },
  created() {
    // Charger les conversations depuis la base de données lors du chargement du composant
    this.fetchConversations();
  },
  methods: {
    // ... autres méthodes
    async fetchConversations() {
      try {
        // Effectuer la requête à votre API pour récupérer les conversations
        const response = await fetch('/api/conversations');
        const data = await response.json();

        this.personalConversations = data.personal;
        this.groupConversations = data.group;
      } catch (error) {
        console.error('Erreur lors de la récupération des conversations:', error);
      }
    },
  },
};
</script>
