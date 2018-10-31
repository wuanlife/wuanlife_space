<template>
  <div class="user-card">
    <img v-bind:src="user.avatar_url || defaultAvatar" />
    <h3 @click="$router.push({ path: `/myspace/${user.id}`})">{{ user.name }}</h3>
  </div>
</template>

<script>
import defaultAvatar from '@/assets/wuanlife_256.jpg'

export default {
  data () {
    return {
      defaultAvatar
    }
  },
  props: {
    user: {
      type: Object,
      required: true
    }
  }
}
</script>

<style type="stylesheet/scss" lang="scss" scoped>
.user-card {
  width: 152px;
  height: 48px;
  box-sizing: border-box;
  padding-left: 24px;
  border-radius: 24px 0 0 24px;
  margin-top: 30px;
  background-color: white;
  display: flex;
  align-items: center;
  position: relative;
  /*justify-content: center;*/
  * {
    display: inline-block;
  }
  img {
    width: 48px;
    height: 48px;
    border-radius: 100%;
    margin-left: -24px;
    /*margin: 5px 10px;*/
    // top: -28.5px;
    // left: 5px;
    // position: absolute;
  }
  h3 {
    font-size: 16px;
    @include text-ellipsis(104px);
    overflow: hidden;
    cursor: pointer;
    box-sizing: border-box;
    padding: 0 14px;
    flex: 1;
    text-align: center;
    color: #666;
    &:hover {
      color: #99ccff;
    }
    &:focus {
      color: #6666ff;
    }
  }
}
</style>
