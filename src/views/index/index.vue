<template>
  <div class="index-container">
    <component v-bind:is="currentRole"> </component>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import UserIndex from './user/index';
  import VisitorIndex from './visitor/index';
  export default {
    name: 'index',
    components: { UserIndex, VisitorIndex },
    data() {
      return {
        currentRole: 'VisitorIndex'
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ])
    },
    created() {
      document.title = '午安网 - 过你想过的生活'
      /*if (this.access_token != null) {*/
      if(this.user.token != ''){
        this.currentRole = 'UserIndex';
      }
    }
  }
</script>

