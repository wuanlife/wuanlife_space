<template>
<div class="article-reply-input">
  <!-- 也许可以用el-input -->
  <textarea v-model="replyContent" @keydown.enter="reply" placeholder="我的回复..."></textarea>
  <el-button class="wl-btn article-replay-btn"
             :loading="loading"
             @click="reply">回复
  </el-button>
</div>
</template>

<script>
import { mapGetters } from 'vuex'
import { Notification } from 'element-ui'
import { postReply } from 'api/reply'

export default {
  name: 'article-reply-input',
  components: {
  },
  data () {
    return {
      replyContent: '',
      loading: false
    }
  },
  computed: {
    ...mapGetters([
      'user'
    ])
  },
  created () {

  },
  updated () {
    console.log(this)
  },
  mounted () {
  },
  methods: {
    async reply (e) {
      e.preventDefault()
      if (this.replyContent === '') {
        Notification.info('评论不能为空')
        return
      }
      this.loading = true
      try {
        const res = await postReply(this.$route.params.id, {
          comment: this.replyContent
        })
        this.$emit('reply-success', res)
        this.replyContent = ''
      } catch (e) {
        Notification.error({
          message: e.data.error || '未知错误',
          offset: 60
        })
        console.log(e)
      }
      this.loading = false
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.article-reply-input {
  textarea {
    font-size: 13px;
    margin-bottom: 31px;
    width: 100%;
    height: 96px;
    resize: none;
    padding: 13px 10px;
    border-radius: 4px;
    border: solid 1px rgba(204, 204, 204, 0.3);
    transition: all 0.3s ease-in-out;
    background: rgba(204, 204, 204, 0.3);
    color: #999999;
    &:hover {
      background: white;
    }
    &:focus {
      background: white;
    }
    &::placeholder {
      color: #999999;
    }
  }
  .submit {
    padding: 9px 29px;
    font-size: 12px;
    color: #ffffff;
  }
  .article-replay-btn {
    font-size: 15px;
    padding: 8px 32px;
  }
}
</style>
