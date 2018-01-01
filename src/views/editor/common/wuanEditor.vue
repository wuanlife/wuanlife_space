<template>
  <div class="">
    <el-form ref="form" :model="form">
        <el-form-item>
        <el-input id="title"
                  v-model="form.title"
                  placeholder="请输入内容">            
        </el-input>
        </el-form-item>
        <el-form-item>
        <div id="content" 
             class="editor-container">
          <quill-editor v-model="form.content"
            ref="myQuillEditor"
            :options="editorOption"
            @blur="onEditorBlur($event)"
            @focus="onEditorFocus($event)"
            @ready="onEditorReady($event)"> 
          </quill-editor>
        </div>
        </el-form-item>
        <el-form-item class="text-right">
        <el-button class="wuan-button" style="width: 124px; height: 30px" type="primary" @click="onSubmit">创建</el-button>
        </el-form-item>
    </el-form>
  </div>
</template>

<script>
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

import { quillEditor } from "vue-quill-editor";

export default {
  name: "wuan-editor",
  components: {
    "quill-editor": quillEditor
  },
  data() {
    return {
      form: {
        title: "",
        content: ""
      },
      editorOption: {}
    };
  },
  computed: {
    editor() {
      return this.$refs.myQuillEditor.quill;
    }
  },
  created() {},
  mounted() {},
  methods: {
    onEditorBlur(quill) {
      console.log("editor blur!", quill);
    },
    onEditorFocus(quill) {
      console.log("editor focus!", quill);
    },
    onEditorReady(quill) {
      console.log("editor ready!", quill);
    },
    onSubmit() {}
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