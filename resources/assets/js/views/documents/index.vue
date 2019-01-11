<template>
    <div class="page-wrapper">
        <my-filterable :title="$t('documents.titles.index')" :resource="resource" :filterable="false" :exportable="false">
            <tr slot="heading">
                <th>#</th>
                <th>{{ $t('fields.ubl_version') }}</th>
                <th>{{ $t('fields.soap_type_id') }}</th>
                <th>{{ $t('fields.document_type_id') }}</th>
                <th>{{ $t('fields.number') }}</th>
                <th>{{ $t('fields.date_of_issue') }}</th>
                <th>{{ $t('fields.external_id') }}</th>
                <th>{{ $t('fields.state_type_id') }}</th>
                <th class="has-text-right">{{ $t('fields.created_at') }}</th>
                <th class="has-text-right">{{ $t('labels.downloads') }}</th>
                <th class="has-text-right">{{ $t('labels.actions') }}</th>
            <tr>
            <tr slot-scope="{ index, item }">
                <td>{{ index }}</td>
                <td>{{ item.ubl_version }}</td>
                <td>{{ item.soap_type_description }}</td>
                <td>{{ item.document_type_code }}</td>
                <td>{{ item.document_number }}</td>
                <td>{{ item.date_of_issue }}</td>
                <td>{{ item.external_id }}</td>
                <td>{{ item.state_type_description }}</td>
                <td class="has-text-right">{{ item.created_at }}</td>
                <td class="has-text-right table-actions">
                    <template v-if="item.external_id">
                        <el-button plain @click.prevent="download(item.download_cdr)" v-if="item.state_type_id  !== '01'">CDR</el-button>
                        <el-button plain @click.prevent="download(item.download_pdf)">PDF</el-button>
                        <el-button plain @click.prevent="download(item.download_xml)">XML</el-button>
                    </template>
                    <template v-else>
                        <el-button plain @click.prevent="create(item.id)">XML</el-button>
                    </template>
                </td>
                <td class="has-text-right table-actions">
                    <el-button icon="el-icon-upload2" :loading="loading" plain
                               @click.prevent="send(item.external_id)"
                               v-if="item.send_xml"></el-button>
                </td>
            </tr>
        </my-filterable>
    </div>
</template>

<script>

    import {post} from '../../helpers/functions'

    export default {
        data () {
            return {
                loading: false,
                resource: 'documents',
                showDialog: false,
                recordId: null,
                records: [],
            }
        },
        methods: {
            send(external_id, index) {
                this.loading = true
                post('/send_xml', {
                    'external_id': external_id
                })
                    .then((response) => {
                        if (response['success']) {
                            this.$eventHub.$emit('reloadData')
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
