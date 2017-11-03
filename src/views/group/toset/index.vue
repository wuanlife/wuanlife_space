<template>
    <div class="set-container">
      <section>
        <el-tabs v-model="activeName" @tab-click="tabChange">
          <el-tab-pane v-if="!myplanetsPosts || myplanetsPosts.length > 0" label="星球资料" name="set-myplanet">
            <div class="set-tabcontent" v-loading="loading_myplanet">
              <div class="form-content">
        <el-form label-width="100px" :model="loginForm" :rules="loginRules" ref="loginForm" class="demo-ruleForm" @keyup.enter.native="submitForm('loginForm')">
          <el-form-item label="星球名称" prop="name" class="form-inputc">
            <p class="name">{{ loginForm.name }}</p>
          </el-form-item>
          <div>星球头像(选填)</div>
          <button id="upload-img" class="img-upload" @click="upload"><span>+</span></button>
          <el-form-item label="星球介绍(选填)" prop="introduction" class="form-inputc">
            <el-input type="textarea" v-model="loginForm.introduction" placeholder="星球介绍(选填)" class="intro"></el-input>
          </el-form-item>
          <el-form-item label="加入星球方式" class="form-inputc">
            <el-select v-model="loginForm.private" placeholder="加入星球方式" class="join-c">
              <el-option label="允许任何人加入" value="false"></el-option>
              <el-option label="需要申请加入" value="true"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label-width="100px" class="form-inputc">
            <el-button type="primary" @click="submitForm('loginForm')">保存</el-button>
          </el-form-item>
        </el-form>
      </div>
            </div>
          </el-tab-pane>
          <el-tab-pane label="成员管理" name="set-mumber">
            <div class="set-tabcontent" v-loading="loading_mumber">
              <ul class="set-cards">
                <li class="set-card" v-show="item.remove" v-for="(item, index) in mumbers">
                  <div>
                    <img :src="item.avatar_url">
                    <div>
                      <span>{{ item.name }}</span>
                    </div>
                    <button @click="removeMumbers(index, item.id)">移除</button>
                  </div>
                </li>
              </ul>
              <el-pagination layout="prev, pager, next, jumper"
                             :page-count="pagination_newtopic"
                             @current-change="loadPosts_newtopic">
              </el-pagination>
            </div>
          </el-tab-pane>
        </el-tabs>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { parseTime } from 'utils/date';
  import { parseQueryParams } from 'utils/url';
  import { getPostsByGroupId, approvePost, collectPost } from 'api/post';
  import { joinGroup, quitGroup, getGroup, setGroup, getGroupMumbers, deleteGroupMumbers } from 'api/group';
  import { getToken } from 'api/qiniu'
  import { UploaderBuilder, Uploader } from 'qiniu4js';

  import PostCard from 'components/PostCard'
  
  // qiniu4js uploader object
  var urlkey='';
  var uploader = new UploaderBuilder()
    .debug(false)//开启debug，默认false
    .domain({http: "http://upload.qiniu.com", https: "https://up.qbox.me"})
    .retry(2)//设置重传次数，默认0，不重传
    .compress(0.5)//默认为1,范围0-1
    .scale([200,0])  //第一个参数是宽度，第二个是高度,[200,0],限定高度，宽度等比缩放.[0,100]限定宽度,高度等比缩放.[200,100]固定长宽
    .size(1024*1024)
    .chunk(true)
    .auto(true)
    .multiple(false)
    .accept(['.gif','.png','.jpg'])//过滤文件，默认无，详细配置见http://www.w3schools.com/tags/
    .tokenShare(true)
    .tokenFunc(function (setToken,task) {
      getToken().then(res => {
        setToken(res.uploadToken);
      })
    })
    //任务拦截器
    .interceptor({
        //拦截任务,返回true，任务将会从任务队列中剔除，不会被上传
      onIntercept: function (task) {
        return task.file.size > 4 * 1024 * 1024;
      },
      //中断任务，返回true，任务队列将会在这里中断，不会执行上传操作。
      onInterrupt: function (task) {
        if (this.onIntercept(task)) {
          alert("请上传小于4m的文件");
          return true;
        }
        else {
          return false;
        }
      }
    }) 
    .listener({
      onReady(tasks) {
          //该回调函数在图片处理前执行,也就是说task.file中的图片都是没有处理过的
        //选择上传文件确定后,该生命周期函数会被回调。
        
      },onStart(tasks){
          //所有内部图片任务处理后执行
        //开始上传
        console.log('upload start')
        
      },onTaskGetKey(task){
        
      },onTaskProgress: function (task) {
          //每一个任务的上传进度,通过`task.progress`获取
        console.log(task.progress);
        
      },onTaskSuccess(task){
        //一个任务上传成功后回调
        console.log(task.result.key);//文件的key
        urlkey=task.result.key;
        document.getElementById("upload-img").style.backgroundImage="url("+process.env.QINIU_DOMAIN_URL+urlkey+")";
      },onTaskFail(task) {
        //一个任务在经历重传后依然失败后回调此函数
        
      },onTaskRetry(task) {
        //开始重传
        
      },onFinish(tasks){
          //所有任务结束后回调，注意，结束不等于都成功，该函数会在所有HTTP上传请求响应后回调(包括重传请求)。
              
      }
    }).build();
    
  export default {
    name: 'group-set',
    data() {
      var validateName = (rule, value, callback) => {
        if (value.length<1 || value.length>80) {
          callback(new Error('星球长度1~80个字符'));
        } else {
          callback();
        }
      };
      var validateIntro = (rule, value, callback) => {
        if (value.length>50) {
          callback(new Error('星球介绍1~50个字符'));
        }else {
          callback();
        }
      };
      return {
        activeName: 'set-myplanet',
        loading_myplanet: false,
        loading_mumber: false,
        loginForm: {
          name: '',
          image_url: '',
          introduction: '',
          private: false,
        },
        loginRules: {
          name: [
            { validator: validateName, trigger: 'blur' }
          ],
          introduction: [
            { validator: validateIntro, trigger: 'blur' }
          ],
        },
        mumbers: [],
        pagination_newtopic: 1,
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ])
    },
    methods: {
      submitForm(formName){
        let groupId = this.$route.query.groupid;
        this.loginForm.image_url=process.env.QINIU_DOMAIN_URL+urlkey;
        this.loginForm.private=(this.loginForm.private=="true")?true:false;
        //console.log(this.loginForm.private);
        this.$refs[formName].validate((valid) => {
          if(valid){
            this.loading=true;
            setGroup(groupId, {
              introduction: this.loginForm.introduction,
              image_url: this.loginForm.image_url,
              private: this.loginForm.private
            })
              .then(
                res => {
                  //转到星球主页
                  this.loading = false;
                  this.$router.push({ path: `/planet/${groupId}`, query: { name: this.loginForm.name } });
                })
              .catch(error => {
                this.$message({
                  message: error.data.error,
                  type: 'error',
                  duration: 1000,
                });
              });
          }else{
            console.log('error submit!!');
            return false;
          }
        })
      },
      upload(event) {
        try {
          uploader.chooseFile();
          event.stopPropagation();
        } catch (error) {
          this.$message({
            message: error,
            type: 'error',
            duration: 1000,
          });
        }
      },
      getMumbers() {
        let groupId = this.$route.query.groupid;
        this.loading_mumbers = true;
        getGroupMumbers(groupId, {
          limit: 20,
          offset: 0
        }).then(response => {
          if (response.data !== undefined) {
            response.data.forEach(function (val) {
              val.remove = true;
            });
            this.mumbers = response.data;
            let pageFinal = parseQueryParams(response.paging.final);
            this.pagination_newtopic = (pageFinal.offset / pageFinal.limit) + 1;
          }
        }).catch((err) => {
        console.dir(err);
        this.$message({
          message: err.error,
          type: 'error',
          duration: 1000,
        });
        this.loading_mumbers = false;
        })
      },
      loadPosts_newtopic(page) {
        let groupId = this.$route.query.groupid;
        this.loading_mumbers = true;
        getGroupMumbers(groupId, {
          limit: 20,
          offset: (page-1)*20 || 0
        }).then(response => {
          response.data.forEach(function (val) {
            val.remove = true;
          });
          this.mumbers = response.data;
          let pageFinal = parseQueryParams(response.paging.final);
          this.pagination_newtopic = (pageFinal.offset / pageFinal.limit) + 1;
        }).catch((err) => {
        console.dir(err);
        this.$message({
          message: err.error,
          type: 'error',
          duration: 1000,
        });
        this.loading_mumbers = false;
        })
      },
      removeMumbers (index, mumberId) {
        let groupId = this.$route.query.groupid;
        deleteGroupMumbers(groupId, mumberId).then(rsponse => {
          
        }).catch((err) => {
        console.dir(err);
        this.$message({
          message: err.error,
          type: 'error',
          duration: 1000,
        });
        })
        this.mumbers[index].remove = false;
      }
    },
    mounted(){
      let groupId = this.$route.query.groupid;
      this.loading_myplanet = true;
      getGroup(groupId).then(response => {
        this.loginForm.name = response.name;
        this.loginForm.image_url = response.image_url;
        this.loginForm.introduction = response.introduction;
        this.loginForm.private = response.private;
        document.getElementById("upload-img").style.backgroundImage="url("+response.image_url+")";
      }).catch((err) => {
        console.dir(err);
        this.$message({
          message: err.error,
          type: 'error',
          duration: 1000,
        });
        this.loading_myplanet = false;
     })
      this.loading_myplanet = false;
      this.getMumbers();
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .set-container {
    display: flex;
    justify-content: center;
    margin: auto;
    max-width: 900px;
    min-width: 690px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 690px;
    }
  }
  .set-tabcontent {
    min-height: 200px;
    margin-top: 5px;
    margin-bottom: 20px;
    background-color: white;
    padding: 20px 30px;
    .el-pagination {
      text-align: center;
    }
    .form-content{
      width: 100%;
        background:#ffffff;
        border-radius:4px;
        width:660px;
        .img-upload{
          margin-top: 8px;
          margin-bottom:10px;
          background:#ffffff;
          box-shadow:0 2px 4px 0 rgba(0,0,0,0.28);
          border-radius:100px;
          width:80px;
          height:80px;
          background-image: url();
          background-repeat: no-repeat;
          background-size:100% 100%;
          span{
            font-size: 30px;
            color: #5677fc;
          }
        }
      
    }
    .set-cards { 
    .set-card {   
      padding: 10px;   
      background-color: #ffffff;
      &:not(:first-child) {
        border-top: 1px solid #D8D8D8;
      }
      div {    
        display: flex;    
        align-items: center;
        font-size:14px;   
        color:#666666;
        position: relative;    
        img {
          width: 50px;    
          height: 50px;   
          border-radius: 100%;    
          margin-right: 8px;   
        }
        div {
          height: 42px;
          display: block;
          width: 512px;
          line-height: 42px;
          > span {
            color: #000000;
            width: 290px;
            font-family:PingFangHK-Medium;
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
          }
        }
        button {
          font-size: 12px;
          font-family:PingFangSC-Regular;
          color: #ffffff;
          background:#5677fc;
          box-shadow:0 2px 4px 0 rgba(0,0,0,0.28);
          border-radius:2px;
          width:80px;
          height:30px;
          border: none;
          padding: 0;
          position: absolute;
          right: 28px;
        }
      }
    }  
  }
  }
  // post card style    
  .set-cards { 
    .index-card {   
      padding: 10px 16px 12px 16px;   
      background-color: #ffffff;  
      &:not(:first-child) {
        margin-top: 8px;
      }
      &:last-child {
        margin-bottom: 20px;
      }
      header {    
        display: flex;    
        align-items: center;    
        margin-bottom: 6px;
        font-size:12px;   
        color:#999999;
        & > .clickable {
          transition: all 0.2s ease-in-out;
          &:hover {
            color: #5677fc;
          }
        }
        img {
          width: 26px;    
          height: 26px;   
          border-radius: 100%;    
          margin-right: 10px;   
        }
        span {    
          &:not(:first-child) {   
            margin-left: 5px;   
          }
        }
        time {    
          margin-left: 12px;    
        }   
      }
      div.index-card-content {
        margin-bottom: 12px;
        h1 {
          display: inline-block;
          position: relative;
          cursor: pointer;
          margin-bottom: 6px;

          color: #2e5897;
          font-family:PingFangHK-Semibold;
          font-size:16px;
          // hover animation
          &::after {
            content: '';
            transition: all 0.5s ease-in-out;
            transform: scaleX(0);
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: 0;
            left: 0;
            background: #2e5897;
          }
          &:hover {
            &::after {
              transform: scaleX(1);
            }
          }
        }
        div.preview-html {
          margin-bottom: 12px;
          word-break: break-all;

          font-size:14px;
          color:#666666;
          letter-spacing:0;
          text-align:justify;
        }
        div.preview-imgs {
          display: flex;
          img {
            margin-right: 15px;
            width: 174px;
            height: 174px;
          }

        }
      }
      footer {
        ul {
          display: flex;
          li {

            transition: all 0.2s ease-in-out;
            cursor: pointer;
            color: #bcbcbc;
            &:not(:first-child):before{
              content:'\00B7';
              padding:0 8px;
            }
            &:hover {
              color: #a3b5fd;
            }
            &.done {
              color: #a3b5fd;
            }
          }
        }
      }  
    }   
  }

  // aside style
  .group-cards {
    display: flex;
    flex-wrap: wrap;
    
  }
  .group-card {
    margin-bottom: 8px;
    &:nth-child(odd) {
      margin-right: 18px;
    }
    button {
      background:#ffffff;
      border-radius:4px;
      border: none;
      padding: 8px 16px;
      width:116px;
      height:34px;
      transition: all 0.4s ease-in-out;
      text-overflow: ellipsis;
      overflow: hidden;
      white-space: nowrap;

      font-family:PingFangHK-Medium;
      font-size:12px;
      color:#5992e4;
      text-align: center;
      &:hover {
        background: #5992e4;
        color: #ffffff;
      }
      &:focus {
        outline: none;
      }
    }
  }
  .group-card-func {
    @extend .group-card;
    button {
      background: #5677fc;
      color: #ffffff;
    }
    
  }
  p.name{
    font-size: 26px;
    color:#666666;
    margin-left: -90px;
    margin-top: 10px;
  }
</style>
