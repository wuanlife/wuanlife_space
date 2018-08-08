<template>
  <div class="user-card wl-card">
      <span @click="$router.push({path: `/myspace/${user.id}`})">
        <img :src="user.avatar_url === 'default_url' ? 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/64/h/64' : `${user.avatar_url}?imageView2/1/w/64/h/64`">
      </span>
      <h2 @click="$router.push({path: `/myspace/${user.id}`})">{{user.name}}</h2>
      <p>发表了<span>{{user.articles_num}}</span>篇文章</p>
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
  > span {
    padding: 4px;
    border: 1px solid #cccccc;
    border-radius: 100%;
    width: 71px;
    height: 71px;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    &:hover {
      border-color: #99ccff;
    }
  }
  img {
    width: 63px;
    height: 63px;
    background-color: #efeff4;
    border-radius: 100%;
  }
  h2 {
    font-size: 18px;
    color: #999999;
    cursor: pointer;
    margin: 19px 0 18px 0;
    width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    &:hover {
      color: $wl-blue-hover;
    }
  }
  p {
    font-size: 14px;
    color: #cccccc;
    span {
      color: #99ccff;
    }
  }
}
</style>
