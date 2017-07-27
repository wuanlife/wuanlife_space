<template>
  <el-menu class="navbar" mode="horizontal">
    <div class="navbar-container">
      <!-- Logo bar -->
      <div class="logo">
        <img src="">
        Wuanlife
      </div>
      <!-- Search bar -->
      <div class="search-container">
        <el-input
          class="search"
          placeholder="Search for ..."
          icon="search"
          v-model="input2"
          :on-icon-click="handleIconClick">
        </el-input>
      </div>
      <!-- avatar bar -->
      <el-dropdown menu-align="start" 
                   v-if="user.token != ''" 
                   class="avatar-container" 
                   trigger="click"
                   @visible-change="visibleChange">
        <div class="avatar-wrapper" :class="{'active' : isShowDrop}">
          <icon-svg icon-class="b" class="avatar-icon"></icon-svg>
          <span>keke</span>
        </div>
        <el-dropdown-menu class="user-dropdown" slot="dropdown">
          <router-link class='inlineBlock' to="/">
            <el-dropdown-item>
              <icon-svg icon-class="b" class="avatar-icon"></icon-svg>
              个人资料
            </el-dropdown-item>
          </router-link>
          <a target='_blank' href="https://github.com/PanJiaChen/vue-element-admin/">
            <el-dropdown-item>
              <icon-svg icon-class="b" class="avatar-icon"></icon-svg>
              我的收藏
            </el-dropdown-item>
          </a>
          <a target='_blank' href="https://github.com/PanJiaChen/vue-element-admin/">
            <el-dropdown-item>
              <icon-svg icon-class="b" class="avatar-icon"></icon-svg>
              邀请好友
            </el-dropdown-item>
          </a>
          <a target='_blank' href="https://github.com/PanJiaChen/vue-element-admin/">
            <el-dropdown-item>
              <icon-svg icon-class="b" class="avatar-icon"></icon-svg>
              修改密码
            </el-dropdown-item>
          </a>
          <a @click="logout">
            <el-dropdown-item>
              <icon-svg icon-class="b" class="avatar-icon"></icon-svg>
              退出登录
            </el-dropdown-item>
          </a>
        </el-dropdown-menu>
      </el-dropdown>
      <!-- login bar (if not logined) -->
      <div v-else class="login-container">
        <span><router-link to="/login">Login</router-link></span>
        <span><router-link to="/signup">Signup</router-link></span>
      </div>
    </div>
  </el-menu>
</template>

<script>
  import { mapGetters } from 'vuex';
  import Levelbar from './Levelbar';
  import TabsView from './TabsView';
  import ErrorLog from 'components/ErrLog';
  import errLogStore from 'vuex-store/errLog';

  export default {
    components: {
      Levelbar,
      TabsView,
      ErrorLog,
    },
    data() {
      return {
        log: errLogStore.state.errLog,
        isShowDrop: false,
      }
    },
    computed: {
      ...mapGetters([
        'user',
        'token',
      ])
    },
    methods: {
      logout() {
        this.$store.dispatch('Logout').then(() => {
          location.reload();// 为了重新实例化vue-router对象 避免bug
        });
      },
      visibleChange() {
        this.isShowDrop = !this.isShowDrop;
      }
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .navbar {
    height: 50px;
    line-height: 50px;
    border-radius: 0px !important;
    background-color: #5677fc;
    .navbar-container {
      display: flex;
      max-width: 900px;
      margin: auto;
      .logo {
        margin-right: 38px;

        font-family:FZLTZHK--GBK1-0;
        font-size:24px;
        color:#ffffff;
        text-align:left;
      }
      .search-container {
        flex: 0 0 480px;
        min-width: 300px;
        @media screen and (max-width: 900px) {
          flex: 0.7;
        }
      }
      .login-container, .avatar-container {
        margin-left: auto;
        margin-right: 30px;
      }
      .login-container {
        font-family:PingFangHK-Regular;
        font-size:14px;
        color:#ffffff;
        span {
          padding: 0 14px;
          &:not(:first-child) {
            border-left: 1px solid #fff;
          }
        }
      }
      .avatar-container {
        .avatar-wrapper {
          position: relative;
          top: 7px;
          padding: 8px 14px 15px 14px;
          width: 114px;
          height: 43px;
          cursor: pointer;
          line-height: 20px;
          transition: all 0.5s ease-in-out;
          font-size:14px;
          
        }
        .avatar-wrapper.active {
          color:#ffffff;
          box-shadow:inset 0 0 3px 0 rgba(0,0,0,0.09);
          border-radius:2px 2px 0 0;
          background: #0074e9;
          border-bottom: 1px solid #3b52ab;
        }
      }
    }
  }

  // dropdown style
  .user-dropdown {
    a {
      font-family:PingFangHK-Medium;
      font-size:14px;
      color:#ffffff;
      li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;
      }
    }
  }
</style>



