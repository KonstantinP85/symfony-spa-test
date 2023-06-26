import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import Main from "../components/Main.vue";
import LinkList from "../components/LinkList.vue";
import UserList from "../components/UserList.vue";
import LinkCreate from "../components/LinkCreate.vue";

const routes: Array<RouteRecordRaw> = [
    {
        path: '/',
        name: 'Main',
        component: Main,
    },
    {
        path: '/link/list',
        name: 'LinkList',
        component: LinkList,
    },
    {
        path: '/link/create',
        name: 'LinkCreate',
        component: LinkCreate,
    },
    {
        path: '/user/list',
        name: 'UserList',
        component: UserList,
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router