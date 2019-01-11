<template>
    <div>
        <div class="card">
            <div class="card-content">
                <div class="columns is-multiline">
                    <div class="column">
                        <h5 class="title is-5">{{title}}</h5>
                    </div>
                    <div class="column is-narrow">
                        <el-button icon="el-icon-download" @click.prevent="exportData" v-if="exportable && (collection.data.length > 0)">{{ $t('buttons.export') }}</el-button>
                        <slot name="create"></slot>
                    </div>
                    <div class="column is-12" v-if="filterable">
                        <div class="filter-row">
                            <el-button type="success" @click.prevent="addFilter" ><i class="fa fa-filter"></i> {{ $t('buttons.add_filter') }}</el-button>
                            <div class="filter-group" v-for="(f, i) in filterCandidates">
                                <div class="filter-success" v-if="f.success">
                                    <el-tag type="info" closable @close="removeFilter(f, i)">
                                        <template v-if="f.query_2">
                                            {{ $t(`fields.${f.column}`) }} <span>{{ $t(`operations.${f.operator}`) }}</span> {{ f.query_1 }} {{ $t(`labels.to`) }} {{ f.query_2 }}
                                        </template>
                                        <template v-else>
                                            {{ $t(`fields.${f.column}`) }} <span>{{ $t(`operations.${f.operator}`) }}</span> {{ f.query_1 }}
                                        </template>
                                    </el-tag>
                                </div>
                                <div class="filter-controls" v-else>
                                    <div>
                                        <el-select v-model="f.column">
                                            <el-option v-for="x in response.filters" :key="x.name" :value="x.name" :label="$t(`fields.${x.name}`)"></el-option>
                                        </el-select>
                                        <small class="error-control" v-if="errors[`f.${i}.column`]">
                                            {{errors[`f.${i}.column`][0]}}
                                        </small>
                                    </div>
                                    <div v-if="f.column">
                                        <el-select v-model="f.operator">
                                            <el-option v-for="x in fetchOperators(f)" :key="x.name" :value="x.name" :label="$t('operations.'+x.name)"></el-option>
                                        </el-select>
                                        <small class="error-control" v-if="errors[`f.${i}.operator`]">
                                            {{errors[`f.${i}.operator`][0]}}
                                        </small>
                                    </div>
                                    <template v-if="f.column && f.operator">
                                        <div v-if="getOperatorComponent(f.operator) === 'single'">
                                            <el-input v-model="f.query_1"></el-input>
                                            <small class="error-control" v-if="errors[`f.${i}.query_1`]">
                                                {{errors[`f.${i}.query_1`][0]}}
                                            </small>
                                        </div>
                                        <div v-if="getOperatorComponent(f.operator) === 'datetime_3'">
                                            <el-date-picker v-model="f.query_1" type="date" value-format="yyyy-MM-dd"></el-date-picker>
                                            <small class="error-control" v-if="errors[`f.${i}.query_1`]">
                                                {{errors[`f.${i}.query_1`][0]}}
                                            </small>
                                        </div>
                                        <template v-if="getOperatorComponent(f.operator) === 'datetime_5'">
                                            <div class="filters-query_1">
                                                <el-date-picker v-model="f.query_1" type="date" value-format="yyyy-MM-dd"></el-date-picker>
                                                <small class="error-control" v-if="errors[`f.${i}.query_1`]">
                                                    {{errors[`f.${i}.query_1`][0]}}
                                                </small>
                                            </div>
                                            <div class="filters-query_2">
                                                <el-date-picker v-model="f.query_2" type="date" value-format="yyyy-MM-dd"></el-date-picker>
                                                <small class="error-control" v-if="errors[`f.${i}.query_2`]">
                                                    {{errors[`f.${i}.query_2`][0]}}
                                                </small>
                                            </div>
                                        </template>
                                        <el-button type="success" icon="el-icon-check" @click.prevent="applyFilterLine(i)"></el-button>
                                    </template>
                                    <div v-if="f">
                                        <a href="#" @click.prevent="removeFilter(f, i)"><i class="fa fa-times"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-12">
                        <table class="table is-hoverable is-narrow is-fullwidth">
                            <thead>
                            <slot name="heading"></slot>
                            </thead>
                            <tbody v-if="collection.data && collection.data.length">
                            <slot v-for="(item, index) in collection.data" :item="item" :index="customIndex(index)"></slot>
                            </tbody>
                            <tbody v-else>
                            <tr>
                                <td colspan="10" class="panel__no_results">
                                    <div>{{$t('labels.no_results_found')}}</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="column is-12">
                        <div class="column is-12 has-text-right">
                            <el-pagination
                                    @current-change="applyChange"
                                    :current-page.sync="query.page"
                                    :page-sizes="[5, 10, 20, 50]"
                                    :page-size="query.limit"
                                    layout="total, prev, pager, next"
                                    :total="total">
                            </el-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">

    import Vue from 'vue'
    import http from '../helpers/http'

    export default {
        props: {
            title: String,
            resource: String,
            filterable: {
                type: Boolean,
                default: true
            },
            exportable: {
                type: Boolean,
                default: true
            }
        },
        data () {
            return {
                loading: false,
                collection: {
                    data: []
                },
                errors: {},
                filterCandidates: [],
                appliedFilters: [],
                query: {
                    sort_column: 'created_at',
                    sort_direction: 'desc',
                    filter_match: 'and',
                    limit: 10,
                    page: 1
                },
                response: {
                    displayable: [],
                    filters: []
                },
                total: 0,
                records: [],
            }
        },
        created() {
            this.$eventHub.$on('reloadData', () => {
                this.getData()
            })
            this.getData()
        },
        mounted() {
//            this.addFilter()
        },
        computed: {
//            reloadData() {
//
//            },
//            filterGroups() {
//                const x = this.filterGroup.map((item) => {
//                    const i = item.filters.map((y) => {
//                        if(typeof y.title === 'undefined') {
//                            y.title = this.$t(y.name)
//                            return y
//                        }
//                        return y
//                    })
//                    item.filters = i
//                    return item
//                })
//
//                return x
//            },
//            getSortable() {
//                const x = this.sortable.map((item) => {
//                    if(typeof item === 'object') {
//                        return item
//                    }
//                    return {name: item, value: this.$t(item)}
//                })
//
//                return x
//            },
//            showFilterReset() {
//                return this.appliedFilters.length > 0
//            },
//            showFilterControls() {
//                if(this.filterCandidates.length === 0) return true
//                const active = this.filterCandidates.filter((f) => {
//                    if(f.column && f.column.name) {
//                        return f
//                    }
//                })
//
//                return active.length > 0
//            },
            fetchOperators() {
                return (f) => {
//                console.log(f)
                    return this.availableOperator().filter((operator) => {
                        let filter = _.find(this.response.filters, {name: f.column})
                        if(filter && operator.parent.includes(filter.type)) {
                            return operator
                        }
                    })
                }
            }
        },
        methods: {
            customIndex(index) {
                return (this.query.limit * (this.query.page - 1)) + index + 1
            },
            getData() {
                if (this.filterable) {
                    http.get(`${this.resource}/data`)
                        .then((response) => {
                            this.response.filters = response.data.filters
                            this.applyChange()
                        })
                } else {
                    this.applyChange()
                }
                // this.addFilter()
            },
            getOperatorComponent(operator_name) {
                let operator = _.find(this.availableOperator(), {name: operator_name})
                return operator.component
            },
//            fetchOperators() {
//
//            },
            resetFilter() {
                Vue.set(this.$data, 'appliedFilters', [])
                this.filterCandidates.splice(0)
                this.addFilter()
                this.query.page = 1
                this.applyChange()
            },
            applyFilterLine(index) {
                this.filterCandidates[index].success = true
                this.applyChange()
            },
            applyFilter() {
//                this.appliedFilters
//                Vue.set(this.$data, 'appliedFilters',
//                    JSON.parse(JSON.stringify(this.filterCandidates))
//                )
                this.query.page = 1
                this.applyChange()
            },
            addFilter() {
                this.filterCandidates.push({
                    column: '',
                    operator: '',
                    query_1: null,
                    query_2: null,
                    placeholder: '',
                    success: false,
                })
            },
            removeFilter(f, i) {
                this.filterCandidates.splice(i, 1)
                this.applyChange()
            },
            labelApplyFilterLine(index) {
                let filter = this.filterCandidates[index]
                return `${filter.column} <span>${filter.operator}</span> ${filter.query_1} |`
            },
            onColumnSelect(i) {
                const value = event.target.value
                if(value.length === 0) {
                    Vue.set(this.filterCandidates[i], 'column', value)
                    return
                }
                const obj = JSON.parse(value)
                Vue.set(this.filterCandidates[i], 'column', obj)

                switch(obj.type) {
                    case 'numeric':
                        this.filterCandidates[i].operator = this.availableOperator()[6]
                        this.filterCandidates[i].query_1 = null
                        this.filterCandidates[i].query_2 = null
                        break;
                    case 'lookup':
                    case 'lookup_only':
                        this.filterCandidates[i].operator = this.availableOperator()[11]
                        this.filterCandidates[i].query_1 = []
                        this.filterCandidates[i].query_2 = null
                        break;

                    case 'static_lookup':
                        this.filterCandidates[i].operator = this.availableOperator()[21]
                        this.filterCandidates[i].query_1 = []
                        this.filterCandidates[i].query_2 = null
                        break;
                    case 'string':
                        this.filterCandidates[i].operator = this.availableOperator()[8]
                        this.filterCandidates[i].query_1 = []
                        this.filterCandidates[i].query_2 = null
                        break;
                    case 'datetime':
                        this.filterCandidates[i].operator = this.availableOperator()[13]
                        this.filterCandidates[i].query_1 = 28
                        this.filterCandidates[i].query_2 = 'days'
                        break;
                }
            },
            onOperatorSelect(f, i, e) {
                // console.log('operator change')
                const value = event.target.value
                if(value.length === 0) {
                    Vue.set(this.filterCandidates[i], 'operator', value)
                    return
                }

                const obj = JSON.parse(value)
                Vue.set(this.filterCandidates[i], 'operator', obj)

                this.filterCandidates[i].query_1 = null
                this.filterCandidates[i].query_2 = null
                // set default values for query_1 and q2
                switch(obj.name) {
                    case 'includes':
                    case 'not_includes':
                        this.filterCandidates[i].query_1 = []
                        this.filterCandidates[i].query_2 = null
                        break;
                    case 'in_the_past':
                    case 'in_the_next':
                    case 'over':
                        this.filterCandidates[i].query_1 = 28
                        this.filterCandidates[i].query_2 = 'days'
                        break;
                    case 'in_the_peroid':
                        this.filterCandidates[i].query_1 = 'today'
                        break;
                }
            },
            exportToFile() {
                //
            },
            onColumnSort(event) {
                this.query.sort_column = event.target.value
                this.query.sort_direction = 'asc'
                this.applyChange()
            },
            onDirectionSort() {
                // hack
                if(this.loading) return

                if(this.query.sort_direction === 'desc') {
                    this.query.sort_direction = 'asc'
                } else {
                    this.query.sort_direction = 'desc'
                }
                this.applyChange()
            },
            onLimitChange(event) {
                this.query.limit = event.target.value
                this.query.page = 1
                this.applyChange()
            },
            nextPage () {
                if(this.collection.next_page_url) {
                    this.query.page = this.query.page + 1
                    this.applyChange()
                }
            },
            prevPage() {
                if(this.collection.prev_page_url) {
                    this.query.page = this.query.page - 1
                    this.applyChange()
                }
            },
            applyChange() {
//                this.loading = true
                this.errors = {}
                const f = {}
                this.filterCandidates.forEach((filter, i) => {
                    f[`f[${i}][column]`] = filter.column
                    f[`f[${i}][operator]`] = filter.operator
                    if(filter.query_1) {
                        f[`f[${i}][query_1]`] = Array.isArray(filter.query_1)
                            ? filter.query_1.join(',,') // will cause issue
                            : filter.query_1
                    }
                    f[`f[${i}][query_2]`] = filter.query_2
                })

                let params = {
                    ...this.query,
                    ...f
                }

                http.get(`${this.resource}/records` , { params: params })
                    .then(response => {
                        this.collection.data = response.data.data
                        this.query.page = response.data.meta.current_page
                        this.query.limit = parseInt(response.data.meta.per_page)
                        this.total = response.data.meta.total
                    })
                    .catch(error => {
                        if(error.response.status === 422) {
                            this.errors = error.response.data.errors
                        }
                    })
            },
            exportData() {
                const f = {}
                this.filterCandidates.forEach((filter, i) => {
                    f[`f[${i}][column]`] = filter.column
                    f[`f[${i}][operator]`] = filter.operator
                    if(filter.query_1) {
                        f[`f[${i}][query_1]`] = Array.isArray(filter.query_1)
                            ? filter.query_1.join(',,') // will cause issue
                            : filter.query_1
                    }
                    f[`f[${i}][query_2]`] = filter.query_2
                })

                let params = {
                    ...this.query,
                    ...f
                }

//                let params = this.getParams()

                let q = Object.keys(params).map(function(k) {
                    return encodeURIComponent(k) + "=" + encodeURIComponent(params[k]);
                }).join('&')

//                this.urlExport = `/api/exports/${this.resource}?${q}`
                window.open(`/${this.resource}/export?${q}`)

//                http.get(`${this.resource}/export` , { params: params })
//                    .then(response => {
//                        console.log(response)
////                        this.collection.data = response.data.data
////                        this.query.page = response.data.meta.current_page
////                        this.query.limit = parseInt(response.data.meta.per_page)
////                        this.total = response.data.meta.total
//                    })
//                    .catch(error => {
//                        console.log(error)
////                        if(error.response.status === 422) {
////                            this.errors = error.response.data.errors
////                        }
//                    })
            },
//            setData(res) {
//                Vue.set(this.$data, 'collection', res.data.collection)
//                this.query.page = this.collection.current_page
//                this.query.limit = this.collection.per_page
//                if(this.loading) this.loading = false
//            },
            availableOperator() {
                return [
                    {name: 'equal_to', parent: ['numeric', 'string'], component: 'single'},
//                    {name: 'not_equal_to', parent: ['numeric', 'string'], component: 'single'},
                    {name: 'less_than', parent: ['numeric'], component: 'single'},
                    {name: 'greater_than', parent: ['numeric'], component: 'single'},
                    {name: 'less_than_or_equal_to', parent: ['numeric'], component: 'single'},
                    {name: 'greater_than_or_equal_to', parent: ['numeric'], component: 'single'},
//                    {name: 'between', parent: ['numeric'], component: 'dual'},
//                    {name: 'not_between', parent: ['numeric'], component: 'dual'},
                    {name: 'contains', parent: ['string', 'lookup'], component: 'single'},
                    {name: 'starts_with', parent: ['string', 'lookup'], component: 'single'},
                    {name: 'ends_with', parent: ['string', 'lookup'], component: 'single'},
//                    {name: 'includes', parent: ['lookup', 'lookup_only'], component: 'lookup'},
//                    {name: 'not_includes', parent: ['lookup', 'lookup_only'], component: 'lookup'},
//                    {name: 'in_the_past', parent: ['datetime'], component: 'datetime_1'},
//                    {name: 'in_the_next', parent: ['datetime'], component: 'datetime_1'},
//                    {name: 'over', parent: ['datetime'], component: 'datetime_4'}, // same as in_the_past
                    {name: 'between_date', parent: ['datetime'], component: 'datetime_5'},
//                    {name: 'in_the_peroid', parent: ['datetime'], component: 'datetime_2'},
                    {name: 'equal_to_date', parent: ['datetime'], component: 'datetime_3'},
//                    {name: 'is_empty', parent: ['date', 'numeric', 'string', 'datetime', 'lookup'], component: 'none'},
//                    {name: 'is_not_empty', parent: ['date', 'numeric', 'string', 'datetime', 'lookup'], component: 'none'},
//                    {name: 'includes', parent: ['static_lookup'], component: 'static_lookup'},
//                    {name: 'not_includes', parent: ['static_lookup'], component: 'static_lookup'},
                ]
            }
        }
    }
</script>
