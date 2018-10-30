<template>
  <div class="relatedPlanets-container view-container">
    <section>
      <header class="wl-card">相关用户</header>
      <div class="empty-container" v-if="emptyUser">
        <h2>没有匹配到任何用户</h2>
      </div>
      <div class="relatedUsersCardBox" v-loading='loading' v-else>
        <search-user v-for="user in relatedUsers"
                     :key="user.id"
                     class="search-card"
                     :user.sync="user">
        </search-user>
      </div>
      <header class="wl-card">相关文章</header>
      <div class="empty-container" v-if="emptyArticle">
        <h2>没有匹配到任何文章</h2>
      </div>
      <div class="relatedArticlesCardBox wl-card" v-loading='loading1' v-else>
        <ul class="index-cards">
          <post-card v-for="post of relatedArticles"
            :key="post.id"
            :post.sync="post"
            class="post-card"
            :footer="false">
          </post-card>
        </ul>
      </div>
      <pagination :pagination.sync="pagination" @current-change="getSearchUsers" @nextPage="nextPage" @previousPage="previousPage"></pagination>
    </section>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { searchArticles } from 'api/post'
import { searchUsers } from 'api/user'
import SearchUser from 'views/search/SearchUser'
import Pagination from 'components/Pagination'
import PostCard from 'components/PostCard'
export default {
  name: 'relatedPlanets-container',
  data () {
    return {
      relatedPlantesData: [],
      relatedUsers: [],
      relatedArticles: [],
      loading: false,
      loading1: false,
      i: 0,
      pagination: {
        pageCount: 1,
        currentPage: 1,
        limit: 3
      },
      emptyUser: false,
      emptyArticle: false
    }
  },
  components: {
    SearchUser,
    Pagination,
    PostCard
  },
  mounted () {
    this.getSearchUsers(1)
    this.getSearchArticles(1)
  },
  computed: {
    ...mapGetters(['user'])
  },
  methods: {
    getSearchUsers (page) {
      var self = this
      this.loading = true
      return new Promise((resolve, reject) => {
        searchUsers(this.$route.query.search, page - 1 || 0, self.pagination.limit)
          .then(res => {
            if (res.users.length === 0) {
              self.emptyUser = true
            } else {
              self.emptyUser = false
              // 动态确定页码
              let a = Math.ceil(
                res.total / self.pagination.limit
              )
              if (a > self.pagination.pageCount) {
                self.pagination.pageCount = a
              }
              self.relatedUsers = res.users
            }
            self.loading = false
            resolve()
          })
          .catch(error => {
            self.loading = false
            reject(error)
          })
      })
    },
    getSearchArticles (offset) {
      var self = this
      this.loading1 = true
      return new Promise((resolve, reject) => {
        searchArticles(
          this.$route.query.search,
          offset - 1 || 0,
          self.pagination.limit
        )
          .then(res => {
            if (res.articles == null) {
              self.emptyArticle = true
            } else {
              self.emptyArticle = false
              // 动态确定页码
              let a = Math.ceil(
                res.total / self.pagination.limit
              )
              if (a > self.pagination.pageCount) {
                self.pagination.pageCount = a
              }
              self.relatedArticles = res.articles
            }
            self.loading1 = false
            resolve()
          })
          .catch(error => {
            self.loading1 = false
            reject(error)
          })
      })
    },
    showMorePlanets () {
      this.morePlanetsBtn = false
      this.relatedPlantesData1 = this.relatedPlantesData
    },
    loadMore () {
      let self = this
      self.i += 1
      searchArticles(this.$route.query.search, 20 * self.i, 20)
        .then(res => {
          self.relatedPostsData = self.relatedPostsData.concat(
            self.dealTime(res.data)
          )
          self.loading1 = false
        })
        .catch(error => {
          console.log(error)
          self.loading1 = false
        })
    },
    query (page) {
      this.getSearchUsers(page)
      this.getSearchArticles(page)
    },
    nextPage () {
      this.query(this.pagination.currentPage)
    },
    previousPage () {
      this.query(this.pagination.currentPage)
    }
  },
  watch: {
    'pagination.currentPage': function (val) {
      // var offset = val - 1
      // getSearchArticles(offset)
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.relatedPlanets-container {
  display: flex;
  justify-content: space-between;
  margin: auto;
  max-width: 680px;
  min-width: 380px;
  section {
    flex: 1;
    > header {
      margin: 31px 0 12px 0;
      padding-left: 17px;
      font-family: PingFangHK-Medium;
      font-size: 18px;
      font-weight: bold;
      color: #5677fc;
      height: 42px;
      line-height: 42px;
      background-color: white;
    }
    .empty-container {
      color: rgba(0, 0, 0, 0.4);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 150px;
      h2 {
        font-size: 20px;
      }
    }
    .relatedUsersCardBox {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .relatedArticlesCardBox {
      background: transparent;
      .post-card {
        width: inherit;
      }
    }
  }
}
.index-cards > li.post-card {
  margin-top: 32px;
}
</style>
<style type="text/css">
.el-card__body {
  padding: 0;
}

.load-more {
  width: 100%;
  margin-bottom: 10px;
  border: 0;
  height: 30px;
  line-height: 30px;
  color: #5677fc;
  outline: none;
}
</style>
