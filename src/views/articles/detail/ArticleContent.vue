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
                  v-loading="locking"
                  class="article-opt"
                  @click="lock(article.id)">
            {{lockedTemp ? '解锁' : '锁定'}}
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
  unapproveArticle,
  collectArticle,
  uncollectArticle,
  lockArticle,
  unlockArticle,
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
      // 请求发送中
      approving: false,
      collecting: false,
      locking: false,
      deleting: false,
      // 显示
      approvedTemp: false,
      approved_numTemp: 0,
      collectedTemp: false,
      collected_numTemp: 0,
      lockedTemp: false,
    };
  },
  computed: {
    ...mapGetters(["user"])
  },
  created() {},
  mounted() {
    this.approvedTemp = this.article.approved;
    this.approved_numTemp = this.article.approved_num;
    this.collectedTemp = this.article.collected;
    this.collected_numTemp = this.article.collected_num;
    this.lockedTemp = this.article.lock;
  },
  methods: {
    async approve() {
      if(this.approving) {
        return;
      }
      this.approving = true
        if(this.approvedTemp) {
        await unapproveArticle(this.$route.params.id)
        this.approvedTemp = !this.approvedTemp;
        this.approved_numTemp--;
      } else {
        await approveArticle(this.$route.params.id)
        this.approvedTemp = !this.approvedTemp;
        this.approved_numTemp++;
      }
      this.approving = false
    },
    async collect() {
      if(this.collecting) {
        return;
      }
      this.collecting = true
      if(this.collectedTemp) {
        await uncollectArticle(this.$route.params.id)
        this.collectedTemp = !this.collectedTemp;
        this.collected_numTemp--;
      } else {
        await collectArticle(this.$route.params.id)
        this.collectedTemp = !this.collectedTemp;
        this.collected_numTemp++;
      }
      this.collecting = false
    },
    async lock() {
      if(this.locking) {
        return;
      }
      this.locking = true;
      try {
        let res = null;
        if(this.lockedTemp) {
          res = await unlockArticle(this.$route.params.id)
        } else {
          res = await lockArticle(this.$route.params.id)
        }
        Notification.info({
          message: res,
          offset: 100
        })
      } catch (e) {
        if(e.data) {
          Notification.error({
            message: e.data.error,
            offset: 100
          })
        } else {
          console.log(e)
        }
      }
      this.locking = false;
    },
    edit(articleId) {
      this.$router.push({path: `/editor/article/${articleId}`})
    },
    async del() {
      if(this.deleting) {
        return;
      }
      this.deleting = true;
      const res = await deleteArticle(this.$route.params.id)
      Notification.info('删除帖子成功')
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
