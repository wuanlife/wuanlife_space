<template>
  <div class="index-aside-card wuan-card clickable" @click="goUserSpace">
    <!--@click="user.token=='' ? $router.push({path: '/login/'}) : $router.push({path: '/mySpace/${activeUser.id}'})">-->
    <img :src="activeUser.avatar_url">
    <div class="wuan-card__content">
      <h2 class="clickable">{{ activeUser.name }}</h2>
      <p>本月发表了{{activeUser.monthly_articles_num}}</p>
    </div>
  </div>
</template>

<script>
  //不加大括号会出错！
  import { mapGetters } from 'vuex'

  export default {
    name: 'aside-card',
    data() {
      return {}
    },
    props: {
      activeUser: {
        type: Object,
        required: true
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ]),
    },
    methods: {
      goUserSpace() {
        if(this.user.token == '') {
          this.$router.push({
            path: '/login/'
          })
        } else {
          this.$router.push({
            path: `/mySpace/${this.activeUser.id}`,
            query: {
              id: this.activeUser.id,
              name: this.activeUser.name,
              avatar_url: this.activeUser.avatar_url
            }
          })
        }
      }
    }
  }
</script>

<style scoped>
  .index-aside-card {
    width: 250px;
    height: 70px;
  }
</style>