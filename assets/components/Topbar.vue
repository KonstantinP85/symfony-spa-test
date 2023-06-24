<template>
    <v-app-bar color="white" class="flex-grow-0" height="60" app>
      <v-btn
          color="info"
          @click=""
          :to="createLinkPath"
      >
        Добавить ссылку
      </v-btn>
      <v-card-text class="text-right"><v-icon>mdi-account</v-icon> Администратор</v-card-text>
        <v-btn @click="logout" class="logout-btn" prepend-icon="mdi-exit-to-app"></v-btn>
    </v-app-bar>
</template>

<script lang="ts">
import {requests, apiConstants} from '../api/main';
export default {
    name: 'Topbar',
    data: () => ({
        link: apiConstants.AUTH.LOGOUT,
        createLinkPath: '/link/create'
    }),
    methods: {
        logout() {
            requests.get(apiConstants.AUTH.LOGOUT, {})
        }
    }
};
</script>