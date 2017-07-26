<template>
    <div class="index-visitor-container">
      <section>     
        asd
      </section>
      <aside>
        asda
      </aside>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';

  export default {
    name: 'index-visitor',
    data() {
      return {
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
      // simulate ajax loading
      setTimeout(() => {
        this.loading = false;
      }, 4000)
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .index-visitor-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 900px;
    min-width: 590px;
    @media screen and (max-width: 900px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 590px;
    }
    aside {
      flex: 0 0 250px;
      @media screen and (max-width: 900px) {
        display: none;
      }
      
    }  
  }
</style>
