<template>
<div v-if="replies">
  <div class="article-replies">
    <header ref="reply-header">
      {{`${pagination.total} 个回复`}}
    </header>
    <div class="replies-container">
      <article-reply v-for="reply in replies"
                    @delete-success="handleDeleteSuccess"
                    ref='reply'
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
  <article-reply-input @reply-success="handleReplySuccess"></article-reply-input>
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
import { mapGetters } from 'vuex'
import { getRepliesByArticleId } from 'api/reply'
import { Notification } from 'element-ui'
import ArticleReply from './ArticleReply'
import ArticleReplyInput from './ArticleReplyInput'

export default {
  name: 'article-replies',
  components: {
    ArticleReply,
    ArticleReplyInput
  },
  data () {
    return {
      replies: null,
      loading: false,
      pagination: {
        current: 1,
        offset: 0,
        limit: 20,
        total: 0
      }
    }
  },
  computed: {
    ...mapGetters(['user'])
  },
  created () {
  },
  mounted () {
    const articleId = this.$route.params.id
    this.loading = true
    getRepliesByArticleId(articleId, this.pagination).then((res) => {
      this.loading = false
      this.replies = res.reply
      this.pagination.total = Number(res.total)
    })
  },
  methods: {
    async handleCurrentChange (currentPage) {
      this.loading = true
      const res = await getRepliesByArticleId(this.$route.params.id, {
        limit: this.pagination.limit,
        offset: (currentPage - 1) * this.pagination.limit
      })
      this.loading = false
      this.replies = res.reply

      // 翻页后评论第一个处于可见范围内
      this.$nextTick(() => {
        this.$refs['reply-header'].scrollIntoView()
      })
    },
    handleDeleteSuccess (reply) {
      this.replies = this.replies.filter((item) => {
        if (item === reply) {
          return false
        }
        return true
      })
      this.pagination.total--
      Notification.info({
        message: '删除评论成功',
        offset: 60
      })
    },
    handleReplySuccess (reply) {
      this.replies.unshift({
        ...reply,
        new: true
      })
      this.pagination.total++
      this.$nextTick(() => {
        this.$refs['reply-header'].scrollIntoView()
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.article-replies {
  margin-bottom: 28px;
  border-radius: 4px;
  border: solid 2px #c8c8c8;
  header {
    padding: 10px 8px;
    border-bottom: 2px solid #c8c8c8;
    font-size: 12px;
    color: $wl-blue;
  }
  .reply-pagination {
    display: flex;
    justify-content: center;
  }
}
</style>
