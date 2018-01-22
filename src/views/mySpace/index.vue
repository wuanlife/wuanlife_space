<template>
  <div id="my-space" class="my-space view-container">
      <aside>
          <user-card
            :user="users"
            class="user-card"></user-card>
      </aside>
      <section>
          <h1>最新内容</h1>
          <ul>
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
import { getUserById } from "api/user";
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
        total: 0,
        avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100'
      },
      pagination: {
        pageCount: 0,
        currentPage: 1,
        limit: 1
      }
    };
  },
  computed: {
    ...mapGetters(["user"])
  },
  mounted() {
    getUserById(Number(this.user.id)).then(res => {
      console.log(res)
      this.users.name = res.name
      this.users.avatar_url = res.avatar_url
    })
    this.loadPosts(1)
  },
  methods: {
    loadPosts(page) {
      const self = this
      getMyArticles({
        id: Number(this.user.id),
        offset: 20 * page,
        limit: 20
      }).then(res => {
        self.dates = res.articles
        self.users.total = res.total
        self.pagination.pageCount = (res.total % 20) === 0 ? (res.total % 20) : (res.total % 20) + 1
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
    .pagination{
      margin-bottom: 148px;
    }
  }
}
</style>
