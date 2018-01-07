<template>
  <div id="wuan-editor" 
        class="editor-container">
    <quill-editor id="editor" v-model="form.content"
      ref="myQuillEditor"
      :options="editorOption"
      @blur="onEditorBlur($event)"
      @focus="onEditorFocus($event)"
      @ready="onEditorReady($event)"> 
      <div id="toolbar" slot="toolbar">
        <!-- Add a bold button -->
        <button class="ql-bold">Bold</button>
        <button class="ql-italic">Italic</button>
        <button class="ql-underline">Underline</button>
        <button class="ql-header" value="1"></button>
        <button class="ql-header" value="2"></button>
        <!-- <button id="custom-button" @click="customButtonClick">[ Click me ]</button> -->
      </div>
    </quill-editor>
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
      editorOption: {
        placeholder: '说些什么吧...',
        modules: {
          toolbar: "#toolbar"
        }
      }
    };
  },
  computed: {
    editor() {
      return this.$refs.myQuillEditor.quill;
    }
  },
  created() {},
  mounted() {
    // this.$refs.myQuillEditor.quill.getModule('toolbar').addHandler('image', this.imgHandler)
  },
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

<style rel="stylesheet/scss" lang="scss">
// editor style
.editor-container {
  font-size: 16px;
  line-height: 24px;
  color: #999999;
  .quill-editor {
    box-shadow: 0px 3px 7px 0px 
      rgba(99, 99, 99, 0.65);
    border-radius: 4px;
    // 工具栏样式
    #toolbar {
    }
    .ql-container {
      height: 300px;
      font-size: 24px;
    }

  }
}
</style>