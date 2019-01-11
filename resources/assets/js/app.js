require('./bootstrap')

import Vue from "vue"
import ElementUI from 'element-ui'

Vue.prototype.$t = (key) => {
    return window.i18n[key] || `[Locale '${key}' not found]`
}

import lang from 'element-ui/lib/locale/lang/es'
import locale from 'element-ui/lib/locale'
locale.use(lang)

Vue.use(ElementUI, {size: 'small'})

Vue.prototype.$eventHub = new Vue()

require('./register')

const app = new Vue({
    el: "#app",
});