<template>
  <div id="post">
    <section>
      <div class="post-container" v-loading="loading">
        <div v-if="post" class="post-wrapper">
          <header>
            <img :src="group.image_url">
            <span class="author">{{ post.author.name }}</span>
            <time>{{ post.create_time | formatTime }}</time>
            <post-state v-if="post.sticky" :text="'置顶'" :color="'#5992e4'"></post-state>
            <post-state v-if="post.lock" :text="'锁定'" :color="'#ccc'"></post-state>
          </header>
          <article>
            <h1>{{ post.title }}</h1>
            <div class="post-html" v-html="post.content"></div>
          </article>
          <footer>
            <div class="btns">
              <el-button type="primary" 
                         :class="{'done': post.approved}"
                         @click="approve(post.id)">
                <icon-svg icon-class="good" class="avatar-icon"></icon-svg>{{ post.approved_num }}
              </el-button>
              <el-button type="primary" 
                         :class="{'done': post.collected}"
                         @click="collect(post.id)">
                <icon-svg icon-class="star" class="avatar-icon"></icon-svg>{{ post.collected_num }}
              </el-button>
            </div>
            <div v-if="group" class="opts">
              <span v-if="group.identity === 'creator' || user.userInfo.id === post.author.id"
                    @click="settop(post.id)">
                {{post.sticky ? '取消置顶' : '置顶'}}
              </span>
              <span v-if="group.identity === 'creator' || user.userInfo.id === post.author.id"
                    @click="lock(post.id)">
                {{post.lock ? '解锁' : '锁定'}}
              </span>
              <span v-if="group.identity === 'creator' || user.userInfo.id === post.author.id"
                    @click="edit(post.id)">
                编辑
              </span>
              <span v-if="group.identity === 'creator' || user.userInfo.id === post.author.id"
                    @click="del(post.id)">
                删除
              </span>
            </div>
          </footer>
        </div>
        <post-reply></post-reply>
      </div>
    </section>
    <aside>

      <div class="aside-card" v-if="group">
        <header>
          <img :src="group.image_url">
          <div class="group-info">
            <h2 @click="$router.push({path: `/group/${group.id}`})">{{ group.name }}</h2>
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
      <div v-else class="aside-card" v-loading="loadingAside">
      </div>
    </aside>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import {
    parseQueryParams,
  } from 'utils/url'
  import PostState from 'components/PostState/PostState';
  import PostReply from './PostReply';
  import { 
    getPost,
    getCommentsByPostId,
    approvePost,
    collectPost,
    deletePost,
    lockPost,
    settopPost,
    replyPost,
    deleteReply,
  } from 'api/post';
  import { getGroup, joinGroup, quitGroup } from 'api/group';

  export default {
    name: 'post',
    components: {
      PostState,
      PostReply
    },
    data() {
      return {
        postid: null,
        post: null,
        pagination: {
          pageCount: 1,
          currentPage: 1,
          limit: 20,
        },
        group: null,
        loading: false,
        loadingAside: false,
        joinGroupLoading: false,
        quitGroupLoading: false,
        commentsObj: null,

        replyPop: false,
        replyInput: '',
        replyLoading: false,
        replypopInput: '',
        replypopLoading: false,
      }
    },
    computed: {
      ...mapGetters([
        'user',
        'access_token',
      ]),
    },
    created() {
      this.postid = this.$route.params.id;
    },
    mounted() {
      let self = this;
      this.loading = true;
      this.loadingAside = true;


      // promise all for loading post and comment
      var loadPostAndComments = function() {
        return new Promise((resolve, reject) => {
          Promise.all([getPost(self.postid), getCommentsByPostId(self.postid)]).then(res => {
            self.post = res[0];
            self.commentsObj = res[1];

            self.loading = false;
            resolve(res[0].group.id)
          }).catch(error => {
            reject(error);
          });
        });
      }
      // loading group data and Authority information
      var loadGroup = function(groupid) {
        
        return new Promise((resolve, reject) => {
          getGroup(groupid).then(res => {
            self.group = res;
            self.loadingAside = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      }

      

      this.loadReplies()
        .then()
        .catch((err) => {
          this.$message({
            message: err.data.error,
            type: 'error',
            duration: 1000,
          });
          this.loading_newtopic = false;
        })
      
      loadPostAndComments()
        .then(loadGroup)
        .catch((err) => {
          this.$message({
            message: err.data.error,
            type: 'error',
            duration: 1000,
          });
          this.loading = false;
          this.loadingAside = false;
          if(err.status == 410) {
            setTimeout(() => {
              this.$router.push({path: `/index/`})
            }, 2000)
          }
        })
    },
    methods: {
      loadReplies(page) {
        var self = this;
        this.replyLoading = true;
        return new Promise((resolve, reject) => {
          getCommentsByPostId(this.postid, ((page || 1) - 1)*self.pagination.limit || 0).then(res => {
            self.commentsObj = res;
            self.replyLoading = false;

            // pagination
            let pageFinal = parseQueryParams(res.paging.final);
            self.pagination.pageCount = (pageFinal.offset / pageFinal.limit) + 1;

            // scroll into view
            if(page) {
              self.$refs.replyHeader.scrollIntoView(true);
            }
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
          this.post.collected_num += this.post.collected ? -1 : 1;
          this.post.collected = !this.post.collected;
        })
      },
      approve(id) {
        var self = this;
        approvePost({
          id: id,
        }).then(() => {
          this.post.approved_num += this.post.approved ? -1 : 1;
          this.post.approved = !this.post.approved;
        })
      },
      reply(reply_floor) {
        var self = this;
        this.replyLoading = true;
        replyPost(this.post.id, {comment: this.replyInput}).then(res => {
          this.replyInput = ''
          this.replyLoading = false;
          this.loadReplies(this.pagination.currentPage);
        }).catch(error => {
          this.replyLoading = false;
          this.$notify({
            title: '错误',
            message: error.data.error,
            type: 'error'
          });
        })
      },
      replyComment(reply_floor) {
        var self = this;
        this.replyPop = true;
        
        this.replypopLoading = true;
        replyPost(this.post.id, {comment: this.replypopInput, floor: reply_floor}).then(res => {
          this.replypopInput = ''
          this.replypopLoading = false;
          this.replyPop = false;
          this.loadReplies(this.pagination.currentPage);
        }).catch(error => {
          this.replypopLoading = false;
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
      // OPTIMIZATION: just re-fetch the data rather than reload page
      settop(id) {
        settopPost(id).then(res => {
          this.$notify({
            title: '成功',
            message: '置顶成功',
            type: 'info'
          });
          setTimeout(() => {
            this.$router.go(0)
          },1000)  
        }).catch(err => {
          this.$message({
            message: err.data.error,
            type: 'error',
            duration: 1000,
          });
        })
      },
      lock(id) {
        lockPost(id).then(res => {
          this.$notify({
            title: '成功',
            message: '锁定成功',
            type: 'info'
          });
          setTimeout(() => {
            this.$router.go(0)
          },1000)  
        }).catch(err => {
          this.$message({
            message: err.data.error,
            type: 'error',
            duration: 1000,
          });
        })
      },
      edit(id) {
        this.$router.push({path: `/post/${this.post.id}/edit`})
      },
      del(id) {
        deletePost(id).then(res => {
          this.$notify({
            title: '成功',
            message: '删除成功，3秒后跳转到星球',
            type: 'info'
          });
          setTimeout(() => {
            this.$router.push({path: `/group/${this.group.id}`})
          },3000)      
        })
      },
      deleteComment(floor) {
        this.replyLoading = true;
        deleteReply(this.$route.params.id, floor)
          .then((res) => {
            this.$notify({
              title: '成功',
              message: res.success,
              type: 'info'
            });
            this.commentsObj = null;
            getCommentsByPostId(this.$route.params.id).then((res) => {
              this.commentsObj = res;
              this.replyLoading = false;
            }).catch((error) => {
              this.replyLoading = false;
            })
          })
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  #post {
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
    }
    aside {
      margin-top: 20px;
      flex: 0 0 250px;
      @media screen and (max-width: 900px) {
        display: none;
      }
      
    }  
  }
  // postdetails container(include post and review)
  .post-container {
    min-height: 300px;
    border-radius:8px;
    padding: 16px;
    background: #ffffff;
    .post-wrapper {
      margin-bottom: 20px;
      header {
        display: flex;
        align-items: center;
        margin-bottom: 18px;
        img {
          background:#d8d8d8;
          margin-right: 12px;
          width:40px;
          height:40px;
          border-radius:100%;
        }
        span.author {
          margin-right: 24px;
          font-family:PingFangHK-Semibold;
          font-size:14px;
          color:#525252;
        }
        time {
          font-family:PingFangHK-Medium;
          font-size:14px;
          color:#525252;
        }
      }
      article {
        h1 {
          margin-bottom: 12px;
          color: #275193;
          font-family:PingFangHK-Semibold;
          font-size:32px;
        }
        .post-html {
          margin-bottom: 39px;
          word-break: break-all;

          font-size:14px;
          color:#333333;
          letter-spacing:0;
          text-align:justify;
        }
      }
      footer {
        display: flex;
        justify-content: space-between;
        .btns {
          button {
            background: #ebf3fb;
            border-radius: 4px;
            height: 36px;
            padding: 8px 10px;
            border: none;
            transition: all 0.2s ease-in-out;

            font-family:PingFangHK-Regular;
            font-size:14px;
            color:#2682cf;
            text-align:justify;
            span {
              svg {
                margin-right: 6px;
              }
            }
            &:hover {
              background: #bbd3db;
              color:#1a529f;
            }
            &.done {
              background: #c3c3c3;
              color: #666666;
              &:hover {
                background: #a3a3a3;
                color: #333333;
              }
            }
          }
        }
        .opts {
          &:not(:first-child) {
            margin-left: 10px; 
          }
          span {
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            font-family:PingFangHK-Medium;
            font-size:14px;
            color:#8a94a9;
            &:hover {
              color:#66ccff;
            }
          }
        }
      }
    }
  }

  // aside part
  .aside-card {
    min-height: 200px;
    width: 248px;
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
          cursor: pointer;

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
    }
  }
  .reviewPopover {
    input {
      padding: 0 15px;
      border:1px solid #97bce2;
      border-radius:4px;
      width:450px;
      height:32px;
    }
    button {
      padding: 7px 16px;
      background: #97bce1;
      border: none;
      border-radius:4px;
      height:34px;

      font-family:PingFangHK-Medium;
      font-size:14px;
      color:#ffffff;
    }
  }
</style>

<style rel="stylesheet/scss" lang="scss">
// adjust for post content
.post-html {
  img {
    max-width: 100%;
  }
}
</style>
