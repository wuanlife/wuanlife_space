<template>
  <el-menu class="navbar" mode="horizontal">
    <div class="navbar-container">
      <!-- Logo bar -->
      <div class="logo" @click="$router.push({path: '/'})">
        <img src="">
        午安网
      </div>
      <!-- Search bar -->
      <div class="search-container">
        <el-input
          class="search"
          placeholder="Search for ..."
          icon="search"
          v-model="searchContent"
          :on-icon-click="()=>{}">
        </el-input>
      </div>
      <div class="notif-container"
           @click="$router.push({path: '/inform/'})">
        <el-badge :is-dot="true">
          <icon-svg icon-class="smallbell" class="notif-icon"></icon-svg>
        </el-badge>
      </div>
      <!-- avatar bar -->
      <el-dropdown menu-align="start" 
                   v-if="user.token != ''" 
                   class="avatar-container" 
                   trigger="click"
                   @visible-change="visibleChange">
        <div class="avatar-wrapper" :class="{'active' : isShowDrop}">
          <icon-svg icon-class="peopleCircle" class="avatar-icon"></icon-svg>
          <span>{{ user.userInfo.name }}</span>
        </div>
        <el-dropdown-menu class="user-dropdown" slot="dropdown">
          <router-link class='inlineBlock' to="/personalData/">
            <el-dropdown-item>
              <icon-svg icon-class="people_2" class="avatar-icon"></icon-svg>
              个人资料
            </el-dropdown-item>
          </router-link>
          <router-link class='inlineBlock' to="/collection/">
            <el-dropdown-item>
              <icon-svg icon-class="starSolid" class="avatar-icon"></icon-svg>
              我的收藏
            </el-dropdown-item>
          </router-link>
          <router-link class='inlineBlock' to="/collection/">
            <el-dropdown-item>
              <icon-svg icon-class="inviteFriend_2" class="avatar-icon"></icon-svg>
              邀请好友
            </el-dropdown-item>
          </router-link>
          <router-link class='inlineBlock' to="/resetpsw/">
            <el-dropdown-item>
              <icon-svg icon-class="lock_2" class="avatar-icon"></icon-svg>
              修改密码
            </el-dropdown-item>
          </router-link>
          <a @click="logout">
            <el-dropdown-item>
              <icon-svg icon-class="poweroff" class="avatar-icon"></icon-svg>
              退出登录
            </el-dropdown-item>
          </a>
        </el-dropdown-menu>
      </el-dropdown>
      <!-- login bar (if not logined) -->
      <div v-else class="login-container">
        <span><router-link to="/login/">登录</router-link></span>
        <span><router-link to="/signup/">注册</router-link></span>
      </div>
    </div>
  </el-menu>
</template>

<script>
  import { mapGetters } from 'vuex';
  import Levelbar from './Levelbar';
  import TabsView from './TabsView';
  import errLogStore from 'vuex-store/errLog';

  export default {
    components: {
      Levelbar,
      TabsView,
    },
    data() {
      return {
        log: errLogStore.state.errLog,
        isShowDrop: false,
        searchContent: '',
      }
    },
    computed: {
      ...mapGetters([
        'user',
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
      },
      handleIconClick() {
        if (this.input2 !== '') {
          this.$store.commit('SET_SEARCHTEXT',this.input2);
          this.$router.push({ path: '/relatedPlanets' });
        } else{
          alert("请输入要搜索的内容");
        }
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
        cursor: pointer;
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
      .notif-container {
        margin-left: auto;
        margin-right: 22px;
        
        .notif-icon {
          display: block;
          background: transparent;
          cursor: pointer;
          color: #ffffff;
          width: 18px;
          height: 18px;
          &:hover {
            color: #dddddd;
          }
        }
      }
      .login-container, .avatar-container {
        
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
          color: #fff;
          font-size:14px;
          .avatar-icon {
            font-size: 18px;
            margin-right: 6px;
          }
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



