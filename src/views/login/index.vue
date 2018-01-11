<template>
  <div class="login-container view-container">
    <section>

      <header>登录</header>

      <div class="people-circle">
        <icon-svg icon-class="peopleCircle_white" class="peopleCircle_white-icon"></icon-svg>
      </div>

      <div class="form-content" v-loading="loading">



        <el-form :model="loginForm" :rules="loginRules" ref="loginForm" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">

          <div class="email-input">

            <icon-svg icon-class="email" class="youxiang-icon"></icon-svg>

            <el-form-item class="form-inputy" prop="email">
              <el-input v-model="loginForm.email" placeholder="输入邮箱"></el-input>
            </el-form-item>

          </div>

          <div class="psw-input">

            <icon-svg icon-class="code" class="mima-icon"></icon-svg>

            <el-form-item prop="password" class="form-inputy">
              <el-input type="password" v-model="loginForm.password" auto-complete="off" placeholder="输入密码"></el-input>
            </el-form-item>

          </div>

          <el-form-item class="form-btny">
            <el-button type="primary" :loading="loading" :disabled="loading" @click="submitForm('loginForm')">登录</el-button>
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
            { validator: validateEmail, trigger: 'blur' },
            { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur,change' }
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
    max-width: 828px;
    min-width: 380px;
    padding-top: 100px;

    section {

      flex: 1;

      header {
        margin: 20px auto 0;
        font-family: MicrosoftYaHei-Bold;
        font-size: 32px;
        color: #5677fc;
      }

      .people-circle {
        width: 78px;
        height: 157px;
        float: right;
        margin-top: -56px;
        margin-right: 51px;
        background-color: #5677fc;
        text-align: center;

        clip-path: polygon( 0 0, 78px 0, 78px 150px, 50px 127px, 0 157px);

        .peopleCircle_white-icon {
          width: 53px;
          height: 54px;
          color: white;
          margin-top: 35px;
        }
      }

      .form-content {
        width: 100%;
        background: #ffffff;
        border-radius: 4px;
        width: 660px;
        height: 625px;
        padding-top: 86px;
        margin: 0 auto;

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