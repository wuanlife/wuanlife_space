<template>
<div class="article-reply-input">
  <!-- 也许可以用el-input -->
  <textarea v-model="replyContent" @keydown.enter="reply" placeholder="我的回复..."></textarea>
  <el-button class="wuan-button submit"
             :loading="loading"
             @click="reply">
    回复
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
          offset: 100
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
    margin-bottom: 13px;
    width: 100%;
    resize: none;
    padding: 16px;
    border-radius: 4px;
    border: solid 2px #c8c8c8;
    transition: all 0.3s ease-in-out;
    &:focus {
      border: solid 2px #5677fc;
    }
    &::placeholder {
      color: #757575;
    }
  }
  .submit {
    padding: 9px 29px;
    font-size: 20px;
    color: #ffffff;
  }
}
</style>
