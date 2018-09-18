<template>
<div class="nav-menu">
  <div class="user-container" v-if="user.uid">
    <div class="write-container" @click="goPath('/editor/drafts/new')">
      <icon-svg icon-class="write"></icon-svg>
      写文章
    </div>
    <el-dropdown menu-align="start"
      class="avatar-container"
      trigger="click"
      @visible-change="visibleChange">
      <div class="avatar-wrapper" :class="{'active' : isShowDrop}">
          <span>{{ user.uname }}</span>
      </div>
      <el-dropdown-menu class="user-dropdown" slot="dropdown">
          <el-dropdown-item @click.native="goPath('/mySpace')">
              我的空间
          </el-dropdown-item>
          <el-dropdown-item @click.native="goPath('/personalData')">
              个人资料
          </el-dropdown-item>
          <el-dropdown-item @click.native="goPath('/changepsw')">
              密码修改
          </el-dropdown-item>
          <el-dropdown-item @click.native="goPath('/collection')">
              我的收藏
          </el-dropdown-item>
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
import { mapGetters } from 'vuex'

export default {
  data () {
    return {
      isShowDrop: false
    }
  },
  computed: {
    ...mapGetters(['user'])
  },
  mounted () {
  },
  updated () {
  },
  methods: {
    logout () {
      this.$store.dispatch('Logout').then(() => {
        location.reload() // 为了重新实例化vue-router对象 避免bug
      })
    },
    goPath (path) {
      this.$router.push({path: path})
    },
    visibleChange () {
      this.isShowDrop = !this.isShowDrop
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.login-container,
.avatar-container {
  font-size: 18px;
  margin-left: 30px;
}
.login-container {
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
  cursor: pointer;
  color: #fff;
}
.avatar-container {
  display: inline-block;
  .avatar-wrapper {
    position: relative;
    cursor: pointer;
    transition: all 0.5s ease-in-out;
    color: #fff;
    .avatar-icon {
      margin-right: 6px;
    }
    > span {
      padding: 0 20px;
      display: inline-block;
    }
  }
  .avatar-wrapper.active {
    color: #ffffff;
    box-shadow: inset 0 0 3px 0 rgba(0, 0, 0, 0.09);
    border-radius: 2px 2px 0 0;
    background: #2953fc;
  }
}

// dropdown style
.user-dropdown {
  margin-top: 0;
  background-color: #5677fc;
  padding-top: 0;
  margin-top: 1px;
  border-radius: 0 0 4px 4px;
  border: 0;
  /deep/ .popper__arrow {
    display: none;
  }
  /deep/ .el-dropdown-menu__item {
    padding: 10px 24px;
    transition: all 0.3s ease-in-out;
    line-height: normal;
    font-size: 13px;
    color: #ffffff;
    &:hover {
      background-color: #2953fc;
    }
  }
}
</style>
