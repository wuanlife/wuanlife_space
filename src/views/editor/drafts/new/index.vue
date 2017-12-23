<template>
  <div id="drafts-new" class="view-container">
    <section>
      <header>
        写文章
      </header>
      <wuan-editor>

      </wuan-editor>
    </section>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import wuanEditor from "../../common/wuanEditor";

export default {
  name: "drafts-new",
  components: {
    wuanEditor: "wuan-editor"
  },
  data() {
    return {
    };
  },
  computed: {
    ...mapGetters(["user", "access_token"])
  },
  created() {
    let name = this.$route.query.name;
    document.title = name + " - 午安网 - 过你想过的生活";
    if (!this.$route.query.groupid) {
      this.$router.go(-1);
      return;
    }
    this.groupid = parseInt(this.$route.query.groupid);
  },
  mounted() {},
  methods: {
    markdown2Html(md) {
      const converter = new showdown.Converter();
      return converter.makeHtml(md);
    },
    onSubmit() {
      this.form.content = this.markdown2Html(this.form.md);
      publishPost(this.groupid, {
        title: this.form.title,
        content: this.form.content
      })
        .then(res => {
          this.$router.push({
            path: `/topic/${res.id}`,
            query: { name: this.form.title }
          });
        })
        .catch(error => {
          console.log(`publish error ${error}`);
        });
    }
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
#post-publish {
  display: flex;
  justify-content: center;
  margin: auto;
  max-width: 660px;
  min-width: 590px;
  section {
    header {
      margin: 15px 0 20px 0;
      font-family: PingFangHK-Medium;
      font-size: 18px;
      color: #5677fc;
    }
    min-width: 0;
    flex: 0 0 590px;
  }
}
.post-publish-container {
  background: #ffffff;
  min-height: 300px;
  padding: 20px 30px;
  // label
  p {
    font-family: PingFangSC-Regular;
    font-size: 12px;
    color: #666666;
  }
}

// editor style
.editor-container {
  font-family: PingFangSC-Regular;
  font-size: 16px;
  line-height: 24px;
  color: #999999;
}
</style>