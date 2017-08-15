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
            <img :src="personalDataForm.avatar_url" class="avatar">
            <div class="avatar-uploader">
              <button @click="changeAvatar"><icon-svg icon-class="edit" class="edit-icon"></icon-svg>修改</button>
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
  import { uploader } from 'utils/uploader'

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
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.loading = true;
            putUser(this.user.userInfo.id, {
              name: this.personalDataForm.name,
              sex: this.personalDataForm.sex,
              birthday: this.personalDataForm.birthday,
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
      },
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