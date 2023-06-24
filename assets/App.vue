<template>
  <v-app>
    <v-layout class="rounded rounded-md">
      <Sidebar/>
      <Topbar/>
      <v-main class="d-flex align-center justify-center" style="min-height: 300px;">
        <v-container>
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
export default defineComponent ({
    name: 'App',
    components: {Sidebar, Topbar},
});
</script>