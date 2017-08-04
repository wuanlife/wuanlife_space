<template>
    <div class="personalData-container">
      <section>     
        <header>个人资料</header>
        <div class="form-content">
          <div class="personalDataUpLoader">
            <img :src="imageUrl" class="avatar">
            <el-upload
              class="avatar-uploader"
              action="https://jsonplaceholder.typicode.com/posts/"
              :show-file-list="false"
              :on-success="handleAvatarSuccess"
              :before-upload="beforeAvatarUpload">
              <button><img />修改</button>
            </el-upload>
          </div>
          <el-form :model="personalDataForm" :rules="personalDataRules" ref="personalDataForm" label-width="100px" class="demo-ruleForm">
            <el-form-item label="邮箱" prop="email" class="personal-data-form-inputy">
              <span>506301700@qq.com</span>
              <el-button @click.prevent="removeDomain(domain)">验证</el-button>
            </el-form-item>
            <el-form-item label="昵称" prop="name" class="personal-data-form-inputy">
              <el-input v-model="personalDataForm.name" placeholder="陶陶"></el-input>
            </el-form-item>
            <el-form-item label="性别" prop="resource" class="personal-data-form-inputy">
              <el-radio-group v-model="personalDataForm.resource">
              <el-radio label="男"></el-radio>
              <el-radio label="女"></el-radio>
              </el-radio-group>
            </el-form-item>
            <el-button type="primary" @click="submitForm('personalDataForm')">保存</el-button>
          </el-form>
        </div>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'personalData-container',
    data() {
      // element-ui validator
      var validateName = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请输入昵称'));
        } else if (value.length < 3 || value.length > 6) {
          callback(new Error('长度在 3 到 6 个字符'));
        }
      };
      var validateResource = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请选择性别'));
        }
      };
      return {
        activeName: 'index-myplanet',
        loading: false,
        imageUrl: '',

        // form part
        personalDataForm: {
          email: '',
          name: '',
          resource: '',
        },
        personalDataRules: {
          name: [
            { validator: validateName, trigger: 'blur' }
          ],
          resource: [
            { validator: validateResource, trigger: 'blur' }
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
            this.$store.dispatch('SetInfo', this.personalDataForm).then(() => {
              this.loading = false;
                // this.showDialog = true;
            }).catch(err => {
              console.dir(err)
              this.$message({
                message: err.error,
                type: 'error',
                duration: 1000,
              });
              this.loading = false;
            });
          } else {
            console.log('error submit!!');
            return false;
          }
        });
      },
      handleAvatarSuccess(res, file) {
        this.imageUrl = URL.createObjectURL(file.raw);
      },
      beforeAvatarUpload(file) {
        const isJPG = file.type === 'image/jpeg';
        const isLt2M = file.size / 1024 / 1024 < 2;

        if (!isJPG) {
          this.$message.error('上传头像图片只能是 JPG 格式!');
        }
        if (!isLt2M) {
          this.$message.error('上传头像图片大小不能超过 2MB!');
        }
        return isJPG && isLt2M;
      }
    },
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .personalData-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 900px;
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
        border-radius:2px;
        width:900px;
        height:657px;
        margin-left: 12px;
        padding-top:32px;
        position: relative;
        .personalDataUpLoader {
          width: 119px;
          max-width: 119px;
          text-align: center;
          margin-left: 20px;
          .avatar {
            width: 119px;
            height: 119px;
            box-shadow: 0 2px 4px 0 rgba(0,0,0,0.28);
            border-radius: 100%;
            margin-bottom: 3px;
          }
          .avatar-uploader {
            button {
              border: none;
              padding: 0;
              background-color: #ffffff;
              color: #5677fc;
              font-size: 14px;
              font-family: PingFangHK-Regular;
              display: flex;
              align-items: center;
              outline: none;
              img {
                width: 14px;
                height: 14px;
                margin-right: 7px;
              }
            }
          }
        }
        .el-form {
          position: absolute;
          right: 12px;
          top: 50px;
          .el-form-item {
            width: 683px;
            height: 80px;
            border-bottom: 1px solid #c6c7c7;
            margin: 0;
          }
          > button{
            background:#5677fc;
            box-shadow:0 2px 4px 0 rgba(0,0,0,0.28);
            border-radius:2px;
            width:124px;
            height:30px;
            border: none;
            font-family:PingFangSC-Regular;
            font-size:12px;
            color:#ffffff;
            padding: 0;
            margin-top: 50px;
          }
        }
      }
    }

  }
</style>
