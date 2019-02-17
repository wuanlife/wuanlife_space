<template>
  <div id="drafts-new" class="view-container">
    <section class="drafts-new-content" v-loading.lock="form === null">
      <header class="wl-card">
        修改文章
      </header>
      <div>
        <el-input v-if="form" class="title-input"
                  v-model="form.title"
                  placeholder="输入标题">
        </el-input>
        <wuan-editor v-if="form" :initialContent="form.content" @content-change="onContentChange">
        </wuan-editor>
        <el-button class="wl-btn submit" type="primary" @click="onSubmit" :loading="submitLoading">修改</el-button>
      </div>
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
  section {
    flex: 662px 0 0;
    margin-top: 120px;
    margin-bottom: 124px;
    background: #ffffff;
  }
}

.title-input {
  margin-bottom: 34px;
  height: 47px;
  border-radius: 4px;
  /deep/ input {
    height: 100%;
    text-align: center;

    font-size: 20px;
    color: #999999;
    background-color: rgba(204, 204, 204, 0.3);
    &:hover {
      background-color: #ffffff;
    }
    &:focus {
      color: #666666;
      background-color: #ffffff;
    }
  }
}
#wuan-editor {
  margin-bottom: 38px;
}
.submit {
  float: right;
  color: #ffffff;
  font-size: 15px;
  height: 32px;
  line-height: 0;
  padding: 0;
  width: 93px;
}
#drafts-new .drafts-new-content {
  background: transparent;
  header {
    font-size: 18px;
    color: #5677fc;
    height: 42px;
    line-height: 42px;
    padding: 0 18px;
    margin-bottom: 31px;
    box-shadow:  0px 3px 7px 0px
    rgba(99, 99, 99, 0.35);
  }
  > div {
    width: 100%;
    padding: 36px 25px;
    background: #ffffff;
    box-shadow:  0px 3px 7px 0px
    rgba(99, 99, 99, 0.35);
    @include clearfix;
  }
}
</style>
