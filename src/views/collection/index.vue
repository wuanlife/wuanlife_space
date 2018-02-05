<!--
  TODO:
    1. Add effect and cursor for <h1> tag
    2. group can be clicked.
 -->

<template>
    <div class="collection-container view-container">
      <section>
        <header class="wl-card">
            我的收藏
        </header>
        <div class="empty-container wl-card" v-if="empty">
          <h2>你还没有任何收藏哦~</h2>
          <img :src="Box404"/>
        </div>
        <div class="collection-tabcontent" v-else>
          <div class="collection-cards">
            <transition-group
              @beforeEnter="beforeEnter"
              @enter="enter"
              tag="ul"
              >
              <collection-card :item.sync='item'
                v-for="(item,index) in collections"
                :data-index = "index"
                :key="item.create_at"
                >
              </collection-card>
            </transition-group>
          </div>
        </div>
        <pagination @current-change="getCollection" :pagination.sync="pagination"></pagination>
      </section>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { getCollection } from 'api/post'
import CollectionCard from 'views/collection/CollectionCard'
import Pagination from 'components/Pagination'
import Box404 from '@/assets/404_images/Box404.png'
export default {
  name: 'collection-container',
  data () {
    return {
      Box404,
      collections: [],
      loading: false,
      pagination: {
        pageCount: 1,
        currentPage: 1,
        limit: 20
      },
      empty: false
    }
  },
  components: {
    CollectionCard,
    Pagination
  },
  computed: {
    ...mapGetters([
      'access_token'
    ])
  },
  mounted () {
    this.getCollection(1)
  },
  methods: {
    getCollection (page) {
      var self = this
      this.loading = true
      return new Promise((resolve, reject) => {
        getCollection((page - 1) * self.pagination.limit || 0, self.pagination.limit).then(res => {
          if (res.articles.length === 0) {
            self.empty = true
          } else {
            self.empty = false
            for (let i = 0, j = res.articles.length; i < j; i++) {
              res.articles[i].create_at = res.articles[i].create_at
            }
            self.collections = res.articles
            // 动态生成分页码
            self.pagination.pageCount = Math.ceil(res.total / self.pagination.limit)
          }
          resolve()
        }).catch(error => {
          reject(error)
        })
      })
    },
    beforeEnter (el) {
      el.style.opacity = 0
      el.style.transform = 'translateY(20px)'
      el.style.transition = 'all 1s ease'
    },
    enter (el) {
      var delay = el.dataset.index * 500
      setTimeout(() => {
        el.style.opacity = 1
        el.style.transform = 'translateY(-20px)'
      }, delay)
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .collection-container {
    display: flex;
    justify-content: center;
    margin: auto;
    max-width: 900px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 714px;
      header {
        margin: 31px 0 32px 0;
        font-size:20px;
        color:#5677fc;
        background-color: white;
        height: 42px;
        line-height: 42px;
        padding-left: 17px;

      }
    }
  }
  .empty-container {
    color: rgba(0,0,0,0.4);
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    height: 400px;
      h2 {
        font-size: 20px;
      }
    /*img {
      width: 200px;
      height: 200px;
      overflow: hidden;
    }*/
  }
  .collection-tabcontent {
    min-height: 200px;
    margin-top: 5px;
  }
</style>
