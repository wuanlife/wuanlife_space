<template>
  <div class="article-state" :style="{background: color}">
    {{ text | undefined }}
  </div>
</template>

<script>
  export default {
    name: 'article-state',
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
  .article-state {
    display: inline-block;
    border-radius: 4px;
    vertical-align: middle;
    font-size: 14px;
    padding: 6px 8px;
    margin-left: 4px;
    color: #fff;
  }
</style>
