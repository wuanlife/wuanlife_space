<template>
  <li class="collection-card wl-card">
    <div class="collection-card-content">
      <h1 @click="$router.push({ path: `/article/${item.id}` })">{{ item.title }}</h1>
      <div class="preview-html" v-html="content">
      </div>
      <div class="preview-imgs">
        <ul>
          <li v-for="(imgs, index) in item.image_url"
              :key="`${item.id}-${index}`">
            <img v-bind:src="imgs" />
          </li>
        </ul>
      </div>
    </div>
    <footer>
      <span class="collection-card-username" @click="$router.push({ path: `/myspace/${item.author.id}` })">{{item.author.name}}</span>
      <div>
        <span>收藏于</span>
        <time>{{ item.create_at | formatTime}}</time>
      </div>
    </footer>
  </li>
</template>

<script>
import { html2Text } from 'filters/index'
export default {
  name: 'collection-card',
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  computed: {
    content: function () {
      return html2Text(this.item.content)
    }
  }
}
</script>

<style lang="scss" type="stylesheet/scss" scoped>
.slide-fade-enter {
  opacity: 0;
  transform: translateX(20px);
}
.slide-fade-enter-active {
  transition: all 3.8s ease;
}
// post card style
.collection-card {
  margin: 10px 0;
  padding: 16px 16px 12px 16px;
  background-color: #ffffff;
  border-radius: 8px;
  &:not(:first-child) {
    margin-top: 8px;
  }
  &:last-child {
    margin-bottom: 108px;
  }
  footer {
    display: flex;
    align-items: center;
    font-size: 11px;
    color: #999999;
    position: relative;
    div {
      position: absolute;
      right: 0;
    }
    span {
      cursor: ppointer;
      &:not(:first-child) {
        margin-left: 5px;
      }
    }
    > .collection-card-plantname {
      cursor: pointer;
      transition: all 0.2s ease-in-out;
      &:hover {
        color: #5677fc;
      }
    }
  }
  div.collection-card-content {
    margin-bottom: 12px;
    h1 {
      margin-bottom: 6px;
      color: #333333;
      font-size: 15px;
      opacity: 0.87;
      cursor: pointer;
      display: inline-block;
      position: relative;
      &::after {
        content: "";
        transition: all 0.5s ease-in-out;
        transform: scaleX(0);
        position: absolute;
        width: 100%;
        height: 2px;
        bottom: 0;
        left: 0;
        background: #2e5897;
      }
      &:hover {
        &::after {
          transform: scaleX(1);
        }
      }
    }
    div.preview-html {
      margin-bottom: 12px;
      word-break: break-all;
      font-size: 11px;
      color: #444444;
      text-align: justify;
      overflow: hidden;
      -webkit-line-clamp: 3;
      display: -webkit-box;
      -webkit-box-orient: vertical;
    }
    div.preview-imgs {
      display: flex;
    }
  }
  .collection-card-username {
      cursor: pointer;
    }
}
</style>
