<template>
  <div class="register-container view-container">
    <section>
      

      <div class="form-content" v-loading="loading">
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
  import { mapGetters } from 'vuex';

  export default {
    name: 'signup',
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
        }else if(value.length < 6 || value.length > 18){
          callback(new Error('请输入6-18位字符作为昵称！'));
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
        loading: false,
        // form part
        signupForm: {
          mail: '',
          name: '',
          password: '',
        },
        signupRules: {
          mail: [
            { validator: validateUser, trigger: 'blur' }
          ],
          name: [
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
            this.$store.dispatch('Signup', {
							...this.signupForm
						}).then((user) => {
							this.loading = false;
							this.$router.push({ path: '/' })
						})
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
    padding-top: 101px;
    
    section {
      flex: 1;

      .form-content {
        width: 100%;
        /*background: #C0C0C0;*/
        background: #ffffff;
        width: 828px;
        height: 713px;
        margin: 0 auto;
        justify-content: center;

        header {
          margin: 0 auto;
          padding-top: 44px;
          font-size: 32px;
          color: #5677fc;
          text-align: center;
        }
        
        .el-form-item {
					margin-bottom: 0px;

					/deep/ .el-form-item__error {
						padding-top: 11px;
						height: 15px;
						font-size: 14px;
						color: #e60012;
					}
				}

				.el-input {
					width: 405px;

					/deep/ input {
						padding-left: 18px;
						font-size: 28px;
						height: 71px;
						color: #434343;
						background-color: rgba(248, 249, 250, 0.45);
						box-shadow: -3px 0px 7px 0px  rgba(99, 99, 99, 0.16);
						border-radius: 4px;
						border: solid 2px rgba(171, 171, 171, 0.45);

						&:focus {
							background-color: rgba(248, 249, 250, 0.4);
							box-shadow: 0px 3px 7px 0px rgba(86, 119, 252, 0.14);
							border-radius: 4px;
							border: solid 2px rgba(0, 64, 185, 0.4);
						}

						&::-webkit-input-placeholder {
							font-size: 20px;
							color: #434343;
							margin-top: 26px;
						}
					}
				}

        .mail-input {
					width: 458px;
					margin: 0 auto;
          padding-top: 72px;
          padding-left: 53px;

          .svg-icon {
						width: 33px;
						height: 33px;
						color: #5677fc;
						margin: 19.5px 0 19.5px -88px;
					}
				}

        .name-input {
          width: 458px;
          margin: 0 auto;
          padding-top: 72px;
          padding-left: 53px;

          .svg-icon {
						width: 33px;
						height: 33px;
						color: #5677fc;
						margin: 19.5px 0 19.5px -88px;
					}
          
          .el-form-item__error {		
						color: #757575;
					}
        }

        .psw-input {
          width: 458px;
          margin: 0 auto;
          padding-top: 72px;
          padding-left: 53px;

          .svg-icon {
						width: 33px;
						height: 33px;
						color: #5677fc;
						margin: 19.5px 0 19.5px -88px;
					}
        }

        .form-btny {
          text-align: center;
          width: 458px;
					height: 142px;
					padding-top: 71px;
					margin: 0 auto;

          button {
            padding: 0;
            width: 458px;
            height: 71px;
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