<template>
    <div class="relatedPlanets-container">
      <section>
        <header>相关用户</header>
        <div class="relatedUsersCardBox" v-loading='loading'>
          <search-user v-for="user in relatedUsers" class="search-card" :user.sync="user">           
          </search-user>
        </div>
        <header>相关文章</header>
        <div class="relatedArticlesCardBox" v-loading='loading1'>
          <ul class="index-cards">
            <search-article v-for="item in relatedArticles" :item.sync="item">
            </search-article>
          </ul>
        </div>
        <pagination :pagination.sync="pagination"></pagination>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { searchArticles,searchUsers } from 'api/post';
  import SearchArticle from 'components/SearchArticle';
  import SearchUser from 'components/SearchUser';
  import Pagination from 'components/Pagination'
  import { searchPosts } from 'api/post';
  
  export default {
    name: 'relatedPlanets-container',
    data() {
      return {
        relatedPlantesData: [],
        relatedUsers: [],
        relatedArticles: [],
        loading: false,
        loading1: false,
        i: 0,
        pagination: {
          pageCount: 245,
          currentPage: 1,
          limit: 2,
        }
      }
    },
    components: {
      SearchArticle,
      SearchUser,
      Pagination
    },
    mounted () {
      this.getSearchUsers();
      this.getSearchArticles(0, 20);
    },
    computed: {
      ...mapGetters([
        'user',
      ])
    },
    methods: {
      getSearchUsers () {
        var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          searchUsers(this.$route.query.search, 0, 20).then(res => {
            if(res.users.length < 3){
              self.relatedUsers = res.users;
            }else {
              for(let i = 0,j = 3; i < j; i++){
                self.relatedUsers[i] = res.users[i];
              }
            }
            self.loading = false;
            resolve();
          }).catch(error => {
            self.loading = false;
            reject(error);
          });
        });
      },
      getSearchArticles (offset, limit) {
        var self = this;
        this.loading1 = true;
        return new Promise((resolve, reject) => {
          searchArticles(this.$route.query.search, offset, limit).then(res => {
            //动态确定页码
            self.pagination.pageCount = Math.ceil(res.total/ limit);
            console.log(res.total);
            console.log(limit);
            console.log(self.pagination.pageCount);
            self.relatedArticles = self.dealTime(res.articles);
            self.loading1 = false;
            resolve();
          }).catch(error => {
            self.loading1 = false;
            reject(error);
          });
        });
      },
      showMorePlanets () {
        this.morePlanetsBtn = false
        this.relatedPlantesData1 = this.relatedPlantesData
      },
      dealTime (arr) {
        for (let i = 0, j = arr.length; i < j; i++) {
          arr[i].create_at = arr[i].create_at.slice(0, 10) + ' ' + arr[i].create_at.slice(11, 16)
        }
        return arr
      },
      loadMore () {
        let self = this
        self.i += 1
        searchPosts(this.$route.query.search, 20 * self.i, 20).then(res => {
            self.relatedPostsData = self.relatedPostsData.concat(self.dealTime(res.data));
            self.loading1 = false;
          }).catch(error => {
            self.loading1 = false;
          });
      }
    },
    watch: {
      'pagination.currentPage': function(val) {
        var offset =  val -1;
        getSearchArticles (offset);
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
        font-family:PingFangHK-Medium;
        font-size:32px;
        color:#5677fc;
        height: 66px;
        line-height: 66px;
        background-color: white;
      }
      .relatedUsersCardBox {
      	display: flex;
      	justify-content: space-between;
      }
      .relatedArticlesCardBox {
       
      }
    }
  }  
  /*.index-cards{
  	margin: auto;
  }*/
</style>
<style type="text/css">
  .el-card__body {
    padding: 0;
  }
.load-more{
  width: 100%;
  margin-bottom: 10px;
  border: 0;
  height: 30px;
  line-height: 30px;
  color: #5677fc;
  outline: none;
}

</style>
