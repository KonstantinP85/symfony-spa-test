<template>

    <v-navigation-drawer>
        <v-list-item>
            <v-list-item class="text-center">
                <v-icon icon="mdi-home" />
                <v-list-item-title class="text-h6"  :to="basePath">TEST TASK</v-list-item-title>
            </v-list-item>
        </v-list-item>
        <v-divider></v-divider>
        <v-list v-if="isAdmin" dense >
            <v-list-item v-for="item in items" :key="item.title" :to="item.link">

                <template v-slot:prepend>
                    <v-icon :icon="item.icon"></v-icon>
                </template>

                <v-list-item-title v-text="item.title" :to="item.link"></v-list-item-title>
            </v-list-item>
        </v-list>

    </v-navigation-drawer>

</template>

<script lang="ts">

export default {
    name: 'Sidebar',
    props: ['isAdmin'],
    data: () => ({
        basePath: '/',
        items: [
            {
                title: 'ПОЛЬЗОВАТЕЛИ',
                icon: 'mdi-email',
                link: '/user/list'
            },
            {
                title: 'ССЫЛКИ',
                icon: 'mdi-link',
                link: '/link/list'
            },
        ],
    }),
};
</script>