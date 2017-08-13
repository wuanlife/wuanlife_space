<template>
  <div class="register-container">
  	<section>
  		<header>创建星球</header>
  		<div class="form-content">
  			<el-form label-width="100px" :model="loginForm" :rules="loginRules" ref="loginForm" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">
            <el-form-item label="星球名称" prop="email" class="form-inputc">
              <el-input v-model="loginForm.email" placeholder="星球名称" class="name"></el-input>
            </el-form-item>
            <div>星球头像(选填)</div>
            <button @click="upload"></button>
            <el-upload
              class="upload-demo upload-c"
              action="https://jsonplaceholder.typicode.com/posts/"
              :on-preview="handlePreview"
              :on-remove="handleRemove"
              :file-list="fileList">
              <el-button size="small" type="primary">+</el-button>
            </el-upload>
            <el-form-item label="星球介绍(选填)" class="form-inputc">
              <el-input type="textarea" v-model="loginForm.inviteword" placeholder="星球介绍(选填)" class="intro"></el-input>
            </el-form-item>
            <el-form-item label="加入星球方式" class="form-inputc">
              <el-select v-model="loginForm.inviteword" placeholder="加入星球方式" class="join-c">
                <el-option label="允许任何人加入" value="0"></el-option>
                <el-option label="验证水电费" value="1"></el-option>
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

  // qiniu4js uploader object
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
        console.log(task.result.hash);//文件hash
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
      var validateUser = (rule, value, callback) => {
        var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
        if (value === '') {
          callback(new Error('请输入邮箱'));
        }else if(!myreg.test(value)){
          callback(new Error('请填写正确的邮箱格式！'));
        } else {
          callback();
        }
      };
      var validateName = (rule, value, callback) => {
        var myregName = /^[0-9a-zA-Z\u4E00-\u9FA5\_]*$/;
        if (value === '') {
          callback(new Error('请输入昵称'));
        }else if(value.length == 0 || value.length > 18){
          callback(new Error('请输入1-18位字符作为昵称！'));
        }else if(!myregName.test(value)){
          callback(new Error('只允许中文、数字、字母和下划线！'));
        } else {
          callback();
        }
      };
      var validatePass = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入密码'));
        }else if(value.length < 6 || value.length>20){
          callback(new Error('请填写6-20位密码'));
        } else {
          callback();
        }
      };
      var validateWord = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入邀请码'));
        } else {
          callback();
        }
      };
      return {
        activeName: 'index-myplanet',
        loading: false,

        // form part
        loginForm: {
          email: '',
          nickname: '',
          password: '',
          inviteword: '',
        },
        loginRules: {
          email: [
            { validator: validateUser, trigger: 'blur' }
          ],
          nickname: [
            { validator: validateName, trigger: 'blur' }
          ],
          password: [
            { validator: validatePass, trigger: 'blur' }
          ],
          inviteword: [
            { validator: validateWord, trigger: 'blur' }
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
      upload() {
        uploader.chooseFile();
      },
      submitForm(formName){
        this.$refs[formName].validate((valid) => {
          if(valid){
            this.loading=true;
            this.$store.dispatch('Signup',this.loginForm).then(() => {
              this.loading = false;
              this.$router.push({ path: '/' });
            }).catch(err => {
              console.dir(err)
              this.$message({
                message: err.error,
                type: 'error',
                duration: 1000,
              });
              this.loading = false;
            });
          }else{
            console.log('error submit!!');
            return false;
          }
        });
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
      }
    }
}
</style>