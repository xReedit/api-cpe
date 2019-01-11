<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form class="columns is-multiline" @submit.prevent="_submit" autocomplete="off">
            <div class="column is-6">
                <my-control :label="$t('fields.identity_document_type_code')" id="identity_document_type_code" :error="errors.identity_document_type_code">
                    <el-select v-model="form.identity_document_type_code">
                        <el-option v-for="option in identity_document_types" :key="option.code" :value="option.code" :label="option.description"></el-option>
                    </el-select>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.number')" control-id="number" :error="errors.number">
                    <el-input v-model="form.number" id="number" :maxlength="11">
                        <template v-if="form.identity_document_type_code === '6'">
                            <el-button type="primary" slot="append" :loading="loading_search_number" icon="el-icon-search" @click.prevent="searchNumber"></el-button>
                        </template>
                    </el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.name')" id="name" :error="errors.name">
                    <el-input v-model="form.name" id="name"></el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.trade_name')" id="trade_name" :error="errors.trade_name">
                    <el-input v-model="form.trade_name" id="trade_name"></el-input>
                </my-control>
            </div>
            <div class="column is-4">
                <my-control :label="$t('fields.country_code')" id="country_code" :error="errors.country_code">
                    <el-select v-model="form.country_code" filterable id="country_id">
                        <el-option v-for="option in countries" :key="option.code" :value="option.code" :label="option.description"></el-option>
                    </el-select>
                </my-control>
            </div>
            <div class="column is-4">
                <my-control :label="$t('fields.department_code')" id="department_code" :error="errors.department_code">
                    <el-select v-model="form.department_code" filterable @change="filterProvince">
                        <el-option v-for="option in departments" :key="option.code" :value="option.code" :label="option.description"></el-option>
                    </el-select>
                </my-control>
            </div>
            <div class="column is-4">
                <my-control :label="$t('fields.province_code')" id="province_code" :error="errors.province_code">
                    <el-select v-model="form.province_code" filterable @change="filterDistrict">
                        <el-option v-for="option in provinces" :key="option.code" :value="option.code" :label="option.description"></el-option>
                    </el-select>
                </my-control>
            </div>
            <div class="column is-4">
                <my-control :label="$t('fields.district_code')" id="district_code" :error="errors.district_code">
                    <el-select v-model="form.district_code" filterable>
                        <el-option v-for="option in districts" :key="option.code" :value="option.code" :label="option.description"></el-option>
                    </el-select>
                </my-control>
            </div>
            <div class="column is-8">
                <my-control :label="$t('fields.address')" :error="errors.address">
                    <el-input v-model="form.address" id="address"></el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.phone')" :error="errors.phone">
                    <el-input v-model="form.phone" id="phone"></el-input>
                </my-control>
            </div>
            <div class="column is-6">
                <my-control :label="$t('fields.email')" :error="errors.email">
                    <el-input v-model="form.email" id="email"></el-input>
                </my-control>
            </div>
            <div class="column is-12 has-text-right">
                <el-button type="default" @click.prevent="close">{{ $t('buttons.cancel') }}</el-button>
                <el-button type="primary" :loading="submit_loading" native-type="submit">{{ button_submit_text }}</el-button>
            </div>
        </form>
    </el-dialog>
</template>
<script>

    import {fetch, ruc} from '../../helpers/functions'
    import {formable} from '../../mixins/formable'

    export default {
        mixins: [formable, ruc],
        props: ['showDialog', 'recordId'],
        data() {
            return {
                titleDialog: this.$t('customers.titles.new'),
                resource: 'customers',
                errors: {},
                form: {},
                countries: [],
                departments: [],
                all_provinces: [],
                all_districts: [],
                provinces: [],
                districts: [],
                identity_document_types: [],
            }
        },
        created() {
            this.initForm()
            fetch(`/${this.resource}/tables`).then(response => {
                this.countries = response.countries
                this.departments = response.departments
                this.all_provinces = response.provinces
                this.all_districts = response.districts
                this.identity_document_types = response.identity_document_types
            })
        },
        methods: {
            initForm() {
                this.errors = {}
                this.form = {
                    id: null,
                    identity_document_type_code: null,
                    number: null,
                    name: null,
                    trade_name: null,
                    country_code: 'PE',
                    department_code: null,
                    province_code: null,
                    district_code: null,
                    address: null,
                    phone: null,
                    email: null,
                }
            },
            close() {
                this.initForm()
                this.$emit('update:showDialog', false)
            },
            async create() {
                if (this.recordId) {
                    this.titleDialog = this.$t('customers.titles.edit')
                    await this._create()
                    this.filterProvinces()
                    this.filterDistricts()
                }
            },
            filterProvince() {
                this.form.province_code = null
                this.form.district_code = null
                this.filterProvinces()
            },
            filterProvinces() {
                this.provinces = this.all_provinces.filter(f => {
                    return f.department_code === this.form.department_code
                })
            },
            filterDistrict() {
                this.form.district_id = null
                this.filterDistricts()
            },
            filterDistricts() {
                this.districts = this.all_districts.filter(f => {
                    return f.province_code === this.form.province_code
                })
            },
            async searchNumber() {
                await this._searchNumber()
                this.filterProvinces()
                this.filterDistricts()
            }
        }
    }
</script>
