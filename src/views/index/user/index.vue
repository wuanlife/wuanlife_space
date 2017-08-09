<template>
    <div class="index-visitor-container">
      <section>     
        <el-tabs v-model="activeName" @tab-click="handleClick">
          <el-tab-pane label="我的星球" name="index-myplanet">
            <div class="index-tabcontent" v-loading="loading_myplanet">
              <ul class="index-cards">
                <li v-for="post of myplanetsPosts_formatted" class="index-card">
                  <header>
                    <img :src="post.author.avatar_url">
                    <span class="clickable">{{ post.author.name }}</span>
                    <span>发表于</span>
                    <span class="clickable"
                      @click="$router.push({path: '/group/' + post.group.id})">
                      {{ post.group.name }}
                    </span>
                    <time>{{ post.create_time_formatted }}</time>
                  </header>
                  <div class="index-card-content">
                    <h1 @click="$router.push({path: `/post/${post.id}`})">{{ post.title }}</h1>
                    <div class="preview-html" v-html="post.content">
                    </div>
                    <div class="preview-imgs">
                      <img v-for="img of post.image_url" :src="img">
                    </div>
                  </div>
                  <footer>
                    <ul>
                      <li @click="$router.push({path: `/post/${post.id}`})" :class="{'done': post.replied}">评论 {{ post.replied_num }}</li>
                      <li @click="approve(post.id)" :class="{'done': post.approved}">点赞 {{ post.approved_num }}</li>
                      <li @click="collect(post.id)" :class="{'done': post.collected}">收藏 {{ post.collected_num }}</li>
                    </ul>
                  </footer>
                </li>  
              </ul>
              <el-pagination layout="prev, pager, next, jumper"
                             :page-count="pagination_myplanet.pageCount"
                             @current-change="loadPosts_myplanet">
              </el-pagination>
            </div>
          </el-tab-pane>
          <el-tab-pane label="最新话题" name="index-newtopic">
            <div class="index-tabcontent" v-loading="loading_newtopic">
              <ul class="index-cards">
                <li v-for="post of newtopicPosts_formatted" class="index-card">
                  <header>
                    <img :src="post.author.avatar_url">
                    <span class="clickable">{{ post.author.name }}</span>
                    <span>发表于</span>
                    <span class="clickable"
                      @click="$router.push({path: '/group/' + post.group.id})">
                      {{ post.group.name }}
                    </span>
                    <time>{{ post.create_time_formatted }}</time>
                  </header>
                  <div class="index-card-content">
                    <h1 @click="$router.push({path: `/post/${post.id}`})">{{ post.title }}</h1>
                    <div class="preview-html" v-html="post.content">
                    </div>
                    <div class="preview-imgs">
                      <img v-for="img of post.image_url" :src="img">
                    </div>
                  </div>
                  <footer>
                    <ul>
                      <li @click="$router.push({path: `/post/${post.id}`})" :class="{'done': post.replied}">评论 {{ post.replied_num }}</li>
                      <li @click="approve(post.id)" :class="{'done': post.approved}">点赞 {{ post.approved_num }}</li>
                      <li @click="collect(post.id)" :class="{'done': post.collected}">收藏 {{ post.collected_num }}</li>
                    </ul>
                  </footer>
                </li>  
              </ul>
              <el-pagination layout="prev, pager, next, jumper"
                             :page-count="pagination_newtopic.pageCount"
                             @current-change="loadPosts_newtopic">
              </el-pagination>
            </div>
          </el-tab-pane>
        </el-tabs>
      </section>
      <aside>
        <header>
          <h2>我加入的星球</h2>
        </header>
        <div class="aside-content">
          <ul class="group-cards">
            <li v-for="group of myGroups" class="group-card">
              <button @click="$router.push({path: `/group/${group.id}`})">{{ group.name }}</button>
            </li>
            <li class="group-card-func">
              <button @click="$router.push({path: `/index/`})">全部星球</button>
            </li>
            <li class="group-card-func">
              <button @click="$router.push({path: `/index/`})">创建星球</button>
            </li>
          </ul>
        </div>
      </aside>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { parseTime } from 'utils/date';
  import { parseQueryParams } from 'utils/url';
  import { 
    getPosts,
    approvePost,
    collectPost,
  } from 'api/post';
  import { getGroups } from 'api/group';

  export default {
    name: 'index-visitor',
    data() {
      return {
        activeName: 'index-myplanet',

        loading_myplanet: false,
        loading_newtopic: false,
        loadingAside: false,
        myplanetsPosts: [],
        newtopicPosts: [],
        pagination_myplanet: {
          pageCount: 1,
          currentPage: 1,
          limit: 20,
        },
        pagination_newtopic: {
          pageCount: 1,
          currentPage: 1,
          limit: 20,
        },

        // aside group
        myGroups: [],
      }
    },
    computed: {
      ...mapGetters([
        'user',
        'token',
      ]),
      myplanetsPosts_formatted: function() {
        if(this.myplanetsPosts.length === 0) {
          return [];
        }
        let newPosts = this.myplanetsPosts.slice(0);
        newPosts = newPosts.map((post) => {
          let newPost = post;
          newPost.create_time_formatted = parseTime(newPost.create_time, 'yyyy-MM-dd HH:mm')
          return newPost;
        })
        return newPosts;
      },
      newtopicPosts_formatted: function() {
        if(this.newtopicPosts.length === 0) {
          return [];
        }
        let newPosts = this.newtopicPosts.slice(0);
        newPosts = newPosts.map((post) => {
          let newPost = post;
          newPost.create_time_formatted = parseTime(newPost.create_time, 'yyyy-MM-dd HH:mm')
          return newPost;
        })
        return newPosts;
      },
    },
    mounted() {
      this.loadPosts_myplanet()
        .then()
        .catch((err) => {
          console.dir(err);
          this.$message({
            message: err.error,
            type: 'error',
            duration: 1000,
          });
          this.loading_myplanet = false;
        })
      this.loadPosts_newtopic()
        .then()
        .catch((err) => {
          console.dir(err);
          this.$message({
            message: err.error,
            type: 'error',
            duration: 1000,
          });
          this.loading_newtopic = false;
        })
      this.loadGroups()
        .then()
        .catch((err) => {
          console.dir(err);
          this.$message({
            message: err.error,
            type: 'error',
            duration: 1000,
          });
          this.loadingAside = false;
        });
    },
    methods: {
       // OPTIMIZE: there is a redundant code using ctrl+c to load newtopic and myplanet posts
       loadPosts_myplanet(page) {
        var self = this;
        this.loading_myplanet = true;
        console.log(`page is ${page}`)
        return new Promise((resolve, reject) => {
          getPosts(false,(page-1)*self.pagination_myplanet.limit || 0).then(res => {
            self.myplanetsPosts = res.data;
            self.loading_myplanet = false;

            // pagination
            let pageFinal = parseQueryParams(res.paging.final);
            self.pagination_myplanet.pageCount = (pageFinal.offset / pageFinal.limit) + 1;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
      loadPosts_newtopic(page) {
        var self = this;
        this.loading_newtopic = true;
        return new Promise((resolve, reject) => {
          getPosts(true,(page-1)*self.pagination_newtopic.limit || 0).then(res => {
            self.newtopicPosts = res.data;
            self.loading_newtopic = false;

            // pagination
            let pageFinal = parseQueryParams(res.paging.final);
            self.pagination_newtopic.pageCount = (pageFinal.offset / pageFinal.limit) + 1;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },    
      loadGroups() {
        var self = this;
        this.loadingAside = true;
        return new Promise((resolve, reject) => {
          getGroups().then(res => {
            self.myGroups = res.data;
            self.loadingAside = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
      // collect post
      collect(id) {
        var self = this;
        collectPost({
          id: id,
          userid: self.user.userInfo.id,
        }).then(() => {
          let myplanetsIndex = self.myplanetsPosts.findIndex(item => {
            return item.id === id
          })
          let newtopicIndex = self.newtopicPosts.findIndex(item => {
            return item.id === id
          })
          let preCollected = self.myplanetsPosts[myplanetsIndex].collected
          if(myplanetsIndex != -1) {
            self.myplanetsPosts[myplanetsIndex].collected = preCollected ? false : true;
            self.myplanetsPosts[myplanetsIndex].collected_num = parseInt(self.myplanetsPosts[myplanetsIndex].collected_num) + (preCollected ? -1 : 1);
          }
          if(newtopicIndex != -1) {
            self.newtopicPosts[newtopicIndex].collected = preCollected ? false : true;
            self.newtopicPosts[newtopicIndex].collected_num = parseInt(self.newtopicPosts[newtopicIndex].collected_num) + (preCollected ? -1 : 1);
          }
        })
      },
      approve(id) {
        var self = this;
        approvePost({
          id: id,
        }).then(() => {
          let myplanetsIndex = self.myplanetsPosts.findIndex(item => {
            return item.id === id
          })
          let newtopicIndex = self.newtopicPosts.findIndex(item => {
            return item.id === id
          })
          let preApproved = self.myplanetsPosts[myplanetsIndex].approved
          if(myplanetsIndex != -1) {
            self.myplanetsPosts[myplanetsIndex].approved = preApproved ? false : true;
            self.myplanetsPosts[myplanetsIndex].approved_num = parseInt(self.myplanetsPosts[myplanetsIndex].approved_num) + (preApproved ? -1 : 1);
          }
          if(newtopicIndex != -1) {
            self.newtopicPosts[newtopicIndex].approved = preApproved ? false : true;
            self.newtopicPosts[newtopicIndex].approved_num = parseInt(self.newtopicPosts[newtopicIndex].approved_num) + (preApproved ? -1 : 1);
          }
        })
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .index-visitor-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 900px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 590px;
    }
    aside {
      flex: 0 0 250px;
      @media screen and (max-width: 900px) {
        display: none;
      }
      header {
        h2 {
          margin: 20px 0;

          font-family:PingFangHK-Medium;
          font-weight: normal;
          font-size:14px;
          color:#5677fc;
        }
      }
      .aside-content {
        .index-aside-card {
          width: 250px;
          height: 70px;
        }
      }
    }

  }
  .index-tabcontent {
    min-height: 200px;
    margin-top: 5px;
    margin-bottom: 20px;
    .el-pagination {
      text-align: center;
    }
  }
  // post card style    
  .index-cards { 
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
</style>
