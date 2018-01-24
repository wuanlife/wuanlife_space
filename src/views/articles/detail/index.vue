<template>
  <div id="article-detail" class="view-container">
    <aside>
      <user-card :user="article ? article.author : {}"></user-card>
    </aside>
    <section>
      <header class="wuan-block">
        文章详情
      </header>
      <div class="wuan-block article-container">
        <article-content :article="article"></article-content>
        <article-replies></article-replies>
        <article-reply-input></article-reply-input>
      </div>
    </section>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getArticle } from "api/article";
  import PostState from 'components/PostState/PostState';
  import UserCard from 'components/UserCard';
  import ArticleContent from './ArticleContent.vue';
  import ArticleReplies from './ArticleReplies';
  import ArticleReplyInput from './ArticleReplyInput.vue';
  export default {
    name: 'article-detail',
    components: {
      UserCard,
      ArticleContent,
      ArticleReplies,
      ArticleReplyInput,
    },
    data() {
      return {
        article: null
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ]),
    },
    created() {

    },
    mounted() {
      let self = this;
      this.loading = true;
      getArticle(this.$route.params.id).then(res => {
        this.article = res;
      });
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
    font-size: 32px;
    color: #5677fc;
  }
  .article-container {
    padding: 31px;
    .article-content {
      margin-bottom: 50px;
    }
    .article-replies {
      margin-bottom:36px;
    }
  }
}
.wuan-block {
  background: #ffffff;
  border-radius: 4px;
}
</style>
