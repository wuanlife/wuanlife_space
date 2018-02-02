<template>
    <!-- Search bar -->
    <div class="search-container">
        <el-input
            class="search"
            placeholder="过你想过的生活..."
            icon="search"
            v-model="searchContent"
            @keyup.enter.native="handleGoSearch">
        </el-input>
    </div>
</template>

<script>
export default {
  data () {
    return {
      searchContent: ''
    }
  },
  methods: {
    handleGoSearch () {
      if (this.searchContent !== '') {
        this.$router.push({
          path: '/search',
          query: { search: this.searchContent }
        })
      } else {
        alert('请输入要搜索的内容')
      }
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.search-container {
  flex: 0 0 600px;
  min-width: 300px;
  @media screen and (max-width: 900px) {
    flex: 0.7;
  }
  .search /deep/ input {
    height: 58px;
    padding: 18px 16px;
    font-size: 22px;
    color: #a9baff;
    &::placeholder {
      color: #a9baff;
    }
  }
}
</style>
