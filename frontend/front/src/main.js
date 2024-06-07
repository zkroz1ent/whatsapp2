import axios from 'axios'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import './assets/tailwind.css'

// Créez une instance Axios avec la configuration CORS ajustée pour le développement
const axiosInstance = axios.create({
  baseURL: 'http://127.0.0.1:8000', // Remplacez avec l'URL de votre serveur
  headers: {
    'Content-Type': 'application/json',
    // 'Access-Control-Allow-Origin': '*' NE PAS UTILISER dans le code de production
  },
})

// Vous pouvez ajouter des intercepteurs avant toute requête pour les finaliser si nécessaire
// Cette partie est facultative et dépend de vos besoins spécifiques
axiosInstance.interceptors.request.use(request => {
  request.headers['Access-Control-Allow-Origin'] = '*'; // déconseillé pour la production
  return request;
});

// appliquer l'instance Axios personnalisée à vue
createApp(App).use(store).use(router).use(store).use(router).provide('axios', axiosInstance).mount('#app')