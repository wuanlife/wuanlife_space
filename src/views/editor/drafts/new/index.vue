<template>
  <div id="drafts-new" class="view-container">
    <section>
      <header>
        写文章
      </header>
      <el-input class="title-input"
                  v-model="form.title"
                  placeholder="输入标题">
      </el-input>
      <wuan-editor @content-change="onContentChange">
      </wuan-editor>
      <el-button class="wl-btn submit" type="primary" @click="onSubmit" :loading="submitLoading">发表</el-button>
    </section>
  </div>
</template>

<script>
import { postArticles } from 'api/article'
import wuanEditor from '../../common/wuanEditor'

export default {
  name: 'drafts-new',
  components: {
    'wuan-editor': wuanEditor
  },
  data () {
    return {
      form: {
        title: '',
        content: ''
      },
      submitLoading: false
    }
  },
  created () {
  },
  mounted () {},
  methods: {
    onContentChange (newContent, oldContent) {
      this.form.content = newContent
    },
    onSubmit () {
      this.submitLoading = true
      postArticles(this.form)
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
  section {
    flex: 658px 0 0;
    margin-top: 120px;
    margin-bottom: 124px;
    padding: 30px 28px 68px 28px;
    background: #ffffff;
    header {
      margin-bottom: 38px;
      font-size: $title-font-size;
      color: $wl-blue;
    }
  }
}

.title-input {
  margin-bottom: 34px;
  height: 60px;
  box-shadow: 0px 3px 7px 0px
    rgba(99, 99, 99, 0.35);
  border-radius: 4px;
  /deep/ input {
    height: 100%;
    text-align: center;

    font-size: 22px;
    color: #333333;
  }
}
#wuan-editor {
  margin-bottom: 26px;
}
/*.submit {
  float: right;
  padding: 13px 43px;

  font-size: 24px;
  color: #ffffff;
}*/
</style>
