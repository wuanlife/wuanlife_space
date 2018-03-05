<template>
  <div id="my-space" class="my-space view-container">
      <aside>
          <user-card
            :user="users"
            class="user-card"></user-card>
      </aside>
      <section>
          <h1 class="wl-card">最新内容</h1>
          <ul v-loading="loading" ref="card-list">
              <div class="loading" v-if="loading || isNull">{{ message }}</div>
              <post-card
                v-for="(date, index) in dates"
                :key="index"
                :post.sync="date"></post-card>
          </ul>
          <pagination
            class="pagination"
            @current-change="loadPosts"
            :pagination.sync="pagination"></pagination>
      </section>
  </div>
</template>

<script>
import PostCard from 'components/PostCard'
import UserCard from 'components/UserCard'
import Pagination from 'components/Pagination'
import { getMyArticles } from 'api/post'
import { mapGetters } from 'vuex'
export default {
  name: 'mySpace',
  components: {
    PostCard,
    UserCard,
    Pagination
  },
  data () {
    return {
      dates: [],
      users: {
        name: '',
        articles_num: 0,
        avatar_url: 'http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg'
      },
      pagination: {
        pageCount: 0,
        currentPage: 1,
        limit: 1
      },
      id: 0,
      loading: false
    }
  },
  computed: {
    ...mapGetters(['user']),
    isNull: function () {
      if (this.dates === []) {
        // this.message = '还未发表东西，快去发表吧！'
        return true
      } else {
        // this.message = ''
        return false
      }
    },
    message: function () {
      if (this.dates === []) {
        return '还未发表东西，快去发表吧！'
      } else {
        return ''
      }
    }
  },
  mounted () {
    if (this.$route.params.id) {
      this.id = this.$route.params.id
    } else {
      this.id = this.user.id
    }
    this.loadPosts(1)
  },
  methods: {
    loadPosts (page) {
      const self = this
      this.loading = true
      getMyArticles({
        id: self.id,
        offset: 20 * (page - 1),
        limit: 20
      }).then(res => {
        if (res.author.id === this.user.id) {
          document.title = '我的空间 - 午安网 - 过你想过的生活'
        } else {
          document.title = `${res.author.name}的空间 - 午安网 - 过你想过的生活`
        }
        res.articles.forEach(element => {
          element.author = res.author
        })
        self.dates = res.articles
        self.users.id = res.author.id
        self.users.name = res.author.name
        self.users.articles_num = res.author.articles_num
        self.users.avatar_url = res.author.avatar_url
        self.pagination.pageCount = Math.ceil(res.author.articles_num / 20)
        self.loading = false
        self.$refs['card-list'].scrollIntoView()
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
#my-space {
  max-width: 739px;
}
.my-space {
  aside {
    .user-card{
      margin-top: 64px;
    }
  }
  section {
    h1 {
      background-color: #ffffff;
      border-radius: 4px;
      padding: 11px 12px;
      margin: 64px 0 6px 0;
      font-size: 20px;
      color: #5677fc;
    }
    .loading{
      min-height: 300px;
    }
    .pagination{
      margin-bottom: 148px;
    }
  }
}
</style>
