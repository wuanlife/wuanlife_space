<template>
  <div class="index-container">
    <component v-bind:is="currentRole"> </component>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import VisitorIndex from './visitor/index'
export default {
  name: 'index',
  components: { VisitorIndex },
  data () {
    return {
      currentRole: 'VisitorIndex'
    }
  },
  computed: {
    ...mapGetters([
      'user'
    ])
  },
  created () {
    document.title = '午安网 - 过你想过的生活'
    /* if (this.access_token != null) { */
    if (this.user.token !== '') {
      // 总是使用visitor视图，登录后只不过顶部栏改变了
      // this.currentRole = 'UserIndex';
    }
  }
}
</script>
