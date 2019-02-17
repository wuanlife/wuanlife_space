<template>
  <div class="index-visitor-container" ref="root">
    <aside>
      <header class="wl-card">
        活跃用户
      </header>
      <div class="aside-content wl-card" v-loading="loadingAside">
        <aside-card v-for="activeUser of activeUsers"
          :key="`activeUser-${activeUser.id}`"
          :activeUser="activeUser"
          >
        </aside-card>
      </div>
    </aside>
    <section>
      <header class="wl-card">
        最新话题
      </header>
      <div class="index-tabcontent" v-loading="loading">
        <ul v-if="posts.length > 0" class="index-cards">
            <post-card v-for="post of posts"
                       :key="`post-${post.id}`"
                       :post.sync="post">
            </post-card>
        </ul>
        <!--<el-pagination layout="prev, pager, next, jumper"
                         :page-count="pagination.pageCount"
                         @current-change="loadPosts">
          </el-pagination>-->
      </div>
      <pagination @current-change="loadPosts" :pagination.sync="pagination" @nextPage="nextPage" @previousPage="previousPage"></pagination>
    </section>
  </div>
</template>

<script>
// import { mapGetters } from 'vuex';
import { getArticles } from 'api/post'
import { getActiveUsers } from 'api/user'
// import { getGroups } from 'api/group';
import PostCard from 'components/PostCard'
import Pagination from 'components/Pagination'
import AsideCard from './AsideCard'
export default {
  name: 'index-visitor',
  components: {
    PostCard,
    Pagination,
    AsideCard
  },
  data () {
    return {
      loading: false,
      loadingAside: false,
      posts: [],
      activeUsers: [],
      discoveryGroups: [],
      pagination: {
        pageCount: 1,
        currentPage: 1,
        limit: 20
      }
    }
  },
  //  computed: {
  //    ...mapGetters([
  //      'user',
  //    ]),
  //  },
  mounted () {
    this.loadPosts(1)
    this.loadActiveUsers()
  },
  updated () {
    this.$refs.root.scrollIntoView()
  },
  methods: {
    loadPosts (page) {
      var self = this
      this.loading = true
      return new Promise((resolve, reject) => {
        getArticles((page - 1) * self.pagination.limit || 0, self.pagination.limit).then(res => {
          self.posts = res.articles
          // 动态生成分页页码
          self.pagination.pageCount = Math.ceil(res.total / self.pagination.limit)
          self.loading = false
          resolve()
        }).catch(err => {
          console.log(err)
        })
      })
    },
    nextPage () { // 下一页
      this.loadPosts(this.pagination.currentPage)
    },
    previousPage () { // 上一页
      this.loadPosts(this.pagination.currentPage)
    },
    loadActiveUsers () {
      var self = this
      this.loading = true
      return new Promise((resolve, reject) => {
        getActiveUsers().then(result => {
          self.activeUsers = result.au
          resolve()
        }).catch(err => {
          console.log(err)
        })
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .index-visitor-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 1000px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      //flex: 0 0 448px;
      flex: 1 1 640px;
      order: 1;
      header {
        margin: 31px 0 12px 0;
        padding-left: 17px;
        font-size: 20px;
        color: #5677fc;
        height: 42px;
        line-height: 42px;
        background-color: white;
      }
    }
    aside {
      margin-left: 56px;
      flex: 0 0 262px;
      order: 2;
      @media screen and (max-width: 900px) {
        display: none;
      }
      header {
        margin: 31px 0 12px 0;
        padding-left: 17px;
        font-family: MicrosoftYaHei-Bold;
        font-size: 20px;
        color: #5677fc;
        height: 42px;
        line-height: 42px;
        background-color: white;
      }
      .aside-content {
        min-height: 100px;
        /*.index-aside-card {
          width: 250px;
          height: 70px;
        }*/
      }
    }
  }

  .index-tabcontent {
    min-height: 200px;
    margin-top: 5px;
    margin-bottom: 20px;
    .el-pagination {
      text-align: center;
    }
  }

  // post card style
  .index-cards {
    .index-card {
      padding: 10px 16px 12px 16px;
      background-color: #ffffff;
      &:not(:first-child) {
        margin-top: 8px;
      }
    }
  }
</style>
