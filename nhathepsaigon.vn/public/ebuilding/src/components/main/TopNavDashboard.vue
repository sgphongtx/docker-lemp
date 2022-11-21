<template>
  <div class="top_nav dashboard_nav">
    <div class="nav_menu">
      <nav>
        <div class="dashboard_logo">
          <img src="/designer/images/logo.png" alt="An phu viet"/>
        </div>

        <ul class="nav navbar-nav navbar-right">
          <li class="">
            <a href="javascript:void(0)" v-on:click="logOut"><i class="fa fa-sign-out pull-right" style="margin-top: 9px"></i> Log Out</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
  import axios from './../../client'
  export default {
    name: 'TopNavDashboard',
    methods: {
      logOut(){
        axios.get(`/logout`)
        this.$store.commit('setAuthenticated', false)
        this.$store.commit('setBuilding', false)
        this.$store.commit('clearUser')
        this.$router.replace({name: 'login'})
      }
    }
  }
</script>

<style scoped>

</style>
