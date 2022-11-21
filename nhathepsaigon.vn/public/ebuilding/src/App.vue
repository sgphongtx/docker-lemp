<template>
  <div v-if="$store.state.authenticated" class="container body">
    <div v-if="$store.state.building" class="main_container">
      <div class="col-md-3 left_col">
        <div class="left_col scroll-view">
          <nav-bar></nav-bar>
          <div class="clearfix"></div>
          <profile></profile>
          <br/>
          <side-bar-menu></side-bar-menu>
        </div>
      </div>
      <top-nav></top-nav>
      <div class="right_col" role="main">
        <router-view></router-view>
      </div>
      <footer>
        <div class="pull-right">
          ©2019 All Rights Reserved
        </div>
        <div class="clearfix"></div>
      </footer>
    </div>
    <div v-else class="main_container">
      <top-nav-dashboard></top-nav-dashboard>
      <building></building>
      <footer class="dashboard_nav">
        <div class="pull-right">
          ©2019 All Rights Reserved
        </div>
        <div class="clearfix"></div>
      </footer>
    </div>
  </div>
  <div v-else>
    <Login></Login>
  </div>
</template>

<script>
  import Login from './components/Login'
  import SideBarMenu from './components/main/SideBarMenu'
  import NavBar from './components/main/NavBar'
  import Profile from './components/main/Profile'
  import TopNav from './components/main/TopNav'
  import TopNavDashboard from './components/main/TopNavDashboard'
  import Building from './components/Building'

  export default {
    name: 'home',
    components: {
      TopNavDashboard,
      Login,
      SideBarMenu,
      NavBar,
      Profile,
      TopNav,
      Building
    },
    data() {
      return {
        user: {},
        countDown: 60
      }
    },
    mounted() {
      console.log(this.$store.state.user)
      //this.$store.commit('setAuthenticated', false)
    },
    computed: {
      isChooseBuilding: function () {
        console.log('computed isChooseBuilding', new Date())
        if (this.$store.state.user && this.$store.state.user.building) return true
        return false
      }
    },
    methods: {}
  }
</script>
<style scoped>
</style>
