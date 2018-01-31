<template>
    <div class="index-visitor-container">
      <section>     
        <el-tabs v-model="activeName" @tab-click="tabChange">
          <el-tab-pane v-if="!myplanetsPosts || myplanetsPosts.length > 0" label="我的星球" name="index-myplanet">
            <div class="index-tabcontent" v-loading="loading_myplanet">
              <ul class="index-cards">
                <post-card v-for="post of myplanetsPosts" 
                           :key="post.id+'myplanet'" 
                           :post.sync="post">  
                </post-card>
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
                <post-card v-for="post of newtopicPosts" 
                           :key="post.id+'newtopic'" 
                           :post.sync="post">  
                </post-card>
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
              <button @click="$router.push({path: `/planet/${group.id}`, query: { name: group.name }})">{{ group.name }}</button>
            </li>
          </ul>
        </div>
        <footer>
          <span class="clickable" @click="$router.push({path: `/universe`, query: { name: '全部星球'}})">全部星球</span>
          <span class="clickable" @click="$router.push({path: `/planet/create`})">创建星球</span>
        </footer>
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
  // import { getGroupsByUserId } from 'api/group';
  import PostCard from 'components/PostCard'

  export default {
    name: 'index-visitor',
    components: {
      PostCard
    },
    data() {
      return {
        activeName: 'index-myplanet',

        loading_myplanet: false,
        loading_newtopic: false,
        loadingAside: false,
        myplanetsPosts: null,
        newtopicPosts: null,
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
      ]),
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
      // OPTIMIZE: use reset
      tabChange(targetTab) {
        switch(targetTab.name) {
          case 'index-newtopic':
            this.pagination_newtopic = {
              pageCount: 1,
              currentPage: 1,
              limit: 20,
            },
            this.loadPosts_newtopic()
            break;
          case 'index-myplanet':
            this.pagination_myplanet = {
              pageCount: 1,
              currentPage: 1,
              limit: 20,
            }
            this.loadPosts_myplanet()
            break;
        }
      },
      // OPTIMIZE: there is a redundant code using ctrl+c to load newtopic and myplanet posts
      loadPosts_myplanet(page=1) {
        var self = this;
        this.loading_myplanet = true;
        console.log(`page is ${page}`)
        return new Promise((resolve, reject) => {
          getPosts(false,(page-1)*self.pagination_myplanet.limit || 0).then(res => {
            if(res.data) {
              self.myplanetsPosts = res.data;
              self.loading_myplanet = false;

              // pagination
              let pageFinal = parseQueryParams(res.paging.final);
              self.pagination_myplanet.pageCount = (pageFinal.offset / pageFinal.limit) + 1;
            } else {
              self.myplanetsPosts = [];
              self.activeName = 'index-newtopic';
            }
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
      loadPosts_newtopic(page=1) {
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
          getGroupsByUserId(self.user.userInfo.id).then(res => {
            self.myGroups = res.data;
            self.loadingAside = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
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
      footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 20px;
        span {
          font-size:14px;
          color:#5992e4;
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
