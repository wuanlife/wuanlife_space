<template>
    <div class="index-visitor-container">
      <section>     
        <header>
          最新话题
        </header>
        <div class="index-tabcontent" v-loading="loading">
          <ul v-if="posts.length > 0" class="index-cards">
            <post-card v-for="post of posts" 
                       :key="post.id" 
                       :post.sync="post">  
            </post-card>
          </ul>
          <!--<el-pagination layout="prev, pager, next, jumper"
                         :page-count="pagination.pageCount"
                         @current-change="loadPosts">
          </el-pagination>-->
          	<!--<pagination @click.native="loadPosts(pagination.currentPage)" :pagination.sync="pagination"></pagination>-->          	
        </div>
        <pagination @loadPosts="loadPosts" :pagination.sync="pagination"></pagination>
      </section>
      <aside>
        <header>
          活跃用户
        </header>
        <div class="aside-content" v-loading="loadingAside">
          <div v-for="activeuser of activeUsers" 
            class="index-aside-card wuan-card clickable"
            @click="user.token=='' ? $router.push({path: '/login/'}) : $router.push({path: '/mySpace/'})"
            >
             <!--+ activeuser.author.id, query: { name: activeuser.author.name }-->
            <img :src="activeuser.avatar_url">
            <div class="wuan-card__content">
              <h2 class="clickable">{{ activeuser.name }}</h2>
              <p>本月发表了{{activeuser.monthly_articles_num}}</p>
            </div>
          </div>
        </div>
        <!--<footer>
          <span class="clickable" @click="$router.push({path: `/universe`, query: { name: '全部星球'}})">全部星球</span>
          <span class="clickable">创建星球</span>
        </footer>-->
      </aside>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { parseQueryParams } from 'utils/url';
  import { getPosts, getMockTest, getArticles } from 'api/post';

  import { getGroups } from 'api/group';
  import PostCard from 'components/PostCard'
  import Pagination from 'components/Pagination'
  export default {
    name: 'index-visitor',
    components: {
      PostCard,
      Pagination
    },
    data() {
      return {
        loading: false,
        loadingAside: false,
        posts: [],
        activeUsers: [],
        discoveryGroups: [],
        pagination: {
          pageCount: 245,
          currentPage: 1,
          limit: 20,
        }
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ]),
    },
    mounted() {
      //获取最新内容数据
      getMockTest().then(res => {
        console.log(res);
      })
      this.loadPosts(1);
    },
    methods: {
      loadPosts(page) {
        var self = this;
        this.loading = true;
        console.log(page);
        return new Promise((resolve, reject) => {
          getArticles(true, (page-1) || 0, self.pagination.limit).then(res => {
            console.dir(res);
            self.posts = res.articles;
            if(res.au)
            self.activeUsers = res.au;
            //动态生成分页页码
            self.pagination.pageCount=Math.ceil(res.total/self.pagination.limit);
            self.loading = false;
            // pagination
            let pageFinal = parseQueryParams(res.paging.final);
            self.pagination.pageCount = Math.ceil(pageFinal.offset / pageFinal.limit) + 1;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
      loadGroups() {
        var self = this;
        this.loadingAside = true;
        return new Promise((resolve, reject) => {
          getGroups().then(res => {
            self.discoveryGroups = res.data;
            self.loadingAside = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .index-visitor-container {
    display: flex;
    justify-content: space-between;
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
        margin: 31px 0 12px 0;
        padding-left: 17px;
        font-family: MicrosoftYaHei-Bold;
	    font-size: 32px;
        color:#5677fc;
        height: 66px;
        line-height: 66px;
        background-color: white;
      }
    }
    aside {
      margin-left: 41px;
      flex: 0 0 250px;
      @media screen and (max-width: 900px) {
        display: none;
      }
      header {
       
          margin: 31px 0 12px 0;
        padding-left: 17px;
        font-family: MicrosoftYaHei-Bold;
	    font-size: 32px;
        color:#5677fc;
        height: 66px;
        line-height: 66px;
        background-color: white;
        
      }
      .aside-content {
        min-height: 100px;
        .index-aside-card {
          width: 250px;
          height: 70px;
        }
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
