<template>
  <div class="register-container view-container">
    <section>
      <header>注册</header>

      <div class="form-content" v-loading="loading">
        <el-form :model="loginForm" :rules="loginRules" ref="loginForm" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">


          <div class="email-input">
            <el-form-item prop="email" class="form-inputy">

              <el-input v-model="loginForm.email" placeholder="输入邮箱">
                <icon-svg icon-class="youxiang" class="youxiang-icon" slot="prefix"></icon-svg>
              </el-input>

            </el-form-item>
          </div>

          <div class="nickname-input">
            <el-form-item prop="nickname" class="form-inputy">



              <el-input auto-complete="off" v-model="loginForm.nickname" placeholder="输入昵称">
                <icon-svg icon-class="peopleCircle_white" class="peopleCircle_white-icon" slot="prefix"></icon-svg>
              </el-input>

            </el-form-item>
          </div>

          <div class="psw-input">
            <el-form-item prop="password" class="form-inputy">
              <el-input type="password" v-model="loginForm.password" auto-complete="off" placeholder="输入密码">
                <icon-svg icon-class="mima" class="mima-icon" slot="prefix"></icon-svg>
              </el-input>

            </el-form-item>
          </div>

          <el-form-item class="form-btny">

            <el-button type="primary" :loading="loading" :disabled="loading" @click="submitForm('loginForm')">注册</el-button>

          </el-form-item>

        </el-form>
      </div>
    </section>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex';

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
      return {
        activeName: 'index-myplanet',
        loading: false,

        // form part
        loginForm: {
          email: '',
          nickname: '',
          password: '',
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
      submitForm(formName){
        this.$refs[formName].validate((valid) => {
          if(valid){
            this.loading=true;
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
  .register-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 828px;
    min-width: 380px;

    section {
      flex: 1;

      header {
        text-align: center;
        margin: 44px auto 0;
        font-family: MicrosoftYaHei-Bold;
        font-size: 32px;
        color: #5677fc;
      }
      .form-content {
        width: 100%;
        background: #ffffff;
        border-radius: 4px;
        width: 660px;
        height: 650px;
        padding-top: 57px;
        margin: 0 auto;


        .email-input {
					width: 458px;
					margin: 0 auto 72px;
          padding-left: 56px;
				}
        .nickname-input {
          width: 458px;
          margin: 0 auto 72px;
          padding-left: 56px;
        }
        .psw-input {
          width: 458px;
          margin: 0 auto 72px;
          padding-left: 56px;
        }
        
        .el-form-item {
          justify-content: center;
        }
        .el-input /deep/ input {
          padding-left: 15px;
        }
        .el-input {
          width: 405px;
        }
        .youxiang-icon {
          width: 56px;
          height: 22px;
          color: #5677fc;
          margin-left: -56px;
        }
        .peopleCircle_white-icon {
          width: 56px;
          height: 22px;
          color: #5677fc;
          margin-left: -56px;
        }
        .mima-icon {
          width: 56px;
          height: 22px;
          color: #5677fc;
          margin-left: -56px;
        }
        .form-btny {
          text-align: center;

          button {
            width: 458px;
            /*height: 72px;*/
            background-color: #5677fc;
            border-radius: 4px;
            font-size: 24px;
            font-family: MicrosoftYaHeiLight;
          }
        }
      }
    }
  }
</style>