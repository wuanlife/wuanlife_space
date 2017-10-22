<template>
  <div id="post-edit" v-loading="loading">
    <section>
      <header>
        编辑帖子
      </header>
      <div class="post-edit-container">
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
            <el-button class="wuan-button" style="width: 124px; height: 30px" type="primary" @click="onSubmit">编辑</el-button>
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
  import { getPost, putPost } from 'api/post';
  import showdown from 'showdown';
  import toMarkdown from 'to-markdown'

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
        loading: true,
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
      console.log(this.$route)
      getPost(this.$route.params.id)
        .then((res) => {
          this.form.title = res.title;
          this.form.md = toMarkdown(res.content);
          this.loading = false;
        })
    },
    mounted() {
    },
    methods: {
      markdown2Html(md) {
        const converter = new showdown.Converter();
        return converter.makeHtml(md)
      },
      onSubmit() {
        this.form.content = this.markdown2Html(this.form.md);
        putPost(this.$route.params.id, {
          title: this.form.title,
          content: this.form.content,
        }).then((res) => {
          this.$router.push({path: `/post/${res.id}`});
        }).catch((error) => {
          console.log(`publish error ${error}`)
        })
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  #post-edit {
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
  .post-edit-container {
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
