<template>
<div class="nav-menu">	  
    <el-dropdown menu-align="start" 
                v-if="user.token != ''" 
                class="avatar-container" 
                trigger="click"
                @visible-change="visibleChange">
    <span class="write-article">
	  	<icon-svg icon-class="write" class="avatar-icon"></icon-svg>
            写文章
	  </span>
    <div class="avatar-wrapper" :class="{'active' : isShowDrop}">
        <span>{{ user.userInfo.name || '陶陶' }} <icon-svg icon-class="triangle1" class="avatar-icon"></icon-svg></span>
    </div>
    <el-dropdown-menu class="user-dropdown" slot="dropdown">    
        <router-link class='inlineBlock' to="/mySpace/">
        <el-dropdown-item>
            <icon-svg icon-class="myspace" class="avatar-icon"></icon-svg>
        我的空间
        </el-dropdown-item>
        </router-link>
        <router-link class='inlineBlock' to="/personalData/">
        <el-dropdown-item>
            <icon-svg icon-class="people_2" class="avatar-icon"></icon-svg>
            个人资料
        </el-dropdown-item>
        </router-link>
        <router-link class='inlineBlock' to="/resetpsw/">
        <el-dropdown-item>
            <icon-svg icon-class="lock_2" class="avatar-icon"></icon-svg>
            密码修改
        </el-dropdown-item>
        </router-link>
        <router-link class='inlineBlock' to="/collection/">
        <el-dropdown-item>
            <icon-svg icon-class="starSolid" class="avatar-icon"></icon-svg>
            我的收藏
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

/*添加登录后首页顶部栏菜单样式*/
.write-article{
	margin-left: 100px;
	cursor: pointer;
}



.login-container,
.avatar-container {
    margin-right: 30px;
    color: white;
}
.login-container {
    
    span {
    padding: 0 14px;
    &:not(:first-child) {
        border-left: 1px solid #fff;     
    }
    }
}
.avatar-container {
	/*设置登陆后顶部菜单排列*/
	*{
  	display: inline-block;
  }
    .avatar-wrapper {
    position: relative;
    /*菜单顶部对齐*/
    /*top: 0px;*/
    padding: 8px 14px 15px 14px;
    width: 114px;
    height: 43px;
    cursor: pointer;
    line-height: 20px;
    transition: all 0.5s ease-in-out;
    color: #fff;
    font-size: 14px;
    .avatar-icon {
        font-size: 10px;
        margin-right: 6px;
    }
    /*> span {
        width: 58px;
        display: inline-block;
    }*/
    }
    /*.avatar-wrapper.active {
    color: #ffffff;
    box-shadow: inset 0 0 3px 0 rgba(0, 0, 0, 0.09);
    border-radius: 2px 2px 0 0;
    background: #0074e9;
    border-bottom: 1px solid #3b52ab;
    }*/
}

// dropdown style
.user-dropdown {
	/*下拉框添加蓝色背景*/
	background-color: #5677fc;
  a {
    font-size: 14px;
    color: #fff;
    li {
    	color: #fff;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 16px; 
    }
  }
}
</style>
