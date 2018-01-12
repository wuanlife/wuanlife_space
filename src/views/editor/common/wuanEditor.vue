<template>
  <div id="wuan-editor" 
        class="editor-container">
    <quill-editor id="editor" v-model="form.content"
      ref="wuanEditor"
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
        <button class="ql-image">Image</button>
        <!-- <button id="custom-button" @click="customButtonClick">[ Click me ]</button> -->
      </div>
    </quill-editor>
    <el-upload
      :action="qnLocation"
      :before-upload='beforeUpload' 
      :data="uploadData" 
      :on-success='upScuccess' 
      ref="upload" 
      style="display:none"
    >
      <el-button id="img-input" 
                 size="small" 
                 type="primary" 
                 v-loading.fullscreen.lock="fullscreenLoading"
                 element-loading-text="插入中,请稍候">点击上传</el-button>
    </el-upload>
  </div>
</template>

<script>
import "quill/dist/quill.core.css";
import "quill/dist/quill.snow.css";
import "quill/dist/quill.bubble.css";

import { quillEditor } from "vue-quill-editor";

import { getToken } from "api/qiniu"

const QINIU_DOMAIN = '';  // 图片服务器域名，展示时用

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
      addRange: [],
      uploadData: {},
      photoUrl: '',
      fullscreenLoading: false,
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
      return this.$refs.wuanEditor.quill;
    }
  },
  created() {},
  mounted() {
    this.$refs.wuanEditor.quill.getModule('toolbar').addHandler('image', this.imgHandler)

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
    // 上传七牛的actiond地址，http 和 https �不一样
    get qnLocation() {
      return location.protocol === 'http:' ? 'http://upload.qiniu.com' : 'https://up.qbox.me'
    },

    // 图片上传之前调取的函数
    // 这个钩子还支持 promise
    beforeUpload(file) {
      return this.qnUpload(file)
    },

    // 图片上传前获得数据token数据
    qnUpload(file) {
      this.fullscreenLoading = true
      const suffix = file.name.split('.')
      const ext = suffix.splice(suffix.length - 1, 1)[0]
      console.log(this.uploadType)
      // TODO: 图片格式/大小限制
      return this.$http('common/get_qiniu_token').then(res => {
        this.uploadData = {
          key: `image/${suffix.join('.')}_${new Date().getTime()}.${ext}`,
          token: res.data
        }
      })
    },

    // 图片上传成功回调   插入到编辑器中
    upScuccess(e, file, fileList) {
      console.log(e)
      this.fullscreenLoading = false
      let vm = this
      let url = ''
      if (this.uploadType === 'image') { // 获得文件上传后的URL地址
        url = STATICDOMAIN + e.key
      } else if (this.uploadType === 'video') {
        url = STATVIDEO + e.key
      }
      if (url != null && url.length > 0) { // 将文件上传后的URL地址插入到编辑器文本中
        let value = url
        // API: https://segmentfault.com/q/1010000008951906
        // this.$refs.myTextEditor.quillEditor.getSelection();
        // 获取光标位置对象，里面有两个属性，一个是index 还有 一个length，这里要用range.index，即当前光标之前的内容长度，然后再利用 insertEmbed(length, 'image', imageUrl)，插入图片即可。
        vm.addRange = vm.$refs.myQuillEditor.quill.getSelection()
        value = value.indexOf('http') !== -1 ? value : 'http:' + value
        vm.$refs.myQuillEditor.quill.insertEmbed(vm.addRange !== null ? vm.addRange.index : 0, vm.uploadType, value, Quill.sources.USER) // 调用编辑器的 insertEmbed 方法，插入URL
      } else {
        this.$message.error(`${vm.uploadType}插入失败`)
      }
      this.$refs['upload'].clearFiles() // 插入成功后清除input的内容
    },
    imgHandler(state) {
      console.log(state)
      this.addRange = this.$refs.wuanEditor.quill.getSelection()
      if (state) {
        let fileInput = document.getElementById('img-input')
        fileInput.click() // 加一个触发事件
      }
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