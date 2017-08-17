<template>
    <div class="relatedPlanets-container">
      <section>
        <header>相关星球</header>
        <div class="planetsBox" v-loading='loading' style="width: 600px;">
          <div v-for="item in relatedPlantesData1" class="search-card">
          	<img v-bind:src="item.image_url"/>
          	<h3 @click="$router.push({ path: `/group/${item.id}` })">{{ item.name }}</h3>
          	<span>{{ item.introduction }}</span>
          </div>
        </div>
        <button @click="showMorePlanets" v-if="morePlanetsBtn">更多相关星球</button>
        <header>相关星球</header>
        <div class="relatedPlanetsCardBox" v-loading='loading1'>
          <ul class="index-cards">
            <li class="index-card" v-for="item in relatedPostsData">
              <header>
                <img v-bind:src="item.author.avatar_url">
                <span class="clickable">{{ item.author.name }}</span>
                <span>发表于</span>
                <span class="clickable" @click="$router.push({ path: `/group/${item.group.id}` })">{{ item.group.name }}</span>
                <time>{{ item.create_time }}</time>
              </header>
              <div class="index-card-content">
                <h1 @click="$router.push({path: `/post/${item.id}`})">{{ item.title }}</h1>
                <div class="preview-html">
                  {{ item.content }}
                </div>
                <div class="preview-imgs">
                  <img v-bind:src="imglink" v-for="imglink in item.image_url">
                </div>
              </div>
            </li>
          </ul>
        </div>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { searchGroups } from 'api/group';
  import { searchPosts } from 'api/post';
  
  export default {
    name: 'relatedPlanets-container',
    data() {
      return {
        relatedPlantesData: [],
        relatedPlantesData1: [],
        relatedPostsData: [],
        loading: false,
        loading1: false,
        morePlanetsBtn: true
      }
    },
    mounted () {
      this.getSearchGroupsData();
      this.getSearchPostsData();
    },
    computed: {
      ...mapGetters([
        'user',
      ])
    },
    methods: {
      getSearchGroupsData () {
        var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          searchGroups(this.$route.query.search).then(res => {
            self.relatedPlantesData = res.data;
            for(let i = 0,j = 3; i < j; i++){
              self.relatedPlantesData1[i] = res.data[i];
            }
            self.loading = false;
            resolve();
          }).catch(error => {
            self.loading = false;
            reject(error);
          });
        });
      },
      getSearchPostsData () {
        var self = this;
        this.loading1 = true;
        return new Promise((resolve, reject) => {
          searchPosts(this.$route.query.search).then(res => {
            self.relatedPostsData = self.dealTime(res.data);
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
          arr[i].create_time = arr[i].create_time.slice(0, 10) + ' ' + arr[i].create_time.slice(11, 16)
        }
        return arr
      }
    },
    watch: {
      searchName: function () {
        this.getSearchGroupsData();
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .relatedPlanets-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 590px;
    min-width: 380px;
    section {
      flex: 1;
      > header {
        margin: 19px 0 10px 0;
        font-family:PingFangHK-Medium;
        font-size:14px;
        color:#5677fc;
      }
      .planetsBox {
        .search-card{
          font-size: 10px;
          background:#ffffff;
          border-radius:4px;
          width:190px;
          height:54px;
          padding: 12px 10px 12px 44px;
          box-sizing: border-box;
          position: relative;
          margin: 0 10px 10px 0;
          display: inline-block;
          > img{
            width:30px;
            height:30px;
            border-radius:100%;
            position: absolute;
            top: 12px;
            left: 10px;
          }
          > h3, span{
            width: 136px;
            height: 14px;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
          }
          > h3{
            color:#5992e4;
            font-family:PingFangHK-Medium;
            cursor: pointer;
          }
          > span{
            color:#666666;
            font-family:PingFangHK-Regular;
          }
        }
      }
      > button{
        background:#1b87f6;
        border-radius:3px;
        padding: 6px 14px 5px 14px;
        border: 0;
        font-family:PingFangHK-Regular;
        font-size:12px;
        color:#ffffff;
        box-shadow: 0 2px 4px 0 rgba(0,0,0,0.28);
        margin-top: 4px;
        margin-bottom: 18px;
        position: relative;
        left: 50%;
        transform: translateX(-50%);
      }
      .relatedPlanetsCardBox {
        
      }
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
      &:last-child {
        margin-bottom: 20px;
      }
      header {    
        display: flex;    
        align-items: center;    
        margin-bottom: 6px;
        font-size:12px;   
        color:#999999;
        & > .clickable {
          transition: all 0.2s ease-in-out;
          &:hover {
            color: #5677fc;
          }
        }
        img {
          width: 26px;    
          height: 26px;   
          border-radius: 100%;    
          margin-right: 10px;   
        }
        span {    
          &:not(:first-child) {   
            margin-left: 5px;   
          }
        }
        time {    
          margin-left: 12px;    
        }   
      }
      div.index-card-content {
        h1 {
          display: inline-block;
          position: relative;
          cursor: pointer;
          margin-bottom: 6px;

          color: #2e5897;
          font-family:PingFangHK-Semibold;
          font-size:16px;
          // hover animation
          &::after {
            content: '';
            transition: all 0.5s ease-in-out;
            transform: scaleX(0);
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: 0;
            left: 0;
            background: #2e5897;
          }
          &:hover {
            &::after {
              transform: scaleX(1);
            }
          }
        }
        div.preview-html {
          margin-bottom: 12px;
          word-break: break-all;
          font-family: PingFangHK-Medium;
          font-size:14px;
          color:#666666;
          letter-spacing:0;
          text-align:justify;
        }
        div.preview-imgs {
          display: flex;
          img {
            margin-right: 15px;
            width: 174px;
            height: 174px;
          }

        }
      }
    }   
  }
</style>
<style type="text/css">
  .el-card__body {
    padding: 0;
  }
</style>
