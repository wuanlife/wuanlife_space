<template>
    <div class="inform-container">
      <section>     
        <el-tabs v-model="activeName" @tab-click="getInforms">
          <el-tab-pane label="帖子通知" name="inform-card">
            <div class="inform-tabcontent" v-loading="loading">
              <ul class="inform-cards">
                <li class="inform-card" v-for="item in cardPosts">
                  <div>
                    <img :src="item.message.image_url">
                    <div>
                      <span>{{ item.user.name }}</span>
                      <p><span>回复了主题帖</span><span>{{ item.post.title }}</span></p>
                    </div>
                    <button @click="$router.push({path: `/topic/${item.post.id}`, query: { name: item.post.title }})">查看</button>
                  </div>
                </li>
              </ul>
            </div>
          </el-tab-pane>
          <el-tab-pane label="星球通知" name="inform-planet">
            <div class="inform-tabcontent" v-loading="loading2">
              <ul class="inform-cards">
                <li class="inform-card" v-for="item in planetPosts">
                  <div>
                    <img :src="item.message.image_url">
                    <div>
                      <span>{{ item.user.name }}</span>
                      <p><span>退出</span><span>{{ item.group.name }}</span></p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </el-tab-pane>
          <el-tab-pane label="私密星球申请" name="inform-applyplanet">
            <div class="inform-tabcontent" v-loading="loading3">
              <ul class="inform-cards">
                <li class="inform-card" v-for="(item, index) in applyplanetPosts">
                  <div>
                    <img :src="item.message.image_url">
                    <div>
                      <p><span>{{ item.user.name }}</span><span>申请加入</span><span>{{ item.group.name }}</span></p>
                      <p>{{ item.message.text }}</p>
                    </div>
                    <div id="inform-applyplanet-btn" v-if="item.message.status ===1">
                      <button @click="dealApplication(index, true)">同意</button>
                      <button @click="dealApplication(index, false)">拒绝</button>
                    </div>
                    <div id="inform-applyplanet-btn" v-else-if="item.message.status ===2">
                      <span>已同意</span>
                    </div>
                    <div id="inform-applyplanet-btn" v-else-if="item.message.status ===3">
                      <span>已拒绝</span>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </el-tab-pane>
        </el-tabs>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getPostInform, dealApplyPlanetPost } from 'api/auth';

  export default {
    name: 'inform-container',
    data() {
      return {
        activeName: 'inform-card',
        loading: false,
        loading2: false,
        loading3: false,
        cardPosts: [],
        planetPosts: [],
        applyplanetPosts: [],
        userid: null,
        informType: 'post',
      }
    },
    created () {
      this.userid = this.$store.state.user.userInfo.id;
    },
    computed: {
      ...mapGetters([
        'user',
        'access_token',
      ])
    },
    mounted() {
      this.getInform();
    },
    methods: {
      getInforms: function (tab) {
        switch (tab.index){
          case '0':
            this.informType = 'post';
            this.loading = true;
        	break;
          case '1':
            this.informType = 'group';
            this.loading2 = true;
            break;
          case '2':
            this.informType = 'apply';
            this.loading3 = true;
            break;
          default:
            this.informType = 'post';
            this.loading = true;
            break;
        }
        var self = this;
        return new Promise((resolve, reject) => {
          getPostInform(self.userid, self.informType).then(res => {
            if (self.informType === 'post') {
              self.cardPosts = res.data;
            } else if (self.informType === 'group') {
              self.planetPosts = res.data;
            } else {
              self.applyplanetPosts = res.data;
            }
            self.loading = false;
            self.loading2 = false;
            self.loading3 = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
      getInform: function () {
        var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          getPostInform(self.userid, self.informType).then(res => {
            self.cardPosts = res.data;
            self.loading = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
      dealApplication: function (index, is_apply) {
        var self = this;
        let mid = this.applyplanetPosts[index].message.id;
        return new Promise((resolve, reject) => {
          dealApplyPlanetPost(self.userid, mid, is_apply).then(res => {
            alert(res.success);
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .inform-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    font-family:PingFangHK-Regular;
    max-width: 590px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 590px;
    }

  }
  .inform-tabcontent {
    min-height: 200px;
    margin-top: 5px;
  }
  // post card style    
  .inform-cards { 
    .inform-card {   
      padding: 10px;   
      background-color: #ffffff;
      border-radius: 4px;  
      &:not(:first-child) {
        margin-top: 8px;
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
          > span {
            color: #000000;
            width: 290px;
            font-family:PingFangHK-Medium;
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
          }
          > p {
            width: 300px;
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            span {
              &:not(:first-child) {
                  margin-left: 5px;
              }
            }
          }
          p:nth-child(1) {
            color: #5992e4;
            font-family:PingFangHK-Medium;
          }
          p:nth-child(2) {
            width: 300px;
            font-family:PingFangHK-Regular;
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
  #inform-applyplanet-btn{
    position: absolute;
    width: auto;
    right: 10px;
    height: 30px;
    font-size: 12px;
    button {
      color: #ffffff;
      background:#5677fc;
      box-shadow:0 2px 4px 0 rgba(0,0,0,0.28);
      border-radius:2px;
      width:80px;
      height:30px;
      border: none;
      padding: 0;
      position: initial;
      &:not(:first-child) {
          margin-left: 27px;
      }
    }
    span {
        color: #999999;
        display: flex;
        align-items: center;
        justify-content: center;
        width:80px;
        height:30px;
    }
  }
    
</style>
<style rel="stylesheet/scss" lang="scss">
  div.inform-container {
    section {
      .el-tabs {
        .el-tabs__header {
          .el-tabs__nav-wrap {
            .el-tabs__nav-scroll {
              .el-tabs__nav {
                div.el-tabs__item {
                  height: 30px;
                  line-height: 25px;
                  margin-top: 15px;
                }
                div.is-active {
                  font-family:PingFangHK-Semibold;
                }
              }
            }
          }
        }
      }
    }
  }
</style>
