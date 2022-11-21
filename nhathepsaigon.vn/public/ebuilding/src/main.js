import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import * as uiv from 'uiv'

// import './registerServiceWorker'
Vue.use(uiv)
Vue.config.productionTip = false
Vue.prototype.$bus = new Vue({})
new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')