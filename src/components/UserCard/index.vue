<template>
  <div class="user-card">
      <img :src="user.avatar_url">
      <h2>{{user.name}}</h2>
      <p>发表了{{user.total}}篇文章</p>
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
.user-card{
  width: 420px;
  height: 366px;
	background-color: #ffffff;
  border-radius: 4px;
  text-align: center;
  padding: 55px 10px 93px 10px;
  margin-top: 71px; 
  img{
    width: 98px;
    height: 98px;
  }
  h2{
    font-size: 28px;
    color: #333333;
    margin: 35px 0 28px 0;
    width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  p{
    font-size: 22px;
    color: #666666;
  }
}
</style>
