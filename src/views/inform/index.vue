<template>
    <div class="inform-container">
      <section>     
        <el-tabs v-model="activeName" @tab-click="handleClick">
          <el-tab-pane label="帖子通知" name="inform-card">
            <div class="inform-tabcontent" v-loading="loading">
              <ul class="index-cards">
                <li class="index-card">
                  <div>
                    <img src="#">
                    <div>
                      <p>格子</p>
                      <p><span>回复了主题帖</span><span>23周周报</span></p>
                    </div>
                    <button>查看</button>
                  </div>
                </li> 
                <li class="index-card">
                  <div>
                    <img src="#">
                    <div>
                      <p>格子</p>
                      <p><span>回复了主题帖</span><span>23周周报</span></p>
                    </div>
                    <button>查看</button>
                  </div>
                </li> 
              </ul>
            </div>
          </el-tab-pane>
          <el-tab-pane label="星球通知" name="inform-planet">
            <div class="inform-tabcontent" v-loading="loading2">
              <ul class="index-cards">
                <li class="index-card">
                  <div>
                    <img src="#">
                    <div>
                      <p>格子</p>
                      <p><span>退出</span><span>午安煎饼计划组</span></p>
                    </div>
                  </div>
                </li>
                <li class="index-card">
                  <div>
                    <img src="#">
                    <div>
                      <p>格子</p>
                      <p><span>退出</span><span>午安煎饼计划组</span></p>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </el-tab-pane>
          <el-tab-pane label="私密星球申请" name="inform-applyplanet">
            <div class="inform-tabcontent" v-loading="loading3">
              <ul class="index-cards">
                <li class="index-card">
                  <div>
                    <img src="#">
                    <div>
                      <p><span>格子</span><span>申请加入</span><span>午安煎饼计划UI组</span></p>
                      <span>加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊</span>
                    </div>
                    <div id="inform-applyplanet-btn">
                      <button>同意</button>
                      <button>拒绝</button>
                    </div>
                  </div>
                </li>
                <li class="index-card">
                  <div>
                    <img src="#">
                    <div>
                      <p><span>格子</span><span>申请加入</span><span>午安煎饼计划UI组</span></p>
                      <span>加加加我啊</span>
                    </div>
                    <div id="inform-applyplanet-btn">
                      <button>同意</button>
                      <button>拒绝</button>
                    </div>
                  </div>
                </li>
                <li class="index-card">
                  <div>
                    <img src="#">
                    <div>
                      <p><span>格子</span><span>申请加入</span><span>午安煎饼计划UI组</span></p>
                      <span>加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊加加加我啊</span>
                    </div>
                    <div id="inform-applyplanet-btn">
                      <span>已拒绝</span>
                    </div>
                  </div>
                </li>
                <li class="index-card">
                  <div>
                    <img src="#">
                    <div>
                      <p><span>格子</span><span>申请加入</span><span>午安煎饼计划UI组</span></p>
                      <span>加加加我啊</span>
                    </div>
                    <div id="inform-applyplanet-btn">
                      <span>已同意</span>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </el-tab-pane>
        </el-tabs>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'inform-container',
    data() {
      return {
        activeName: 'inform-card',
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
      this.loading3 = true;
      // simulate ajax loading
      setTimeout(() => {
        this.loading = false;
      }, 4000)
      setTimeout(() => {
        this.loading2 = false;
      }, 6000)
      setTimeout(() => {
        this.loading3 = false;
      }, 8000)

    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .inform-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    font-family:PingFangHK-Semibold;
    max-width: 590px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 590px;
    }

  }
  .inform-tabcontent {
    min-height: 200px;
    margin-top: 5px;
  }
  // post card style    
  .index-cards { 
    .index-card {   
      padding: 10px;   
      background-color: #ffffff;
      border-radius: 4px;  
      &:not(:first-child) {
        margin-top: 8px;
      }  
      div {    
        display: flex;    
        align-items: center;
        font-size:14px;   
        color:#666666;
        position: relative;    
        img {
          width: 50px;    
          height: 51px;   
          border-radius: 100%;    
          margin-right: 8px;   
        }
        div {
          height: 42px;
          display: block;
          p:nth-child(1) {
            color: #000000;
            span {
              color: #5992e4;
              &:not(:first-child) {
                  margin-left: 5px;
              }
            }
          }
          p:nth-child(2) {
            span {
              color: #666666;
              &:not(:first-child) {
                  margin-left: 5px;
              }
            }
          }
          > span {    
            width: 290px;
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            &:not(:first-child) {   
              margin-rigth: 5px;   
            }
          }
        }
        button {
          font-size: 12px;
          color: #ffffff;
          background:#5677fc;
          box-shadow:0 2px 4px 0 rgba(0,0,0,0.28);
          border-radius:2px;
          width:80px;
          height:30px;
          border: none;
          padding: 0;
          position: absolute;
          right: 38px;
        }
      }
    }  
  }
  #inform-applyplanet-btn{
    position: absolute;
    right: 20px;
    height: 30px;
    font-size: 12px;
    button {
      color: #ffffff;
      background:#5677fc;
      box-shadow:0 2px 4px 0 rgba(0,0,0,0.28);
      border-radius:2px;
      width:80px;
      height:30px;
      border: none;
      padding: 0;
      position: initial;
      &:not(:first-child) {
          margin-left: 27px;
      }
    }
    span {
        color: #999999;
        display: flex;
        align-items: center;
        justify-content: center;
        width:80px;
        height:30px;
    }
  }   
</style>
