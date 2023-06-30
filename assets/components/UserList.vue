<template>

        <v-card>
            <v-card-title>
                Список пользователей
                <v-spacer></v-spacer>
            </v-card-title>
            <div>
                <div class="d-flex justify-end">
                    <div class="user-search-field">
                        <v-text-field
                            class="ma-2 pa-2"
                            v-model="search"
                            append-icon="mdi-magnify"
                            label="Search"
                            single-line
                            hide-details
                        ></v-text-field>
                    </div>
                </div>
                <v-data-table-server
                  class="elevation-1"
                  v-model:items-per-page="itemsPerPage"
                  :headers="headers"
                  :items="users"
                  :items-length="totalItems"
                  :search="search"
                  v-model:sort-by="sortBy"
                  fixed-header
                  :loading="loading"
                  v-model:page="page"
                  @update:options="userList"
                >
                  <template v-slot:item="{ item }">
                    <tr>
                      <td>{{item.columns.id}}</td>
                      <td>{{item.columns.login}}</td>
                      <td>{{item.columns.roles}}</td>

                      <td>
                        <v-tooltip text="Редактировать"  location="bottom">
                          <template v-slot:activator="{ props }">
                            <v-btn
                                size="x-small"
                                v-bind="props"
                                @click="openEditForm(item.columns.id)"
                            >
                              <v-icon
                                  small
                                  color="purple darken-2"
                              >
                                mdi-pen
                              </v-icon>
                            </v-btn>
                          </template>
                        </v-tooltip>
                        <v-tooltip text="Удалить"  location="bottom">
                          <template v-slot:activator="{ props }">
                            <v-btn
                                size="x-small"
                                v-bind="props"
                                @click="showDeleteDialog(item.columns.id)"
                            >
                              <v-icon
                                  small
                                  color="purple darken-2"
                              >
                                mdi-trash-can
                              </v-icon>
                            </v-btn>
                          </template>
                        </v-tooltip>
                        <v-tooltip text="Поменять роль"  location="bottom">
                          <template v-slot:activator="{ props }">
                            <v-btn
                                class="ml-2"
                                size="x-small"
                                v-bind="props"
                                @click="showChangeRoleDialog(item.columns.id, item.columns.roles)"
                            >
                              <v-icon
                                  small
                                  color="purple darken-2"
                              >
                                mdi-swap-horizontal
                              </v-icon>
                            </v-btn>
                          </template>
                        </v-tooltip>
                      </td>
                    </tr>
                  </template>
                </v-data-table-server>
            </div>
            <v-dialog
                v-model="dialog"
                width="auto"
            >
              <v-card>
                <v-card-text>
                  Вы действительно хотите удалить пользователя и все его ссылки?
                </v-card-text>
                <v-card-actions class="d-flex justify-end">
                  <v-btn color="grey" @click="dialog = false">Закрыть</v-btn>
                  <v-btn color="pink" @click="deleteUser">Удалить</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>

            <v-dialog
                v-model="editForm"
                class="edit-dialog"
            >
              <v-card>
                <v-card-text>
                  Редактирование пользователя
                </v-card-text>
                <v-card class="rounded-card pa-5">
                  <v-form
                      ref="form"
                      lazy-validation
                  >
                    <v-text-field
                        label="Логин"
                        :counter="255"
                        @focus="alert=false"
                        v-model="login"
                        :rules="commonRules"
                        required
                    ></v-text-field>
                  </v-form>
                  <v-alert
                      type="warning"
                      v-model="alert"
                      closable
                  >
                    <span>{{ warning }}</span>
                    <li v-for="(value, name) in errorObject">
                      {{ name }}: {{ value }}
                    </li>
                  </v-alert>
                </v-card>
                <v-card-actions class="d-flex justify-end">
                  <v-btn color="grey" @click="closeEditForm">Закрыть</v-btn>
                  <v-btn color="green" @click="editUser">Сохранить</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>


            <v-dialog
                v-model="changeRoleForm"
                width="auto"
            >
              <v-card>
                <v-card-text>
                  {{ actionText }}
                </v-card-text>
                <v-card-actions>
                  <v-btn color="grey" @click="changeRoleForm = false">Закрыть</v-btn>
                  <v-btn v-if="isAdmin" color="red" @click="changeRoles('make_user')">Забрать роль админа</v-btn>
                  <v-btn v-else color="green" @click="changeRoles('make_admin')">Сделать админом</v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>

        </v-card>

