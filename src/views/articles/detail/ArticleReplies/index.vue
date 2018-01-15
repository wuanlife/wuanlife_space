<template>
<div v-if="replies" class="article-replies">
  <header>
    {{`${pagination.total} 个回复`}}
  </header>
  <div class="replies-container">
    <article-reply v-for="reply in replies" 
                   :key="reply.id"
                   :reply="reply">
    </article-reply>
  </div>
  <div class="reply-pagination">
    <el-pagination
      small
      layout="prev, pager, next"
      :total="pagination.total"
      :page-size="20"
       @current-change="handleCurrentChange"
      :current-page.sync="pagination.current"
      
    >
    </el-pagination>
  </div>
</div>
<div v-else class="article-replies" v-loading="true">
  <header>
    正在加载评论...
  </header>
  <div class="replies-container">
  </div>
</div>
</template>

<script>
import { mapGetters } from "vuex";
import { getRepliesById } from "api/reply";
import ArticleReply from "./ArticleReply";

export default {
  name: "article-replies",
  components: {
    ArticleReply
  },
  data() {
    return {
      replies: null,
      loading: false,
      pagination: {
        current: 1,
        total: 0
      },
    };
  },
  computed: {
    ...mapGetters(["user"])
  },
  created() {
  },
  mounted() {
    const self = this;
    const articleId = this.$route.params.id
    this.loading = true;
    getRepliesById(articleId, this.pagination).then((res) => {
      this.loading = false;
      this.replies = res.reply;
      this.pagination.total = Number(res.total);
    })
  },
  methods: {
    handleCurrentChange(currentPage) {
      // scrollIntoView让第一个元素处于可见范围
      console.log(currentPage)

    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.article-replies {
  border-radius: 4px;
  border: solid 2px #c8c8c8;
  header {
    padding: 12px 16px;
    border-bottom: 2px solid #c8c8c8;
    font-size: 20px;
    color: #5677fc;
  }
}
</style>
