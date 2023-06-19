import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Main from "../components/Main.vue";

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'Main',
        component: Main,
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router