<template>
  <div class="user-card wl-card">
      <img :src="user.avatar_url === 'default_url' ? 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/64/h/64' : `${user.avatar_url}?imageView2/1/w/64/h/64`">
      <h2 @click="$router.push({path: `/myspace/${user.id}`})">{{user.name}}</h2>
      <p>发表了{{user.articles_num}}篇文章</p>
  </div>
</template>

<script>
export default {
  name: 'userCard',
  data () {
    return {}
  },
  props: {
    user: {
      type: Object,
      required: true
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss"  scoped>
.user-card {
  width: 264px;
  height: 229px;
  background-color: #ffffff;
  border-radius: 4px;
  text-align: center;
  padding: 34px 10px 58px 10px;
  img {
    width: 64px;
    height: 64px;
    background-color: #efeff4;
  }
  h2 {
    font-size: 17px;
    color: #333333;
    cursor: pointer;
    margin: 24px 0 18px 0;
    width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    &:hover {
      color: $wl-blue-hover;
    }
  }
  p {
    font-size: 13px;
    color: #666666;
  }
}
</style>
