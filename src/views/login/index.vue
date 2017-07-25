<template>
    <div class="login-container">
      <section>     
        <header>Login</header>
        <div class="form-content">
          <el-form :model="loginForm" :rules="loginRules" ref="loginForm" label-width="100px" class="demo-ruleForm">
            <el-form-item label="Email" prop="email">
              <el-input v-model.number="loginForm.email"></el-input>
            </el-form-item>
            <el-form-item label="Password" prop="password">
              <el-input type="password" v-model="loginForm.password" auto-complete="off"></el-input>
            </el-form-item>
            <el-form-item label-width="100px">
              <el-button type="primary" @click="submitForm('loginForm')">login</el-button>
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
        if (value === '') {
          callback(new Error('请再次输入Username'));
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
            { validator: validateUser, trigger: 'blur' }
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
        'token',
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
              console.log(err);
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
      }
    }

  }
</style>
