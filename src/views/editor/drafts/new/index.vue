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
    "wuan-editor": wuanEditor
  },
  data() {
    return {};
  },
  created() {
  },
  mounted() {},
  methods: {
    onSubmit() {}
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
#drafts-new {
  width: 1054px;
  section {
    margin-top: 89px;
    margin-bottom: 124px;
    padding: 49px 40px 107px 40px;
    background: #ffffff;
    header {
      margin-bottom: 65px;
      font-size: 32px;
      letter-spacing: 0px;
      color: #5677fc;
    }
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