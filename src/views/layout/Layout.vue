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
  import { Navbar, AppMain } from 'views/layout';

  export default {
    name: 'layout',
    components: {
      Navbar,
      AppMain
    },
    computed: {
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  @import "src/styles/mixin.scss";
  .app-wrapper {
    @include clearfix;
    position: relative;
    height: 100%;
    width: 100%;
    .navbar-wrapper {
      height: 50px;
    }
    .main-container {
      background-color: #f8f9fa;
      height: calc(100vh - 50px);
      overflow: auto;
      transition: all .28s ease-out;
    }
  }
</style>
