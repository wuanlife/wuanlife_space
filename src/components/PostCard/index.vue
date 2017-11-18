<template>
  <li class="post-card">
    <header>
      <img :src="post.author.avatar_url">
      <span class="clickable">{{ post.author.name }}</span>
      <span>发表于</span>
      <span class="clickable"
        @click="$router.push({path: '/planet/' + post.group.id, query: { name: post.group.name }})">
        {{ post.group.name }}
      </span>
      <time>{{ post.create_time | formatTime }}</time>
      <post-state v-if="post.sticky" :text="'置顶'" :color="'#5992e4'"></post-state>
      <post-state v-if="post.lock" :text="'锁定'" :color="'#ccc'"></post-state>
    </header>
    <div class="post-card-content">
      <h1 @click="$router.push({path: `/topic/${post.id}`, query: { name: post.title }})">{{ post.title }}</h1>
      <div class="preview-html" v-html="post.content">
      </div>
      <div class="preview-imgs">
        <img v-for="img of post.image_url" :src="img">
      </div>
    </div>
    <footer>
      <ul>
        <li @click="$router.push({path: `/topic/${post.id}`, query: { name: post.title }})" :class="{'done': post.replied}">评论 {{ post.replied_num }}</li>
        <li @click="approve(post.id)" :class="{'done': post.approved}">点赞 {{ post.approved_num }}</li>
        <li @click="collect(post.id)" :class="{'done': post.collected}">收藏 {{ post.collected_num }}</li>
      </ul>
    </footer>
  </li> 
</template>

<script>
  import { mapGetters } from 'vuex';
  import { parseTime } from 'utils/date';
  import PostState from 'components/PostState/PostState';
  import { 
    approvePost,
    collectPost,
  } from 'api/post';

  export default {
    name: 'post-card',
    components: {
      PostState
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
        collectPost({
          id: id,
          userid: self.user.userInfo.id,
        }).then(() => {
          self.post.collected_num += self.post.collected ? -1 : 1
          self.post.collected = !self.post.collected
          self.$emit('on-collected', self.post.id)
        })
      },
      approve(id) {
        var self = this;
        if(this.user.token == '') {
          this.$router.push({path: '/login/'})
          return
        }
        approvePost({
          id: id,
        }).then(() => {
          self.post.approved_num += self.post.approved ? -1 : 1
          self.post.approved = !self.post.approved
          self.$emit('on-approved', self.post.id)
        })
      }
    },
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .post-card {   
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
    div.post-card-content {
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
</style>
