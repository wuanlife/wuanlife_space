<!-- 
  TODO: 
    1. better not to use inline style like '<span style="font-family:PingFangHK.../>' 
    2. birthday should be implemented
    3. validator have some bug so I commented them, waitting to be fixed
 -->

<template>
    <div class="personalData-container">
      <section>     
        <header>个人资料</header>
        <div class="form-content" v-loading="loading">
          <div class="personalDataUpLoader">
            <img :src="personalDataForm.avatar_url" class="avatar" id="avatar">
            <div class="avatar-uploader">
              <button id="changeAvatar" @click="changeAvatar"><icon-svg icon-class="edit" class="edit-icon"></icon-svg>修改</button>
            </div>
          </div>
          <el-form :model="personalDataForm" :rules="personalDataRules" ref="personalDataForm" class="personalDataForm" :label-position="labelPosition">
            <el-form-item label="邮箱" prop="email" label-width="66px">
              <span style="font-family:PingFangHK-Semibold;font-size:16px;color:#666666;text-align:left;width: 210px;display: inline-block;margin-right: 11px;">{{ personalDataForm.mail }}</span>
              <el-button @click.prevent="checkMail(domain)" style="font-size: 12px;color: #ffffff;background-color: #5677fc;font-family:PingFangSC-Regular;box-shadow:0 2px 4px 0 rgba(0,0,0,0.28);border-radius:2px;width:80px;height:30px;border: none;padding: 0;">验证</el-button>
            </el-form-item>
            <el-form-item label="昵称" prop="name" label-width="66px">
              <el-input v-model="personalDataForm.name" placeholder="陶陶" style="width:208px;"></el-input>
            </el-form-item>
            <el-form-item label="性别" prop="sex" label-width="66px">
              <el-radio-group v-model="personalDataForm.sex">
                <el-radio label="man">男</el-radio>
                <el-radio label="woman">女</el-radio>
                <el-radio label="secret">保密</el-radio>
              </el-radio-group>
            </el-form-item>
            <el-form-item label="生日" label-width="66px">
              <el-date-picker :editable="false" class="birthday-input" placeholder="1994年12月30日" :clearable="false" type="date" v-model="personalDataForm.birthday" style="width: 160px;font-size: 16px;font-family:PingFangHK-Semibold;color: #000000;"></el-date-picker>
            </el-form-item>
            <el-button type="primary" @click="submitForm('personalDataForm')">保存</el-button>
          </el-form>
        </div>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getUser, putUser } from 'api/user';
  import { UploaderBuilder, Uploader } from 'qiniu4js';
  import { getToken } from 'api/qiniu';

  var avatarImgKey = '';
  var uploader = new UploaderBuilder()
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
      onTaskSuccess(task){
        //一个任务上传成功后回调
        console.log(task.result.key);//文件的key
        avatarImgKey=task.result.key;
        document.getElementById("avatar").setAttribute("src",process.env.QINIU_DOMAIN_URL+avatarImgKey);
      },onTaskFail(task) {
        //一个任务在经历重传后依然失败后回调此函数
        
      },onTaskRetry(task) {
        //开始重传
        
      },onFinish(tasks){
          //所有任务结束后回调，注意，结束不等于都成功，该函数会在所有HTTP上传请求响应后回调(包括重传请求)。
        
      }
    }).build();
    
  export default {
    name: 'personalData-container',
    data() {
      // element-ui validator
      /*var validateName = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入昵称'));
        } else if (value.length < 2 || value.length > 15) {
          callback(new Error('长度在 2 到 15 个字符'));
        }
      };
      var validateSex = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请选择性别'));
        }
      };*/
      return {
        loading: false,
        userDetails: null,

        // form part
        personalDataForm: {
          birthday: '',
          sex: '',
          mail_checked: '',
          avatar_url: '',
          mail: '',
          name: '',
          code: '',
        },
        personalDataRules: {
          /*name: [
            { validator: validateName, trigger: 'blur' }
          ],
          sex: [
            { validator: validateSex, trigger: 'blur' }
          ],*/
        },
        
        labelPosition: 'left'
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ])
    },
    mounted() {
      this.getUserDetails().then((res) => {
        console.dir(res)
      });
    },
    methods: {
      submitForm(formName) {
        console.log('submit')
        this.personalDataForm.avatar_url = process.env.QINIU_DOMAIN_URL+avatarImgKey
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.loading = true;
            putUser(this.user.userInfo.id, {
              name: this.personalDataForm.name,
              sex: this.personalDataForm.sex,
              birthday: this.personalDataForm.birthday,
              avatar_url: this.personalDataForm.avatar_url,
            }).then(res => {
              this.loading = false;
            })
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      },
      getUserDetails () {
        var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          getUser(self.user.userInfo.id).then(res => {
            self.personalDataForm = res;
            self.loading = false;
            resolve(res);
          }).catch(error => {
            self.loading = false;
            reject(error);
          });
        });
      },
      changeAvatar () {
        uploader.chooseFile();
      }
    },
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .personalData-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 900px;
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
        border-radius:2px;
        width:900px;
        height:657px;
        margin-left: 12px;
        padding-top:32px;
        position: relative;
        .personalDataUpLoader {
          width: 119px;
          max-width: 119px;
          text-align: center;
          margin-left: 20px;
          .avatar {
            width: 119px;
            height: 119px;
            box-shadow: 0 2px 4px 0 rgba(0,0,0,0.28);
            border-radius: 100%;
            margin-bottom: 3px;
          }
          .avatar-uploader {
            display: flex;
            justify-content: center;
            button {
              border: none;
              padding: 0;
              background-color: #ffffff;
              color: #5677fc;
              font-size: 14px;
              font-family: PingFangHK-Regular;
              display: flex;
              align-items: center;
              outline: none;
              .edit-icon {
                width: 14px;
                height: 14px;
                margin-right: 7px;
              }
            }
          }
        }
        .personalDataForm{
          font-size: 16px;
        }
        .el-form {
          position: absolute;
          right: 12px;
          top: 50px;
          .el-form-item {
            width: 683px;
            height: 80px;
            border-bottom: 1px solid #c6c7c7;
            margin: 0;
            display: flex;
            align-items: center;
          }
          > button{
            background:#5677fc;
            box-shadow:0 2px 4px 0 rgba(0,0,0,0.28);
            border-radius:2px;
            width:124px;
            height:30px;
            border: none;
            font-family:PingFangSC-Regular;
            font-size:12px;
            color:#ffffff;
            padding: 0;
            margin-top: 50px;
          }
        }
      }
    }
  }
</style>