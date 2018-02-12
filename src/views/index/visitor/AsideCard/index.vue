<template>
  <div class="index-aside-card index-card clickable" @click="goUserSpace">
    <!--@click="user.token=='' ? $router.push({path: '/login/'}) : $router.push({path: '/mySpace/${activeUser.id}'})">-->
    <img :src="`${activeUser.avatar_url}?imageView2/1/w/40/h/40` || defaultAvatar">
    <div class="index-card content">
      <h2 class="clickable">{{ activeUser.name }}</h2>
      <p>本月发表了 {{activeUser.monthly_articles_num}} 篇</p>
    </div>
  </div>
</template>

<script>
// 不加大括号会出错！
import { mapGetters } from 'vuex'
import defaultAvatar from '@/assets/wuanlife_256.jpg'

export default {
  name: 'aside-card',
  data () {
    return {
      defaultAvatar
    }
  },
  props: {
    activeUser: {
      type: Object,
      required: true
    }
  },
  computed: {
    ...mapGetters([
      'user'
    ])
  },
  methods: {
    goUserSpace () {
      if (this.user.token === '') {
        this.$router.push({
          path: '/login/'
        })
      } else {
        this.$router.push({
          path: `/mySpace/${this.activeUser.id}`
        })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
  .index-aside-card {
    /*position: fixed;*/
    width: 250px;
    height: 70px;
  }
  .index-card {
    display: flex;
    align-items: center;
    background-color: #fff;
    padding: 10px;
    img {
      margin: 10px 8px 10px 0;
      width: 40px;
      height: 40px;
      //添加圆角
      border-radius: 20px;
    }
    .content {
      min-width: 0;
      flex-direction: column;
      align-items: flex-start;//行内浮动
      h2 {
        margin: 2px 0;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        font-size:14px;
        color:#5992e4;
        text-align:left;
      }
      p {
        display: block;
        margin: 0;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        font-size:14px;
        color:#666666;
        text-align:left;
      }
    }
  }
</style>
