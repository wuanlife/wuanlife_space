<template>
<div class="article-replies">
  <header>
    {{`replies.count 个回复`}}
  </header>
  <div class="replies-container">
    <article-reply></article-reply>
  </div>
  <div class="reply-pagination">

  </div>
</div>
</template>

<script>
import { mapGetters } from 'vuex';
import ArticleReply from './ArticleReply'

export default {
    name: 'article-replies',
    components: {
        ArticleReply
    },
    data() {
        return {
        
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
