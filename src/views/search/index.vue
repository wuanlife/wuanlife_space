<template>
    <div class="relatedPlanets-container">
      <section>
        <header>相关星球</header>
        <div class="planetsBox" v-loading='loading' style="width: 600px;">
          <el-card v-for="item in relatedPlantesData" class="planetsBox-card relatedPlanets__boxCard">
            <img v-bind:src="item.image_url" style="width:30px;height:30px;border-radius:100%;float: left;margin-right: 4px;" />
            <h3 style="width: 136px;font-family:PingFangHK-Medium;font-size:10px;color:#5992e4;text-align:left;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;cursor: pointer;" @click="$router.push({ path: `/group/${item.id}` })">{{ item.name }}</h3>
            <span style="width: 136px;font-family:PingFangHK-Regular;font-size:10px;color:#666666;text-align:left;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;display: block;">{{ item.introduction }}</span>
          </el-card>
        </div>
        <button>更多相关星球</button>
        <header>相关星球</header>
        <div class="relatedPlanetsCardBox" v-loading='loading1'>
          <ul class="index-cards">
            <li class="index-card">
              <header>
                <img src="#">
                <span class="clickable">陶陶</span>
                <span>发表于</span>
                <span class="clickable">鬼扯天地</span>
                <time>2017-02-21 22:41</time>
              </header>
              <div class="index-card-content">
                <h1>Ubantu开荒笔记</h1>
                <div class="preview-html">
                  安装后。终端操作：sudo apt updatesudo apt upgrade重启，执行上述update命令一次，然后执行：sudo apt install ***常用的软件安装后。终端操作：sudo apt updatesudo apt upgrade重启，执行上述update命令一次，然后执行：sudo apt install ***常用的软件...
                </div>
                <div class="preview-imgs">
                  <img src="#">
                  <img src="#">
                </div>
              </div>
            </li>
            <li class="index-card">
              <header>
                <img src="#">
                <span class="clickable">陶陶</span>
                <span>发表于</span>
                <span class="clickable">鬼扯天地</span>
                <time>2017-02-21 22:41</time>
              </header>
              <div class="index-card-content">
                <h1>Ubantu开荒笔记</h1>
                <div class="preview-html">
                  安装后。终端操作：sudo apt updatesudo apt upgrade重启，执行上述update命令一次，然后执行：sudo apt install ***常用的软件安装后。终端操作：sudo apt updatesudo apt upgrade重启，执行上述update命令一次，然后执行：sudo apt install ***常用的软件...
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
  
  export default {
    name: 'relatedPlanets-container',
    data() {
      return {
        relatedPlantesData: [],
        loading: false,
        loading1: false,
      }
    },
    mounted () {
      this.getSearchGroupsData();
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
            self.loading = false;
            resolve();
          }).catch(error => {
            self.loading = false;
            reject(error);
          });
        });
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
        .planetsBox-card {
          width: 190px;
          height: 54px;
          background:#ffffff;
          border-radius:4px;
          box-shadow: none;
          border: none;
          padding: 12px 10px;
          display: inline-block;
          margin-right: 10px;
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
        margin-top: 14px;
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
