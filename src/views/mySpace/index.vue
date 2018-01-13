<template>
  <div id="my-space" class="my-space view-container">
      <aside>
          <user-card
            :user="user"
            class="user-card"></user-card>
      </aside>
      <section>
          <h1>最新内容</h1>
          <ul>
              <post-card
                v-for="(date, index) in dates"
                :key="index"
                :post.sync="date"></post-card>
          </ul>
          <paginatiom></paginatiom>
      </section>
  </div>
</template>

<script>
import PostCard from "components/PostCard";
import UserCard from "components/UserCard";
import Paginatiom from "components/Pagination";
import { getMyArticles } from "api/post";
import { getUser } from "api/user"
export default {
  name: "mySpace",
  components: {
    PostCard,
    UserCard,
    Paginatiom
  },
  data() {
    return {
      dates: [],
      user: {}
    };
  },
  mounted() {
    let id = this.$route.params.id
    getUser(id).then(res => {
      console.log('/////')
      console.log(res)
    })
    getMyArticles({
      id: id,
      offset: 0,
      limit: 20
    }).then(res => {
      console.log(res);
      this.dates = res.articles;
      this.user = {
        name: "淘淘",
        total: 200,
        avatar_url:
          "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100"
      };
    });
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
#my-space {
  max-width: 1180px;
}
.my-space {
  aside {
    .user-card{
      margin-top: 71px;
    }
  }
  section {
    h1 {
      background-color: #ffffff;
      border-radius: 4px;
      padding: 17px 18px;
      margin: 71px 0 12px 0;
      font-size: 32px;
      color: #5677fc;
    }
  }
}
</style>
