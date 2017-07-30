<template>
    <div class="collection-container">
      <section>     
        <header>
            我的收藏
        </header>
        <div class="collection-tabcontent">
          <ul class="collection-cards">
            <li class="collection-card">
              <div class="collection-card-content">
                <h1>Ubuntu开荒笔记</h1>
                <div class="preview-html">
                  安装后。终端操作：sudo apt updatesudo apt upgrade重启，执行上述update命令一次，然后执行：sudo apt install ***常用的软件安装后。终端操作：sudo apt updatesudo apt upgrade重启，执行上述update命令一次，然后执行：sudo apt install ***常用的软件...
                </div>
                <div class="preview-imgs">
                  <img src="#">
                  <img src="#">
                  <img src="#">
                </div>
              </div>
              <footer>
                <span>鬼扯天地</span>
                <div>
                  <span>收藏于</span>
                  <time>2017-02-21 22:41</time>
                </div>
              </footer>
            </li>
            <li class="collection-card">
              <div class="collection-card-content">
                <h1>Ubuntu开荒笔记</h1>
                <div class="preview-html">
                  安装后。终端操作：sudo apt updatesudo apt upgrade重启，执行上述update命令一次，然后执行：sudo apt install ***常用的软件安装后。终端操作：sudo apt updatesudo apt upgrade重启，执行上述update命令一次，然后执行：sudo apt install ***常用的软件...
                </div>
              </div>
              <footer>
                <span>鬼扯天地</span>
                <div>
                  <span>收藏于</span>
                  <time>2017-02-21 22:41</time>
                </div>
              </footer>
            </li>
          </ul>
        </div>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'collection-container',
    data() {
      return {
        
      }
    },
    computed: {
      ...mapGetters([
        'user',
        'access_token',
      ])
    },
    mounted() {
      
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .collection-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 590px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 590px;
      header {
        margin: 15px 0 20px 0;
        font-family:PingFangHK-Medium;
        font-size:18px;
        color:#5677fc;
      }
    }
  }
  .collection-tabcontent {
    min-height: 200px;
    margin-top: 5px;
  }
  // post card style    
  .collection-cards { 
    .collection-card {   
      padding: 16px 16px 12px 16px;   
      background-color: #ffffff;
      border-radius: 8px;  
      &:not(:first-child) {
        margin-top: 8px;
      }  
      footer {    
        display: flex;    
        align-items: center;
        font-size:12px;   
        color:#999999;
        position: relative;
        font-family:PingFangHK-Medium;
        div {
          position: absolute;
          right: 0;
        }
        span {    
          &:not(:first-child) {   
            margin-left: 5px;   
          }
        } 
      }
      div.collection-card-content {
        margin-bottom: 12px;
        h1 {
          margin-bottom: 6px;
          color: #003585;
          font-family:PingFangHK-Semibold;
          font-size:16px;
          opacity: 0.87;
        }
        div.preview-html {
          margin-bottom: 12px;
          word-break: break-all;
          font-size:14px;
          color:#666666;
          letter-spacing:0;
          text-align:justify;
          font-family:PingFangHK-Medium;
        }
        div.preview-imgs {
          display: flex;
          img {
            width: 174px;
            height: 174px;
            margin-right: 15px;
            background:#d8d8d8;
            border-radius:4px;
            align-self: center;
          }
        }
      }
    }   
  }
</style>
