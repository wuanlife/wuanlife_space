<template>
  <div class='simplemde-container' :style="{height:height+'px',zIndex:zIndex}">
    <textarea :id='id'>
    </textarea>
  </div>
</template>

<script>
  import 'simplemde/dist/simplemde.min.css';
  import SimpleMDE from 'simplemde';
  import { getToken } from 'api/qiniu'
  import { UploaderBuilder, Uploader } from 'qiniu4js';


  export default {
    name: 'Sticky',
    props: {
      value: String,
      id: {
        type: String,
        default: 'markdown-editor'
      },
      autofocus: {
        type: Boolean,
        default: false
      },
      placeholder: {
        type: String,
        default: ''
      },
      height: {
        type: Number,
      },
      zIndex: {
        type: Number,
        default: 10
      },
      toolbar: {
        type: Array
      }
    },
    data() {
      return {
        simplemde: null,
        hasChange: false
      };
    },
    watch: {
      value(val) {
        if (val === this.simplemde.value() && !this.hasChange) return;
        this.simplemde.value(val);
      }
    },
    mounted() {
      this.simplemde = new SimpleMDE({
        element: document.getElementById(this.id),
        autofocus: this.autofocus,
        toolbar: this.toolbar,
        spellChecker: false,
        insertTexts: {
          link: ['[', ']( )']
        },
        toolbar: [
          "bold", 
          "italic", 
          "heading", 
          "|",
          "quote",
          "unordered-list",
          "ordered-list",
          "|", 
          "code",
          "link",
          {
            name: "custom",
            action: function customFunction(editor){
              // qiniu uploader part
              var urlkey='';
              var uploader = new UploaderBuilder()
                .domain({http: "http://upload.qiniu.com", https: "https://up.qbox.me"})
                .retry(2)//设置重传次数，默认0，不重传
/*                .scale([200,0])  //第一个参数是宽度，第二个是高度,[200,0],限定高度，宽度等比缩放.[0,100]限定宽度,高度等比缩放.[200,100]固定长宽
                .size(1024*1024)*/
                .chunk(true)
                .auto(true)
                .multiple(false)
                .accept(['.gif','.png','.jpg'])//过滤文件，默认无，详细配置见http://www.w3schools.com/tags/
                .tokenShare(true)
                .tokenFunc(function (setToken,task) {
                  getToken().then(res => {
                    setToken(res.uploadToken);
                  })
                })
                //任务拦截器
                .interceptor({
                    //拦截任务,返回true，任务将会从任务队列中剔除，不会被上传
                  onIntercept: function (task) {
                    return task.file.size > 4 * 1024 * 1024;
                  },
                  //中断任务，返回true，任务队列将会在这里中断，不会执行上传操作。
                  onInterrupt: function (task) {
                    if (this.onIntercept(task)) {
                      alert("请上传小于4m的文件");
                      return true;
                    }
                    else {
                      return false;
                    }
                  }
                }) 
                .listener({
                  onTaskSuccess(task){
                    //一个任务上传成功后回调
                    console.log(task.result.key);//文件的key
                    let pos = editor.codemirror.getCursor();
                    editor.codemirror.setSelection(pos, pos);
                    editor.codemirror.replaceSelection(`![${task.result.key}](${process.env.QINIU_DOMAIN_URL+task.result.key})`);
                    
                  },onTaskFail(task) {
                    //一个任务在经历重传后依然失败后回调此函数
                    
                  },onTaskRetry(task) {
                    //开始重传
                    
                  },onFinish(tasks){   
                  }
                }).build();
              uploader.chooseFile();
            },
            className: "fa fa-image",
            title: "Custom Button",
          },
          "|",
          "preview",
          "side-by-side",
          "fullscreen",
        ],
      // hideIcons: ['guide', 'heading', 'quote', 'image', 'preview', 'side-by-side', 'fullscreen'],
        placeholder: this.placeholder
      });
      if (this.value) {
        this.simplemde.value(this.value);
      }
      this.simplemde.codemirror.on('change', () => {
        if (this.hasChange) {
          this.hasChange = true
        }
        this.$emit('input', this.simplemde.value());
      });
    },
    destroyed() {
      this.simplemde = null;
    }
};
</script>

<style>
.simplemde-container .CodeMirror {
  /*height: 150px;*/
  min-height: 150px;
}

.simplemde-container .CodeMirror-scroll {
  min-height: 150px;
}

.simplemde-container .CodeMirror-code {
  padding-bottom: 40px;
}

.simplemde-container .editor-statusbar {
  display: none;
}

.simplemde-container .CodeMirror .CodeMirror-code .cm-link {
  color: #1482F0;
}

.simplemde-container .CodeMirror .CodeMirror-code .cm-string.cm-url {
  color: #2d3b4d;
  font-weight: bold;
}

.simplemde-container .CodeMirror .CodeMirror-code .cm-formatting-link-string.cm-url {
  padding: 0 2px;
  font-weight: bold;
  color: #E61E1E;
}
</style>


