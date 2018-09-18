<template>
  <div class="register-container view-container">
    <section>

      <div class="form-content  wl-card" v-loading="loading">
        <header>注册</header>
        <el-form :model="signupForm" :rules="signupRules" ref="signupForm" class="demo-ruleForm" @keyup.enter.native="submitForm('signupForm')">

          <div class="mail-input">
            <el-form-item prop="mail" class="form-inputy">

              <el-input v-model="signupForm.mail" placeholder="输入邮箱" clearable>
                <icon-svg icon-class="youxiang" class="youxiang-icon" slot="prefix"></icon-svg>
              </el-input>

            </el-form-item>
          </div>

          <div class="name-input">
            <el-form-item prop="name" class="form-inputy">

              <el-input auto-complete="off" v-model="signupForm.name" placeholder="输入昵称" clearable>
                <icon-svg icon-class="peopleCircle_white" class="peopleCircle_white-icon" slot="prefix"></icon-svg>
              </el-input>

            </el-form-item>
          </div>

          <div class="psw-input">
            <el-form-item prop="password" class="form-inputy">
              <el-input type="password" v-model="signupForm.password" auto-complete="off" placeholder="输入密码" clearable>
                <icon-svg icon-class="mima" class="mima-icon" slot="prefix"></icon-svg>
              </el-input>

            </el-form-item>
          </div>

          <el-form-item class="form-btny">

            <el-button type="primary" :loading="loading" :disabled="loading" @click="submitForm('signupForm')">注册</el-button>

          </el-form-item>

        </el-form>
      </div>
    </section>
  </div>
</template>
<script>
import { mapGetters } from 'vuex'
import { signup, getAccessToken } from 'api/auth'
import { Notification } from 'element-ui'

export default {
  name: 'signup',
  data () {
    // element-ui validator
    var validateUser = (rule, value, callback) => {
      var myreg = /^([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|_|.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/
      if (value === '') {
        callback(new Error('请输入邮箱'))
      } else if (!myreg.test(value)) {
        callback(new Error('请填写正确的邮箱格式！'))
      } else {
        callback()
      }
    }
    var validateName = (rule, value, callback) => {
      var myregName = /^[0-9a-zA-Z\u4E00-\u9FA5_]*$/
      if (value === '') {
        callback(new Error('请输入昵称'))
      } else if (value.length < 6 || value.length > 18) {
        callback(new Error('请输入6-18位字符作为昵称！'))
      } else if (!myregName.test(value)) {
        callback(new Error('只允许中文、数字、字母和下划线！'))
      } else {
        callback()
      }
    }
    var validatePass = (rule, value, callback) => {
      if (value === '') {
        callback(new Error('请输入密码'))
      } else if (value.length < 6 || value.length > 20) {
        callback(new Error('请填写6-20位密码'))
      } else {
        callback()
      }
    }
    return {
      loading: false,
      // form part
      signupForm: {
        email: '',
        name: '',
        password: ''
      },
      signupRules: {
        mail: [{ validator: validateUser, trigger: 'blur' }],
        name: [{ validator: validateName, trigger: 'blur' }],
        password: [{ validator: validatePass, trigger: 'blur' }]
      }
    }
  },
  computed: {
    ...mapGetters(['user'])
  },
  mounted () {},
  methods: {
    submitForm (formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.loading = true
          // 获取ID-Token
          const { clientId } = this.$route.query
          signup({
            ...this.signupForm,
            // set client_id default: 'wuan'
            client_id: clientId || 'wuan'
          }).then(data => {
            // set idToken to cookies
            this.$cookie.set(`${clientId || 'wuan'}-id-token`, data['ID-Token'], 7)
            // 获取Access-Token
            this.$store.commit('SET_USER', {
              ...JSON.parse(atob(data['ID-Token'].split('.')[1]))
            })
          })
            .then(getAccessToken)
            .then((result) => {
              this.$cookie.set(`${clientId || 'wuan'}-access-token`, result['Access-Token'], 7)
              this.loading = false
              this.$router.push('/')
            }).catch(err => {
              Notification.error({
                message: err,
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
.register-container {
  display: flex;
  justify-content: space-between;
  margin: auto;
  max-width: 518px;
  min-width: 380px;
  padding-top: 132px;

  section {
    flex: 1;

    .form-content {
      width: 100%;
      /*background: #C0C0C0;*/
      background: #ffffff;
      width: 518px;
      height: 446px;
      margin: 0 auto;
      justify-content: center;

      header {
        margin: 0 auto;
        padding-top: 27px;
        font-size: 20px;
        color: #5677fc;
        text-align: center;
      }

      .el-form-item {
        margin-bottom: 0px;

        /deep/ .el-form-item__error {
          padding-top: 6px;
          height: 16px;
          font-size: 8px;
          color: #e60012;
        }
      }

      .el-input {
        width: 254px;

        /deep/ input {
          padding-left: 11px;
          font-size: 15px;
          height: 45px;
          color: #434343;
          background-color: rgba(248, 249, 250, 0.4);
          box-shadow: 0px 3px 7px 0px rgba(99, 99, 99, 0.16);
          border-radius: 4px;
          border: solid 2px rgba(171, 171, 171, 0.4);

          &:focus {
            background-color: rgba(248, 249, 250, 0.4);
            box-shadow: 0px 3px 7px 0px rgba(86, 119, 252, 0.16);
            border-radius: 4px;
            border: solid 2px rgba(0, 64, 185, 0.4);
          }

          &::-webkit-input-placeholder {
            font-size: 12px;
            color: #434343;
            margin-top: 16px;
          }
        }
      }

      .mail-input {
        width: 287px;
        margin: 0 auto;
        padding-top: 35px;
        padding-left: 33px;

        .svg-icon {
          width: 25px;
          height: 21px;
          color: #5677fc;
          margin: 16px 0 16px -44px;
        }
      }

      .name-input {
        width: 287px;
        margin: 0 auto;
        padding-top: 45px;
        padding-left: 33px;

        .svg-icon {
          width: 25px;
          height: 21px;
          color: #5677fc;
          margin: 16px 0 16px -44px;
        }

        .el-form-item__error {
          color: #757575;
        }
      }

      .psw-input {
        width: 287px;
        margin: 0 auto;
        padding-top: 45px;
        padding-left: 33px;

        .svg-icon {
          width: 25px;
          height: 21px;
          color: #5677fc;
          margin: 16px 0 16px -44px;
        }
      }

      .form-btny {
        text-align: center;
        width: 288px;
        height: 45px;
        padding-top: 44px;
        margin: 0 auto;

        button {
          padding: 0;
          width: 288px;
          height: 45px;
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
