<template>
  <div id="article-detail" class="view-container">
    <aside>
      <user-card :user="testUser"></user-card>
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
  import {
    parseQueryParams,
  } from 'utils/url'
  import PostState from 'components/PostState/PostState';
  import UserCard from 'components/UserCard';
  import ArticleContent from './ArticleContent.vue';
  import ArticleReplies from './ArticleReplies';
  import ArticleReplyInput from './ArticleReplyInput.vue';

  import { 

  } from 'api/post';

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
        testUser: {
          "id": "58",
          "name": "xiaochao_php",
          "articles_num":"154",
          "avatar_url": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2/1/w/100/h/100"
        },
        article: {
          "id": "1",
          "title": "通过接口编辑",
          "content": "成功",
          "update_at":"2017-07-20T12:50:30.176Z",
          "create_at": "2017-07-20T12:50:30.176Z",
          "lock": true,
          "approved": false,
          "approved_num": "0",
          "collected": true,
          "collected_num": "1",
        },
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
