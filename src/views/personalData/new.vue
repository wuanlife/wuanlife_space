<template>
  <div id="personal-data" class="personal-data view-container">
      <section>
          <h1>个人资料</h1>
      <div class="personal-data-form">
          <div class="form-left">
              <img src="" id="avatar">
              <button @click="changeAvatar"><icon-svg icon-class="modify" class="avatar-icon"></icon-svg>修改</button>
          </div>
          <div class="form-right">
              <div class="form-item">
                  <span>邮箱</span>
                  <p>659622323@qq.com</p>
              </div>
              <div class="form-item">
                  <span>昵称</span>
                  <input type="text" v-model="name">
              </div>
              <div class="form-item">
                  <span>性别</span>
                  <div class="form-item-sex">
                    <input type="radio" id="man" value="man" v-model="sex">
                    <label for="man" :class="[sex === 'man' ? 'label-active' : '']">男</label>
                    <input type="radio" id="woman" value="woman" v-model="sex">
                    <label for="woman" :class="[sex === 'woman' ? 'label-active' : '']">女</label>
                    <input type="radio" id="secrecy" value="secrecy" v-model="sex">
                    <label for="secrecy" :class="[sex === 'secrecy' ? 'label-active' : '']">不想透露</label>
                  </div>
              </div>
              <div class="form-item">
                  <span>生日</span>
                  <div class="form-item-date">
                      <date-picker
                        :min="1970"
                        :max="2018"
                        :defaultNum="yearNumber"
                        v-on:pick="year"
                        class="date-picker"></date-picker>年
                      <date-picker
                        :min="1"
                        :max="12"
                        :defaultNum="mouthNumber"
                        v-on:pick="mouth"
                        class="date-picker"></date-picker>月
                      <date-picker
                        :min="dayMin"
                        :max="dayMax"
                        :defaultNum="dayNumber"
                        v-on:pick="day"
                        class="date-picker"></date-picker>日
                  </div>
              </div>
          </div>
      </div>
      <button class="save">保存</button>
      </section>
  </div>
</template>

<script>
import DatePicker from 'components/DatePicker'
import { UploaderBuilder, Uploader } from 'qiniu4js'
import { getToken } from 'api/qiniu'

var avatarImgKey = '';
  var uploader = new UploaderBuilder()
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
      onTaskSuccess(task){
        //一个任务上传成功后回调
        console.log(task.result.key);//文件的key
        avatarImgKey=task.result.key;
        document.getElementById("avatar").setAttribute("src",process.env.QINIU_DOMAIN_URL+avatarImgKey);
      },onTaskFail(task) {
        //一个任务在经历重传后依然失败后回调此函数
        
      },onTaskRetry(task) {
        //开始重传
        
      },onFinish(tasks){
          //所有任务结束后回调，注意，结束不等于都成功，该函数会在所有HTTP上传请求响应后回调(包括重传请求)。
        
      }
    }).build();

