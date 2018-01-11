<template>
<div class="article-content">
    <article>
        <h1>
            {{ article.title }}
            <post-state v-if="article.sticky" :text="'置顶'" :color="'#5992e4'"></post-state>
            <post-state v-if="article.lock" :text="'锁定'" :color="'#ccc'"></post-state>
        </h1>
        <time>{{ article.create_at | formatTime }}</time>
        <div class="article-html" v-html="article.content"></div>
    </article>
    <footer>
        <div class="btns">
            <div class="article-btn"
                 :class="{'done': article.approved}"
                 @click="approve(article.id)">
                <icon-svg icon-class="zan" class="avatar-icon"></icon-svg>{{ article.approved_num }}
            </div>
            <div class="article-btn"
                 :class="{'done': article.collected}"
                 @click="collect(article.id)">
                <icon-svg icon-class="shoucang" class="avatar-icon"></icon-svg>{{ article.collected_num }}
            </div>
        </div>
        <div class="article-opts">
            <span v-if="true"
                  class="article-opt"
                  @click="settop(article.id)">
            {{article.sticky ? '取消置顶' : '置顶'}}
            </span>
            <span v-if="true"
                  class="article-opt"
                  @click="lock(article.id)">
            {{article.lock ? '解锁' : '锁定'}}
            </span>
            <span v-if="true"
                  class="article-opt"
                  @click="edit(article.id)">
            编辑
            </span>
            <span v-if="true"
                  class="article-opt"
                  @click="del(article.id)">
            删除
            </span>
        </div>
    </footer>
</div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import {
      approveArticle,
      collectArticle,
      deleteArticle,
  } from 'api/post'

  export default {
    name: 'article-content',
    components: {

    },
    props: ['article'],
    data() {
      return {
      }
    },
    computed: {
      ...mapGetters([
        'user',
        'access_token',
      ]),
    },
    created() {
    },
    mounted() {

    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
article {
    h1 {
        font-size: 28px;
        color: #434343;
        margin-bottom: 38px;
    }
    time {
        display: block;
        font-size: 20px;
        color: #434343;
        margin-bottom: 47px;
    }
    .article-html {
        margin-bottom: 66px;
        p {
            font-size: 20px;
            color: #434343;
        }
    }
}
footer {
    display: flex;
    justify-content: space-between;
    font-size: 20px;
    
    .article-btn {
        display: inline-block;
        color: #666666;
        cursor: pointer;
        margin-left: 20px;
        &:not(:last-child) {
            margin-right: 54px;
        }
        .svg-icon {
            color: #666666;
            transition: all 0.3s ease-in-out;
            margin-right: 12px;
        }
        &.done {
            color:#5677fc;
            .svg-icon {
                color:#5677fc
            }
        }
    }
    .article-opt {
        cursor: pointer;
        color: #5677fc;
        transition: all 0.3s ease-in-out;
        &:hover {
            color: #0040b9
        }
    }
}
</style>
