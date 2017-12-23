import axios from 'axios';
import MockAdapter from 'axios-mock-adapter'
import { Message, MessageBox } from 'element-ui';
import store from '../store';
// import router from '../router';

// 创建axios实例
const service = axios.create({
  baseURL: process.env.BASE_API, // api的base_url
  timeout: 5000                  // 请求超时时间
});

// const mock = new MockAdapter(axios);
// const mockAdapter = axios.defaults.adapter;

// mock.restore();
// axios.defaults.adapter = mockAdapter;

// request拦截器
service.interceptors.request.use(config => {
  // Do something before request is sent
  if (store.getters.user.token) {
    config.headers['Access-Token'] = store.getters.user.token;
  }
  return config;
}, error => {
  Promise.reject(error);
})

// respone拦截器
service.interceptors.response.use(
  response => {
    if (response.status === 200) {
      return response.data
    }
    if (response.status === 204) {
      return {}
    }
  },
  error => {
    console.dir(error);// for debug
    console.log('err' + error);// for debug
    if (error.response.status == 401) {
      MessageBox.confirm('你已被登出，可以取消继续留在该页面，或者重新登录', '确定登出', {
        confirmButtonText: '重新登录',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        store.dispatch('Logout').then(() => {
          location.reload();// 为了重新实例化vue-router对象 避免bug
        });
      })
      return Promise.reject(error);
    }
    /* Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    });*/
    return Promise.reject(error.response);
  }
)

export default service;
