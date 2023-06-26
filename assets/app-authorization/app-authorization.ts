import { createApp } from 'vue';
import App from './App.vue';
import  vuetify  from '../plugins/vuetify';
import '../styles/app-authorization/index.sass';

createApp(App).use(vuetify).mount("#app-authorization");