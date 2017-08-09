<template>
  <div class="group-public-container">
    <section>    
      <div v-if="groupid" class="group-publish">
        <button @click="$router.push({path: '/post/publish/', query: { groupid: groupid }})">
          <icon-svg icon-class="smallbell"></icon-svg>发表帖子
        </button>
      </div> 
      <div class="index-tabcontent" v-loading="loading">
        <ul v-if="posts.length > 0" class="index-cards">
          <li v-for="post of posts" class="index-card">
            <header>
              <img :src="post.author.avatar_url">
              <span class="clickable">{{ post.author.name }}</span>
              <span>发表于</span>
              <span class="clickable"
                @click="$router.push({path: '/group/' + post.group.id})">
                {{ group && group.name || '' }}
              </span>
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
                <li @click="reply(post.id)" :class="{'done': post.replied}">评论 {{ post.replied_num }}</li>
                <li @click="approve(post.id)" :class="{'done': post.approved}">点赞 {{ post.approved_num }}</li>
                <li @click="collect(post.id)" :class="{'done': post.collected}">收藏 {{ post.collected_num }}</li>
              </ul>
            </footer>
          </li>  
        </ul>
        <el-pagination layout="prev, pager, next, jumper"
                       :page-count="pagination.pageCount"
                       @current-change="loadPosts">
        </el-pagination>
      </div>
    </section>
    <aside>
      <div class="aside-card" v-if="group">
        <header>
          <img :src="group.image_url">
          <div class="group-info">
            <h2>{{ group.name }}</h2>
            <span>{{ group.post_num }} 话题　</span>
            <span>{{ group.member_num }} 成员</span>
          </div>
        </header>
        <div class="aside-card-content">
          <p>{{ group.introduction }}</p>
          <span>星球主: {{ group.creator.name }}</span>
        </div>
        <footer>
          <el-button class="func-button" style="width: 90px; height: 30px" v-if="group.identity == 'member'">退出星球</el-button>
          <el-button class="func-button" style="width: 90px; height: 30px" v-else-if="group.identity == 'not_applied'">加入星球</el-button>
        </footer>
      </div>
      <!-- for aside loading -->
      <div v-else class="aside-card" v-loading="loadingAside"></div>
    </aside>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { parseTime } from 'utils/date';
  import { parseQueryParams } from 'utils/url';
  import { getPostsByGroupId, approvePost, collectPost } from 'api/post';
  import { getGroup } from 'api/group';

  export default {
    name: 'group-public',
    data() {
      return {
        loading: false,
        loadingAside: false,
        posts: [],
        groupid: null,
        group: null,
        discoveryGroups: [],
        pagination: {
          pageCount: 1,
          currentPage: 1,
          limit: 20,
        }
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
    created() {
      this.groupid = this.$route.params.id;
    },
    mounted() {
      this.loadPosts()
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
      this.loadGroup(this.groupid)
        .then(this.loadPosts)
        .catch((err) => {
          this.$message({
            message: err.error,
            type: 'error',
            duration: 1000,
          });
          this.loadingAside = false;
        })
      //this.loading2 = true;
    },
    methods: {
      loadPosts(page) {
        var self = this;
        this.loading = true;
        console.log(`page is ${page}`)
        return new Promise((resolve, reject) => {
          getPostsByGroupId(self.groupid,(page-1)*self.pagination.limit || 0).then(res => {
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
      loadGroup(groupid) {
        var self = this;
        this.loadingAside = true;
        return new Promise((resolve, reject) => {
          getGroup(groupid).then(res => {
            self.group = res;
            self.loadingAside = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
      reply(postid) {
        if(!this.user.id) {
          this.$router.push({path: '/login/'});
          return;
        }
        this.$router.push({path: `/post/${postid}`})
      },
      approve(postid) {
        if(!this.user.id) {
          this.$router.push({path: '/login/'});
          return;
        }
        approvePost(postid).then(res => {
          this.$message({
            message: '点赞帖子成功！',
            type: 'success',
            duration: 1000,
          });
        })
      },
      collect(postid) {
        if(!this.user.id) {
          this.$router.push({path: '/login/'});
          return;
        }
        collectPost(postid).then(res => {
          this.$message({
            message: '收藏帖子成功！',
            type: 'success',
            duration: 1000,
          });
        })
      },
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .group-public-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 900px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      margin-top: 20px;
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
      margin-top: 20px;
      flex: 0 0 250px;
      @media screen and (max-width: 900px) {
        display: none;
      }
    }
  }
  // post publish div
  .group-publish {
    width: 100%;   
    height:40px;
    button {
      width: 100%;
      height: 100%;
      background:#ffffff;
      border: none;
      margin-bottom: 4px;
      box-shadow:0 2px 4px 0 rgba(0,0,0,0.06);
      border-radius:4px;
      transition: all 0.5s ease-in-out;

      font-family:PingFangHK-Regular;
      font-size:12px;
      color:#1b87f6;
      &:hover {
        color: #ffffff;
        background: #66ccff;
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

  // aside part
  .aside-card {
    width: 248px;
    min-height: 200px;
    padding: 10px 14px;
    background:#ffffff;
    border:1px solid #f0f6fd;
    box-shadow:0 2px 5px 0 rgba(0,0,0,0.24);
    border-radius:4px;
    header {
      display: flex;
      margin-bottom: 10px;
      img {
        flex: 0 0 60px;
        width:60px;
        height:60px;
        border-radius:100%;
        margin-right: 7px;
      }
      .group-info {
        min-width: 0;
        align-self: center;
        h2 {
          text-overflow: ellipsis;
          overflow: hidden;

          font-family:PingFangHK-Medium;
          font-size:16px;
          color:#275597;
        }
        span {
          font-family:PingFangHK-Medium;
          font-size:12px;
          color:#8a94a9;
        }
      }
    }
    .aside-card-content {
      margin: 14px 0 20px 0;
      p {
        word-break: break-all;
        margin-bottom: 23px;

        font-family:PingFangHK-Regular;
        font-size:14px;
        color:#333333;
      }
      span {
        font-family:PingFangHK-Regular;
        font-size:14px;
        color:#8a94a9;
      }
    }
    footer {
      text-align: center;
      button {
        background:#1b87f6;
        border-radius:4px;
        padding: 8px 20px;
        border: none;

        font-family:PingFangHK-Regular;
        font-size:12px;
        color:#ffffff;
        &:hover {
          background: #4db3ff;
          border-color: #4db3ff;
        }
      }
    }
  }
</style>
