<!-- 
  OPTIMIZATION:
    1. the aside should be seperated
 -->

<template>
  <div class="group-container">
    <component v-if="group" v-bind:is="currentRole" :group.sync="group"> </component>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getGroup } from 'api/group';
  import PrivateGroup from './private';
  import PublicGroup from './public';

  export default {
    name: 'group',
    components: { PrivateGroup, PublicGroup },
    data() {
      return {
        currentRole: 'PublicGroup',
        groupid: null,
        group: null,
      }
    },
    computed: {
      ...mapGetters([
        'token',
        'userInfo',
      ])
    },
    created() {
      let name = this.$route.query.name;
      document.title= name + ' - 午安网 - 过你想过的生活';
    },
    mounted() {
      var self = this;
      this.groupid = this.$route.params.id;
      getGroup(this.groupid).then(res => {
        self.group = res;
        if(!self.group.private){
          this.currentRole = 'PublicGroup';
        } else {
          this.currentRole = 'PrivateGroup';
        }
      }).catch(error => {
      });
    }
  }
</script>

