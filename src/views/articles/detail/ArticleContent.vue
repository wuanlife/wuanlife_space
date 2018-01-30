<template>
<div v-if="article" class="article-content">
    <article>
        <h1>
            {{ article.title }}
            <article-state v-if="article.lock" :text="'锁定'" :color="'#ccc'"></article-state>
        </h1>
        <time>{{ article.create_at | formatTime }}</time>
        <div class="article-html" v-html="article.content"></div>
    </article>
    <footer>
        <div class="btns">
            <div class="article-btn"
                 v-loading="approving"
                 :class="{'done': approvedTemp}"
                 @click="approve(article.id)">
                <icon-svg icon-class="zan" class="avatar-icon"></icon-svg>{{ approved_numTemp }}
            </div>
            <div class="article-btn"
                 v-loading="collecting"
                 :class="{'done': collectedTemp}"
                 @click="collect(article.id)">
                <icon-svg icon-class="shoucang" class="avatar-icon"></icon-svg>{{ collected_numTemp }}
            </div>
        </div>
        <div class="article-opts">
            <span v-if="true"
                  class="article-opt"
                  @click="lock(article.id)">
            {{article.lock ? '解锁' : '锁定'}}
            </span>
            <span v-if="user.id === article.author.id"
                  class="article-opt"
                  @click="edit(article.id)">
            编辑
            </span>
            <span v-if="user.id === article.author.id"
                  class="article-opt"
                  @click="del(article.id)">
            删除
            </span>
        </div>
    </footer>
</div>
<div v-else class="article-content" v-loading="true">
    <article>
        <h1>
            加载中...
        </h1>
    </article>
</div>
</template>

<script>
import { mapGetters } from 'vuex';
import { Notification } from 'element-ui'
import ArticleState from 'components/ArticleState';
import {
  approveArticle,
  collectArticle,
  lockArticle,
  deleteArticle,
} from "api/article";

export default {
  name: "article-content",
  components: {
    ArticleState
  },
  props: ["article"],
  data() {
    return {
      approving: false,
      collecting: false,
      collectedTemp: false,
      collected_numTemp: 0,
      approvedTemp: false,
      approved_numTemp: 0,
    };
  },
  computed: {
    ...mapGetters(["user"])
  },
  created() {},
  mounted() {
    this.collectedTemp = this.article.collected;
    this.collected_numTemp = this.article.collected_num;
    this.approvedTemp = this.article.approved;
    this.approved_numTemp = this.article.approved_num;
  },
  methods: {
    async approve() {
      this.approving = true
      const res = await approveArticle(this.$route.params.id)
        if(this.approvedTemp) {
        this.approvedTemp = !this.approvedTemp;
        this.approved_numTemp--;
      } else {
        this.approvedTemp = !this.approvedTemp;
        this.approved_numTemp++;
      }
      this.approving = false
    },
    async collect() {
      this.collecting = true
      const res = await collectArticle(this.$route.params.id)
      if(this.collectedTemp) {
        this.collectedTemp = !this.collectedTemp;
        this.collected_numTemp--;
      } else {
        this.collectedTemp = !this.collectedTemp;
        this.collected_numTemp++;
      }
      this.collecting = false
    },
    async lock() {
      const res = await lockArticle(this.$route.params.id)
      console.log(res);
      Notification.info(res)
    },
    edit(articleId) {
      this.$router.push({path: `/editor/article/${articleId}`})
    },
    async del() {
      const res = await deleteArticle(this.$route.params.id)
      console.log(res);
    }
  }
};
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
    position: relative;
    color: #666666;
    cursor: pointer;
    margin-left: 20px;
    transition: all 0.3s ease-in-out;
    &:not(:last-child) {
      margin-right: 54px;
    }
    .svg-icon {
      color: #666666;
      transition: all 0.3s ease-in-out;
      margin-right: 12px;
    }
    &:hover {
      color: #5677fc;
      .svg-icon {
        color: #5677fc;
      }
    }
    &.done {
      color: #5677fc;
      .svg-icon {
        color: #5677fc;
      }
    }
  }
  .article-opt {
    cursor: pointer;
    color: #5677fc;
    transition: all 0.3s ease-in-out;
    &:hover {
      color: #0040b9;
    }
  }
}
</style>
