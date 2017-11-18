<template>
  <div class="allGroups-container">
    <section>
      <header>全部星球</header>
      <div class="planetsBox" v-loading='loading'>
        <div v-for="item in myGroups" class="allGroups-card">
          <img v-bind:src="item.image_url"/>
          <h3 @click="$router.push({ path: `/planet/${item.id}`, query: { name: item.name }})">{{ item.name }}</h3>
          <span>{{ item.introduction }}</span>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
  import { mapGetters } from 'vuex';
  import { getGroupsByUserId,getGroups } from 'api/group';
  
  export default {
    name: 'allGroups',
    data () {
      return {
        loading: false,
        myGroups: []
      }
    },
    computed: {
      ...mapGetters([
        'user',
      ]),
    },
    created() {
      let name = this.$route.query.name;
      document.title = name + ' - 午安网 - 过你想过的生活';
    },
    mounted() {
      if (this.user.userInfo.id) {
        this.loadUserGroups()
        .then()
        .catch((err) => {
          console.dir(err);
          this.$message({
            message: err.error,
            type: 'error',
            duration: 1000,
          });
          this.loading = false;
        });
      } else{
        this.loadGroups()
        .then()
        .catch((err) => {
          console.dir(err);
          this.$message({
            message: err.error,
            type: 'error',
            duration: 1000,
          });
          this.loading = false;
        });
      }
    },
    methods: {
      loadUserGroups () {
        var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          getGroupsByUserId(self.user.userInfo.id).then(res => {
            self.myGroups = res.data;
            self.loading = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
      loadGroups () {
        var self = this;
        this.loading = true;
        return new Promise((resolve, reject) => {
          getGroups(self.user.userInfo.id).then(res => {
            self.myGroups = res.data;
            self.loading = false;
            resolve();
          }).catch(error => {
            reject(error);
          });
        });
      },
    }
  }
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
  .allGroups-container{
    display: flex;
    justify-content: space-between;
    margin: auto;
    max-width: 700px;
    min-width: 590px;
    @media screen and (max-width: 700px) {
      justify-content: center;
    }
    section {
      min-width: 0;
      flex: 0 0 716px;
      header {
        font-family:PingFangHK-Medium;
        font-size:18px;
        color:#5677fc;
        margin-top: 15px;
        line-height: 25px;
      }
      .planetsBox {
        padding-top: 20px;
        .allGroups-card {
          background:#ffffff;
          border-radius:4px;
          width:342px;
          height:86px;
          position: relative;
          padding: 14px 0 0 68px;
          margin-bottom: 12px;
          margin-right: 16px;
          box-sizing: border-box;
          display: inline-block;
          img {
            width:50px;
            height:50px;
            border-radius:100%;
            position: absolute;
            top: 10px;
            left: 10px;
            margin-right: 8px;
          }
          h3,span {
            font-size:14px;
            width: 252px;
            display: block;
          }
          h3 {
            font-family:PingFangHK-Medium;
            color:#5992e4;
            line-height: 20px;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
          }
          span {
            font-family:PingFangHK-Regular;
            color:#666666;
            min-height: 19px;
            word-wrap: break-word;
            overflow : hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
          }
        }
      }
    }
    
  }
</style>
