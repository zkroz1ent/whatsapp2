<template>
    <div class="flex h-screen">
      <!-- Liste des conversations -->
      <div class="w-1/4 bg-gray-100 border-r border-gray-300">
        <div class="p-4 border-b border-gray-300">
          <input type="text" placeholder="Rechercher..." class="w-full p-2 border rounded" />
        </div>
        <ul class="overflow-y-auto h-full">
          <li v-for="(conversation, index) in conversations" :key="index" @click="selectConversation(index)" class="cursor-pointer p-4 border-b border-gray-300 hover:bg-gray-200">
            <div class="font-bold">{{ conversation.name }}</div>
            <div class="text-sm text-gray-600">{{ conversation.lastMessage }}</div>
          </li>
        </ul>
      </div>
  
      <!-- Fenêtre de chat -->
      <div class="flex-1 flex flex-col">
        <div class="p-4 border-b border-gray-300 flex items-center">
          <div class="font-bold">{{ selectedConversation.name }}</div>
        </div>
        <div class="flex-1 overflow-y-auto p-4">
          <div v-for="(message, index) in selectedConversation.messages" :key="index" :class="{'text-right': message.isMine}">
            <div :class="['inline-block p-2 rounded-lg', message.isMine ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-800']">
              {{ message.text }}
            </div>
          </div>
        </div>
        <div class="p-4 border-t border-gray-300 flex items-center">
          <input v-model="newMessage" type="text" placeholder="Tapez un message..." class="flex-1 p-2 border rounded" @keyup.enter="sendMessage" />
          <button @click="sendMessage" class="ml-2 bg-green-500 text-white p-2 rounded">Envoyer</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ChatPage',
    data() {
      return {
        conversations: [
          {
            name: 'User1',
            lastMessage: 'aaaaaa',
            messages: [
              { text: 'bbbbbb', isMine: false },
              { text: 'cccc', isMine: true }
            ]
          },
          {
            name: 'lodibidon',
            lastMessage: 'grrr',
            messages: [
              { text: 'non', isMine: false },
              { text: 'Oui', isMine: true }
            ]
          }
        ],
        selectedConversationIndex: 0,
        newMessage: ''
      };
    },
    computed: {
      selectedConversation() {
        return this.conversations[this.selectedConversationIndex];
      }
    },
    methods: {
      selectConversation(index) {
        this.selectedConversationIndex = index;
      },
      sendMessage() {
        if (this.newMessage.trim() !== '') {
          this.selectedConversation.messages.push({ text: this.newMessage, isMine: true });
          this.newMessage = '';
        }
      }
    }
  };
  </script>
  