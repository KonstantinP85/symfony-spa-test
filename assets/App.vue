<template>
    <v-app>
        <Topbar/>
        <Sidebar/>
        <v-main>
            <v-container fluid>
                <router-view></router-view>
            </v-container>
        </v-main>
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