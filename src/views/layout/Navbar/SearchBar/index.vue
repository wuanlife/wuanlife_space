<template>
    <!-- Search bar -->
    <div class="search-container">
        <el-input
            class="search"
            placeholder="过你想过的生活..."
            icon="search"
            v-model="searchContent"
            @keyup.enter.native="handleGoSearch">
            <icon-svg @click.native="handleGoSearch" slot="suffix" id="icon-search" icon-class="search" class="el-input__icon"></icon-svg>
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
  @media screen and (max-width: 1000px) {
    flex: 0.8;
  }
  .search /deep/ input {
    height: 37px;
    padding: 10px 20px;
    font-size: 14px;
    color: #a9baff;
    &::placeholder {
      color: #a9baff;
    }
  }
  #icon-search {
    cursor: pointer;
    margin-right: 20px;
    font-size: 18px;
    color: #a9baff;
  }
}
</style>
