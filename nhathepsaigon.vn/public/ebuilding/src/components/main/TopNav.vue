<template>
  <div class="top_nav">
    <div class="nav_menu">
      <nav>
        <div class="nav toggle">
          <a id="menu_toggle" v-on:click="toggleMenu"><i class="fa fa-bars"></i></a>
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
    name: 'TopNav',
    methods: {
      toggleMenu() {
        const el = document.body;
        if(el.classList[0] === 'nav-md'){
          el.classList.add('nav-sm')
          el.classList.remove('nav-md')
        } else {
          el.classList.add('nav-md')
          el.classList.remove('nav-sm')
        }
      },
      logOut(){
        axios.get(`/logout`)
        this.$store.commit('setAuthenticated', false)
        this.$store.commit('clearUser')
        this.$router.replace({name: 'login'})
      }
    }
  }
</script>

<style scoped>

</style>
