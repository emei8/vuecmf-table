import Vue from 'vue'
import App from './App.vue'
import 'babel-polyfill'

import VueSuperTable from './lib/vuecmf-table'

Vue.use(VueSuperTable)

new Vue({
  el: '#app',
  render: h => h(App)
})
