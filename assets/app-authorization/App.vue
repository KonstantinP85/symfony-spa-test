<template>

    <v-app>
        <v-main>
            <v-container fluid style="margin-top: 10%">
                <v-container fill-height style="max-width: 900px">
                    <v-layout row wrap>
                        <v-flex xs12 sm6 offset-sm3 text-center>
                            <v-card class="rounded-card pa-5">
                                <h1>Авторизация</h1>

                                <v-form
                                        ref="form"
                                        lazy-validation
                                        v-on:submit.prevent="login"
                                >
                                    <v-text-field
                                            v-model="model.login"
                                            label="Логин"
                                            :rules="nameRules"
                                            @focus="hideAlert"
                                            required
                                    ></v-text-field>

                                    <v-text-field
                                            v-model="model.password"
                                            type="password"
                                            label="Пароль"
                                            :rules="nameRules"
                                            @focus="hideAlert"
                                            required
                                    ></v-text-field>
                                    <v-alert type="warning" :value="alert">
                                        <span>{{ warning }}</span>
                                    </v-alert>
                                    <v-btn type="submit" >ВОЙТИ</v-btn>
                                </v-form>

                            </v-card>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-container>
        </v-main>
    </v-app>


</template>

<script lang="ts">
import {requests, apiConstants} from '../api/main';
export default {
    data() {
        return {
            warning: '',
            alert: false,
            model: {
                login: '',
                password: '',
            },
            nameRules: [
                v => !!v || 'Обязательное поле',
            ],
        };
    },
    methods: {
        isFormInvalid() {
            return this.$refs.form.validate()
                .then((success) => {
                    return success;
                });
        },
        hideAlert() {
            this.alert = false
        },
        async login() {
            try {
                const login_response = await requests.post(apiConstants.FORM.LOGIN, {
                    login: this.model.login,
                    password: this.model.password
                })
            } catch (error) {
                if (error.response.status === 401) {
                    this.alert = true;
                    this.warning = error.response.data.error;
                }
                return;
            }

            await requests.post(apiConstants.AUTH.LOGIN, {
                login: this.model.login,
                password: this.model.password
            })
        },
    },
};
</script>