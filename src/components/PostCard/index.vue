<template>
  <li class="post-card" >
    <header>
      <img :src="post.author.avatar_url === 'default_url' ? 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100' : post.author.avatar_url">
      <span class="clickable" @click="$router.push({path: `/myspace/${post.author.id}`})">{{ post.author.name }}</span>
      <time>{{ post.create_at | formatTime }}</time>
    </header>
    <div class="post-card-content">
      <h1 @click="$router.push({path: `/article/${post.id}`})" :title="post.title">{{ post.title }}</h1>
      <div class="preview-html" v-html="content">
      </div>
      <div class="preview-imgs">
        <img v-for="(img,index) of post.image_urls" :key="index" :src="img">
      </div>
    </div>
    <footer>
      <ul>
        <li @click="$router.push({path: `/article/${post.id}`})" :class="{'done': post.replied}"><icon-svg icon-class="pinglun" class="avatar-icon"></icon-svg>评论 {{ post.replied_num }}</li>
        <li @click="approve(post.id, post.approved)" :class="{'done': post.approved}"><icon-svg icon-class="zan" class="avatar-icon"></icon-svg>点赞 {{ post.approved_num }}</li>
        <li @click="collect(post.id, post.collected)" :class="{'done': post.collected}"><icon-svg icon-class="shoucang" class="avatar-icon"></icon-svg>收藏 {{ post.collected_num }}</li>
      </ul>
    </footer>
  </li>
</template>

<script>
import { mapGetters } from 'vuex'
// import ArticleState from 'components/ArticleState/ArticleState';
import { html2Text } from 'filters/index'
import {
  approveArticle,
  collectArticle,
  unapproveArticle,
  uncollectArticle
} from 'api/article'

export default {
  name: 'article-card',
  components: {
    // ArticleState
  },
  props: {
    post: {
      type: Object,
      required: true
    }
  },
  data () {
    return {
    }
  },
  computed: {
    ...mapGetters([
      'user'
    ]),
    content: function () {
      return html2Text(this.post.content)
    }
  },
  mounted () {
  },
  methods: {
    // collect post
    async collect (id, val) {
      var self = this
      if (this.user.token === '') {
        this.$router.push({path: '/login/'})
        return
      }
      if (val) {
        await uncollectArticle(id).then(() => {
          self.post.collected_num--
          self.post.collected = !val
        })
      } else {
        await collectArticle(id).then(() => {
          self.post.collected_num++
          self.post.collected = !val
        })
      }
    },
    async approve (id, val) {
      var self = this
      if (this.user.token === '') {
        this.$router.push({path: '/login/'})
        return
      }
      if (val) {
        await unapproveArticle(id).then(() => {
          self.post.approved = !val
          self.post.approved_num--
        })
      } else {
        await approveArticle(id).then(() => {
          self.post.approved = !val
          self.post.approved_num++
        })
      }
    },
    toSpace (id) {
      if (this.user.token === '') {
        this.$router.push({path: '/login/'})
        return
      }
      this.$router.push({path: '/myspace', query: {id: id}})
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .post-card {
    padding: 24px 17px 0px 17px;
    width: 448px;
    background-color: #ffffff;
    border-radius: 4px;
    &:not(:first-child) {
      margin-top: 12px;
    }
    &:last-child {
      margin-bottom: 78px;
    }
    header {
      display: flex;
      align-items: center;
      margin-bottom: 17px;
      font-size:12px;
      color:#434343;
      & > .clickable {
        transition: all 0.2s ease-in-out;
        &:hover {
          color: #5677fc;
        }
      }
      img {
        width: 32px;
        height: 32px;
        border-radius: 100%;
        margin-right: 14px;
        background-color: #aaaaaa;
      }
      span {
        font-size: 15px;
        color: #333333;
        &:not(:first-child) {
          margin-left: 5px;
        }
      }
      time {
        margin-left: 12px;
        font-size: 11px;
        color: #999999;
        flex-grow: 1;
        text-align: right;
      }
    }
    div.post-card-content {
      margin-bottom: 12px;
      h1 {
        display: inline-block;
        position: relative;
        cursor: pointer;
        margin-bottom: 11px;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;

        color: #434343;
        font-size:15px;
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
          color: #5677fc;
          &::after {
            transform: scaleX(1);
          }
        }
      }
      div.preview-html {
        word-break: break-all;
        font-size:11px;
        color:#444444;
        letter-spacing:0;
        text-align:justify;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 3;
        overflow: hidden;
      }
      div.preview-imgs {
        display: flex;
        margin-top: 20px;
        img {
          margin-right: 9px;
          width: 132px;
          height: 132px;
          background-color: #f8f9fa;
        }

      }
    }
    footer {
      margin-top: 24px;
      margin-left: -17px;
      margin-right: -17px;
      border-top: 2px solid #dcdcdc;
      padding: 10px 0 9px 0;
      ul {
        display: flex;
        justify-content: space-around;
        font-size: 12px;
        height: 19px;
        align-items: center;
        li {
          transition: all 0.2s ease-in-out;
          cursor: pointer;
          flex-grow: 1;
          text-align: center;
          color: #666666;
          border-right: 2px solid #dcdcdc;
          .avatar-icon{
            margin-right: 13px;
            font-size: 12px;
          }
          &:last-child{
            border: 0;
          }
          // &:not(:first-child):before{
          //   content:'\00B7';
          //   padding:0 8px;
          // }
          &:hover {
            color: #5677fc;
          }
          &.done {
            color: #5677fc;
          }
        }
      }
    }
  }
</style>
