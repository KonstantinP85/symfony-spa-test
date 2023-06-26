<template>
  <v-app>
    <v-layout class="rounded rounded-md">
      <Sidebar :is-admin="isAdmin"/>
      <Topbar :user-login="userLogin"/>
      <v-main>
        <v-container fluid="true">
          <router-view></router-view>
        </v-container>
      </v-main>
    </v-layout>
  </v-app>
</template>

<script lang="ts">
import { defineComponent } from "vue";
import Sidebar from './components/Sidebar.vue';
import Topbar from './components/Topbar.vue';
import {apiConstants, requests} from "./api/main";
export default defineComponent ({
    name: 'App',
    components: {Sidebar, Topbar},
    data: () => ({
        userLogin: '',
        isAdmin: false
    }),
    methods: {
        async currentUser() {
            try {
                const response = await requests.get(apiConstants.USER.CURRENT_USER, {})
                this.userLogin = response.data.login
                this.isAdmin = response.data.roles.includes("ROLE_ADMIN")
            } catch (err) {
                console.log(err);
            }
        }
    },
    mounted() {
        this.currentUser();
    },
});
</script>