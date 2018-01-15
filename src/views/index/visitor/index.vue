<template>
    <div class="index-visitor-container">
      <section>     
        <header>
          最新话题
        </header>
        <div class="index-tabcontent" v-loading="loading">
          <ul v-if="posts.length > 0" class="index-cards">
            <post-card v-for="post of posts" 
                       :key="post.id" 
                       :post.sync="post">  
            </post-card>
          </ul>
          <!--<el-pagination layout="prev, pager, next, jumper"
                         :page-count="pagination.pageCount"
                         @current-change="loadPosts">
          </el-pagination>-->
          	<!--<pagination @click.native="loadPosts(pagination.currentPage)" :pagination.sync="pagination"></pagination>-->
          	<pagination @loadPosts="loadPosts" :pagination.sync="pagination"></pagination>
        </div>
      </section>
      <aside>
        <header>
          活跃用户
        </header>
        <div class="aside-content" v-loading="loadingAside">
          <div v-for="activeuser of posts" 
            class="index-aside-card wuan-card clickable"
            @click="user.token=='' ? $router.push({path: '/login/'}) : $router.push({path: '/mySpace/'})"
            >
             <!--+ activeuser.author.id, query: { name: activeuser.author.name }-->
            <img :src="activeuser.author.avatar_url">
            <div class="wuan-card__content">
              <h2 class="clickable">{{ activeuser.author.name }}</h2>
              <p>本月发表了{{activeuser.author.monthly_posts_num}}</p>
            </div>
          </div>
        </div>
        <!--<footer>
          <span class="clickable" @click="$router.push({path: `/universe`, query: { name: '全部星球'}})">全部星球</span>
          <span class="clickable">创建星球</span>
        </footer>-->
      </aside>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { parseQueryParams } from 'utils/url';
  import { getPosts, getMockTest, getArticles } from 'api/post';

  // import { getGroups } from 'api/group';
  import PostCard from 'components/PostCard'
  import Pagination from 'components/Pagination'
  export default {
    name: 'index-visitor',
    components: {
      PostCard,
      Pagination
    },
    data() {
      return {
        loading: false,
        loadingAside: false,
        posts: [],
        discoveryGroups: [],
        pagination: {
          pageCount: 3,
          currentPage: 1,
          limit: 1,
        }
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ]),
    },
    mounted() {
      //获取最新内容数据
      getMockTest().then(res => {
        console.log(res);
      })
//    getArticles().then(res => {
//
//      console.log(res)
//      console.log(this.posts)
//      this.posts = res.articles
//      console.log(this.posts)
//    })
      this.loadPosts(1)
      // this.loadPosts()
      //   .then()
      //   .catch((err) => {
      //     console.dir(err);
      //     this.$message({
      //       message: err.error,
      //       type: 'error',
      //       duration: 1000,
      //     });
      //     this.loading = false;
      //   })
      // this.loadGroups()
      //   .then()
      //   .catch((err) => {
      //     console.dir(err);
      //     this.$message({
      //       message: err.error,
      //       type: 'error',
      //       duration: 1000,
      //     });
      //     this.loadingAside = false;
      //   });
    },
    methods: {
      loadPosts(page) {
        var self = this;
        this.loading = true;
        console.log(page);
        return new Promise((resolve, reject) => {
          getPosts(true,(page)*self.pagination.limit || 0).then(res => {
            console.dir(res);
            self.posts = res.data;
            self.loading = false;

            // pagination
            let pageFinal = parseQueryParams(res.paging.final);
            self.pagination.pageCount = (pageFinal.offset / pageFinal.limit) + 1;
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
            self.discoveryGroups = res.data;
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
      flex: 0 0 714px;
      header {
        margin: 31px 0 12px 0;
        padding-left: 17px;
        font-family: MicrosoftYaHei-Bold;
	    font-size: 32px;
        color:#5677fc;
        height: 66px;
        line-height: 66px;
        background-color: white;
      }
    }
    aside {
      margin-left: 41px;
      flex: 0 0 250px;
      @media screen and (max-width: 900px) {
        display: none;
      }
      header {
       
          margin: 31px 0 12px 0;
        padding-left: 17px;
        font-family: MicrosoftYaHei-Bold;
	    font-size: 32px;
        color:#5677fc;
        height: 66px;
        line-height: 66px;
        background-color: white;
        
      }
      .aside-content {
        min-height: 100px;
        .index-aside-card {
          width: 250px;
          height: 70px;
        }
      }
      /*footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 20px;
        span {
          font-family:PingFangHK-Regular;
          font-size:14px;
          color:#5992e4;
        }
      }*/
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
</style>
