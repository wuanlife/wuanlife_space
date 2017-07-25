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
          placeholder="请选择日期"
          icon="search"
          v-model="input2"
          :on-icon-click="handleIconClick">
        </el-input>
      </div>
      <!-- avatar bar -->
      <el-dropdown v-if="user" class="avatar-container" trigger="click">
        <div class="avatar-wrapper">
          <img class="user-avatar" :src="avatar+'?imageView2/1/w/80/h/80'">
          <i class="el-icon-caret-bottom"></i>
        </div>
        <el-dropdown-menu class="user-dropdown" slot="dropdown">
          <router-link class='inlineBlock' to="/">
            <el-dropdown-item>
              首页
            </el-dropdown-item>
          </router-link>
          <a target='_blank' href="https://github.com/PanJiaChen/vue-element-admin/">
            <el-dropdown-item>
              项目地址
            </el-dropdown-item>
          </a>
          <el-dropdown-item divided><span @click="logout" style="display:block;">退出登录</span></el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
      <!-- login bar (if not logined) -->
      <div class="login-container">
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
  import errLogStore from 'store/errLog';

  export default {
    components: {
      Levelbar,
      TabsView,
      ErrorLog,
    },
    data() {
      return {
        log: errLogStore.state.errLog
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
        this.$store.dispatch('LogOut').then(() => {
          location.reload();// 为了重新实例化vue-router对象 避免bug
        });
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
    }
  }
</style>



