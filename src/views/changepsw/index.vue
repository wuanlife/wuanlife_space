<template>
    <div class="register-container view-container">
        <section>

            <header>修改密码</header>
            <div class="form-content" v-loading="loading">

                <el-form label-width="100px" :model="loginForm" :rules="loginRules" ref="loginForm" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">

                    <!--原密码和新密码之间的转换有点难度-->
                    <el-form-item label="" prop="password" class="form-inputy">
                        <el-input type="password" v-model="loginForm.password" placeholder="原密码"></el-input>
                    </el-form-item>

                    <el-form-item label="" prop="password" class="form-inputy">
                        <el-input type="password" v-model="loginForm.password" placeholder="输入密码"></el-input>
                    </el-form-item>

                    <el-form-item label="" prop="confirmpassword" class="form-inputy">
                        <el-input type="password" v-model="loginForm.confirmpassword" auto-complete="off" placeholder="请再输入一遍"></el-input>
                    </el-form-item>
                    
                    <el-form-item label-width="100px" class="form-btny">
                        <el-button type="primary" :loading="loading" @click="submitForm('loginForm')">修改</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </section>
    </div>
</template>
<script>
  import { mapGetters } from 'vuex';
  import { changepassword } from 'api/auth';

  export default {
    name: 'index-visitor',
    data() {
      // element-ui validator
      var validatePassword = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入密码'));
        }else if(value.length < 6 || value.length>20){
          callback(new Error('请填写6-20位密码'));
        } else {
          callback();
        }
      };
      var validatePassword2 = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入密码'));
        }else if(value.length < 6 || value.length>20){
          callback(new Error('请填写6-20位密码'));
        }else if(value!==this.loginForm.password){
          callback(new Error('请确认新密码一致！'));
        } else {
          callback();
        }
      };
      return {
        activeName: 'index-myplanet',
        loading: false,

        // form part
        loginForm: {
          password: '',
          confirmpassword: '',
          token: '',
        },
        loginRules: {
          password: [
            { validator: validatePassword, trigger: 'blur' }
          ],
          confirmpassword: [
            { validator: validatePassword2, trigger: 'blur' }
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
          this.loginForm.token=this.$route.query.token;
          if(valid){
            this.loading = true;
            //resetpassword(this.loginForm);
            return new Promise((resolve, reject) => {
              resetpassword(this.loginForm).then(
                res => {
                  this.$message({
                    message: '修改密码成功!',
                    type: 'success',
                    duration: 2000,
                  });
                  this.$router.push({ path: '/login' });
                  this.loading = false;
                }).catch(error => {
                this.$message({
                  message: error.data.error,
                  type: 'error',
                  duration: 2000,
                });
                this.loading = false;
                reject(error);
              });
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
    .register-container {
        display: flex;
        justify-content: space-between;
        margin: auto;
        max-width: 660px;
        min-width: 380px;
        section {
            flex: 1;
            header {
                margin: 15px 0 20px 0;
                font-family: PingFangHK-Medium;
                font-size: 18px;
                color: #5677fc;
            }
            div.form-content {
                width: 100%;
                background: #ffffff;
                border-radius: 4px;
                width: 660px;
                height: 400px;
                padding-top: 40px;
            }
        }
    }
</style>