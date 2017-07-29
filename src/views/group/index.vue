<template>
  <div class="group-container">
    <component v-bind:is="currentRole"> </component>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import PrivateGroup from './private';
  import PublicGroup from './public';

  export default {
    name: 'group',
    components: { PrivateGroup, PublicGroup },
    data() {
      return {
        currentRole: 'PublicGroup'
      }
    },
    computed: {
      ...mapGetters([
        'token',
        'userInfo',
      ])
    },
    created() {
      /*if (this.access_token != null) {*/
      if(true){
        this.currentRole = 'PublicGroup';
      }
    }
  }
</script>

