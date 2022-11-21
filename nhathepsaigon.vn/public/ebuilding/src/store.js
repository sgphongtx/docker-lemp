import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'
import Cookies from 'js-cookie'
Vue.use(Vuex)

export default new Vuex.Store({
  plugins: [
    createPersistedState({
      storage: {
        getItem: key => Cookies.get(key),
        setItem: (key, value) => Cookies.set(key, value, {expires: 365}),
        removeItem: key => Cookies.remove(key)
      }
    })
  ],
  state: {
    authenticated: false,
    building: false,
    user: {},
    setting: {}
  },
  mutations: {
    setAuthenticated (state, payload) {
      state.authenticated = payload
    },
    setBuilding (state, payload){
      state.building = payload
    },
    setUser (state, payload) {
      if(payload.avatar) state.user.avatar = payload.avatar
      if(payload.email) {
        state.user.email = payload.email
        state.user.fullName = payload.fullName ? payload.fullName : payload.email
      }else if(state.user.fullName) state.user.fullName = payload.fullName

      if(payload._id) state.user.id = payload._id
      if(payload.role) state.user.role = payload.role
      state.user.building = payload.building
    },
    clearUser(state){
      state.user = {}
    }
  },
  actions: {}
})
