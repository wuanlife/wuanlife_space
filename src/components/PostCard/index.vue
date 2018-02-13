<template>
  <li class="post-card wl-card">
    <div class="post-card-content">
      <header>
        <img :src="post.author.avatar_url === 'default_url' ? 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/32/h/32' : `${post.author.avatar_url}?imageView2/1/w/32/h/32`"
             @click="$router.push({path: `/myspace/${post.author.id}`})">
        <span class="clickable" @click="$router.push({path: `/myspace/${post.author.id}`})">{{ post.author.name }}</span>
        <time>{{ post.create_at | formatTime }}</time>
      </header>
      <h1 @click="$router.push({path: `/article/${post.id}`})" :title="post.title">{{ post.title }}</h1>
      <div class="preview-html" v-text="content">
      </div>
      <div class="preview-imgs">
        <img v-for="(img,index) of post.image_urls" :key="index" :src="img + '?imageView2/1/w/132/h/132'">
      </div>
    </div>
    <footer v-if="footer">
      <ul>
        <li @click="$router.push({path: `/article/${post.id}`})" :class="{'done': post.replied}"><icon-svg icon-class="pinglun" class="avatar-icon"></icon-svg>评论 {{ post.replied_num }}</li>
        <li @click="approve(post.id)" :class="{'done': approvedTemp}" v-loading="approving"><icon-svg icon-class="zan" class="avatar-icon"></icon-svg>点赞 {{ approved_numTemp }}</li>
        <li @click="collect(post.id)" :class="{'done': collectedTemp}" v-loading="collecting"><icon-svg icon-class="shoucang" class="avatar-icon"></icon-svg>收藏 {{ collected_numTemp }}</li>
      </ul>
    </footer>
  </li>
</template>

<script>
import { mapGetters } from 'vuex'
// import ArticleState from 'components/ArticleState/ArticleState';
import { html2Text } from 'filters/index'
import { Notification } from 'element-ui'
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
    },
    footer: {
      type: Boolean,
      default: true
    }
  },
  data () {
    return {
      approving: false,
      collecting: false,
      approvedTemp: false,
      collectedTemp: false,
      approved_numTemp: 0,
      collected_numTemp: 0
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
    this.approvedTemp = this.post.approved
    this.collectedTemp = this.post.collected
    this.approved_numTemp = this.post.approved_num
    this.collected_numTemp = this.post.collected_num
  },
  methods: {
    // collect post
    async collect (id) {
      var self = this
      if (self.collecting) {
        return
      }
      self.collecting = true
      if (this.user.token === '') {
        this.$router.push({path: '/login/'})
        return
      }
      try {
        if (self.collectedTemp) {
          await uncollectArticle(id).then(() => {
            self.collected_numTemp--
            self.collectedTemp = !self.collectedTemp
          })
        } else {
          await collectArticle(id).then(() => {
            self.collected_numTemp++
            self.collectedTemp = !self.collectedTemp
          })
        }
      } catch (e) {
        if (e.data) {
          Notification.error({
            message: e.data.error,
            offset: 60
          })
        } else {
          console.log(e)
        }
      }
      self.collecting = false
    },
    async approve (id) {
      var self = this
      if (self.approving) {
        return
      }
      self.approving = true
      if (this.user.token === '') {
        this.$router.push({path: '/login/'})
        return
      }
      try {
        if (self.approvedTemp) {
          await unapproveArticle(id).then(() => {
            self.approvedTemp = !self.approvedTemp
            self.approved_numTemp--
          })
        } else {
          await approveArticle(id).then(() => {
            self.approvedTemp = !self.approvedTemp
            self.approved_numTemp++
          })
        }
      } catch (e) {
        if (e.data) {
          Notification.error({
            message: e.data.error,
            offset: 60
          })
        } else {
          console.log(e)
        }
      }
      self.approving = false
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
    width: 448px;
    background-color: #ffffff;
    border-radius: 4px;
    &:not(:first-child) {
      margin-top: 12px;
    }
    &:last-child {
      margin-bottom: 78px;
    }
    div.post-card-content {
      padding: 24px 17px;
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
          cursor: pointer;
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
      border-top: 1px solid #dcdcdc;
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
          position: relative;
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
