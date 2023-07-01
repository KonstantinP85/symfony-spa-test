<template>

    <v-app>
        <v-main>
            <v-container style="margin-top: 10%">
                <v-container fill-height class="login-window">

                  <v-card text-center>
                    <v-card class="rounded-card pa-5">
                      <v-card-title>
                        <h3>Авторизация</h3>
                      </v-card-title>

                      <v-form
                          ref="form"
                          lazy-validation
                          v-on:submit.prevent="login"
                      >
                        <v-text-field
                            v-model="model.login"
                            label="Логин"
                            :rules="rules"
                            @focus="hideAlert"
                            required
                        ></v-text-field>

                        <v-text-field
                            v-model="model.password"
                            type="password"
                            label="Пароль"
                            :rules="rules"
                            @focus="hideAlert"
                            required
                        ></v-text-field>
                        <v-alert
                            type="warning"
                            v-model="alert"
                            closable
                        >
                          <span>{{ warning }}</span>
                        </v-alert>
                        <div class="d-flex justify-end">
                          <v-btn
                              class="mt-5"
                              type="submit"
                          >
                            ВОЙТИ
                          </v-btn>
                        </div>
                      </v-form>

                    </v-card>
                  </v-card>

                </v-container>
            </v-container>
        </v-main>
    </v-app>


</template>

<script lang="ts">
import {requests, apiConstants} from '../api/main';
import { defineComponent } from 'vue'

export default defineComponent ({
    data() {
        return {
            warning: '',
            alert: false,
            model: {
                login: '',
                password: '',
            },
            rules: [
                (value: any) => {
                    if (value) return true
                    return 'Поле должно быть заполнено'
                },
            ],
        };
    },
    methods: {
        hideAlert(): void {
            this.alert = false
        },
        login(): any {
            requests.post(apiConstants.FORM.LOGIN, {
                login: this.model.login,
                password: this.model.password
            }).then((response) => {
                if (response.data.status && response.data.status == 'error') {
                    this.alert = true
                    this.warning = response.data.result
                }
            }).catch((error) => {
                console.log(error)
            })

            return
        },
    },
});
</script>