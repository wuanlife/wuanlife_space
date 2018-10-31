<template>
  <div class="index-aside-card index-card clickable" @click="goUserSpace">
    <!--@click="user.token=='' ? $router.push({path: '/login/'}) : $router.push({path: '/mySpace/${activeUser.id}'})">-->
    <div class="img-box">
      <img :src="`${activeUser.avatar_url}?imageView2/1/w/40/h/40` || defaultAvatar">
    </div>
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
    width: 262px;
    height: 70px;
    box-sizing: border-box;
    padding: 10px 12px 10px 23px;
  }
  .index-card {
    display: flex;
    align-items: stretch;
    background-color: #fff;
    .img-box {
      display: flex;
      align-items: flex-end;
      img {
        // margin: 10px 8px 10px 0;
        margin-right: 18px;
        width: 38px;
        height: 38px;
        //添加圆角
        border-radius: 100%;
      }
    }
    .content {
      min-width: 0;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: space-around;
      align-items: flex-start;
      border-bottom: 1px solid #ccc;
      h2 {
        margin: 2px 0;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        font-size:14px;
        color:#666;
        text-align:left;
        &:hover {
          color: #99ccff;
        }
      }
      p {
        display: block;
        margin: 0;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        font-size:12px;
        color:#999;
        text-align:left;
      }
    }
  }
</style>
