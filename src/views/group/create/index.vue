<!-- 
  TODO:
    1. hide the "plus icon" after uploaded image
    2. jump to the group page after created
    3. add 'enter' event for quick submit
    4. BUG: the first upload doesn't work
    5. add default value for loginForm.private
 -->

<template>
  <div class="register-container">
    <section>
      <header>创建星球</header>
      <div class="form-content">
        <el-form label-width="100px" :model="loginForm" :rules="loginRules" ref="loginForm" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">
          <el-form-item label="星球名称" prop="name" class="form-inputc">
            <el-input v-model="loginForm.name" placeholder="星球名称" class="name"></el-input>
          </el-form-item>
          <div>星球头像(选填)</div>
          <button id="upload-img" class="img-upload" @click="upload"><span>+</span></button>
          <el-form-item label="星球介绍(选填)" prop="introduction" class="form-inputc">
            <el-input type="textarea" v-model="loginForm.introduction" placeholder="星球介绍(选填)" class="intro"></el-input>
          </el-form-item>
          <el-form-item label="加入星球方式" class="form-inputc">
            <el-select v-model="loginForm.private" placeholder="加入星球方式" class="join-c">
              <el-option label="允许任何人加入" value="false"></el-option>
              <el-option label="需要申请加入" value="true"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label-width="100px" class="form-inputc">
            <el-button type="primary" @click="submitForm('loginForm')">创建</el-button>
          </el-form-item>
        </el-form>
      </div>
    </section>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex';
  import { getToken } from 'api/qiniu'
  import { UploaderBuilder, Uploader } from 'qiniu4js';
  import { createGroup } from 'api/group';

  // qiniu4js uploader object
  var urlkey='';
  var uploader = new UploaderBuilder()
    .debug(false)//开启debug，默认false
    .domain({http: "http://upload.qiniu.com", https: "https://up.qbox.me"})
    .retry(2)//设置重传次数，默认0，不重传
    .compress(0.5)//默认为1,范围0-1
    .scale([200,0])  //第一个参数是宽度，第二个是高度,[200,0],限定高度，宽度等比缩放.[0,100]限定宽度,高度等比缩放.[200,100]固定长宽
    .size(1024*1024)
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
      onReady(tasks) {
          //该回调函数在图片处理前执行,也就是说task.file中的图片都是没有处理过的
        //选择上传文件确定后,该生命周期函数会被回调。
        
      },onStart(tasks){
          //所有内部图片任务处理后执行
        //开始上传
        console.log('upload start')
        
      },onTaskGetKey(task){
        
      },onTaskProgress: function (task) {
          //每一个任务的上传进度,通过`task.progress`获取
        console.log(task.progress);
        
      },onTaskSuccess(task){
        //一个任务上传成功后回调
        console.log(task.result.key);//文件的key
        urlkey=task.result.key;
        document.getElementById("upload-img").style.backgroundImage="url("+process.env.QINIU_DOMAIN_URL+urlkey+")";
      },onTaskFail(task) {
        //一个任务在经历重传后依然失败后回调此函数
        
      },onTaskRetry(task) {
        //开始重传
        
      },onFinish(tasks){
          //所有任务结束后回调，注意，结束不等于都成功，该函数会在所有HTTP上传请求响应后回调(包括重传请求)。
              
      }
    }).build();


  export default {
    name: 'index-visitor',
    data() {
      // element-ui validator
      var validateName = (rule, value, callback) => {
        if (value.length<1 || value.length>80) {
          callback(new Error('星球长度1~80个字符'));
        } else {
          callback();
        }
      };
      var validateIntro = (rule, value, callback) => {
        if (value.length>50) {
          callback(new Error('星球介绍1~50个字符'));
        }else {
          callback();
        }
      };
      return {
        activeName: 'index-myplanet',
        loading: false,

        // form part
        loginForm: {
          name: '',
          image_url: '',
          introduction: '',
          private: false,
        },
        loginRules: {
          name: [
            { validator: validateName, trigger: 'blur' }
          ],
          introduction: [
            { validator: validateIntro, trigger: 'blur' }
          ],
        }
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ])
    },
    mounted() {

    },
    methods: {
      upload(event) {
        try {
          uploader.chooseFile();
          event.stopPropagation();
        } catch (error) {
          this.$message({
            message: error,
            type: 'error',
            duration: 1000,
          });
        }
      },
      submitForm(formName){
        this.loginForm.image_url=process.env.QINIU_DOMAIN_URL+urlkey;
        this.loginForm.private=(this.loginForm.private=="true")?true:false;
        //console.log(this.loginForm.private);
        this.$refs[formName].validate((valid) => {
          if(valid){
            this.loading=true;
            createGroup(this.loginForm)
              .then(
                res => {
                  //转到星球主页
                  this.loading = false;
                  this.$router.push({ path: `/planet/${res.id}`, query: { name: this.loginForm.name }});
                })
              .catch(error => {
                this.$message({
                  message: error.data.error,
                  type: 'error',
                  duration: 1000,
                });
              });
          }else{
            console.log('error submit!!');
            return false;
          }
        })
      },
    },
  }
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
.register-container{
  display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 660px;
    min-width: 380px;
    section {
      flex: 1;
      header {
        margin: 15px 0 20px 0;
        font-family:PingFangHK-Medium;
        font-size:18px;
        color:#5677fc;
      }
      div.form-content {
        width: 100%;
        background:#ffffff;
        border-radius:4px;
        width:660px;
        height:699px;
        padding-top:40px;
        padding-left:30px;
        .img-upload{
          margin-top: 8px;
          margin-bottom:10px;
          background:#ffffff;
          box-shadow:0 2px 4px 0 rgba(0,0,0,0.28);
          border-radius:100px;
          width:80px;
          height:80px;
          background-image: url();
          background-repeat: no-repeat;
          background-size:100% 100%;
          span{
            font-size: 30px;
            color: #5677fc;
          }
        }
      }
    }
}
</style>