export default {
  name: 'personalData',
  components: {
    DatePicker
  },
  data () {
    return {
      yearNumber: 1970,
      mouthNumber: 1,
      dayNumber: 1,
      dayMin: 1,
      dayMax: 31,
      leap: false,
      sex: '',
      name: '淘淘'
    }
  },
  computed: {
    birthday: {
      get: function () {
            return new Date(`${this.yearNumber}-${this.mouthNumber}-${this.dayNumber}`)
        },
      set: function (val) {}
    }
  },
  methods: {
    year: function (val) {
      this.yearNumber = val

      // 判断闰年
      let isLeap = this.yearNumber % 4 === 0 && this.yearNumber % 100 !== 0 || this.yearNumber % 400 === 0
      if (isLeap) {
        this.leap = true
      } else {
        this.leap = false
      }
    },
    mouth: function (val) {
      this.mouthNumber = val

      // 相应改变每月的天数
      if (this.mouthNumber === 1 || this.mouthNumber === 3 || this.mouthNumber === 5 || this.mouthNumber === 7 || this.mouthNumber === 8 || this.mouthNumber === 10 || this.mouthNumber === 12) {
        this.dayMax = 31
      } else {
        this.dayMax = 30
      }
      if (this.mouthNumber === 2) {
        if (this.leap === true) {
          this.dayMax = 29
        } else {
          this.dayMax = 28
        }
      }
    },
    day: function (val) {
      this.dayNumber = val
    },
    changeAvatar: function () {
      uploader.chooseFile()
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
#personal-data{
    min-width: 1152px;
    margin-top: 127px;
    margin-bottom: 36px;
    background-color: #fff;
    border-radius: 4px;
    padding: 69px 177px 109px 177px;
    text-align: center;
    section{
        h1{
            font-size: 32px;
            color: #5677fc;
            margin-bottom: 52px;
        }
        .personal-data-form{
            display: flex;
            justify-content: space-between;
            .form-left{
                img{
                    display: block;
                    height: 155px;
                    width: 156px;
                    border-radius: 100%;
                    background-color: rgb(165, 164, 164);
                    box-shadow: 0px 4px 5px 0px 
		rgba(181, 181, 181, 0.75);
                    margin-bottom: 13px;
                }
                button{
                    border: 0;
                    padding: 0;
                    font-size: 18px;
                    color: #5677fc;
                    background-color: transparent;
                    cursor: pointer;
                    .avatar-icon{
                        margin-right: 10px;
                    }
                }
            }
            .form-right{
                border-left: solid 2px #c9c9c9;
                padding-left: 45px;
                .form-item{
                    display: flex;
                    min-height: 70px;
                    align-items: center;
                    font-size: 28px;
                    color: #434343;
                    margin-bottom: 60px;
                    &:last-child{
                        margin-bottom: 0;
                    }
                    span{
                        margin-right: 51px;
                        font-size: 24px;
                        color: #434343;
                    }
                    &>input{
                        height: 70px;
                        width: 403px;
                        color: #434343;
                        box-shadow: 0px 3px 7px 0px 
	                	rgba(99, 99, 99, 0.16);
	                    border-radius: 4px;
	                    border: solid 2px rgba(171, 171, 171, 0.45);
                        padding: 0 25px; 
                    }
                    &>input:focus{
                        background-color: rgba(248, 249, 250, 0.4);
	                    box-shadow: 0px 3px 7px 0px 
	                	rgba(86, 119, 252, 0.16);
	                    border-radius: 4px;
	                    border: solid 2px rgba(0, 64, 185, 0.4);
                    }
                    .form-item-sex{
                        display: flex;
                        align-items: center;
                        justify-content: flex-start;
                        width: 403px;
                        input{
                            width: 0;
                            height: 0;
                        }
                        label{
                            width: 103px;
                            height: 70px;
                            line-height: 70px;
                            display: inline-block;
                            cursor: pointer;
                            background-color: rgba(248, 249, 250, 0.45);
	                        box-shadow: 0px 3px 7px 0px 
                    		rgba(99, 99, 99, 0.16);
	                        border-radius: 4px;
                        	border: solid 2px rgba(171, 171, 171, 0.45);
                            font-size: 28px;
                            color: #434343;
                            font-weight: normal;
                            margin-right: 48px;
                            &:last-child{
                                font-size: 22px;
                                margin-right: 0;
                            }
                        }
                        .label-active{
                            background-color: rgba(248, 249, 250, 0.4);
	                        box-shadow: 0px 3px 7px 0px 
                    		rgba(86, 119, 252, 0.16);
                        	border: solid 2px rgba(0, 64, 185, 0.4);
                        }
                    }
                    .form-item-date{
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        .date-picker{
                            margin-left: 15px;
                            z-index: 0;
                            &:first-child{
                                margin-left: 0;
                            }
                        }
                    }
                }
            }
        }
    }
}
.save{
    margin-top: 108px;
    width: 240px;
	height: 59px;
	background-color: #5677fc;
	border-radius: 4px;
    border: 0;
    cursor: pointer;
    font-size: 24px;
    color: #fff;
}
</style>
