<template>
    <div class="collection-container">
      <section>     
        <header>
            我的收藏
        </header>
        <div class="collection-tabcontent" v-loading="loading">
          <ul class="collection-cards">
            <li class="collection-card" v-for="item in collecations">
              <div class="collection-card-content">
                <h1>{{ item.title }}</h1>
                <div class="preview-html">
                  {{ item.content }}
                </div>
                <div class="preview-imgs">
                  <ul>
                  	<li v-for="imgs in item.image_url"><img v-bind:src="imgs" /></li>
                  </ul>
                </div>
              </div>
              <footer>
                <span>{{ item.group.name }}</span>
                <div>
                  <span>收藏于</span>
                  <time>{{ item.create_time }}</time>
                </div>
              </footer>
            </li>
          </ul>
        </div>
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
        var self = this;
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
        })
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
    max-width: 590px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 590px;
      header {
        margin: 15px 0 20px 0;
        font-family:PingFangHK-Medium;
        font-size:18px;
        color:#5677fc;
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
          &:not(:first-child) {   
            margin-left: 5px;   
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
