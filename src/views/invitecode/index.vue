<template>
    <div class="invitecode-container">
      <section>     
        <header>邀请好友</header>
        <div class="code-content">
          <h2>邀请码</h2>
          <p v-if="userDetails">{{ userDetails.code }}</p>
          <p v-else>加载中...</p>
        </div>
      </section>
    </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getUser } from 'api/user';

  export default {
    name: 'invitecode',
    data() {
      return {
        loading: false,
        userDetails: null,
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ])
    },
    mounted() {
      var self = this;
      getUser(this.user.userInfo.id).then(res => {
        self.userDetails = res;
        console.dir(res)
      })
    },
    methods: {

    },
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .invitecode-container {
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 660px;
    min-width: 380px;
    section {
      flex: 1;
      header {
        margin: 15px 0 20px 0;

        font-family:PingFangHK-Medium;
        font-size:18px;
        color:#5677fc;
      }
    }
  }

  .code-content {
    background:#ffffff;
    border-radius:4px;
    padding: 130px 235px 230px 235px;
    margin: auto;
    h2 {
      margin-bottom: 4px;

      font-size:22px;
      color:#999999;
    }
    p {
      font-size:40px;
      color:#5677fc;
      letter-spacing:5px;
    }
  }
</style>
