<template>
  <div class="default-pagination" v-show="(pagination.pageCount==0)||(pagination.pageCount==1) ? false : true">
    <div class="center">
      <span v-if="pagination.currentPage != 1">
      <span @click="pagination.currentPage--">上一页</span>
      </span>

      <span v-else>
      <span class="disabled">上一页</span>
      </span>

      <el-pagination layout="pager" @current-change="pageChange" :current-page.sync="pagination.currentPage" :page-count="pagination.pageCount">
      </el-pagination>

      <span v-if="pagination.currentPage != pagination.pageCount">
      <span @click="pagination.currentPage++">下一页</span>
      </span>

      <span v-else>
      <span class="disabled">下一页</span>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {}
  },
  props: ['pagination'],
  // watch: {
  //   //观察对象属性的变化时
  //   'pagination.currentPage': function(page) {
  //     console.log(132);
  //     this.$emit('loadPosts', page)
  //   }
  // },
  methods: {
    pageChange (p) {
      this.$emit('current-change', p)
    }
  }
}
</script>
<style>
.default-pagination {
  width: inherit;
  text-align: center;
  margin-bottom: 20px;
}

.center {
  /*添加flex使内容超出max-width后不会溢出*/
  /*display: flex;*/
  /*max-width: 450px;*/
  display: inline-block;
}

.center * {
  padding: 0;
  margin: 0;
  display: inline-block;
  /*middle和baseline无法搞定时,考虑用line-hight*/
  /*vertical-align: 80%;*/
  line-height: 13px;
}

.default-pagination span {
  cursor: pointer;
  /*-moz-user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;*/
  user-select: none;
  font-size: 12px;
}

.disabled {
  color: darkgray;
  cursor: auto;
}
</style>
