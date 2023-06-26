<template>

    <v-card>
      <v-card-title>
        Добавление ссылки
      </v-card-title>
      <v-card class="rounded-card pa-5">
        <v-form
            ref="form"
            lazy-validation
        >
          <v-text-field
              label="Название"
              :counter="255"
              @focus="alert=false"
              :rules="commonRules"
              v-model="linkName"
              required
          ></v-text-field>

          <v-text-field
              label="Ссылка"
              :counter="255"
              @focus="alert=false"
              :rules="commonRules.concat(linkRules)"
              v-model="link"
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
        <v-card-actions>
          <v-btn color="grey" :to="pathMainPage">На главную</v-btn>
          <v-btn color="green" @click="createLink">Сохранить</v-btn>
        </v-card-actions>
      </v-card>

      <v-dialog
          v-model="dialog"
          width="auto"
      >
        <v-card>
          <v-card-text>
            Отлично! Ссылка создана
          </v-card-text>
          <v-card-actions>
            <v-btn color="grey" :to="pathMainPage">На главную</v-btn>
            <v-btn color="green" @click="newLink">Создать еще ссылку</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-card>

</template>

<script lang="ts">
import {requests, apiConstants} from '../api/main';
import {defineComponent} from "vue";

export default defineComponent ({
    name: 'LinkCreate',
    data: () => ({
        pathMainPage: '/',
        linkName: '',
        link: '',
        alert: false,
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
        dialog: false,
        warning: 'Ошибка создания',
        errorObject: {}
    }),
    methods: {
        async createLink() {
            try {
                const result = await requests.post(apiConstants.LINK.CREATE, { name: this.linkName, url: this.link })
                if (result.status && result.status == 204) {
                    this.dialog = true
                } else {
                  if (result.data.status == "error") {
                    this.errorObject = JSON.parse(result.data.result)
                  }
                  this.alert = true
                }
            } catch (err) {
                console.log(err);
            }
        },
        newLink() {
            this.linkName = ''
            this.link = ''
            this.dialog = false
        }
    }
});
</script>