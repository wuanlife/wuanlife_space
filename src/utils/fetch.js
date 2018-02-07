import axios from 'axios'
import MockAdapter from 'axios-mock-adapter'
import router from '@/router'
import { MessageBox, Notification } from 'element-ui'
import mockData from 'api/mock'
import store from '../store'
// import router from '../router';

// 创建axios实例
const service = axios.create({
  baseURL: process.env.BASE_API, // api的base_url
  timeout: 5000 // 请求超时时间
})

// request拦截器
service.interceptors.request.use(config => {
  // Do something before request is sent
  if (store.getters.user['Access-Token']) {
    config.headers['Access-Token'] = store.getters.user['Access-Token']
  }
  return config
}, error => {
  Promise.reject(error)
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
    // console.log('err' + error);// for debug
    if (!error.response) {
      Notification({
        message: error.message,
        offset: 60
      })
    } else if (error.response.status === 401) {
      MessageBox.confirm('未登录或登录状态过期，可以取消继续留在该页面，或者重新登录', '跳转登录界面', {
        confirmButtonText: '重新登录',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        store.dispatch('Logout').then(() => {
          router.push({path: '/login'})
        })
      })
      return Promise.reject(error)
    }
    /* Message({
      message: error.message,
      type: 'error',
      duration: 5 * 1000
    }); */
    return Promise.reject(error.response)
  }
)

const mock = new MockAdapter(service, { delayResponse: 2000 })

mockData(mock)
if (process.env.API_MOCK === undefined || !process.env.API_MOCK) {
  mock.restore()
}
export default service
