<template>
  <div class="register-container">
  	<section>
  		<header>创建星球</header>
  		<div class="form-content">
  			<el-form label-width="100px" :model="loginForm" :rules="loginRules" ref="loginForm" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">
            <el-form-item label="星球名称" prop="email" class="form-inputc">
              <el-input v-model="loginForm.email" placeholder="星球名称" class="name"></el-input>
            </el-form-item>
            <div>星球头像(选填)</div>
            <el-upload
              class="upload-demo upload-c"
              action="https://jsonplaceholder.typicode.com/posts/"
              :on-preview="handlePreview"
              :on-remove="handleRemove"
              :file-list="fileList">
              <el-button size="small" type="primary">+</el-button>
            </el-upload>
            <el-form-item label="星球介绍(选填)" class="form-inputc">
              <el-input type="textarea" v-model="loginForm.inviteword" placeholder="星球介绍(选填)" class="intro"></el-input>
            </el-form-item>
            <el-form-item label="加入星球方式" class="form-inputc">
              <el-select v-model="loginForm.inviteword" placeholder="加入星球方式" class="join-c">
                <el-option label="允许任何人加入" value="0"></el-option>
                <el-option label="验证水电费" value="1"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label-width="100px" class="form-inputc">
              <el-button type="primary" @click="submitForm('loginForm')">创建</el-button>
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
      var validateWord = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入邀请码'));
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
          inviteword: '',
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
          inviteword: [
            { validator: validateWord, trigger: 'blur' }
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
            this.$store.dispatch('Signup',this.loginForm).then(() => {
              this.loading = false;
              this.$router.push({ path: '/' });
            }).catch(err => {
              console.dir(err)
              this.$message({
                message: err.error,
                type: 'error',
                duration: 1000,
              });
              this.loading = false;
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
        height:699px;
        padding-top:40px;
        padding-left:30px;
      }
    }
}
</style>