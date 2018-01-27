<!-- 
  TODO:
    1. Add effect and cursor for <h1> tag
    2. group can be clicked.
 -->

<template>
    <div class="collection-container view-container">
      <section>     
        <header>
            我的收藏
        </header>
        <div class="empty-container" v-if="empty">
          <h2>你还没有任何收藏哦~</h2>
        	<img src="../../assets/404_images/404_box.png"/>        	
        </div>
        <div class="collection-tabcontent" v-else>
          <ul class="collection-cards animated fadeInUp">
            	<collection-card :item.sync='item' 
              v-for="item in collecations"
            	></collection-card>
          </ul>
        </div>
        <pagination @current-change="getCollection" :pagination.sync="pagination"></pagination>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getCollection } from 'api/post';
  import CollectionCard from 'views/collection/CollectionCard';
  import Pagination from 'components/Pagination'
  
  export default {
    name: 'collection-container',
    data() {
      return {
        collecations: [],
        loading: false,
        pagination: {
          pageCount: 1,
          currentPage: 1,
          limit: 20,
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
        'access_token',
      ])
    },
    mounted() {
      this.getCollection(1);
    },
    methods: {
      getCollection(page) {
        var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          getCollection((page - 1)*self.pagination.limit || 0, self.pagination.limit).then(res => {
            console.log(res);
            if(res.articles.length==0) {
              self.empty = true;
            }else {
              self.empty = false;
            for (let i  = 0, j = res.articles.length; i < j; i++) {
              res.articles[i].create_at = res.articles[i].create_at;
            }
            self.collecations = res.articles;
             //动态生成分页页码
            self.pagination.pageCount=Math.ceil(res.total/self.pagination.limit);
            }
            self.loading = false;
            resolve();
          }).catch(reeor => {
            self.loading = false;
            reject(error);
          })
        })
      },
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
        margin: 31px 0 12px 0;
        font-family:MicrosoftYaHei-Bold;
        font-size:32px;
        color:#5677fc;
        background-color: white;
        height: 66px;
        line-height: 66px;
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
