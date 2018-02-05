<template>
  <div id="drafts-new" class="view-container">
    <section v-loading.lock="form === null" class="wl-card">
      <header>
        修改文章
      </header>
      <el-input v-if="form" class="title-input"
                  v-model="form.title"
                  placeholder="输入标题">
      </el-input>
      <wuan-editor v-if="form" :initialContent="form.content" @content-change="onContentChange">
      </wuan-editor>
      <el-button class="wuan-button submit" type="primary" @click="onSubmit" :loading="submitLoading">修改</el-button>
    </section>
  </div>
</template>

<script>
// THINKING: 是否应该把修改和写作合并成一个？
import { getArticle, putArticle } from 'api/article'
import wuanEditor from '../common/wuanEditor'

export default {
  name: 'edit',
  components: {
    'wuan-editor': wuanEditor
  },
  data () {
    return {
      form: null,
      submitLoading: false
    }
  },
  created () {
  },
  mounted () {
    getArticle(this.$route.params.id).then(res => {
      this.form = {
        title: res.title,
        content: res.content
      }
    })
  },
  methods: {
    onContentChange (newContent, oldContent) {
      this.form.content = newContent
    },
    onSubmit () {
      this.submitLoading = true
      putArticle(this.$route.params.id, this.form)
        .then((res) => {
          this.submitLoading = false
          const articleId = res.id
          this.$router.push({path: `/article/${articleId}`})
        })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
#drafts-new {
  width: 1054px;
  section {
    margin-top: 89px;
    margin-bottom: 124px;
    padding: 49px 40px 107px 40px;
    background: #ffffff;
    header {
      margin-bottom: 65px;
      font-size: 32px;
      letter-spacing: 0px;
      color: #5677fc;
    }
  }
}

.title-input {
  margin-bottom: 55px;
  height: 94px;
  box-shadow: 0px 3px 7px 0px
    rgba(99, 99, 99, 0.35);
  border-radius: 4px;
  /deep/ input {
    height: 100%;
    text-align: center;

    font-size: 36px;
    letter-spacing: 0px;
    color: #757575;
  }
}
#wuan-editor {
  margin-bottom: 42px;
}
.submit {
  float: right;
  padding: 13px 43px;

  font-size: 24px;
  color: #ffffff;
}
</style>
