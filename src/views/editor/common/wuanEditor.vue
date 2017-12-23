<template>
  <div class="post-publish-container">
    <el-form ref="form" :model="form">
        <el-form-item>
        <p>标题</p>
        <el-input v-model="form.title"
                    placeholder="请输入内容">            
        </el-input>
        </el-form-item>
        <el-form-item>
        <p>内容</p>
        <div class="editor-container">
            <md-editor id='contentEditor' 
                        ref="contentEditor"
                        v-model='form.md' 
                        :height='300'
                        placeholder='请输入内容'
                        :zIndex='20'></md-editor>
        </div>
        </el-form-item>
        <el-form-item class="text-right">
        <el-button class="wuan-button" style="width: 124px; height: 30px" type="primary" @click="onSubmit">创建</el-button>
        </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

import { quillEditor } from "vue-quill-editor";

export default {
  name: "wuan-editor",
  components: {
    wuanEditor: "wuan-editor"
  },
  data() {
    return {
      groupid: null,
      form: {
        title: "",
        content: "",
        md: ""
      },
      content: "## Simplemde",
      html: ""
    };
  },
  computed: {
    ...mapGetters(["user", "access_token"])
  },
  created() {
    let name = this.$route.query.name;
    document.title = name + " - 午安网 - 过你想过的生活";
    if (!this.$route.query.groupid) {
      this.$router.go(-1);
      return;
    }
    this.groupid = parseInt(this.$route.query.groupid);
  },
  mounted() {},
  methods: {
    markdown2Html(md) {
      const converter = new showdown.Converter();
      return converter.makeHtml(md);
    },
    onSubmit() {
      this.form.content = this.markdown2Html(this.form.md);
      publishPost(this.groupid, {
        title: this.form.title,
        content: this.form.content
      })
        .then(res => {
          this.$router.push({
            path: `/topic/${res.id}`,
            query: { name: this.form.title }
          });
        })
        .catch(error => {
          console.log(`publish error ${error}`);
        });
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
#post-publish {
  display: flex;
  justify-content: center;
  margin: auto;
  max-width: 660px;
  min-width: 590px;
  section {
    header {
      margin: 15px 0 20px 0;
      font-family: PingFangHK-Medium;
      font-size: 18px;
      color: #5677fc;
    }
    min-width: 0;
    flex: 0 0 590px;
  }
}
.post-publish-container {
  background: #ffffff;
  min-height: 300px;
  padding: 20px 30px;
  // label
  p {
    font-family: PingFangSC-Regular;
    font-size: 12px;
    color: #666666;
  }
}

// editor style
.editor-container {
  font-family: PingFangSC-Regular;
  font-size: 16px;
  line-height: 24px;
  color: #999999;
}
</style>