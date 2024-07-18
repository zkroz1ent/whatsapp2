<template>
  <div class="flex h-screen">

    <!-- Liste des conversations -->
    <div :class="['w-1/4 bg-gray-100 dark:bg-slate-900 border-r border-gray-300 transition-transform transform', { '-translate-x-full': !isSidebarOpen }]">
      <div class="p-4 border-b border-gray-300 flex justify-between items-center">
        <input v-model="searchQuery" type="text" placeholder="Rechercher..." class="w-full p-2 border rounded" />
        <button @click="toggleSidebar" class="ml-2 bg-green-500 text-white p-2 rounded">⇦</button>
      </div>
      <div class="overflow-y-auto h-full">
        <!-- Sections de Conversations ... -->

        <div class="p-4">
          <h2 class="text-xl dark:text-white font-bold mb-2">Notifications</h2>
          <ul>
            <li v-for="notif in receivedNotifications" :key="notif.id">
              {{ notif.content }} - {{ notif.createdAt }}
            </li>
          </ul>
          <button @click="showNotificationSettings = true" class="my-2 bg-blue-500 text-white p-2 rounded">Gérer les Notifications</button>
        </div>

      </div>
    </div>

    <!-- Fenêtre de chat -->
    <div class="flex-1 flex flex-col" v-if="selectedConversation || isGlobal">
      <div class="p-2 border-b border-gray-300 flex items-center">
        <button @click="toggleSidebar" class="mr-2 bg-green-500 text-white p-2 rounded">⇨</button>
        <div class="font-bold dark:text-white">{{ isGlobal ? 'Fil Global' : selectedConversation.name }}</div>
      </div>
      <div class="flex-1 overflow-y-auto p-4">
        <div v-for="(message, index) in (isGlobal ? globalMessages : selectedConversation.messages)" :key="index" :class="{ 'text-right': message.isMine }">
          <div :class="['inline-block p-2 rounded-lg', message.isMine ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-800']">
            {{ message.content }}
          </div>
        </div>
      </div>
      <div class="p-4 border-t border-gray-300 flex items-center">
        <input v-model="newMessage" type="text" placeholder="Tapez un message..." class="flex-1 p-2 border rounded" @keyup.enter="sendMessage" />
        <button @click="sendMessage" class="ml-2 bg-green-500 text-white p-2 rounded">Envoyer</button>
      </div>
    </div>

    <!-- Afficher NotificationSettings si showNotificationSettings est vrai -->
    <NotificationSettings v-if="showNotificationSettings" @close="showNotificationSettings = false" />

    <!-- Modales existantes ... -->
  </div>
</template>

<script>
import { reactive, toRefs } from 'vue';
import axios from 'axios';
import NotificationSettings from '../components/NotificationSettings.vue';  // Assurez-vous que le chemin est correct

