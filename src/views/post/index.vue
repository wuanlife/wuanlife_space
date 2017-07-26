<template>
    <div id="post">
      <section>
        <div class="post-container">
          <div class="post-wrapper">
            <header>
              <img src="#">
              <span class="author">taotao</span>
              <time>2017-01-28 22:41</time>
            </header>
            <article>
              <h1>Ubuntu keke</h1>
              <div>asadasdasdas<strong>asdasdasd</strong>asdasdasdasd</div>
            </article>
            <footer>
              <div class="btns">
                <el-button type="primary" class="done">
                  <icon-svg icon-class="b" class="avatar-icon"></icon-svg>999+
                </el-button>
                <el-button type="primary">
                  <icon-svg icon-class="b" class="avatar-icon"></icon-svg>999+
                </el-button>
              </div>
              <div class="opts">
                <span>重置</span>
                <span>锁定</span>
                <span>编辑</span>
                <span>删除</span>
              </div>
            </footer>
          </div>
          <div class="review-wrapper">
            <header>
              asdasd
            </header>
            <ul>
              
            </ul>
          </div>
        </div>
      </section>
      <aside>
        asdaasdasd
      </aside>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'post',
    data() {
      return {
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
      // simulate ajax loading
      console.log('im here')
      setTimeout(() => {
        this.loading = false;
      }, 4000)
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  #post {
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
    }
    aside {
      margin-top: 20px;
      flex: 0 0 250px;
      @media screen and (max-width: 900px) {
        display: none;
      }
      
    }  
  }
  // postdetails container(include post and review)
  .post-container {
    border-radius:8px;
    padding: 16px;
    background: #ffffff;
    .post-wrapper {
      header {

      }
      article {

      }
      footer {
        display: flex;
        justify-content: space-between;
        .btns {
          button {
            background: #ebf3fb;
            border-radius: 4px;
            height: 36px;
            padding: 8px 10px;
            border: none;
            transition: all 0.2s ease-in-out;

            font-family:PingFangHK-Regular;
            font-size:14px;
            color:#2682cf;
            text-align:justify;
            span {
              svg {
                margin-right: 6px;
              }
            }
            &:hover {
              background: #bbd3db;
              color:#1a529f;
            }
            &.done {
              background: #c3c3c3;
              color: #666666;
              &:hover {
                background: #a3a3a3;
                color: #333333;
              }
            }
          }
        }
        .opts {

        }
      }
    }
    .review-wrapper {

    }
  }
</style>
