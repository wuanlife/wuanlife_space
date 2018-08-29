<template>
  <div class="app-wrapper">
    <div class="navbar-wrapper">
      <navbar></navbar>
    </div>
    <div class="main-container">
      <app-main></app-main>
    </div>
  </div>
</template>

<script>
import { Navbar, AppMain } from 'views/layout'

export default {
  name: 'layout',
  components: {
    Navbar,
    AppMain
  },
  computed: {},
  mounted () {
    this.$store.commit('CLEAR_USER')
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.app-wrapper {
  @include clearfix;
  position: relative;
  height: 100%;
  width: 100%;
  min-width: 750px;
  .navbar-wrapper {
    z-index: 100;
  }
  .main-container {
    background-color: #f8f9fa;
    height: calc(100vh - #{$nav-height});
    overflow: auto;
    transition: all 0.28s ease-out;
  }
}
</style>
