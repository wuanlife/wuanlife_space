// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from './router';
import store from 'vuex-store';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-default/index.css';
// import 'assets/custom-theme/index.css'; // 换肤版本element-ui css
import NProgress from 'nprogress'; // Progress 进度条
import 'nprogress/nprogress.css';// Progress 进度条 样式
import 'normalize.css/normalize.css';// normalize.css 样式格式化
import 'assets/iconfont/iconfont'; // iconfont 具体图标见https://github.com/PanJiaChen/vue-element-admin/wiki
import * as filters from './filters'; // 全局vue filter
import IconSvg from 'components/Icon-svg';// svg 组件

// register globally
Vue.component('icon-svg', IconSvg)
Vue.use(ElementUI);

// register global utility filters.
Object.keys(filters).forEach(key => {
  Vue.filter(key, filters[key])
});


// register global progress.
const blackList = ['inform', 'collection'];// 重定向黑名单
router.beforeEach((to, from, next) => {
  NProgress.start(); // 开启Progress
  if (store.getters.user.token != '') { // 判断是否有token
    if (to.path === '/login') {
      next({ path: '/' });
    } else {
      next()
    }
  } else {
    if (blackList.indexOf(to.name) == -1) { // 不在录重定向黑名单，直接进入
      next()
    } else {
      next('/login'); // 否则全部重定向到登录页
      NProgress.done(); // 在hash模式下 改变手动改变hash 重定向回来 不会触发afterEach 暂时hack方案 ps：history模式下无问题，可删除该行！
    }
  }
});

router.afterEach(() => {
  NProgress.done(); // 结束Progress
});

Vue.config.productionTip = false;

// 生产环境错误日志
if (process.env === 'production') {
  Vue.config.errorHandler = function(err, vm) {
    console.log(err, window.location.href);
    errLog.pushLog({
      err,
      url: window.location.href,
      vm
    })
  };
}

new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
})


