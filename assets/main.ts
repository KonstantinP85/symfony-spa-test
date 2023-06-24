import { createApp } from "vue";
import App from './App.vue';
import router from './router/main';
import vuetify from './plugins/vuetify';
import './styles/index.sass';

createApp(App).use(router).use(vuetify).mount("#app-main");