<!-- 
  我的收藏页面
 -->

<template>
  <div id="" class="collection-container">
    <section>
      <header>我的收藏</header>
      <div class="collection-card">
        <el-card :body-style="{ padding: '0px' }" v-for="o in 2" class="collection-body">
          <div class="collection-title">卧槽！谁来告诉我我这是眼袋还是卧蚕！我才知道我笑起来那么吓人！</div>
          <p>昨天拍了个照片也没太注意。。发给朋友看他们说你最近眼袋怎么那么重！ 然而露珠平时好像是没有眼袋的啊！ 百度了一下说卧蚕什么紧贴下睫毛啊细细一条啊但是露珠的好像并不细。。。哭 大...
          昨天拍了个照片.</p>
          <span class="collection-creator">陶陶欸还贵我就不v</span>
          <span class="collection-time">收藏于 2015-04-28 13:31</span>
        </el-card>
      </div>
      <div class="collection-page">
        <el-pagination
          background
          layout="prev, pager, next"
          :total="1000"
          prev-text="上一页"
          next-text="下一页">
        </el-pagination>
      </div>
    </section>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getCollectPost } from 'api/post';

  export default {
    name: 'collection-container',
    data() {
      return {
        collecations: [],
        loading: false,
      }
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
        /*var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          getCollectPost(self.user.userInfo.id).then(res => {
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
        })*/
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
    justify-content: space-between;
    margin: auto;
    max-width: 714px;
    min-width: 714px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 714px;
      header {
        width: 714px;
        height: 66px;
        margin: 31px 0 20px 0;
        padding-left:17px;
        font-family: MicrosoftYaHei-Bold;
        font-size: 32px;
        font-weight: normal;
        line-height:66px;
        color: #5677fc;
        background-color: #ffffff;
        border-radius: 4px;
      }
      .collection-card{
        .collection-body{
          margin-bottom:30px;
        }
        .collection-title{
          width: 650px;
          height: 25px;
          margin:29px 0 0 28px;
          overflow:hidden;
          white-space:nowrap;
          text-overflow:ellipsis;
          font-family: MicrosoftYaHei-Bold;
          font-size: 24px;
          font-weight: normal;
          color: #333333;
        }
        p{
          width: 653px;
          height: 81px;
          margin:19px 0 0 28px;
          font-family: MicrosoftYaHei;
          font-size: 18px;
          line-height:26px;
          font-weight: normal;
          font-stretch: normal;
          overflow:hidden;
          text-overflow:ellipsis;
          color: #444444;
        }
        .collection-creator{
          display:inline-block;
          margin:0 0 0 28px;
          float:left;
          width: 100px;
          height: 52px;
          font-family: MicrosoftYaHei;
          font-size: 18px;
          font-weight: normal;
          font-stretch: normal;
          line-height:52px;
          color: #999999;
          overflow:hidden;
          white-space:nowrap;
          text-overflow:ellipsis;
        }
        .collection-time{
          display:inline-block;
          margin:0 25px 0 0;
          float:right;
          height: 52px;
          font-family: MicrosoftYaHei;
          font-size: 18px;
          font-weight: normal;
          font-stretch: normal;
          line-height:52px;
          color: #999999;
        }
      }
      .collection-page{
        width:420px;
        margin:60px auto;
      }
    }
  }
</style>
