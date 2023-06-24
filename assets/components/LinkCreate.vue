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
              :rules="commonRules"
              v-model="linkName"
              required
          ></v-text-field>

          <v-text-field
              label="Ссылка"
              :counter="255"
              :rules="commonRules.concat(linkRules)"
              v-model="link"
              required
          ></v-text-field>
        </v-form>
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
            <v-btn color="green" @click="dialog=false">Создать еще ссылку</v-btn>
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
    }),
    methods: {
        async createLink() {
            try {
                const result = await requests.post(apiConstants.LINK.CREATE, { name: this.linkName, url: this.link })
                this.linkName = ''
                this.link = ''
                this.dialog = true
            } catch (err) {
                console.log(err);
            }
        },
    }
});
</script>