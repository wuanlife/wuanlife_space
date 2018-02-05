<template>
  <div class="relatedPlanets-container view-container">
    <section>
      <header>相关用户</header>
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
      <header>相关文章</header>
      <div class="empty-container" v-if="emptyArticle">
        <h2>没有匹配到任何文章</h2>
      </div>
      <div class="relatedArticlesCardBox" v-loading='loading1' v-else>
        <ul class="index-cards">
          <search-article v-for="item in relatedArticles"
                          :key="item.id"
                          :item.sync="item">
          </search-article>
        </ul>
      </div>
      <pagination :pagination.sync="pagination" @current-change="getSearchUsers"></pagination>
    </section>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { searchArticles } from 'api/post'
import { searchUsers } from 'api/user'
import SearchArticle from 'views/search/SearchArticle'
import SearchUser from 'views/search/SearchUser'
import Pagination from 'components/Pagination'

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
        limit: 2
      },
      emptyUser: false,
      emptyArticle: false
    }
  },
  components: {
    SearchArticle,
    SearchUser,
    Pagination
  },
  mounted () {
    this.getSearchUsers()
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
        searchUsers(this.$route.query.search, page - 1 || 0, 20)
          .then(res => {
            if (res.users.length === 0) {
              self.emptyUser = true
            } else {
              self.emptyUser = false
              if (res.users.length < 3) {
                self.relatedUsers = res.users
              } else {
                for (let i = 0, j = 3; i < j; i++) {
                  self.relatedUsers[i] = res.users[i]
                }
              }
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
              self.pagination.pageCount = Math.ceil(
                res.total / self.pagination.limit
              )
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
  max-width: 714px;
  min-width: 380px;
  section {
    /*flex: 1;*/
    > header {
      margin: 31px 0 12px 0;
      padding-left: 17px;
      font-family: PingFangHK-Medium;
      font-size: 20px;
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
      height: 200px;
      align-items: center;
    }
    .relatedArticlesCardBox {
    }
  }
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
