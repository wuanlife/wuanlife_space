<template>
    <div class="index-visitor-container">
      <section>     
        <el-tabs v-model="activeName" @tab-click="handleClick">
          <el-tab-pane label="我的星球" name="index-myplanet">
            <div class="index-tabcontent" v-loading="loading">
              <ul class="index-cards">
                <li class="index-card">
                  <header>
                    <img src="#">
                    <span>taotao</span>
                    <span>posted in</span>
                    <span>guice</span>
                    <time>2017-02-21</time>
                  </header>
                  <div class="index-card-content">
                    
                  </div>
                  <footer>
                    
                  </footer>
                </li>  
              </ul>
              <el-pagination
                layout="prev, pager, next"
                :total="1000">
              </el-pagination>
            </div>
          </el-tab-pane>
          <el-tab-pane label="最新话题" name="index-newtopic">
            <div class="index-tabcontent" v-loading="loading2">
              newtopic
            </div>
          </el-tab-pane>
        </el-tabs>
      </section>
      <aside>
        <header>
          <h2>发现星球</h2>
        </header>
        <div class="aside-content">
          <div class="index-aside-card wuan-card">
            <img src="http://img.alicdn.com/bao/uploaded/i2/TB2x7C0nFXXXXbsXpXXXXXXXXXX_!!101742512.jpg">
            <div class="wuan-card__content">
              <h2>asdasdqwdqwdqasdasdasdasdasdwdqwdqwdqwd</h2>
              <p>asdadqwdqwdguiqasdasdwgdiuqwgdiuqwgdqiuw</p>
            </div>
          </div>
        </div>
      </aside>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'index-visitor',
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
      flex: 0 0 590px;
    }
    aside {
      flex: 0 0 250px;
      @media screen and (max-width: 900px) {
        display: none;
      }
      header {
        h2 {
          margin: 20px 0;

          font-family:PingFangHK-Medium;
          font-size:14px;
          color:#5677fc;
        }
      }
      .aside-content {
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
  }
  // post card style
  .index-cards {
    .index-card {
      padding: 10px 16px 12px 16px;
      background-color: #ffffff;
      header {
        display: flex;
        align-items: center;

        font-size:12px;
        color:#999999;
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
    }
  }
</style>
