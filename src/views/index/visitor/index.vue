<template>
    <div class="index-visitor-container">
      <section>     
        <header>
          最新话题
        </header>
        <div class="index-tabcontent" v-loading="loading2">
          <ul class="index-cards">
            <li v-for="post of posts" class="index-card">
              <header>
                <img :src="post.author.avatar_url">
                <span class="clickable">{{ post.author.name }}</span>
                <span>发表于</span>
                <span class="clickable">{{ post.group.name }}</span>
                <time>{{ post.create_time_formatted }}</time>
              </header>
              <div class="index-card-content">
                <h1>{{ post.title }}</h1>
                <div class="preview-html" v-html="post.content">
                </div>
                <div class="preview-imgs">
                  <img v-for="img of post.image_url" :src="img">
                </div>
              </div>
              <footer>
                <ul>
                  <li :class="{'done': post.replied}">评论 {{ post.replied_num }}</li>
                  <li :class="{'done': post.approved}">点赞 {{ post.approved_num }}</li>
                  <li :class="{'done': post.collected}">收藏 {{ post.collected_num }}</li>
                </ul>
              </footer>
            </li>  
          </ul>
          <el-pagination
            layout="prev, pager, next"
            :total="1000">
          </el-pagination>
        </div>
      </section>
      <aside>
        <header>
          <h2>发现星球</h2>
        </header>
        <div class="aside-content">
          <div v-for="group of discoveryGroups" class="index-aside-card wuan-card">
            <img :src="group.image_url">
            <div class="wuan-card__content">
              <h2 class="clickable">{{ group.name }}</h2>
              <p>{{ group.introduction }}</p>
            </div>
          </div>
        </div>
        <footer>
          <span class="clickable">全部星球</span>
          <span class="clickable">创建星球</span>
        </footer>
      </aside>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { parseTime } from 'utils/date';
  import { getPosts } from 'api/post';
  import { getGroups } from 'api/group';

  export default {
    name: 'index-visitor',
    data() {
      return {
        activeName: 'index-myplanet',
        loading: false,
        posts: [],
        discoveryGroups: [],
      }
    },
    computed: {
      ...mapGetters([
        'user',
        'access_token',
      ]),
      posts_formatted: function() {
        if(this.posts.length === 0) {
          return [];
        }
        let newPosts = new Array(this.posts);
        newPosts = newPosts.map((post) => {
          let newPost = post;
          newPost.create_time_formatted = parseTime(newPost.create_time, 'yyyy-MM-dd HH:mm')
          return newPost;
        })
        return newPosts;
      },
    },
    mounted() {
      var self = this;
      this.loading = true;
      this.loadingAside = true;
      
      // promise all for loading post and comment
      var loadPosts = function() {
        return new Promise((resolve, reject) => {
          getPosts(true).then(res => {
            console.dir(res);
            self.posts = res.data;
            self.loading = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      }
      // loading group data and Authority information
      var loadGroups = function(groupid) {
        
        return new Promise((resolve, reject) => {
          getGroups().then(res => {
            console.dir(res)
            self.discoveryGroups = res;
            self.loadingAside = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      }

      loadPosts()
        .then()
        .catch((err) => {
          console.dir(err);
          this.$message({
            message: err.error,
            type: 'error',
            duration: 1000,
          });
          this.loading = false;
        })
      loadGroups()
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
      header {
        margin: 15px 0 20px 0;
        font-family:PingFangHK-Medium;
        font-size:18px;
        color:#5677fc;
      }
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
      footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 20px;
        span {
          font-family:PingFangHK-Regular;
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
