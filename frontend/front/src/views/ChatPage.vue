<template>
  <div class="flex h-screen">
    <!-- Liste des conversations -->
    <div
      :class="['w-1/4 bg-gray-100 border-r border-gray-300 transition-transform transform', { '-translate-x-full': !isSidebarOpen }]">
      <div class="p-4 border-b border-gray-300 flex justify-between items-center">
        <input v-model="searchQuery" type="text" placeholder="Rechercher..." class="w-full p-2 border rounded" />
        <button @click="toggleSidebar" class="ml-2 bg-green-500 text-white p-2 rounded">⇦</button>
      </div>
      <div class="overflow-y-auto h-full">
        <div class="p-4">
          <h2 class="text-xl font-bold mb-2">Personnelles</h2>
          <ul>
            <li v-for="(conversation, index) in filteredPersonalConversations" :key="index"
              @click="selectConversation(index, 'personal')"
              class="cursor-pointer p-4 border-b border-gray-300 hover:bg-gray-200">
              <div class="font-bold">{{ conversation.name }}</div>
              <div class="text-sm text-gray-600">{{ conversation.lastMessage }}</div>
            </li>
          </ul>
        </div>
        <div class="p-4">
          <h2 class="text-xl font-bold mb-2">Groupes</h2>
          <ul>
            <li v-for="(conversation, index) in filteredGroupConversations" :key="index"
              @click="selectConversation(index, 'group')"
              class="cursor-pointer p-4 border-b border-gray-300 hover:bg-gray-200">
              <div class="font-bold">{{ conversation.name }}</div>
              <div class="text-sm text-gray-600">{{ conversation.lastMessage }}</div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Fenêtre de chat -->
    <div class="flex-1 flex flex-col">
      <div class="p-4 border-b border-gray-300 flex items-center">
        <button @click="toggleSidebar" class="mr-2 bg-green-500 text-white p-2 rounded">⇨</button>
        <div class="font-bold">{{ selectedConversation.name }}</div>
      </div>
      <div class="flex-1 overflow-y-auto p-4">
        <div v-for="(message, index) in selectedConversation.messages" :key="index"
          :class="{ 'text-right': message.isMine }">
          <div
            :class="['inline-block p-2 rounded-lg', message.isMine ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-800']">
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
      personalConversations: [
        {
          name: 'Alice',
          lastMessage: 'Salut, comment ça va ?',
          messages: [
            { text: 'Salut, comment ça va ?', isMine: false },
            { text: 'Ça va bien, merci !', isMine: true }
          ]
        },
        {
          name: 'damien',
          lastMessage: 'On se voit ce soir ?',
          messages: [
            { text: 'On se voit ce soir ?', isMine: false },
            { text: 'Oui, à 20h.', isMine: true }
          ]
        }
      ],
      groupConversations: [
        {
          name: 'groupe 1',
          lastMessage: 'didididi',
          messages: [
            { text: 'loooo', isMine: false },
            { text: 'hmh', isMine: true }
          ]
        },
        {
          name: 'grp2',
          lastMessage: 'La réunion est reportée à demain.',
          messages: [
            { text: 'La réunion', isMine: false },
            { text: 'D’accord', isMine: true }
          ]
        }
      ],
      selectedConversationIndex: 0,
      selectedConversationType: 'personal',
      newMessage: ''
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
    selectedConversation() {
      return this.selectedConversationType === 'personal'
        ? this.personalConversations[this.selectedConversationIndex]
        : this.groupConversations[this.selectedConversationIndex];
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

        var postData = {
          // "conversationId": this.selectedConversation.name,
          "content": this.newMessage
          // "isMine": true
        };

        this.newMessage = '';
        let token = localStorage.getItem('token');
        console.log("tokendadzadazdazdazdazdazdzad");
        console.log(token);

        try {
          const response = await axios.post('http://127.0.0.1:8000/api/message', postData, {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          });
          console.log('Message envoyé', response.data);
        } catch (error) {
          console.error('Erreur lors de la connexion:', error);
        }
      }
    },
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    }
  }
};
</script>

<style scoped>
/* Ajoutez ici des styles spécifiques à ce composant si nécessaire */
</style>