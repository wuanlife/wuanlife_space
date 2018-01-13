<template>
<div class="nav-menu">
  <div class="user-container" v-if="user.id">
    <div class="write-container">
      <icon-svg icon-class="write"></icon-svg>
      写文章
    </div>
    <el-dropdown menu-align="start" 
      class="avatar-container" 
      trigger="click"
      @visible-change="visibleChange">
      <div class="avatar-wrapper" :class="{'active' : isShowDrop}">
          <icon-svg icon-class="peopleCircle" class="avatar-icon"></icon-svg>
          <span>{{ user.name }}</span>
      </div>
      <el-dropdown-menu class="user-dropdown" slot="dropdown">
          <router-link class='inlineBlock' to="/personalData/">
          <el-dropdown-item>
              个人资料
          </el-dropdown-item>
          </router-link>
          <router-link class='inlineBlock' to="/collection/">
          <el-dropdown-item>
              我的收藏
          </el-dropdown-item>
          </router-link>
          <router-link class='inlineBlock' to="/invitecode/">
          <el-dropdown-item>
              邀请好友
          </el-dropdown-item>
          </router-link>
          <router-link class='inlineBlock' to="/resetpsw/">
          <el-dropdown-item>
              修改密码
          </el-dropdown-item>
          </router-link>
          <el-dropdown-item @click.native="logout">
              退出登录
          </el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
  </div>
  <!-- login bar (if not logined) -->
  <div v-else class="login-container">
    <span><router-link to="/login/">登录</router-link></span>
    <span><router-link to="/signup/">注册</router-link></span>
  </div>
</div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  data() {
    return {
      isShowDrop: false
    };
  },
  computed: {
    ...mapGetters(["user"])
  },
  mounted() {
  },
  updated() {
  },
  methods: {
    logout() {
      this.$store.dispatch("Logout").then(() => {
        location.reload(); // 为了重新实例化vue-router对象 避免bug
      });
    },
    visibleChange() {
      this.isShowDrop = !this.isShowDrop;
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.login-container,
.avatar-container {
  margin-left: 30px;
}
.login-container {
  font-size: 14px;
  color: #ffffff;
  span {
    padding: 0 14px;
    &:not(:first-child) {
        border-left: 1px solid #fff;
    }
  }
}
.user-container {
  margin-left: 30px;
}
.write-container {
  display: inline-block;
  color: #fff;
}
.avatar-container {
  display: inline-block;
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
    font-size: 14px;
    .avatar-icon {
      font-size: 18px;
      margin-right: 6px;
    }
    > span {
      width: 58px;
      display: inline-block;
    }
  }
  .avatar-wrapper.active {
    color: #ffffff;
    box-shadow: inset 0 0 3px 0 rgba(0, 0, 0, 0.09);
    border-radius: 2px 2px 0 0;
    background: #0074e9;
    border-bottom: 1px solid #3b52ab;
  }
}

// dropdown style
.user-dropdown {
  a {
    font-size: 14px;
    color: #ffffff;
    li {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 16px;
    }
  }
}
</style>
