<!-- 
  我的收藏页面
 -->

<template>
  <div id="" class="collection-container">
    <section>
      <header>我的收藏</header>
    </section>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getCollectPost } from 'api/post';

  export default {
    name: 'collection-container',
    data() {
      return {
        collecations: [],
        loading: false,
      }
    },
    computed: {
      ...mapGetters([
        'user',
        'access_token',
      ])
    },
    mounted() {
      this.getCollectPosts();
    },
    methods: {
      getCollectPosts () {
        /*var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          getCollectPost(self.user.userInfo.id).then(res => {
            for (let i  = 0, j = res.data.length; i < j; i++) {
              res.data[i].create_time = self.dealTime(res.data[i].create_time);
            }
            self.collecations = res.data;
            self.loading = false;
            resolve();
          }).catch(reeor => {
            self.loading = false;
            reject(error);
          })
        })*/
      },
      dealTime (time) {
        return time.slice(0, 10) + ' ' + time.slice(11, 16);
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .collection-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 714px;
    min-width: 714px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 714px;
      header {
        width: 714px;
        height: 66px;
        margin: 88px 0 20px 0;
        padding-left:17px;
        font-family: MicrosoftYaHei-Bold;
        font-size: 32px;
        font-weight: normal;
        line-height:66px;
        color: #5677fc;
        background-color: #ffffff;
        border-radius: 4px;
      }
    }
  }
  .collection-tabcontent {
    min-height: 200px;
    margin-top: 5px;
  }
  // post card style    
  .collection-cards { 
    .collection-card {   
      padding: 16px 16px 12px 16px;   
      background-color: #ffffff;
      border-radius: 8px;  
      &:not(:first-child) {
        margin-top: 8px;
      }  
      footer {    
        display: flex;    
        align-items: center;
        font-size:12px;   
        color:#999999;
        position: relative;
        font-family:PingFangHK-Medium;
        div {
          position: absolute;
          right: 0;
        }
        span {    
          cursor: ppointer;
          &:not(:first-child) {   
            margin-left: 5px;   
          }
        }
        > .collection-card-plantname{
          cursor: pointer;
          transition: all 0.2s ease-in-out;
          &:hover {
            color: #5677fc;
          }
        }
      }
      div.collection-card-content {
        margin-bottom: 12px;
        h1 {
          margin-bottom: 6px;
          color: #003585;
          font-family:PingFangHK-Semibold;
          font-size:16px;
          opacity: 0.87;
          cursor: pointer;
          display: inline-block;
          position: relative;
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
          font-family:PingFangHK-Medium;
        }
        div.preview-imgs {
          display: flex;
          ul {
            li {
              display: inline-block;
              box-sizing: border-box;
              img {
                width: 174px;
                height: 174px;
                margin-right: 15px;
                background:#d8d8d8;
                border-radius:4px;
                align-self: center;
              }
            }
          }
        }
      }
    }   
  }
</style>
