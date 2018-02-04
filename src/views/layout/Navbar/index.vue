<template>
  <el-menu class="navbar" mode="horizontal">
    <div class="navbar-container">
      <!-- Logo bar -->
      <div class="logo" @click="$router.push({path: '/'})">
        午安空间
      </div>
      <!-- Search bar -->
      <search-bar></search-bar>
      <!-- avatar bar -->
      <nav-menu></nav-menu>
    </div>
  </el-menu>
</template>

<script>
import { mapGetters } from 'vuex'
import SearchBar from './SearchBar'
import NavMenu from './NavMenu'

export default {
  components: {
    'search-bar': SearchBar,
    'nav-menu': NavMenu
  },
  data () {
    return {
    }
  },
  computed: {
    ...mapGetters(['user'])
  },
  methods: {
    logout () {
      this.$store.dispatch('Logout').then(() => {
        location.reload() // 为了重新实例化vue-router对象 避免bug
      })
    },
    visibleChange () {
      this.isShowDrop = !this.isShowDrop
    },
    handleIconClick () {
      if (this.input2 !== '') {
        this.$store.commit('SET_SEARCHTEXT', this.searchContent)
        this.$router.push({ path: '/search' })
      } else {
        alert('请输入要搜索的内容')
      }
    },
    clickDot () {
      this.dotShow = false
    }
  },
  mounted () {
    // this.toWs();
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.navbar {
  line-height: $nav-height;
  border-radius: 0px !important;
  background-color: $wl-blue;
  .navbar-container {
    display: flex;
    max-width: 1200px;
    justify-content: space-around;
    margin: auto;
    .logo {
      cursor: pointer;
      margin-right: 38px;
      font-size: 30px;
      color: #ffffff;
      text-align: left;
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
  }
}
</style>
