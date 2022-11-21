<template>
  <div>
    <a class="hiddenanchor" id="signup"></a>
    <a class="hiddenanchor" id="signin"></a>
    <div class="login_wrapper">
      <div class="animate form login_form">
        <section class="login_content">
          <form @submit.prevent="login">
            <img src="designer/images/logo.png" class="logo">
            <div>
              <input type="text" class="form-control" ref="email" placeholder="Email" required="required"
                     v-model="email"/>
            </div>
            <div>
              <input type="password" class="form-control" ref="password" placeholder="Password" required="required"
                     v-model="password"/>
            </div>
            <div class="error" v-if="msg">{{msg}}</div>
            <div>
              <button class="btn btn-default submit" type="submit">Log in</button>
            </div>

            <div class="clearfix"></div>

            <div class="separator">
              <div>
                <p>©2019 All Rights Reserved</p>
              </div>
            </div>
          </form>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from './../client'

  export default {
    name: 'Login',
    data() {
      return {
        email: '',
        password: '',
        msg: '',
        isCalling: false
      }
    },
    mounted() {
      this.$refs.email.focus()
      this.toggleBodyClass('addClass', 'login')
      this.toggleBodyClass('removeClass', 'nav-md')
    },
    methods: {
      toggleBodyClass(addRemoveClass, className) {
        const el = document.body
        if (addRemoveClass === 'addClass') {
          el.classList.add(className)
        } else {
          el.classList.remove(className)
        }
      },
      login() {
        if (!this.isCalling) {
          axios.post(`/login`, {user: {email: this.email, password: this.password}}).then(response => {
            this.isCalling = false
            if (response.data.s === 400) {
              this.email = ''
              this.password = ''
              this.msg = response.data.msg
              return
            }
            this.$store.commit('setAuthenticated', true)
            this.$store.commit('setUser', response.data.data.user)
            this.$router.replace({name: 'Dashboard', query: {ft: true}})
          }).catch(err => {
            this.email = ''
            this.password = ''
            this.msg = err.message
          })
        }
      }
    }
  }
</script>

<style scoped>
  .error {
    text-align: left;
    color: red
  }
  .login_content .logo {
    max-width: 100%;
    margin-bottom: 20px;
  }
</style>
