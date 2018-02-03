<template>
<div class="article-reply"
     :class="{'article-reply-new': reply.new}">
  <header>
      <h3 @click="$router.push({path: `/mySpace/${reply.user.id}`})">{{reply.user.name}}</h3>
      <time>{{reply.create_at | formatTime}}</time>
  </header>
  <p>{{ reply.comment }}</p>
  <div class="opts clearfix" v-loading="deleting">
      <span v-if="reply.user.id === user.id"
            :class="{'opt': true}"
            @click="del">
        删除
      </span>
  </div>
</div>
</template>

<script>
import { mapGetters } from 'vuex'
import { deleteReply } from 'api/reply'
import ArticleReply from './ArticleReply'

export default {
  name: 'article-reply',
  props: {
    reply: {
      type: Object,
      required: true
    },
    new: {
      type: Boolean,
      default: false
    }
  },
  components: {
    ArticleReply
  },
  data () {
    return {
      deleting: false,
      deleted: false
    }
  },
  computed: {
    ...mapGetters(['user'])
  },
  created () {},
  mounted () {
    this.loading = true
  },
  methods: {
    async del () {
      if (this.deleting) {
        return
      }
      this.deleting = true
      try {
        await deleteReply(this.$route.params.id, this.reply.floor)
        this.deleted = true
        this.$emit('delete-success', this.reply)
      } catch (e) {
        console.log(e)
      }
      this.deleting = false
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.article-reply {
  margin: 0 5px;
  padding: 21px 0 7px 0;
  &:not(:last-child) {
    border-bottom: 2px solid #c8c8c8;
    &::after {
      content: "";
      width: 80%;
      height: 1px;
    }
  }
  &-new {
    // background: red;
  }
  header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 31px;
    h3 {
      display: inline-block;
      font-size: 12px;
      color: $wl-blue;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
      &:hover {
        color: $wl-blue-hover;
      }
      &:active {
        color: $wl-blue-active;
      }
    }
    time {
      font-size: 10px;
      color: #434343;
    }
  }
  p {
    font-size: 12px;
    margin-bottom: 16px;
    color: #434343;
  }
  .opts {
    .opt {
      float: right;
      cursor: pointer;
      font-size: 10px;
      color: $wl-blue;
      transition: all 0.3s ease-in-out;
      &:hover {
        color: $wl-blue-hover;
      }
    }
  }
}
</style>
