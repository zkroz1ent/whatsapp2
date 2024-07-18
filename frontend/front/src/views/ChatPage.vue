<template>
  <div class="flex h-screen">
    <!-- Liste des conversations -->
    <div :class="['w-1/4 bg-gray-100 dark:bg-slate-900 border-r border-gray-300 transition-transform transform', { '-translate-x-full': !isSidebarOpen }]">
      <div class="p-4 border-b border-gray-300 flex justify-between items-center">
        <input v-model="searchQuery" type="text" placeholder="Rechercher..." class="w-full p-2 border rounded" />
        <button @click="toggleSidebar" class="ml-2 bg-green-500 text-white p-2 rounded">⇦</button>
      </div>
      <div class="overflow-y-auto h-full">
        <div class="p-4">
          <h2 class="text-xl dark:text-white font-bold mb-2">Personnelles</h2>
          <button @click="showCreateConversationModal = true" class="my-2 bg-blue-500 text-white p-2 rounded">Nouvelle Conversation</button>
          <ul>
            <li v-for="(conversation, index) in filteredPersonalConversations" :key="index"
                @click="selectConversation(index, 'personal')"
                class="cursor-pointer p-4 border-b border-gray-300 hover:scale-110">
              <div class="font-bold dark:text-white">{{ conversation.name }}</div>
              <div class="text-sm dark:text-white text-gray-600">{{ conversation.lastMessage }}</div>
            </li>
          </ul>
        </div>
        <div class="p-4">
          <h2 class="text-xl dark:text-white font-bold mb-2">Groupes</h2>
          <button @click="showCreateGroupModal = true" class="my-2 bg-blue-500 text-white p-2 rounded">Nouveau Groupe</button>
          <ul>
            <li v-for="(conversation, index) in filteredGroupConversations" :key="index"
                @click="selectConversation(index, 'group')"
                class="cursor-pointer dark:text-white p-4 border-b border-gray-300 hover:scale-110">
              <div class="font-bold">{{ conversation.name }}</div>
              <div class="text-sm dark:text-white text-gray-600">{{ conversation.lastMessage }}</div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Fenêtre de chat -->
    <div class="flex-1 flex flex-col" v-if="selectedConversation">
      <div class="p-2 border-b border-gray-300 flex items-center">
        <button @click="toggleSidebar" class="mr-2 bg-green-500 text-white p-2 rounded">⇨</button>
        <div class="font-bold dark:text-white">{{ selectedConversation.name }}</div>
      </div>
      <div class="flex-1 overflow-y-auto p-4">
        <div v-for="(message, index) in selectedConversation.messages" :key="index"
             :class="{ 'text-right': message.isMine }">
          <div :class="['inline-block p-2 rounded-lg', message.isMine ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-800']">
            {{ message.text }}
          </div>
        </div>
      </div>
      <div class="p-4 border-t border-gray-300 flex items-center">
        <input v-model="newMessage" type="text" placeholder="Tapez un message..." class="flex-1 p-2 border rounded"
               @keyup.enter="sendMessage" />
        <button @click="sendMessage" class="ml-2 bg-green-500 text-white p-2 rounded">Envoyer</button>
      </div>
    </div>

    <!-- Modale de création de conversation -->
    <div v-if="showCreateConversationModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
      <div class="bg-white p-4 rounded shadow-lg w-1/3">
        <h2 class="font-bold text-xl mb-4">Nouvelle Conversation</h2>
        <select v-model="newConversationUser" class="w-full p-2 border mb-4 rounded">
          <option v-for="user in users" :key="user.id" :value="user.id">{{ user.username }}</option>
        </select>
        <input v-model="newConversationMessage" type="text" placeholder="Message" class="w-full p-2 border mb-4 rounded" />
        <div class="flex justify-end">
          <button @click="showCreateConversationModal = false" class="bg-gray-500 text-white p-2 rounded mr-2">Annuler</button>
          <button @click="createConversation" class="bg-blue-500 text-white p-2 rounded">Créer</button>
        </div>
      </div>
    </div>

    <!-- Modale de création de groupe -->
    <div v-if="showCreateGroupModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
      <div class="bg-white p-4 rounded shadow-lg w-1/3">
        <h2 class="font-bold text-xl mb-4">Nouveau Groupe</h2>
        <input v-model="newGroupName" type="text" placeholder="Nom du Groupe" class="w-full p-2 border mb-4 rounded" />
        <select v-model="newGroupUsers" multiple class="w-full p-2 border mb-4 rounded">
          <option v-for="user in users" :key="user.id" :value="user.id">{{ user.username }}</option>
        </select>
        <input v-model="newGroupMessage" type="text" placeholder="Message" class="w-full p-2 border mb-4 rounded" />
        <div class="flex justify-end">
          <button @click="showCreateGroupModal = false" class="bg-gray-500 text-white p-2 rounded mr-2">Annuler</button>
          <button @click="createGroupConversation" class="bg-blue-500 text-white p-2 rounded">Créer</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'ChatPage',
  data() {
    return {
      searchQuery: '',
      isSidebarOpen: true,
      personalConversations: [],
      groupConversations: [],
      users: [],
      selectedConversationIndex: 0,
      selectedConversationType: 'personal',
      newMessage: '',
      showCreateConversationModal: false,
      showCreateGroupModal: false,
      newConversationUser: '',
      newConversationMessage: '',
      newGroupName: '',
      newGroupUsers: [],
      newGroupMessage: ''
    };
  },
  computed: {
    filteredPersonalConversations() {
      return this.personalConversations.filter(conversation =>
        conversation.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        conversation.lastMessage.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    filteredGroupConversations() {
      return this.groupConversations.filter(conversation =>
        conversation.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        conversation.lastMessage.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    filteredUsers() {
      return this.users.filter(user =>
        user.username.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
        user.email.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    selectedConversation() {
      const conversations = this.selectedConversationType === 'personal'
        ? this.personalConversations
        : this.groupConversations;
      return conversations[this.selectedConversationIndex] || null;
    }
  },
  methods: {
    selectConversation(index, type) {
      this.selectedConversationIndex = index;
      this.selectedConversationType = type;
    },
    async sendMessage() {
      if (this.newMessage.trim() !== '') {
        this.selectedConversation.messages.push({ text: this.newMessage, isMine: true });

        const postData = { "content": this.newMessage };

        this.newMessage = '';
        let token = localStorage.getItem('token');

        try {
          const response = await axios.post('http://127.0.0.1:8000/api/message', postData, {
            headers: { 'Authorization': `Bearer ${token}` }
          });
          console.log('Message envoyé', response.data);
        } catch (error) {
          console.error('Erreur lors de la connexion:', error);
        }
      }
    },
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },
    async getConversations() {
      let token = localStorage.getItem('token');
      let userId = localStorage.getItem('userId');

      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/conversations/${userId}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        });

        this.personalConversations = response.data.personal;
        this.groupConversations = response.data.group;

        if (this.personalConversations.length > 0) {
          this.selectConversation(0, 'personal');
        } else if (this.groupConversations.length > 0) {
          this.selectConversation(0, 'group');
        }
      } catch (error) {
        console.error('Erreur lors de la récupération des conversations:', error);
      }
    },
    async getUsers() {
      let token = localStorage.getItem('token');

      try {
        const response = await axios.get('http://127.0.0.1:8000/api/users', {
          headers: { 'Authorization': `Bearer ${token}` }
        });

        this.users = response.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des utilisateurs:', error);
      }
    },
    async createConversation() {
      let token = localStorage.getItem('token');
      let senderId = localStorage.getItem('userId');

      const postData = {
        content: this.newConversationMessage,
        sender: senderId,
        receiver: this.newConversationUser
      };

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/conversation', postData, {
          headers: { 'Authorization': `Bearer ${token}` }
        });

        console.log('Conversation créée', response.data);
        this.newConversationUser = '';
        this.newConversationMessage = '';
        this.showCreateConversationModal = false;
        this.getConversations(); // Refresh the conversation list
      } catch (error) {
        console.error('Erreur lors de la création de la conversation:', error);
      }
    },
    async createGroupConversation() {
      let token = localStorage.getItem('token');
      let senderId = localStorage.getItem('userId');

      const postData = {
        name: this.newGroupName,
        users: this.newGroupUsers, // List of user IDs
        content: this.newGroupMessage,
        sender: senderId
      };

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/group-conversation', postData, {
          headers: { 'Authorization': `Bearer ${token}` }
        });

        console.log('Groupe créé', response.data);
        this.newGroupName = '';
        this.newGroupUsers = [];
        this.newGroupMessage = '';
        this.showCreateGroupModal = false;
        this.getConversations(); // Refresh the conversation list
      } catch (error) {
        console.error('Erreur lors de la création du groupe:', error);
      }
    }
  },
  mounted() {
    this.getConversations();
    this.getUsers(); // Fetch users when component mounts
  }
}
</script>

<style scoped>
/* Ajoutez ici des styles spécifiques à ce composant si nécessaire */
</style>