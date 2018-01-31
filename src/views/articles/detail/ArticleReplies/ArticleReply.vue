<template>
<div class="article-reply"
     :class="{'article-reply-new': reply.new}">
  <header>
      <h3>{{reply.user_name}}</h3>
      <time>{{reply.create_at | formatTime}}</time>
  </header>
  <p>{{ reply.comment }}</p>
  <div class="opts clearfix" v-loading="deleting">
      <span v-if="reply.user_id === user.id"
            :class="{'opt': true}"
            @click="del">
        删除
      </span>
  </div>
</div>
</template>

<script>
import { mapGetters } from "vuex";
import { deleteReply } from 'api/reply'
import ArticleReply from "./ArticleReply";

export default {
  name: "article-reply",
  props: {
    reply: {
      type: Object,
      required: true,
    },
    new: {
      type: Boolean,
      default: false,
    }
  },
  components: {
    ArticleReply
  },
  data() {
    return {
      deleting: false,
      deleted: false,
    };
  },
  computed: {
    ...mapGetters(["user"])
  },
  created() {},
  mounted() {
    let self = this;
    this.loading = true;

  },
  methods: {
    async del() {
      if(this.deleting) {
        return;
      }
      this.deleting = true;
      try {
        const res = await deleteReply(this.$route.params.id, this.reply.floor);
        this.deleted = true;
        this.$emit('delete-success', this.reply)
      } catch (e) {
        console.log(e)
      }
      this.deleting = false;

    }
  }
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
  &-new {
    // background: red;
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
