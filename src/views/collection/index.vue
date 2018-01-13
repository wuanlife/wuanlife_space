<!-- 
  TODO:
    1. Add effect and cursor for <h1> tag
    2. group can be clicked.
 -->

<template>
    <div class="collection-container">
      <section>     
        <header>
            我的收藏
        </header>
        <div class="collection-tabcontent">
          <ul class="collection-cards">
            	<collection-card :item.sync='item' v-for="item in collecations"></collection-card>
              <!--<div class="collection-card-content">
                <h1 @click="$router.push({ path: `/topic/${item.id}`, query: { name: item.title } })">{{ item.title }}</h1>
                <div class="preview-html">
                  {{ item.content }}
                </div>
                <div class="preview-imgs">
                  <ul>
                  	<li v-for="imgs in item.image_url"><img v-bind:src="imgs" /></li>
                  </ul>
                </div>
              </div>
              <footer>
                <span class="collection-card-plantname" @click="$router.push({ path: `/group/${item.group.id}` })">{{ item.group.name }}</span>
                <div>
                  <span>收藏于</span>
                  <time>{{ item.create_time }}</time>
                </div>
              </footer>-->
          </ul>
        </div>
        <pagination></pagination>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getCollectPost } from 'api/post';
  import CollectionCard from 'components/CollectionCard';
  import Pagination from 'components/Pagination'
  
  export default {
    name: 'collection-container',
    data() {
      return {
        collecations: [
        //following data is only used for display the layout , please delete this when the data can be dynamicly injected .
        {
          id: 1,
          title: 'this is a collection title',
          content: '昨天拍了个照片也没太注意。。发给朋友看他们说你最近眼袋怎么那么重！ 然而露珠平时好像是没有眼袋的啊！ 百度了一下说卧蚕什么紧贴下睫毛啊细细一条啊但是露珠的好像并不细。。。哭 大...',
          image_url: [{url: ''},{url: ''}],
          create_time: '2017-08-11',
          group: {
            name: '陶陶'
          }
        },{
          id: 1,
          title: 'this is a collection title',
          content: '昨天拍了个照片也没太注意。。发给朋友看他们说你最近眼袋怎么那么重！ 然而露珠平时好像是没有眼袋的啊！ 百度了一下说卧蚕什么紧贴下睫毛啊细细一条啊但是露珠的好像并不细。。。哭 大...',
          image_url: [{url: ''},{url: ''}],
          create_time: '2017-08-11',
          group: {
            name: '陶陶'
          }
        }],
        loading: false,
      }
    },
    components: {
      CollectionCard,
      Pagination
    },
    computed: {
      ...mapGetters([
        'user',
        'access_token',
      ])
    },
    mounted() {
      this.getCollectPosts();
    },
    methods: {
      getCollectPosts () {
        var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          getCollectPost(self.user.userInfo.id).then(res => {
            console.log(res.data);
            for (let i  = 0, j = res.data.length; i < j; i++) {
              res.data[i].create_time = self.dealTime(res.data[i].create_time);
            }
            self.collecations = res.data;
            self.loading = false;
            resolve();
          }).catch(reeor => {
            self.loading = false;
            reject(error);
          })
        })
      },
      dealTime (time) {
        return time.slice(0, 10) + ' ' + time.slice(11, 16);
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
      //pagination底部空间
        margin-bottom: 20px;
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
  .collection-tabcontent {
    min-height: 200px;
    margin-top: 5px;
  }

</style>
