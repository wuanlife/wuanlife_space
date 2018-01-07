<template>
<div class="article-reply">
  <header>
      <h3>keke</h3>
      <time>time</time>
  </header>
  <p>我追过清风我追过清风我追过清风我追过清风我追过清风</p>
  <div class="opts clearfix">
      <span class="opt"
            @click="deleteReply">
        删除
      </span>
  </div>
</div>
</template>

<script>
import { mapGetters } from 'vuex';
import ArticleReply from './ArticleReply'

export default {
    name: 'article-reply',
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
.article-reply {
    padding: 17px;
    header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 50px;
        h3 {
            display: inline-block;
            font-size: 20px;
            color: #5677fc;
        }
        time {
            font-size: 16px;
            color: #434343;
        }
    }
    p {
        font-size: 20px;
        margin-bottom: 27px;
        color: #434343;
    }
    .opts {
        .opt {
            float: right;
            cursor: pointer;
            font-size: 16px;
            color: #5677fc;
        }
    }
}
</style>
