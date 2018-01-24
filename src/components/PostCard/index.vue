<template>
  <li class="post-card">
    <header>
      <img :src="post.author.avatar_url === 'default_url' ? 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100' : post.author.avatar_url">
      <span class="clickable" @click="$router.push({path: `/myspace/${post.author.id}`})">{{ post.author.name }}</span>
      <time>{{ post.create_at | formatTime }}</time>
    </header>
    <div class="post-card-content">
      <h1 @click="$router.push({path: `/topic/${post.id}`, query: { name: post.title }})" :title="post.title">{{ post.title }}</h1>
      <div class="preview-html" v-html="post.content">
      </div>
      <div class="preview-imgs">
        <img v-for="(img,index) of post.image_urls" :key="index" :src="img">
      </div>
    </div>
    <footer>
      <ul>
        <li @click="$router.push({path: `/topic/${post.id}`, query: { name: post.title }})" :class="{'done': post.replied}"><icon-svg icon-class="pinglun" class="avatar-icon"></icon-svg>评论 {{ post.replied_num }}</li>
        <li @click="approve(post.id)" :class="{'done': post.approved}"><icon-svg icon-class="zan" class="avatar-icon"></icon-svg>点赞 {{ post.approved_num }}</li>
        <li @click="collect(post.id)" :class="{'done': post.collected}"><icon-svg icon-class="shoucang" class="avatar-icon"></icon-svg>收藏 {{ post.collected_num }}</li>
      </ul>
    </footer>
  </li> 
</template>

<script>
  import { mapGetters } from 'vuex';
  import { parseTime } from 'utils/date';
  // import ArticleState from 'components/ArticleState/ArticleState';
  import {
    approveArticle,
    collectArticle,
  } from 'api/article';

  export default {
    name: 'article-card',
    components: {
      // ArticleState
    },
    props: {
      post: {
        type: Object,
        required: true,
      },
    },
    data() {
      return {
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ]),
    },
    mounted() {
    },
    methods: {
      // collect post
      collect(id) {
        var self = this;
        if(this.user.token == '') {
          this.$router.push({path: '/login/'})
          return
        }
        collectArticle({
          id: id,
          userid: self.user.userInfo.id,
        }).then(() => {
          self.post.collected_num += self.post.collected ? 1 : -1 ;
          self.post.collected = !self.post.collected;
          self.$emit('on-collected', self.post.id);
        })
      },
      approve(id) {
        var self = this;
        if(this.user.token == '') {
          this.$router.push({path: '/login/'})
          return
        }
        approveArticle(id).then(() => {
          self.post.approved_num += self.post.approved ? -1 : 1
          self.post.approved = !self.post.approved
          self.$emit('on-approved', self.post.id)
        })
      },
      toSpace(id) {
        var self = this
        if(this.user.token == '') {
          this.$router.push({path: '/login/'})
          return
        }
        this.$router.push({path: '/myspace', query: {id: id}})
      }
    },
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .post-card {   
    padding: 38px 26px 0px 26px;
    width: 714px;  
    background-color: #ffffff;
    border-radius: 4px;
    &:not(:first-child) {
      margin-top: 20px;
    }
    &:last-child {
      margin-bottom: 108px;
    }
    header {    
      display: flex;    
      align-items: center;    
      margin-bottom: 27px;
      font-size:12px;   
      color:#999999; 
      & > .clickable {
        transition: all 0.2s ease-in-out;
        &:hover {
          color: #5677fc;
        }
      }   
      img {
        width: 50px;    
        height: 50px;   
        border-radius: 100%;    
        margin-right: 24px;   
      }
      span {    
        font-size: 24px;
        color: #333333;
        &:not(:first-child) {   
          margin-left: 5px;   
        }
      }
      time {    
        margin-left: 12px;
        font-size: 18px;
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
        margin-bottom: 19px;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;

        color: #333333;
        font-size:24px;
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
        word-break: break-all;
        font-size:18px;
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
        margin-top: 33px;
        img {
          margin-right: 15px;
          width: 210px;
          height: 210px;
        }

      }
    }
    footer {
      margin-top: 40px;
      margin-left: -26px;
      margin-right: -26px;
      border-top: 1px solid #dcdcdc;
      padding: 14px 0;
      ul {
        display: flex;
        justify-content: space-around;
        font-size: 20px;
        height: 30px;
        align-items: center;
        li {
          transition: all 0.2s ease-in-out;
          cursor: pointer;
          flex-grow: 1;
          text-align: center;
          color: #666666;
          border-right: 2px solid #dcdcdc;
          .avatar-icon{
            margin-right: 20px;
            font-size: 24px;
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
