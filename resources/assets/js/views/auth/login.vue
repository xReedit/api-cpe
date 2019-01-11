<template>
    <div class="auth-login">
        <form class="columns is-multiline" @submit.prevent="login">
            <div class="column is-12">
                <h2 class="title is-3">{{ $t('login.titles.index') }}</h2>
            </div>
            <div class="column is-12">
                <my-control :label="$t('fields.email')" cfor="email" :errors="errors" error-field="email">
                    <el-input v-model="form.email" id="email"></el-input>
                </my-control>
            </div>
            <div class="column is-12">
                <my-control :label="$t('fields.password')" cfor="password" :errors="errors" error-field="password">
                    <el-input type="password"  v-model="form.password" id="password"></el-input>
                </my-control>
            </div>
            <div class="column is-12">
                <el-checkbox v-model="form.remember">{{ $t('fields.remember_account') }}</el-checkbox>
            </div>
            <div class="column is-12">
                <el-button type="primary" native-type="submit" :loading="loading">{{ $t('buttons.login') }}</el-button>
            </div>
        </form>
    </div>
</template>

<script>

    import http from '../../helpers/http'

    export default {
        data() {
            return {
                loading: false,
                errors: {},
                form: {
                    email: null,
                    password: null,
                    remember: false
                }
            }
        },
        methods: {
            login() {
                this.loading = true
                http.post('/login', this.form)
                    .then(response => {
                        location.href = '/'
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.message
                        } else {
                            console.log(error)
                        }
                    })
                    .then(() => {
                        this.loading = false
                    });
            }
        }
    }
</script>