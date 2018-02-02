<template>
  <div class="login-container view-container">
    <section>
      <header>登录</header>
      <div class="people-circle">
        <div class="people-circle-a"></div>
        <div class="people-circle-b"></div>
        <icon-svg icon-class="peopleCircle_white" class="peopleCircle_white-icon"></icon-svg>
      </div>
      <div class="form-content">

        <el-form :model="loginForm" :rules="loginRules" ref="loginForm" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">
          <div class="mail-input">
            <el-form-item class="form-inputy" prop="mail">
              <el-input v-model="loginForm.mail" placeholder="输入邮箱" clearable>
                <icon-svg icon-class="youxiang" slot="prefix"></icon-svg>
              </el-input>
            </el-form-item>
          </div>
          <div class="psw-input">
            <el-form-item prop="password" class="form-inputy">
              <el-input type="password" v-model="loginForm.password" auto-complete="off" placeholder="输入密码" clearable>
                <icon-svg icon-class="mima" slot="prefix"></icon-svg>
              </el-input>
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
import { mapGetters } from 'vuex'
import { Notification } from 'element-ui'

export default {
  name: 'index-visitor',
  data () {
    // element-ui validator
    var validateMail = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请输入邮箱'))
      } else {
        callback()
      }
    }
    var validatePass = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请输入密码'))
      } else {
        callback()
      }
    }
    return {
      loading: false,

      // form part
      loginForm: {
        mail: '',
        password: ''
      },
      loginRules: {
        mail: [
          { validator: validateMail, trigger: 'blur' },
          {
            type: 'email',
            message: '请输入正确的邮箱地址',
            trigger: 'blur,change'
          }
        ],
        password: [{ validator: validatePass, trigger: 'blur' }]
      }
    }
  },
  computed: {
    ...mapGetters(['user'])
  },
  mounted () {
    // TODO: 支持redirect
    // 已登录则跳转到首页
    if (this.user.id) {
      this.$router.push({ path: '/index' })
    }
  },
  methods: {
    submitForm (formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.loading = true
          this.$store
            .dispatch('Login', {
              ...this.loginForm
            }).then(user => {
              this.loading = false
              this.$router.push({ path: '/' })
            }).catch(err => {
              Notification.error({
                message: err.data.error,
                offset: 100
              })
              this.loading = false
            })
        } else {
          console.log('error submit!!')
          return false
        }
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.login-container {
  display: flex;
  justify-content: space-between;
  margin: auto;
  max-width: 828px;
  min-width: 380px;
  padding-top: 101px;
  section {
    flex: 1;
    header {
      float: left;
      margin-top: 20px;
      margin-left: 101px;
      font-size: 32px;
      color: #5677fc;
    }
    .people-circle {
      margin-top: 0;
      text-align: center;
      width: 139px;
      height: 157px;
      float: right;

      .people-circle-a {
        width: 48px;
        height: 157px;
        float: left;
        background-color: #5677fc;
        background: linear-gradient(-40deg, transparent 29px, #5677fc 0);
      }

      .people-circle-b {
        width: 33px;
        height: 157px;
        float: left;
        margin-left: -3px;
        background-color: #5677fc;
        background: linear-gradient(30deg, transparent 30px, #5677fc 0);
      }

      .peopleCircle_white-icon {
        width: 53px;
        height: 54px;
        float: left;
        color: white;
        margin-top: 35px;
        margin-left: -66px;
      }
    }
    .form-content {
      width: 100%;
      /*background: #C0C0C0;*/
      background: #ffffff;
      width: 828px;
      height: 625px;
      margin: 0 auto;
      justify-content: center;

      .el-form-item {
        margin-bottom: 0px;

        /deep/ .el-form-item__error {
          padding-top: 10px;
          height: 15px;
          font-size: 14px;
          color: #e60012;
        }
      }
      .el-input {
        width: 408px;

        /deep/ input {
          padding-left: 18px;
          font-size: 28px;
          height: 72px;
          color: #434343;
          background-color: rgba(248, 249, 250, 0.35);
          box-shadow: 0px 3px 7px 0px rgba(99, 99, 99, 0.12);
          border-radius: 4px;
          border: solid 2px rgba(171, 171, 171, 0.35);

          &:focus {
            background-color: rgba(248, 249, 250, 0.4);
            box-shadow: 0px 3px 7px 0px rgba(86, 119, 252, 0.16);
            border-radius: 4px;
            border: solid 2px rgba(0, 64, 185, 0.4);
          }

          &::-webkit-input-placeholder {
            font-size: 20px;
            color: #434343;
            margin-top: 27px;
          }
        }
      }

      .mail-input {
        width: 464px;
        margin: 0 auto;
        padding-top: 136px;

        .svg-icon {
          width: 33px;
          height: 33px;
          color: #5677fc;
          margin: 19.5px 0 19.5px -88px;
        }
      }

      .psw-input {
        width: 464px;
        margin: 0 auto;
        padding-top: 76px;

        .svg-icon {
          width: 33px;
          height: 33px;
          color: #5677fc;
          margin: 19.5px 0 19.5px -88px;
        }
      }

      .form-btny {
        width: 458px;
        height: 142px;
        padding-top: 70px;
        margin-left: 132px;
        button {
          padding: 0;
          width: 458px;
          height: 72px;
          background-color: #5677fc;
          border-radius: 4px;
          font-size: 24px;
          color: #ffffff;
        }
      }
    }
  }
}
</style>
