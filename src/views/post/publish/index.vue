<template>
  
  <div id="post-publish">
    <section>
      <header>
        发表帖子
      </header>
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
          <el-form-item>
            <el-button type="primary" @click="onSubmit">立即创建</el-button>
            <el-button>取消</el-button>
          </el-form-item>
        </el-form>
      </div>
    </section>
  </div>
</template>

<script>
  // simple markdown
  import MdEditor from 'components/MdEditor';
  import { mapGetters } from 'vuex';
  import {  } from 'api/post';

  export default {
    name: 'post-pulish',
    components: { MdEditor },
    data() {
      return {
        groupid: null,
        form: {
          title: '',
          content: '',
          md: '',
        },
        content: '## Simplemde',
        html: '',
      }
    },
    computed: {
      ...mapGetters([
        'user',
        'access_token',
      ]),
      
    },
    created() {
      this.groupid = this.$route.params.groupid;
    },
    mounted() {
    },
    methods: {
      markdown2Html(md) {
        import('showdown').then(showdown => {
          const converter = new showdown.Converter();
          return converter.makeHtml(this.content)
        })
      },
      onSubmit() {
        this.form.content = this.markdown2Html(this.form.md);
      }
    }
  }
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
        font-family:PingFangHK-Medium;
        font-size:18px;
        color:#5677fc;
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
      font-family:PingFangSC-Regular;
      font-size:12px;
      color:#666666;
    }
  }

  // editor style
  .editor-container {
    font-family:PingFangSC-Regular;
    font-size:16px;
    line-height: 24px;
    color:#999999;
  }
</style>
