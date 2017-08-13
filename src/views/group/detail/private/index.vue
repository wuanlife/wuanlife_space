<template>
  <div class="group-private-container">
    <section v-if="(group.identity === 'creator' || group.identity === 'member')">    
      <div v-if="(group.identity === 'creator' || group.identity === 'member')" class="group-publish">
        <button @click="$router.push({path: '/post/publish/', query: { groupid: group.id }})">
          <icon-svg icon-class="smallbell"></icon-svg>发表帖子
        </button>
      </div> 
      <div class="index-tabcontent" v-loading="loading">
        <ul v-if="posts.length > 0" class="index-cards">
          <post-card v-for="post of posts" 
                     :key="post.id" 
                     :post.sync="post">  
          </post-card>  
        </ul>
        <el-pagination layout="prev, pager, next, jumper"
                       :page-count="pagination.pageCount"
                       @current-change="loadPosts">
        </el-pagination>
      </div>
    </section>
    <section v-else>
      <div class="group-apply-card">
        <h2>验证信息</h2>
        <input type="text" placeholder="邀请码">
        <button class="func-button">申请加入</button>
      </div>
      <div class="group-apply-notice">加入星球后方可浏览内容</div>
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
          <el-button v-if="group.identity == 'member'"
                     class="func-button" 
                     style="width: 90px; height: 30px"
                     @click="quitGroup">
            退出星球
          </el-button>
          <el-button v-else-if="group.identity == 'not_applied'"
                     class="func-button" 
                     style="width: 90px; height: 30px"
                     @click="joinGroup">
            加入星球
          </el-button>
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
  import { joinGroup, quitGroup } from 'api/group';

  import PostCard from 'components/PostCard'
  export default {
    name: 'group-private',
    components: {
      PostCard
    },
    props: {
      group: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
        loading: false,
        loadingAside: false,
        posts: [],
        discoveryGroups: [],
        pagination: {
          pageCount: 1,
          currentPage: 1,
          limit: 20,
        },
        joinGroupLoading: false,
        quitGroupLoading: false,
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
    },
    mounted() {
      if(group.identity === 'creator' || group.identity === 'member') {
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
      }
    },
    methods: {
      loadPosts(page) {
        var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          getPostsByGroupId(self.group.id,(page-1)*self.pagination.limit || 0).then(res => {
            self.posts = res.data;
            self.loading = false;

            // pagination
            try {
              let pageFinal = parseQueryParams(res.paging.final);
              self.pagination.pageCount = (pageFinal.offset / pageFinal.limit) + 1;
            } catch (e) {
              //console.log(e);
            }
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
      quitGroup() {
        this.quitGroupLoading = true;
        quitGroup(this.group.id).then(res => {
          this.quitGroupLoading = false;
          this.$notify({
            title: '成功',
            message: 'quit success',
            type: 'info'
          });
          this.group.identity = 'not_applied';
        })
      },
      joinGroup() {
        this.joinGroupLoading = true;
        joinGroup(this.group.id).then(res => {
          this.joinGroupLoading = false;
          this.$notify({
            title: '成功',
            message: 'join success',
            type: 'success'
          });
          this.group.identity = 'member';
        })
      },
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .group-private-container {
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

  // private apply part
  div.group-apply-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    margin: auto;
    margin-top: 55px;
    padding: 20px 32px;
    background:#ffffff;
    border:1px solid #f0f6fd;
    box-shadow:0 2px 5px 0 rgba(0,0,0,0.24);
    border-radius:4px;
    width:508px;
    height:268px;
    h2 {
      font-family:PingFangHK-Semibold;
      font-size:16px;
      font-weight: bold;
      color:#275597;
      text-align:left;
    }
    input {
      width: 80%;
      padding: 12px 10%;
      text-align: center;
      margin: auto;
      margin-top: 83px;
      border: none;
      border-bottom: 1px solid #c6c7c7;

      font-family:PingFangHK-Semibold;
      font-size:22px;
      color:#000000;
      &:focus {
        outline: none;
      }
    }
    button {
      margin-left: auto;
      padding: 7px 20px;
    }
  }
  div.group-apply-notice {
    margin-top: 60px;
    text-align: center;

    font-family:PingFangHK-Semibold;
    font-size:32px;
    color:#999999;
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
