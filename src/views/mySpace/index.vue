<template>
  <div id="my-space" class="my-space view-container">
      <aside>
          <user-card
            :user="users"
            class="user-card"></user-card>
      </aside>
      <section>
          <h1>最新内容</h1>
          <ul class="mySpace-content" v-loading="loading">
              <post-card
                v-for="(date, index) in dates"
                :key="index"
                :post.sync="date"></post-card>
          </ul>
          <pagination
            class="pagination"
            @loadPosts="loadPosts"
            :pagination.sync="pagination"></pagination>
      </section>
  </div>
</template>

<script>
import PostCard from "components/PostCard";
import UserCard from "components/UserCard";
import Pagination from "components/Pagination";
import { getMyArticles } from "api/post";
import { mapGetters } from "vuex";
export default {
  name: "mySpace",
  components: {
    PostCard,
    UserCard,
    Pagination
  },
  data() {
    return {
      dates: [],
      users: {
        name: '',
        articles_num: 0,
        avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
      },
      pagination: {
        pageCount: 0,
        currentPage: 1,
        limit: 1
      },
      id: 0,
      loading: false
    };
  },
  computed: {
    ...mapGetters(["user"])
  },
  mounted() {
    if(this.$route.params.id) {
      this.id = this.$route.params.id
    } else {
      this.id = this.user.id
    }
    this.loadPosts(1)
  },
  methods: {
    loadPosts(page) {
      const self = this
      this.loading = true
      getMyArticles({
        id: self.id,
        offset: 20 * (page - 1),
        limit: 20
      }).then(res => {
        res.articles.forEach(element => {
          element.author = res.author
        })
        self.dates = res.articles
        self.users.name = res.author.name
        self.users.articles_num = res.author.articles_num
        self.users.avatar_url = res.author.avatar_url
        self.pagination.pageCount = Math.ceil(res.author.articles_num / 20)
        self.loading = false
      }).catch(err => {
        console.log(err)
      })
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
#my-space {
  max-width: 1180px;
}
.my-space {
  aside {
    .user-card{
      margin-top: 71px;
    }
  }
  section {
    h1 {
      background-color: #ffffff;
      border-radius: 4px;
      padding: 17px 18px;
      margin: 71px 0 12px 0;
      font-size: 32px;
      color: #5677fc;
    }
    :mySpace-content{
      min-height: 400px;
    }
    .pagination{
      margin-bottom: 148px;
    }
  }
}
</style>
