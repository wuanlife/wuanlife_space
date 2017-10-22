<template>
  <div class="post-tag" :style="{background: color}">
    {{ text | undefined }}
  </div>
</template>

<script>
  export default {
    name: 'post-tag',
    props: {
      color: {
        type: String,
        required: true,
      },
      text: {
        type: String,
        required: true,
      }
    },
    data() {
      return {
        
      }
    },
    mounted() {
    },
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .post-tag {
    padding: 3px 4px;
    margin-left: 4px;
    color: #fff;
  }
</style>
