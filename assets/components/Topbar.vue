<template>
    <v-app-bar color="white" class="flex-grow-0" height="48" app>
        <v-card-text class="text-right"><v-icon>mdi-account</v-icon> Администратор</v-card-text>
        <v-btn color="#5865f2" @click="logout" prepend-icon="mdi-exit-to-app"></v-btn>
    </v-app-bar>
</template>

<script lang="ts">
import {requests, apiConstants} from '../api/main';
export default {
    name: 'Topbar',
    data: () => ({
        link: apiConstants.AUTH.LOGOUT
    }),
    methods: {
        logout() {
            requests.get(apiConstants.AUTH.LOGOUT, {})
        }
    }
};
</script>