</template>
<script lang="ts">
import {requests, apiConstants} from '../api/main';
import { defineComponent } from "vue";

export default defineComponent ({
    name: 'UserList',
    data: () => ({
        totalItems: 5,
        page: 1,
        pageCount: 1,
        options: {},
        search: '',
        sort: 'id',
        order: 'asc',
        sortBy: [{key: 'id', order: 'asc'}],
        loading: false,
        itemsPerPage: 10,
        users: [],
        headers: [
            { title: 'Идентификатор', key: 'id' },
            { title: 'Логин', align: 'start', key: 'login'},
            { title: 'Роль', sortable: false, key: 'roles' },
            { title: 'Действия', key: 'action' },
        ],
        editForm: false,
        idForEdit: 0,
        dialog: false,
        idForDelete: 0,
        changeRoleForm: false,
        login: '',
        commonRules: [
            (value: any) => {
                if (value) return true
                return 'Поле должно быть заполнено'
            },
            (value: any) => {
                if (value?.length <= 255) return true
                return 'Должно быть менее 255 символов'
            },
        ],
        alert: false,
        errorObject: {},
        warning: 'Ошибка создания',
        actionText: 'Изменение роли',
        changeRolesId: 0,
        isAdmin: false
    }),
    methods: {
        async userList() {
            this.loading = true
            try {
                const result = await requests.get(apiConstants.USER.DEFAULT, { page: this.page, countPerPage: this.itemsPerPage, order: this.sortBy[0].order, sort: this.sortBy[0].key, login: this.search})
                this.users = result.data.items
                this.totalItems = result.data.total
                this.pageCount = result.data.total/result.data.limit
                this.loading = false
            } catch (err) {
                console.log(err);
            }
        },
        async getUser(id: number) {
            try {
                const response = await requests.get(apiConstants.USER.OPERATION(id), {})
                this.login = response.data.login
            } catch (err) {
                console.log(err);
            }
        },

        async editUser() {
            const result = await requests.put(apiConstants.USER.OPERATION(this.idForEdit), { login: this.login})
            if (result.data.status == "error") {
                this.errorObject = JSON.parse(result.data.result)
                this.alert = true
            } else {
                this.editForm = false
                await this.userList()
            }
        },
        closeEditForm() {
            this.editForm = false
            this.alert = false
        },
        openEditForm(id: number) {
            this.editForm = true
            this.idForEdit = id
            if (id != 0) {
                this.getUser(id)
            }
        },

        async deleteUser() {
            try {
                await fetch(apiConstants.USER.OPERATION(this.idForDelete), {method: 'DELETE'})
                this.dialog = false
                await this.userList()
            } catch (error) {
                console.log(error)
            }
        },
        showDeleteDialog(id: number) {
            this.dialog = true
            this.idForDelete = id
        },

        showChangeRoleDialog(id: number, roles: any) {
            this.changeRolesId = id
            this.isAdmin = !!roles.includes("ROLE_ADMIN");
            this.changeRoleForm = true
        },
        async changeRoles(status: string) {
            const result = await requests.patch(apiConstants.USER.CHANGE_ROLE(this.changeRolesId, status), {})
            if (result.data.status == "error") {
                this.errorObject = JSON.parse(result.data.result)
                this.alert = true
            } else {
                this.changeRoleForm = false
                await this.userList()
            }
        }
    },
    mounted() {
        this.userList();
    },
});
</script>
<script setup lang="ts">
</script>