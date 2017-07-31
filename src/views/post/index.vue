<template>
  
  <div id="post">
    <el-popover
      ref="reviewPopover"
      placement="bottom-start"
      width="558"
      popper-class="reviewPopover"
      trigger="click">
      <input placeholder="请输入内容" type="text">
      <button>回复</button>
    </el-popover>
    <section>
      <div class="post-container" v-loading="loading">
        <div v-if="post_formatted" class="post-wrapper">
          <header>
            <img src="#">
            <span class="author">{{ post_formatted.author.name }}</span>
            <time>{{ post_formatted.create_time_formatted }}</time>
          </header>
          <article>
            <h1>{{ post_formatted.title }}</h1>
            <div class="post-html" v-html="post_formatted.content"></div>
          </article>
          <footer>
            <div class="btns">
              <el-button type="primary" class="done">
                <icon-svg icon-class="good" class="avatar-icon"></icon-svg>{{ post_formatted.approved_num }}
              </el-button>
              <el-button type="primary">
                <icon-svg icon-class="star" class="avatar-icon"></icon-svg>{{ post_formatted.collected_num }}
              </el-button>
            </div>
            <div class="opts">
              <span>重置</span>
              <span>锁定</span>
              <span>编辑</span>
              <span>删除</span>
            </div>
          </footer>
        </div>
        <div class="review-wrapper" v-if="commentsObj_formatted">
          <header>
            {{ commentsObj_formatted.reply_count }} reviews
          </header>
          <ul>
            <li v-for="comment of commentsObj_formatted.reply">
              <header>
                <h2>{{ comment.user_name }}</h2>
                <time> 2017-02-13</time>
              </header>
              <div class="review-html" v-html="comment.comment">
              </div>
              <footer>
                <span v-popover:reviewPopover>回复</span>
                <span>删除</span>
              </footer>
            </li>

          </ul>
          <el-pagination
            class="review-paging"
            layout="prev, pager, next"
            :total="1000">
          </el-pagination>
          <footer class="review-reply">
            <input placeholder="请输入内容" type="text">
            <button>回复</button>
          </footer>
        </div>
      </div>
    </section>
    <aside>
      <div class="aside-card">
        <header>
          <img src="#">
          <div class="group-info">
            <h2>asdasdasdqwdqwdqwdqwdqwdqwd</h2>
            <span>999+ top</span>
            <span>999+ mem</span>
          </div>
        </header>
        <div class="aside-card-content">
          <p>asdqwudgqouwdgqouwdgoquwgfoqwbfoqgwfouqgwfouqbwovuqbwouqownvnqownvqogdouqwgdoquwgdoquwgdouqwgofuqwofugqwougqwofugqwfouqgwfouqgofugqwou</p>
          <span>Creator: Taotao</span>
        </div>
        <footer>
          <el-button>退出星球</el-button>
        </footer>
      </div>
    </aside>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getPost,getCommentsByPostId } from 'api/post';
  import { parseTime } from 'utils/date'

  export default {
    name: 'post',
    data() {
      return {
        postid: null,
        post: null,
        loading: false,
        commentsObj: null,
      }
    },
    computed: {
      ...mapGetters([
        'user',
        'access_token',
      ]),
      post_formatted: function() {
        if(!this.post) {
          return null;
        }
        let newPost = Object.assign({}, this.post);
        newPost.create_time_formatted = parseTime(newPost.create_time, 'yyyy-MM-dd HH:mm')
        return newPost;
      },
      commentsObj_formatted: function() {
        if(!this.commentsObj) {
          return null;
        }
        let newCommentsObj = Object.assign({}, this.commentsObj);
        newCommentsObj.reply = newCommentsObj.reply.map((comment) => {
          let newComment = Object.assign({}, comment);
          newComment.create_time_formatted = parseTime(newComment.create_time, 'yyyy-MM-dd HH:mm')
          return newComment;
        })
        return newCommentsObj;
      }
    },
    created() {
      this.postid = this.$route.params.id;
    },
    mounted() {
      this.loading = true;

      // promise all for loading post and comment
      var promise = Promise.all([getPost(this.postid), getCommentsByPostId(this.postid)])
      promise.then(res => {
        this.post = res[0];
        this.commentsObj = res[1];

        this.loading = false;
      }).catch((err) => {
        this.$message({
          message: err.error,
          type: 'error',
          duration: 1000,
        });
        this.loading = false;
      })
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
    .review-wrapper {
      border:1px solid #dce8f4;
      border-radius:4px;
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
  }

  // aside part
  .aside-card {
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
