<template>
    <div class="page-wrapper">
        <my-filterable :title="$t('summaries.titles.index')" :resource="resource" :filterable="false" :exportable="false">
            <el-button type="primary" @click.prevent="create()" slot="create">{{ $t('buttons.new') }}</el-button>
            <tr slot="heading">
                <th>#</th>
                <th>{{ $t('fields.identifier') }}</th>
                <th>{{ $t('fields.date_of_issue') }}</th>
                <th>{{ $t('fields.ticket') }}</th>
                <th>{{ $t('fields.state_type_id') }}</th>
                <th class="has-text-right">{{ $t('fields.created_at') }}</th>
                <th class="has-text-right">{{ $t('labels.downloads') }}</th>
                <th class="has-text-right">{{ $t('labels.actions') }}</th>
            <tr>
            <tr slot-scope="{ index, item }">
                <td>{{ index }}</td>
                <td>{{ item.identifier }}</td>
                <td>{{ item.date_of_issue }}</td>
                <td>{{ item.ticket }}</td>
                <td>{{ item.state_type_description }}</td>
                <td class="has-text-right">{{ item.created_at }}</td>
                <td class="has-text-right table-actions">
                    <el-button plain @click.prevent="download(item.download_cdr)">CDR</el-button>
                    <el-button plain @click.prevent="download(item.download_xml)">XML</el-button>
                </td>
                <td class="has-text-right table-actions">
                    <el-button icon="el-icon-search" :loading="loading" plain
                               @click.prevent="checkTicket(item.id)"
                               v-if="item.check_ticket"></el-button>
                </td>
            </tr>
        </my-filterable>
        <summary-form :showDialog.sync="showDialog"
                       :external="false"></summary-form>
    </div>
</template>

<script>

    import SummaryForm from './form.vue'
    import {get} from '../../helpers/functions'

    export default {
        components: {SummaryForm},
        data () {
            return {
                loading: false,
                resource: 'summaries',
                showDialog: false,
                records: [],
            }
        },
        methods: {
            create() {
                this.showDialog = true
            },
            checkTicket(id) {
                this.loading = true
                get(`/${this.resource}/check_ticket/${id}`)
                    .then((response) => {
                        if(response.success) {
                            this.$eventHub.$emit('reloadData')
                            this.$message.success(response.message)
                        } else {
                            this.$message.error(response.message)
                        }
                    })
                    .catch((error) => {
                        this.$message.error(error.response.data.message)
                    })
                    .then(() => {
                        this.loading = false
                    })
            },
            download(download) {
                window.open(download, '_blank');
            }
        }
    }
</script>
