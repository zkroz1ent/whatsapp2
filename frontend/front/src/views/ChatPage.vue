<template>
  <div class="flex h-screen">

    <!-- Liste des conversations -->
    <div :class="['w-1/4 bg-gray-100 dark:bg-slate-900 border-r border-gray-300 transition-transform transform', { '-translate-x-full': !isSidebarOpen }]">
      <div class="p-4 border-b border-gray-300 flex justify-between items-center">
        <input v-model="searchQuery" type="text" placeholder="Rechercher..." class="p-2 border rounded" />
        <button @click="displayModal" class="ml-2 bg-blue-500 text-white hover:bg-blue-600 p-2 rounded">Créer un Groupe</button>
        <button @click="toggleSidebar" class="ml-2 bg-green-500 text-white p-2 rounded">⇦</button>
      </div>
      <div class="overflow-y-auto h-full">
        <!-- Sections de Conversations ... -->

        <div class="p-4">
          <h2 class="text-xl dark:text-white font-bold mb-2">Notifications</h2>
          <ul>
            <li v-for="notif in receivedNotifications" :key="notif.id">
              {{ notif.content }} - {{ notif.createdAt }}
            </li></ul>
          <h2 class="text-xl dark:text-white font-bold mb-2">Groupes</h2>
          <ul>
            <li v-for="(conversation, index) in filteredGroupConversations" :key="index"
              @click="selectConversation(index, 'group')"
              class="cursor-pointer dark:text-white p-4 border-b border-gray-300 hover:scale-110">
              <div class="font-bold">{{ conversation.name }}</div>
              <div class="text-sm dark:text-white text-gray-600">{{ conversation.lastMessage }}</div>
            </li>
          </ul>
          <button @click="showNotificationSettings = true" class="my-2 bg-blue-500 text-white p-2 rounded">Gérer les Notifications</button>
        </div>

      </div>
    </div>

    <!-- Fenêtre de chat -->
    <div class="flex-1 flex flex-col">

      
    <div v-if="showModal" class="modal-overlay bg-gray-200">
    <div class="modal-popup">
      <button class="p-2 close-button bg-red-500 rounded" @click="showModal = false">&times;</button>
    <div class="group-name">
      <label for="groupName">Nom du Groupe :</label>
      <input type="text" class="ml-2 rounded" id="groupName" v-model="groupName">
    </div>
    <div class="group-container">
    <button id="dropdownSearchButton" data-dropdown-toggle="dropdownSearch" data-dropdown-placement="bottom" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">Dropdown search
      <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
      </svg>
    </button>

    <div id="dropdownSearch" class="z-10 hidden bg-white rounded-lg shadow w-60 dark:bg-gray-700">
      <div class="p-3">
        <label for="input-group-search" class="sr-only">Search</label>
        <div class="relative">
          <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
            </svg>
          </div>
          <input type="text" id="input-group-search" v-model="searchQuery" class="block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search user">
        </div>
      </div>

      <ul v-show="dropdownOpen" class="h-48 px-3 pb-3 overflow-y-auto text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownSearchButton">
        <li v-for="user in filteredUsers" :key="user.id">
          <div class="flex items-center ps-2 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
            <input :id="'checkbox-item-' + user.id" type="checkbox" v-model="selectedUsers" :value="user.id" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
            <label :for="'checkbox-item-' + user.id" class="w-full py-2 ms-2 text-sm font-medium text-gray-900 rounded dark:text-gray-300">{{ user.name }}</label>
          </div>
        </li>
      </ul>
      <a href="#" class="flex items-center p-3 text-sm font-medium text-red-600 border-t border-gray-200 rounded-b-lg bg-gray-50 dark:border-gray-600 hover:bg-gray-100 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-red-500 hover:underline">
        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
          <path d="M6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 10H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Zm11-3h-6a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2Z"/>
        </svg>
        Delete user
      </a>
    </div>
  </div>
    
      <button @click="createUser">Créer</button>
    </div>
  </div>

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
      showNotificationSettings: false,  // Pour afficher NotificationSettings
      showModal: false,
      users: [
        { id: 1, name: 'Alice' },
        { id: 2, name: 'Bob' },
        { id: 3, name: 'Charlie' },
      ],
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
    });
  },
  computed: {
    filteredUsers() {
      const query = this.searchQuery.toLowerCase();
      return this.users.filter(user => user.name.toLowerCase().includes(query));
    },
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
    }

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
  }
}
},
  mounted(){  
    this.getConversations();
    this.getUsers();
    this.getCommissions();
    this.getReceivedNotifications();
  },

    createUser() {
      // Logique pour créer l'utilisateur (appel API, etc.)
      this.showModal = false;
    },
    displayModal() {
      this.showModal = true;
    },
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    }}
</script>
<style scoped>
</style>