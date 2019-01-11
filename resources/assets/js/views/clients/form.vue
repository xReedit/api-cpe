<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form class="columns is-multiline" @submit.prevent="submit" autocomplete="off">
            <div class="column is-6">
                <my-control :label="$t('fields.name')" cfor="name" :errors="errors" error-field="name">
                    <el-input v-model="form.name" id="name"></el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.email')" cfor="email" :errors="errors" error-field="email">
                    <el-input v-model="form.email" id="email"></el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.company_name')" cfor="company_name" :errors="errors" error-field="company_name">
                    <el-input v-model="form.company_name" id="company_name"></el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.company_number')" cfor="company_number" :errors="errors" error-field="company_number">
                    <el-input v-model="form.company_number" id="company_number" :maxlength="11"></el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.soap_type_id')" cfor="soap_type_id" :errors="errors" error-field="soap_type_id">
                    <el-select v-model="form.soap_type_id" id="soap_type_id">
                        <el-option v-for="option in soap_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                    </el-select>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.soap_username')" cfor="soap_username" :errors="errors" error-field="soap_username">
                    <el-input v-model="form.soap_username" id="soap_username"></el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.soap_password')" cfor="soap_password" :errors="errors" error-field="soap_password">
                    <el-input v-model="form.soap_password" id="soap_password"></el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.certificate')" cfor="certificate" :errors="errors" error-field="certificate">
                    <el-input v-model="form.certificate" id="certificate" :readonly="true">
                        <el-upload slot="append"
                                :headers="headers"
                                :data="{'type': 'certificate'}"
                                action="/clients/uploads"
                                :show-file-list="false"
                                :on-success="successUpload">
                            <el-button type="primary" icon="el-icon-upload"></el-button>
                        </el-upload>
                    </el-input>
                </my-control>
            </div>
            <div class="column is-12 has-text-right">
                <el-button @click.prevent="close()">{{ $t('buttons.cancel') }}</el-button>
                <el-button type="primary" native-type="submit" :loading="submit_loading">{{ $t('buttons.save') }}</el-button>
            </div>
        </form>
    </el-dialog>
</template>
<script>

    import {fetch} from '../../helpers/functions'
    import {EventBus} from '../../helpers/bus'
    import {formable} from '../../mixins/formable'

    export default {
        mixins: [formable],
        props: ['showDialog', 'recordId'],
        data() {
            return {
                headers: headers_token,
                titleDialog: null,
                resource: 'clients',
                soap_types: []
            }
        },
        created() {
            this.initForm()
            this._tables().then(response => {
                this.soap_types = response.soap_types
            })
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    name: null,
                    email: null,
                    company_name: null,
                    company_number: null,
                    soap_type_id: '01',
                    soap_user: null,
                    soap_password: null,
                    certificate: null
                }
            },
            create() {
                this.titleDialog = (this.recordId)? this.$t('clients.titles.edit'):this.$t('clients.titles.new')
                if (this.recordId) {
                    this._create()
                }
            },
            submit() {
                this._submit().then(() => {
                    this.$eventHub.$emit('reloadData')
                    this.close()
                })
            },
            successUpload(response, file, fileList) {
                if (response.success) {
                    this.form.certificate = response.path
                } else {
                    this.$message({message:this.$t('actions.upload.error'), type: 'error'})
                }
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
        }
    }
</script>