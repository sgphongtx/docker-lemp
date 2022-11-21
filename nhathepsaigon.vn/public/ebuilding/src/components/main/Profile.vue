<template>
  <div class="profile clearfix">
    <div class="profile_pic">
      <img v-bind:src="reversedAvatar" alt="..." class="img-circle profile_img">
    </div>
    <div class="profile_info">
      <span>Welcome,</span>
      <h2>{{$store.state.user.fullName}}</h2>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'Profile',
    computed: {
      reversedAvatar: function() {
        console.log('call avatar change', this.$store.state.user)
        if(this.$store.state.user && this.$store.state.user.avatar) return this.$store.state.user.avatar
        return '/designer/images/img.jpg'
      }
    }
  }
</script>

<style scoped>

</style>
