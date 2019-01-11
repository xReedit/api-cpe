import Vue from 'vue'

Vue.component('my-control', require('./components/Control'));
Vue.component('my-filterable', require('./components/Filterable.vue'));

Vue.component('auth-login', require('./views/auth/login.vue'));
Vue.component('clients-index', require('./views/clients/index.vue'));
Vue.component('documents-index', require('./views/documents/index.vue'));
Vue.component('summaries-index', require('./views/summaries/index.vue'));
Vue.component('summaries_annulment-index', require('./views/summaries_annulment/index.vue'));
Vue.component('annulments-index', require('./views/annulments/index.vue'));