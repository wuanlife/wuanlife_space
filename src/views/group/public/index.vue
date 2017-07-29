<template>
  <div class="group-public-container">
    <section>     
      <div class="index-tabcontent" v-loading="loading2">
        <ul class="index-cards">
          <li class="index-card">
            <header>
              <img src="#">
              <span class="clickable">taotao</span>
              <span>posted in</span>
              <span class="clickable">guice</span>
              <time>2017-02-21</time>
            </header>
            <div class="index-card-content">
              <h1>asdwqdqwdqw</h1>
              <div class="preview-html">
                asdasdasdasd<strong>strongtest</strong>asdasdasdasdwqdqwdqwdqwdwqdqwdqwasdadqwhdqiuwdgiquwdgqiwudgqiuwdgqwiudgqdwqdqwdoiqhwdoiqwdoiqwd
              </div>
              <div class="preview-imgs">
                <img src="#">
                <img src="#">
              </div>
            </div>
            <footer>
              <ul>
                <li class="done">reviews 134</li>
                <li>approves 134</li>
                <li>collections 134</li>
              </ul>
            </footer>
          </li>  
        </ul>
        <el-pagination
          layout="prev, pager, next"
          :total="1000">
        </el-pagination>
      </div>
    </section>
    <aside>
      <div class="aside-card">
        <header>
          <img src="#">
          <div class="group-info">
            <h2>asdasdasdqwdqwdqwdqwdqwdqwd</h2>
            <span>999+ top</span>
            <span>999+ mem</span>
          </div>
        </header>
        <div class="aside-card-content">
          <p>asdqwudgqouwdgqouwdgoquwgfoqwbfoqgwfouqgwfouqbwovuqbwouqownvnqownvqogdouqwgdoquwgdoquwgdouqwgofuqwofugqwougqwofugqwfouqgwfouqgofugqwou</p>
          <span>Creator: Taotao</span>
        </div>
        <footer>
          <el-button>退出星球</el-button>
        </footer>
      </div>
    </aside>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'group-public',
    data() {
      return {
        activeName: 'index-myplanet',
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
      this.loading = true;
      this.loading2 = true;
      // simulate ajax loading
      setTimeout(() => {
        this.loading = false;
      }, 4000)
      setTimeout(() => {
        this.loading2 = false;
      }, 6000)

    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .group-public-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 900px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      margin-top: 20px;
      min-width: 0;
      flex: 0 0 590px;
      header {
        margin: 15px 0 20px 0;
        font-family:PingFangHK-Medium;
        font-size:18px;
        color:#5677fc;
      }
    }
    aside {
      margin-top: 20px;
      flex: 0 0 250px;
      @media screen and (max-width: 900px) {
        display: none;
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
        margin-bottom: 12px;
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
      footer {
        ul {
          display: flex;
          li {

            transition: all 0.2s ease-in-out;
            cursor: pointer;
            color: #bcbcbc;
            &:not(:first-child):before{
              content:'\00B7';
              padding:0 8px;
            }
            &:hover {
              color: #a3b5fd;
            }
            &.done {
              color: #a3b5fd;
            }
          }
        }
      }  
    }   
  }

  // aside part
  .aside-card {
    width: 248px;
    padding: 10px 14px;
    background:#ffffff;
    border:1px solid #f0f6fd;
    box-shadow:0 2px 5px 0 rgba(0,0,0,0.24);
    border-radius:4px;
    header {
      display: flex;
      margin-bottom: 10px;
      img {
        flex: 0 0 60px;
        width:60px;
        height:60px;
        border-radius:100%;
        margin-right: 7px;
      }
      .group-info {
        min-width: 0;
        align-self: center;
        h2 {
          text-overflow: ellipsis;
          overflow: hidden;

          font-family:PingFangHK-Medium;
          font-size:16px;
          color:#275597;
        }
        span {
          font-family:PingFangHK-Medium;
          font-size:12px;
          color:#8a94a9;
        }
      }
    }
    .aside-card-content {
      margin: 14px 0 20px 0;
      p {
        word-break: break-all;
        margin-bottom: 23px;

        font-family:PingFangHK-Regular;
        font-size:14px;
        color:#333333;
      }
      span {
        font-family:PingFangHK-Regular;
        font-size:14px;
        color:#8a94a9;
      }
    }
    footer {
      text-align: center;
      button {
        background:#1b87f6;
        border-radius:4px;
        padding: 8px 20px;
        border: none;

        font-family:PingFangHK-Regular;
        font-size:12px;
        color:#ffffff;
        &:hover {
          background: #4db3ff;
          border-color: #4db3ff;
        }
      }
    }
  }
</style>
