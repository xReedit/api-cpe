<template>
    <el-dialog :title="$t('summaries_annulment.titles.new')" :visible="showDialog" @close="close">
        <form class="columns is-multiline" @submit.prevent="submit" autocomplete="off">
            <div class="column is-4">
                <my-control :label="$t('fields.client_id')" cfor="client_id" :errors="errors" error-field="client_id">
                    <el-select v-model="form.user_id" id="client_id" filterable>
                        <el-option v-for="option in clients" :key="option.user_id" :value="option.user_id" :label="option.company_name"></el-option>
                    </el-select>
                </my-control>
            </div>
            <div class="column is-3">
                <my-control :label="$t('fields.date_of_issue')" cfor="date_of_issue" :errors="errors" error-field="date_of_issue">
                    <el-date-picker v-model="form.date_of_issue" type="date" :clearable="false" value-format="yyyy-MM-dd"></el-date-picker>
                </my-control>
            </div>
            <div class="column is-3">
                <my-control :label="$t('fields.date_of_reference')" cfor="date_of_reference" :errors="errors" error-field="date_of_reference">
                    <el-date-picker v-model="form.date_of_reference" type="date" :clearable="false" value-format="yyyy-MM-dd"></el-date-picker>
                </my-control>
            </div>
            <div class="column is-narrow">
                <el-button type="primary" icon="el-icon-search" @click="searchDocuments" :loading="loading_search">{{ $t('buttons.search')}} </el-button>
            </div>
            <template v-if="documents.length > 0">
                <div class="column">
                    <label>Documento anular</label>
                    <el-select v-model="selectDocuments.document_id" filterable>
                        <el-option v-for="option in documents" :key="option.id" :value="option.id" :label="option.document_number"></el-option>
                    </el-select>
                </div>
                <div class="column is-narrow">
                    <el-button type="primary" icon="el-icon-plus" @click="addDocument"></el-button>
                </div>
            </template>
            <template v-if="form.documents.length > 0">
                <div class="column is-12">
                    <table class="table is-fullwidth">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ $t('fields.date_of_issue') }}</th>
                            <th>{{ $t('fields.number') }}</th>
                            <th>{{ $t('fields.total') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(row, index) in form.documents">
                            <td>{{ index + 1 }}</td>
                            <td>{{ row.date_of_issue }}</td>
                            <td>{{ row.document_number }}</td>
                            <td>{{ row.total }}</td>
                            <td>
                                <el-button type="primary" icon="el-icon-minus" @click="removeDocument(index)"></el-button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </template>
            <template v-else>
                <div class="column is-12">
                    {{ $t('labels.no_results_found') }}
                </div>
            </template>
            <div class="column is-12 has-text-right">
                <el-button type="default" @click.prevent="close">{{ $t('buttons.cancel') }}</el-button>
                <el-button type="primary"
                           :loading="submit_loading"
                           native-type="submit"
                           v-if="form.documents.length > 0">{{ $t('buttons.generate') }}</el-button>
            </div>
        </form>
    </el-dialog>
</template>
<script>

    import {formable} from '../../mixins/formable'
    import {post} from '../../helpers/functions'

    export default {
        mixins: [formable],
        props: ['showDialog'],
        data () {
            return {
                loading: false,
                loading_search: false,
                resource: 'summaries_annulment',
                clients: [],
                selectDocuments: {},
                documents: [],
            }
        },
        created() {
            this.initForm()
            this._tables().then(response => {
                this.clients = response.clients
            })
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    user_id: null,
                    type: 3,
                    date_of_issue: null,
                    date_of_reference: null,
                    documents: [],
                }
            },
            searchDocuments() {
                this.documents = []
                this.form.documents = []
                this.selectDocuments = {}
                this.loading_search = true
                post(`/${this.resource}/search_documents`, this.form)
                    .then((response) => {
                        this.documents = response.data
                    })
                    .catch((error) => {
                        this.$message.error(error.response.data.message)
                    })
                    .then(() => {
                        this.loading_search = false
                    })
            },

            submit() {
                this._submit().then(() => {
                    this.$eventHub.$emit('reloadData')
                    this.close()
                })
            },
            close() {
                this.$emit('update:showDialog', false)
                this.initForm()
            },
            addDocument() {
                let document = _.find(this.documents, {id: this.selectDocuments.document_id})
                this.form.documents.push({
                    id: this.selectDocuments.document_id,
                    date_of_issue: document.date_of_issue,
                    document_number: document.document_number,
                    total: document.total,
                })
            },
            removeDocument(index) {
                this.form.documents.splice(index, 1)
            }
        }
    }
</script>
