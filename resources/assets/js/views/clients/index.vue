<template>
    <div class="page-wrapper">
        <my-filterable :title="$t('clients.titles.index')" :resource="resource" :filterable="false" :exportable="false">
            <el-button type="primary" @click.prevent="create()" slot="create">{{ $t('buttons.new') }}</el-button>
            <tr slot="heading">
                <th>#</th>
                <th>{{ $t('fields.name') }}</th>
                <th>{{ $t('fields.username') }}</th>
                <th>{{ $t('fields.email') }}</th>
                <th>{{ $t('fields.api_token') }}</th>
                <th>{{ $t('fields.company_name') }}</th>
                <th>{{ $t('fields.company_number') }}</th>
                <th class="has-text-right">{{ $t('fields.created_at') }}</th>
                <th class="has-text-right">{{ $t('labels.actions') }}</th>
            <tr>
            <tr slot-scope="{ index, item }">
                <td>{{ index }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.username }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.api_token }}</td>
                <td>{{ item.company_name }}</td>
                <td>{{ item.company_number }}</td>
                <td class="has-text-right">{{ item.created_at }}</td>
                <td class="has-text-right table-actions">
                    <el-button icon="el-icon-edit" plain @click.prevent="create(item.id)" ></el-button>
                    <el-button type="danger" icon="el-icon-delete" plain @click.prevent="_destroy(item.id)" ></el-button>
                </td>
            </tr>
        </my-filterable>
        <client-form :showDialog.sync="showDialog"
                    :recordId="recordId"></client-form>
    </div>
</template>

<script>

    import ClientForm from './form.vue'
    import {deletable} from "../../mixins/deletable"

    export default {
        mixins: [deletable],
        components: {ClientForm},
        data () {
            return {
                resource: 'clients',
                showDialog: false,
                recordId: null,
                records: [],
            }
        },
        methods: {
            create(recordId = null) {
                this.recordId = recordId
                this.showDialog = true
            },
            destroy(id) {
                this._destroy(id).then(() => this.$eventHub.$emit('reloadData'))
            },
        }
    }
</script>