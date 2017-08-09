<template>
  <div class="register-container">
  	<section>
  		<header>找回密码</header>
  		<div class="form-content">
  			<el-form label-width="100px" :model="loginForm" :rules="loginRules" ref="loginForm" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">
            <el-form-item label="邮箱" prop="email" class="form-inputy">
              <el-input v-model="loginForm.email" placeholder="输入邮箱"></el-input>
            </el-form-item>
            <el-form-item label-width="100px" class="form-btny">
              <el-button type="primary" @click="submitForm('loginForm')">找回密码</el-button>
            </el-form-item>
        </el-form>
  		</div>
  	</section>
  </div>
</template>
<script>
  import { mapGetters } from 'vuex';
  import { resetpsw } from 'api/auth';

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
      return {
        activeName: 'index-myplanet',
        loading: false,

        // form part
        loginForm: {
          email: '',
        },
        loginRules: {
          email: [
            { validator: validateUser, trigger: 'blur' }
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
          //console.log(this.loginForm.email+valid);
          if(valid){
            console.log(this.loginForm);
            //console.log(resetpsw(this.loginForm));
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
.register-container{
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
        height:400px;
        padding-top:40px;
      }
    }
}
</style>