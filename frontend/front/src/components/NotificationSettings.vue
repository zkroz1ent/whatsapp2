<template>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Gestion des Notifications</h2>

        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" v-model="notifications.global" class="form-checkbox h-5 w-5 text-green-600" />
                <span class="ml-2">Fil Global</span>
            </label>
        </div>

        <div v-for="commission in commissions" :key="commission.id" class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" :value="commission.id" v-model="notifications.commissions"
                    class="form-checkbox h-5 w-5 text-green-600" />
                <span class="ml-2">{{ commission.name }}</span>
            </label>
        </div>

        <button @click="saveNotifications"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Enregistrer
        </button>

        <h2 class="text-2xl font-bold mt-8 mb-4">Notifications Reçues</h2>
        <ul class="list-disc pl-5">
            <li v-for="notif in receivedNotifications" :key="notif.id" class="mb-2">
                {{ notif.content }} - {{ notif.createdAt }}
            </li>
        </ul>
    </div>
</template>

<script>
import axios from 'axios';
import { reactive, toRefs, onMounted } from 'vue';

export default {
    name: 'NotificationSettings',
    setup() {
        const state = reactive({
            userId: localStorage.getItem('userId'),  // Assurez-vous que l'ID utilisateur est bien récupéré
            commissions: [],
            notifications: {
                global: false,
                commissions: []
            },
            receivedNotifications: []
        });

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

        const getReceivedNotifications = async () => {
            const token = localStorage.getItem('token');
            const id = localStorage.getItem('userId');

            try {
                const response = await axios.get(`http://127.0.0.1:8000/api/notifications/${id}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                state.receivedNotifications = response.data;
            } catch (error) {
                console.error('Erreur lors de la récupération des notifications:', error);
            }
        };

        const saveNotifications = async () => {
            const token = localStorage.getItem('token');
            const id = localStorage.getItem('userId');

            const data = {
                userId: id,
                notifications: [
                    ...(state.notifications.global ? [{ type: 'global' }] : []),
                    ...state.notifications.commissions.map(commissionId => ({ type: 'commission', id: commissionId }))
                ]
            };

            try {
                await axios.post('http://127.0.0.1:8000/api/notifications', data, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                alert('Notifications mises à jour avec succès');
            } catch (error) {
                console.error('Erreur lors de la mise à jour des notifications:', error);
            }
        };

        onMounted(() => {
            getCommissions();
            getReceivedNotifications();
        });

        return {
            ...toRefs(state),
            saveNotifications
        };
    }
};
</script>