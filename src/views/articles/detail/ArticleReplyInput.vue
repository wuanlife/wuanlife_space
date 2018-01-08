<template>
<div class="article-reply-input">
  <textarea placeholder="我的回复..."></textarea>
  <el-button class="wuan-button submit"
             @click="reply">
    回复
  </el-button>
</div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'article-reply-input',
  components: {
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
  created() {

  },
  mounted() {
    let self = this;
    this.loading = true;
  },
  methods: {
    reply() {

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
    &::-webkit-input-placeholder {
      color: #757575;
    }
    &:-moz-placeholder { /* Firefox 18- */
      color: #757575;  
    }
    &::-moz-placeholder {  /* Firefox 19+ */
      color: #757575;  
    }
    &:-ms-input-placeholder {
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
