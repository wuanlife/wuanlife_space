<template>
    <div class="index-visitor-container">
      <section>
      
        <el-tabs v-model="activeName" @tab-click="handleClick">
          <el-tab-pane label="myplanet" name="index-myplanet">
            <div class="index-tabcontent" v-loading="loading">
              asdasdasdasdasdas
            </div>
          </el-tab-pane>
          <el-tab-pane label="myplanet" name="index-newtopic">
            <div class="index-tabcontent" v-loading="loading2">
              newtopic
            </div>
          </el-tab-pane>
        </el-tabs>
      </section>
      <aside>
        <header>
          <h1>发现星球</h1>
        </header>
        <div class="aside-container">
          
        </div>
      </aside>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'index-visitor',
    data() {
      return {
        activeName: 'index-myplanet',
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
      this.loading2 = true;
      // simulate ajax loading
      setTimeout(() => {
        this.loading = false;
      }, 4000)
      setTimeout(() => {
        this.loading2 = false;
      }, 6000)

    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .index-visitor-container {
    margin: auto;
    width: 750px;
  }
  .index-tabcontent {
    min-height: 200px;
  }
</style>
