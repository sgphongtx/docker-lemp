import Vue from 'vue'
import Router from 'vue-router'
import store from './store'
import Login from './components/Login'
import Building from './components/Building'
import Apartment from './components/Apartment'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  linkExactActiveClass: 'cur',
  routes: [
    {
      path: '/',
      name: 'Dashboard',
      component: Building
    },
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/can-ho',
      name: 'can-ho',
      component: Apartment
    }
  ]
})

router.beforeEach((to, from, next) => {
  console.log(store.state.authenticated, to, store.state.user)
  if (to.name !== 'login') {
    if (!store.state.authenticated) {
      return next({name: 'login'})
    }
  } else {
    store.commit('setAuthenticated', false)
  }
  next()
})

export default router
