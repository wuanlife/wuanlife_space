<template>
  <div class="user-card">
    <img v-bind:src="user.image || defaultAvatar" />
    <h3 @click="$router.push({ path: `/planet/${user.id}`, query: { name: user.name }})">{{ user.name }}</h3>
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
  width: 150px;
  height: 50px;
  margin-top: 30px;
  background-color: white;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  position: relative;
  /*justify-content: center;*/
  * {
    display: inline-block;
  }
  img {
    width: 57px;
    height: 57px;
    border-radius: 100%;
    /*margin: 5px 10px;*/
    top: -28.5px;
    left: 5px;
    position: absolute;
  }
  h3 {
    font-size: 11px;
    margin-right: 5px;
    @include text-ellipsis(80px);
    overflow: hidden;
  }
}
</style>