export default {
  name: 'ChatPage',
  components: { NotificationSettings },
  setup() {
    const state = reactive({
      searchQuery: '',
      isSidebarOpen: true,
      personalConversations: [],
      groupConversations: [],
      users: [],
      commissions: [],
      globalMessages: [],
      selectedConversationIndex: 0,
      selectedConversationType: 'personal',
      newMessage: '',
      showCreateConversationModal: false,
      showCreateGroupModal: false,
      showCreateCommissionModal: false,
      newConversationUser: '',
      newConversationMessage: '',
      newGroupName: '',
      newGroupUsers: [],
      newGroupMessage: '',
      newCommissionName: '',
      isGlobal: false,
      selectedConversation: {},
      receivedNotifications: [],
      showNotificationSettings: false  // Pour afficher NotificationSettings
    });

    // Méthodes
    const loadGlobalMessages = async () => {
      const token = localStorage.getItem('token');
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/messages/global', {
          headers: { Authorization: `Bearer ${token}` }
        });
        state.globalMessages = response.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des messages globaux:', error);
      }
    };

    const loadConversationMessages = async (conversationId) => {
      const token = localStorage.getItem('token');
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/messages/commission/${conversationId}`, {
          headers: { Authorization: `Bearer ${token}` }
        });
        state.selectedConversation.messages = response.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des messages de la commission:', error);
      }
    };

    const selectConversation = (index, type) => {
      state.selectedConversationIndex = index;
      state.selectedConversationType = type;
      state.isGlobal = false;

      const selected = state.selectedConversationType === 'personal' ? state.personalConversations : state.groupConversations;
      state.selectedConversation = {
        ...selected[state.selectedConversationIndex],
        messages: []
      };

      loadConversationMessages(state.selectedConversation.id);
    };

    const selectCommission = (commissionId, index) => {
      state.selectedConversationIndex = index;
      state.selectedConversationType = 'commission';
      state.isGlobal = false;

      state.selectedConversation = {
        ...state.commissions[index],
        messages: []
      };

      console.log('Selected Commission:', state.selectedConversation);
      loadConversationMessages(commissionId);
    };

    const selectGlobalFeed = () => {
      state.isGlobal = true;
      state.selectedConversationIndex = -1;
      state.selectedConversationType = '';
      loadGlobalMessages();
    };

    const sendMessage = async () => {
      if (state.newMessage.trim() !== '') {
        const postData = {
          content: state.newMessage,
          sender: localStorage.getItem('userId'),
          isGlobal: state.isGlobal
        };

        if (!state.isGlobal) {
          postData["commissionId"] = state.selectedConversation.id;
        }

        const endpoint = 'http://127.0.0.1:8000/api/message';
        const token = localStorage.getItem('token');

        try {
          const response = await axios.post(endpoint, postData, {
            headers: { Authorization: `Bearer ${token}` }
          });

          console.log('Message envoyé', response.data);
          state.newMessage = '';

          if (state.isGlobal) {
            loadGlobalMessages();
          } else {
            loadConversationMessages(state.selectedConversation.id);
          }
        } catch (error) {
          console.error('Erreur lors de l\'envoi du message:', error);
        }
      }
    };

    const getReceivedNotifications = async () => {
      const token = localStorage.getItem('token');

      try {
        const response = await axios.get('http://127.0.0.1:8000/api/notifications', {
          headers: { Authorization: `Bearer ${token}` }
        });

        state.receivedNotifications = response.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des notifications:', error);
      }
    };

    const getConversations = async () => {
      const token = localStorage.getItem('token');
      const userId = localStorage.getItem('userId');

      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/conversations/${userId}`, {
          headers: { Authorization: `Bearer ${token}` }
        });

        state.personalConversations = response.data.personal;
        state.groupConversations = response.data.group;

        if (state.personalConversations.length > 0) {
          selectConversation(0, 'personal');
        } else if (state.groupConversations.length > 0) {
          selectConversation(0, 'group');
        }
      } catch (error) {
        console.error('Erreur lors de la récupération des conversations:', error);
      }
    };

    const getUsers = async () => {
      const token = localStorage.getItem('token');

      try {
        const response = await axios.get('http://127.0.0.1:8000/api/users', {
          headers: { Authorization: `Bearer ${token}` }
        });

        state.users = response.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des utilisateurs:', error);
      }
    };

    const getCommissions = async () => {
      const token = localStorage.getItem('token');

      try {
        const response = await axios.get('http://127.0.0.1:8000/api/commissions', {
          headers: { Authorization: `Bearer ${token}` }
        });

        state.commissions = response.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des commissions:', error);
      }
    };

    const createConversation = async () => {
      const token = localStorage.getItem('token');
      const senderId = localStorage.getItem('userId');

      const postData = {
        content: state.newConversationMessage,
        sender: senderId,
        receiver: state.newConversationUser
      };

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/conversation', postData, {
          headers: { Authorization: `Bearer ${token}` }
        });

        console.log('Conversation créée', response.data);
        state.newConversationUser = '';
        state.newConversationMessage = '';
        state.showCreateConversationModal = false;
        getConversations(); // Refresh the conversation list
      } catch (error) {
        console.error('Erreur lors de la création de la conversation:', error);
      }
    };

    const createGroupConversation = async () => {
      const token = localStorage.getItem('token');
      const senderId = localStorage.getItem('userId');

      const postData = {
        name: state.newGroupName,
        users: state.newGroupUsers, // List of user IDs
        content: state.newGroupMessage,
        sender: senderId
      };

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/group-conversation', postData, {
          headers: { Authorization: `Bearer ${token}` }
        });

        console.log('Groupe créé', response.data);
        state.newGroupName = '';
        state.newGroupUsers = [];
        state.newGroupMessage = '';
        state.showCreateGroupModal = false;
        getConversations(); // Refresh the conversation list
      } catch (error) {
        console.error('Erreur lors de la création du groupe:', error);
      }
    };

    const createCommission = async () => {
      const token = localStorage.getItem('token');

      const postData = {
        name: state.newCommissionName
      };

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/commissions', postData, {
          headers: { Authorization: `Bearer ${token}` }
        });

        console.log('Commission créée', response.data);
        state.newCommissionName = '';
        state.showCreateCommissionModal = false;
        getCommissions(); // Refresh the commission list
      } catch (error) {
        console.error('Erreur lors de la création de la commission:', error);
      }
    };

    return {
      ...toRefs(state),
      selectConversation,
      selectCommission,
      selectGlobalFeed,
      sendMessage,
      getConversations,
      getUsers,
      getCommissions,
      createConversation,
      createGroupConversation,
      createCommission,
      getReceivedNotifications
    };
  },
  mounted() {
    this.getConversations();
    this.getUsers();
    this.getCommissions();
    this.getReceivedNotifications();
  }
};
</script>

<style scoped>
/* Complétez si nécessaire */
</style>