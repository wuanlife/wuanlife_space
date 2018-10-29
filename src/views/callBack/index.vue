<template>
  <div class="callback-page">
    请求用户信息中
  </div>
</template>

<script>
import storage from '../../utils/localStorage.js'
export default {
  data () {
    return {}
  },
  created () {
    const { access_token: accessToken, id_token: idToken } = this.$route.query
    const user = JSON.parse(atob(idToken.split('.')[1]))
    // this.$cookie.set(`wuan-id-token`, idToken, 7)
    // this.$cookie.set(`wuan-access-token`, accessToken, 7)
    console.log(user)
    storage.setItem('user', {
      accessToken,
      idToken,
      ...user
    })
    this.$store.commit('SET_USER', {
      accessToken,
      idToken,
      ...user
    })
    this.$router.push({path: '/'})
  }
}
</script>

<style scoped>
.callback-page {
  text-align: center;
}
</style>
