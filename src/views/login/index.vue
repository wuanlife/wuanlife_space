<template>
  <div class="login-container view-container wl-card">
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
import { mapGetters, mapMutations } from 'vuex'
import { login, getAccessToken } from 'api/auth'
import { Notification } from 'element-ui'

// import fetch from 'utils/fetch'

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
        email: '',
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
    if (this.user.uid) {
      this.$router.push({ path: '/' })
    }
  },
  methods: {
    ...mapMutations({
      SET_USER: 'setUser'
    }),
    submitForm (formName) {
      // 如果已经登录
      if (this.user.id) {
        Notification.error({
          message: '已经登录，请先退出',
          offset: 60
        })
      }

      this.$refs[formName].validate(valid => {
        if (valid) {
          this.loading = true
          // 登录并且获取ID-Token
          const { clientId } = this.$route.query
          login({
            ...this.loginForm,
            // set client_id default: 'wuan'
            client_id: clientId || 'wuan'
          }).then(data => {
            // set idToken to cookies
            this.$cookie.set(`${clientId || 'wuan'}-id-token`, data['ID-Token'], 7)
            console.log('login->SET_USER')

            this.$store.commit('SET_USER', {
              ...data,
              ...JSON.parse(atob(data['ID-Token'].split('.')[1]))
            })
          }).then(getAccessToken)
            .then((result) => {
              this.$cookie.set(`${clientId || 'wuan'}-access-token`, result['Access-Token'], 7)
              this.loading = false
              this.$router.push('/')
              this.$store.commit('SET_USER', {
                ...result
              })
            })
            .catch(err => {
              Notification.error({
                message: err.data.error,
                offset: 60
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
  max-width: 518px;
  min-width: 380px;
  margin-top: 132px;
  section {
    flex: 1;
    header {
      float: left;
      margin-top: 12px;
      margin-left: 63px;
      font-size: 20px;
      color: #5677fc;
    }
    .people-circle {
      margin-top: 0;
      text-align: center;
      width: 81px;
      height: 98px;
      float: right;

      .people-circle-a {
        width: 49px;
        height: 98px;
        float: left;
        background-color: #5677fc;
        background: linear-gradient(-40deg, transparent 30px, #5677fc 0);
      }

      .people-circle-b {
        width: 32px;
        height: 98px;
        float: left;
        margin-left: -32px;
        background-color: #5677fc;
        background: linear-gradient(40deg, transparent 30px, #5677fc 0);
      }

      .peopleCircle_white-icon {
        width: 33px;
        height: 34px;
        float: left;
        color: white;
        margin-top: 22px;
        margin-left: -40px;
      }
    }
    .form-content {
      width: 100%;
      /*background: #C0C0C0;*/
      background: #ffffff;
      width: 518px;
      height: 391px;
      margin: 0 auto;
      justify-content: center;

      .el-form-item {
        margin-bottom: 0px;

        /deep/ .el-form-item__error {
          padding-top: 5px;
          height: 15px;
          font-size: 9px;
          color: #e60012;
        }
      }
      .el-input {
        width: 255px;

        /deep/ input {
          padding-left: 11px;
          font-size: 17px;
          height: 46px;
          color: #434343;
          background-color: rgba(248, 249, 250, 0.45);
          box-shadow: 0px 3px 7px 0px rgba(99, 99, 99, 0.16);
          border-radius: 4px;
          border: solid 2px rgba(171, 171, 171, 0.45);

          &:focus {
            background-color: rgba(248, 249, 250, 0.4);
            box-shadow: 0px 3px 7px 0px rgba(86, 119, 252, 0.16);
            border-radius: 4px;
            border: solid 2px rgba(0, 64, 185, 0.4);
          }

          &::-webkit-input-placeholder {
            font-size: 12px;
            color: #434343;
            margin-top: 17px;
          }
        }
      }

      .mail-input {
        width: 290px;
        margin: 0 auto;
        padding-top: 85px;

        .svg-icon {
          width: 25px;
          height: 21px;
          color: #5677fc;
          margin: 16px 0 16px -44px;
        }
      }

      .psw-input {
        width: 290px;
        margin: 0 auto;
        padding-top: 46px;

        .svg-icon {
          width: 25px;
          height: 21px;
          color: #5677fc;
          margin: 16px 0 16px -44px;
        }
      }

      .form-btny {
        width: 287px;
        height: 46px;
        padding-top: 43px;
        margin-left: 82px;
        button {
          padding: 0;
          width: 287px;
          height: 46px;
          background-color: #5677fc;
          border-radius: 4px;
          font-size: 15px;
          color: #ffffff;
        }
      }
    }
  }
}
</style>
