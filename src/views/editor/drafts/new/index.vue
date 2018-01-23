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
      <el-button class="wuan-button submit" type="primary" @click="onSubmit" :loading="submitLoading">发表</el-button>
    </section>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import { postArticles } from 'api/article';
import wuanEditor from "../../common/wuanEditor";

export default {
  name: "drafts-new",
  components: {
    "wuan-editor": wuanEditor
  },
  data() {
    return {
      form: {
        title: '',
        content: '',
      },
      submitLoading: false,
    };
  },
  created() {
  },
  mounted() {},
  methods: {
    onContentChange(newContent, oldContent) {
      this.form.content = newContent;
    },
    onSubmit() {
      this.submitLoading = true;
      postArticles(this.form)
      .then((res) => {
        this.submitLoading = false;
        const articleId = res.id
        this.$router.push({path: `/article/${articleId}`})
      })
    }
  }
};
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