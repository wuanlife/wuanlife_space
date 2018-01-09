<template>
    <div class="login-container">
      <section>     
        <header>登录</header>
        <div class="form-content" v-loading="loading">
          <el-form :model="loginForm" :rules="loginRules" ref="loginForm" label-width="100px" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">
            
            <icon-svg icon-class="email" class="youxiang-icon"></icon-svg>
            <el-form-item label="" prop="email" class="form-inputy">
              <el-input v-model="loginForm.email" placeholder="输入邮箱"></el-input>
            </el-form-item>
            
            <icon-svg icon-class="code" class="mima-icon"></icon-svg>
            <el-form-item label="" prop="password" class="form-inputy">
              <el-input type="password" v-model="loginForm.password" auto-complete="off" placeholder="输入密码"></el-input>
            </el-form-item>
            
            <el-form-item label-width="100px" class="form-btny">
              <el-button type="primary" :loading="loading" :disabled="loading" @click="submitForm('loginForm')">登录</el-button>
            </el-form-item>
          </el-form>

          <!--<a href="#/signup/" id="register">注册账号</a>
          <a href="#/findpassword/" id="findpassword">找回密码</a>-->
          
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
      var validateEmail = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入邮箱'));
        } else {
          callback();
        }
      };
      var validatePass = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入密码'));
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
          password: '',
        },
        loginRules: {
          email: [
            { validator: validateEmail, trigger: 'blur' }
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
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            this.loading = true;
            this.$store.dispatch('Login', this.loginForm).then(() => {
              this.loading = false;
              this.$router.push({ path: '/' });
                // this.showDialog = true;
            }).catch(err => {
              console.dir(err)
              this.$message({
                message: err.data.error,
                type: 'error',
                duration: 2000,
              });
              this.loading = false;
            });
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      },
    },
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .login-container {
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
        height:500px;
        padding-top:40px;
        #register{
          font-family:PingFangHK-Medium;
          font-size:14px;
          color:#5677fc;
          margin-left:144px;
        }
        #findpassword{
          font-family:PingFangHK-Medium;
          font-size:14px;
          color:#5677fc;
          margin-left:260px;
        }
      }
    }

  }
</style>
