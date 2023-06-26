<template>

    <v-card>
        <v-card-title>
            Список ссылок
        <v-spacer></v-spacer>

          <v-row>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                  v-model="searchName"
                  label="Название"
                  class="pa-4"
                  @update:modelValue="linkList"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                  v-model="searchLink"
                  label="Ссылка"
                  class="pa-4"
                  @update:modelValue="linkList"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" md="3">
              <v-text-field
                  type="number"
                  v-model="searchClickCount"
                  label="Количество переходов"
                  class="pa-4"
                  @update:modelValue="linkList"
              ></v-text-field>
            </v-col>
            <v-col cols="12" sm="6" md="2">
              <v-btn
                  class="mt-5"
                  size="x-small"
                  @click="reset"
              >
                Сбросить
              </v-btn>
            </v-col>
          </v-row>
        </v-card-title>

      <div>
        <v-data-table-server
            class="elevation-1"
            v-model:items-per-page="itemsPerPage"
            :headers="headers"
            :items="links"
            :items-length="totalItems"
            :search="search"
            v-model:sort-by="sortBy"
            fixed-header
            :loading="loading"
            v-model:page="page"
            @update:options="linkList"
        >
            <template v-slot:item="{ item }">
              <tr>
                <td>{{item.columns.id}}</td>
                <td>{{item.columns.name}}</td>
                <td>{{item.columns.url}}</td>
                <td>{{item.columns.status}}</td>
                <td>{{item.columns.clickCount}}</td>

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
                    <v-tooltip text="Модерация"  location="bottom">
                      <template v-slot:activator="{ props }">
                        <v-btn
                            class="ml-2"
                            size="x-small"
                            v-bind="props"
                            @click="showModerationDialog(item.columns.id, item.columns.status)"
                        >
                          <v-icon
                              small
                              color="purple darken-2"
                          >
                            mdi-state-machine
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
                  Вы действительно хотите удалить ссылку?
              </v-card-text>
              <v-card-actions>
                <v-btn color="grey" @click="dialog = false">Закрыть</v-btn>
                <v-btn color="pink" @click="deleteLink">Удалить</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog
              v-model="moderationForm"
              width="auto"
          >
            <v-card>
              <v-card-text>
                Модерация
              </v-card-text>
              <v-card-actions>
                <v-btn color="grey" @click="moderationForm = false">Закрыть</v-btn>
                <v-btn v-if="currentStatus === 'moderation'" color="red" @click="moderate('draft')">В черновик</v-btn>
                <v-btn v-if="currentStatus !== 'moderation'" color="pink" @click="moderate('moderation')">В Модерацию</v-btn>
                <v-btn v-if="currentStatus === 'moderation'" color="green" @click="moderate('published')">Опубликовать</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <v-dialog
              v-model="editForm"
              class="edit-dialog"
          >
            <v-card>
              <v-card-text>
                Редактирование ссылки
              </v-card-text>
              <v-card class="rounded-card pa-5">
                <v-form
                    ref="form"
                    lazy-validation
                >
                  <v-text-field
                      label="Название"
                      :counter="255"
                      @focus="alert=false"
                      v-model="linkName"
                      :rules="commonRules"
                      required
                  ></v-text-field>

                  <v-text-field
                      label="Ссылка"
                      :counter="255"
                      @focus="alert=false"
                      v-model="link"
                      :rules="commonRules.concat(linkRules)"
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
              <v-card-actions>
                <v-btn color="grey" @click="closeEditForm">Закрыть</v-btn>
                <v-btn color="green" @click="editLink">Сохранить</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
    </v-card>

</template>

<script lang="ts">
import {requests, apiConstants} from '../api/main';
import { defineComponent } from "vue";

export default defineComponent ({
    name: 'LinkList',
    data: () => ({
        totalItems: 5,
        search: '',
        page: 1,
        pageCount: 1,
        options: {},
        name_search: '',
        sortBy: [{ key: 'name', order: 'asc' }],
        loading: false,
        itemsPerPage: 10,
        headers: [
            { title: 'Идентификатор', key: 'id' },
            { title: 'Название', align: 'start', key: 'name'},
            { title: 'Ссылка', sortable: false, key: 'url' },
            { title: 'Статус', key: 'status' },
            { title: 'Количество переходов', key: 'clickCount' },
            { title: 'Действия', key: 'action' },
        ],
        links: [],
        dialog: false,
        idForDelete: 0,
        idForEdit: 0,
        linkName: '',
        link: '',
        editForm: false,
        searchName: null,
        searchLink: null,
        searchClickCount: null,
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
        linkRules: [
            (value: any) => {
                if (/https?:\/\/(?:w{1,3}\.)?[^\s.]+(?:\.[a-z]+)*(?::\d+)?((?:\/\w+)|(?:-\w+))*\/?(?![^<]*(?:<\/\w+>|\/?>))/.test(value)) return true
            return 'Ссылка не валидна'
            },
        ],
        moderationForm: false,
        idForModeration: 0,
        currentStatus: 'moderation',
        warning: 'Ошибка создания',
        errorObject: {},
        alert: false,
    }),
    methods: {
        async linkList() {
            this.loading = true

            const result = await requests.get(apiConstants.LINK.DEFAULT, { page: this.page, countPerPage: this.itemsPerPage, order: this.sortBy[0].order, sort: this.sortBy[0].key, name: this.searchName, url: this.searchLink, clickCount: this.searchClickCount})
            this.links = result.data.items
            this.totalItems = result.data.total
            this.pageCount = result.data.total/result.data.limit
            this.loading = false

        },
        async editLink() {
            const result = await requests.put(apiConstants.LINK.OPERATION(this.idForEdit), { name: this.linkName, url: this.link})
            if (result.data.status == "error") {
                this.errorObject = JSON.parse(result.data.result)
                this.alert = true
            } else {
                this.editForm = false
                await this.linkList()
            }
        },
        async getLink(id: number) {

            const result = await requests.get(apiConstants.LINK.OPERATION(id))
            this.linkName = result.data.name
            this.link = result.data.url
        },

        confirm(data: object) {
            if (this.idForEdit == 0) {
            }
        },
        closeEditForm() {
            this.editForm = false
            this.alert = false
        },
        openEditForm(id: number) {
            this.linkName = ''
            this.link = ''
            this.editForm = true
            this.idForEdit = id
            if (id != 0) {
                this.getLink(id)
            }
        },

        showDeleteDialog(id: number) {
            this.dialog = true
            this.idForDelete = id
        },
        async deleteLink() {
            try {
                await fetch(apiConstants.LINK.OPERATION(this.idForDelete), {method: 'DELETE'})
                this.dialog = false
                await this.linkList()
            } catch (error) {
                console.log(error)
            }
        },

        reset() {
            this.searchName = null
            this.searchLink =  null
            this.searchClickCount = null
            this.linkList()
        },

        showModerationDialog(id: number, status: string) {
            this.moderationForm = true
            this.idForModeration = id
            this.currentStatus = status
        },

        async moderate(status: string) {
            const result = await requests.patch(apiConstants.LINK.MODERATION(this.idForModeration, status), {})
            if (result.data.status == "error") {
                this.errorObject = JSON.parse(result.data.result)
                this.alert = true
            } else {
                this.moderationForm = false
                await this.linkList()
            }
        }
    },
    mounted() {
        this.linkList();
    },
});
</script>
<style lang="sass" scoped>


</style>