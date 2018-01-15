<template>
<div class="article-reply">
  <header>
      <h3>{{reply.user_name}}</h3>
      <time>{{reply.create_at | formatTime}}</time>
  </header>
  <p>{{ reply.comment }}</p>
  <div class="opts clearfix">
      <span class="opt"
            @click="deleteReply">
        删除
      </span>
  </div>
</div>
</template>

<script>
import { mapGetters } from "vuex";
import ArticleReply from "./ArticleReply";

export default {
  name: "article-reply",
  props: [
    'reply'
  ],
  components: {
    ArticleReply
  },
  data() {
    return {};
  },
  computed: {
    ...mapGetters(["user"])
  },
  created() {},
  mounted() {
    let self = this;
    this.loading = true;

  },
  methods: {}
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.article-reply {
  margin: 0 17px;
  padding: 17px 0;
  &:not(:last-child) {
    border-bottom: 2px solid #c8c8c8;
    &::after {
      content: "";
      width: 80%;
      height: 1px;
    }
  }
  header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 50px;
    h3 {
      display: inline-block;
      font-size: 20px;
      color: #5677fc;
    }
    time {
      font-size: 16px;
      color: #434343;
    }
  }
  p {
    font-size: 20px;
    margin-bottom: 27px;
    color: #434343;
  }
  .opts {
    .opt {
      float: right;
      cursor: pointer;
      font-size: 16px;
      color: #5677fc;
      transition: all 0.3s ease-in-out;
      &:hover {
        color: #0040b9;
      }
    }
  }
}
</style>
