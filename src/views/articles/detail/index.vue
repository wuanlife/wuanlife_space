<template>
  <div v-if="article" id="article-detail" class="view-container">
    <aside>
      <user-card :user="article.author"></user-card>
    </aside>
    <section>
      <header class="wl-card">
        文章详情
      </header>
      <div class="wl-card article-container">
        <article-content :article="article"></article-content>
        <article-replies></article-replies>
      </div>
    </section>
  </div>
  <!-- 未加载骨架 -->
  <div v-else id="article-detail" class="view-container">
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { getArticle } from 'api/article'
import UserCard from 'components/UserCard'
import ArticleContent from './ArticleContent.vue'
import ArticleReplies from './ArticleReplies'
export default {
  name: 'article-detail',
  components: {
    UserCard,
    ArticleContent,
    ArticleReplies
  },
  data () {
    return {
      article: null
    }
  },
  computed: {
    ...mapGetters([
      'user'
    ])
  },
  created () {

  },
  mounted () {
    this.loading = true
    getArticle(this.$route.params.id).then(res => {
      this.article = res
    })
  },
  methods: {
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
#article-detail {
  margin-top: 31px;
}
#article-detail > section {
  & > header {
    padding: 17px;
    margin-bottom: 14px;
    font-size: $title-font-size;
    color: #5677fc;
  }
  .article-container {
    padding: 20px;
    margin-bottom: 30px;
    .article-content {
      margin-bottom: 50px;
    }
    .article-replies {
      margin-bottom:36px;
    }
  }
}
</style>
