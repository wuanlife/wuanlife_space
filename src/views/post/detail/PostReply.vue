<template>
  <div class="review-wrapper" v-if="commentsObj">
    <header ref="replyHeader">
      {{ commentsObj.reply_count }} reviews
    </header>
    <ul v-loading="replyLoading">
      <li v-for="comment of commentsObj.reply">
        <header>
          <h2>{{ comment.user_name }}</h2>
          <time> {{ comment.create_time | formatTime }} </time>
        </header>
        <div class="review-html" v-html="comment.comment">
        </div>
        <footer v-if="group">
          <el-popover
            ref="reviewPopover"
            placement="bottom-start"
            width="558"
            :disabled="replyPop"
            popper-class="reviewPopover"
            trigger="click">
            <input v-model="replypopInput" placeholder="请输入内容" type="text">
            <button :class="{ 'wuan-loading' : replypopLoading }" 
                    @click="replyComment(comment.floor)">回复</button>
            <span slot="reference">回复</span>
          </el-popover>
          <span v-if="group.identity === 'creator' || user.userInfo.id === comment.user_id"
                @click="deleteComment(comment.floor)">删除</span>
        </footer>
      </li>

    </ul>
    <el-pagination layout="prev, pager, next, jumper"
                   :page-count="pagination.pageCount"
                   :current-page="pagination.currentPage"
                   @current-change="loadReplies">
    </el-pagination>
    <footer class="review-reply">
      <input placeholder="请输入内容" type="text" v-model="replyInput">
      <button :class="{ 'wuan-loading' : replyLoading }" @click="reply()">回复</button>
    </footer>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import {
    parseQueryParams,
  } from 'utils/url'
  import { 
    getCommentsByPostId,
    replyPost,
    deleteReply,
  } from 'api/post';
  import { getGroup, joinGroup, quitGroup } from 'api/group';

  export default {
    name: 'post',
    components: {
      PostState
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
  .review-wrapper {
    border:1px solid #dce8f4;
    border-radius:4px;
    .el-pagination {
      padding: 10px;
      text-align: center;
    }
    & > header {
      padding: 16px;
      border-bottom: 1px solid #dce8f4;

      text-align:justify;
      font-weight: bold;
      font-family:PingFangHK-Medium;
      font-size:14px;
      color:#215094;
    }
    ul {
      padding: 0 16px;
      li {
        padding: 16px 0 8px 0;
        border-bottom: 1px solid #dce8f4;
        header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          margin-bottom: 6px;
          h2 {
            font-family:PingFangHK-Medium;
            font-size:14px;
            color:#215094;
          }
          time {
            font-family:PingFangHK-Medium;
            font-size:14px;
            color:#8a94a9;
          }
        }
        .review-html {
          margin-bottom: 6px;
          word-break: break-all;

          font-size:14px;
          color:#333333;
          letter-spacing:0;
          text-align:justify;
        }
        footer {
          span {
            margin-right: 28px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            font-family:PingFangHK-Medium;
            font-size:14px;
            color:#8a94a9;
            text-align:justify;
            &:hover {
              color:#66ccff;
            }
          }
        }
      }
    }
    .review-paging {
      text-align: center;
      padding: 18px 0;
    }
    footer.review-reply {
      padding: 16px 0;
      text-align: center;
      border-top: 1px solid #dce8f4;
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
  }
</style>
