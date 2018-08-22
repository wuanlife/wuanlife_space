import Vue from 'vue'
import { login, signup, getAccessToken } from 'api/auth'
import { putUser } from 'api/user'
// import { storeWithExpiration } from 'utils'

// const loadUser = () => {
//   const user = storeWithExpiration.get('user')
//   if (user && user.id) {
//     return user
//   }
// }

const user = {
  state: {},
  mutations: {
    SET_USER: (state, userInfo) => {
      console.log('setUser')
      for (const key in userInfo) {
        Vue.set(state, key, userInfo[key])
      }
    },
    CLEAR_USER: state => {
      for (const key in state) {
        state[key] = null
      }
    }
  },

  actions: {
    // 邮箱登录
    async Login ({ commit }, params) {
      // return ID-Token
      console.log('action->login')
      const tokenData = await login(params)
      console.log('tokenData:' + [...tokenData])
      const idToken = tokenData['ID-Token']
      // console.log('idToken')
      const user = JSON.parse(atob(idToken.split('.')[1]))
      console.log('action->login->atob')
      commit('SET_USER', {
        ...tokenData,
        ...user
      })
      // storeWithExpiration.set('user', userWithToken)
      return idToken
    },
    // for later one-use token, Logout should in actions
    Logout ({ commit }) {
      commit('CLEAR_USER')
      // storeWithExpiration.set('user', {})
    },
    // get Access-Token
    async AccessToken ({ commit }, params) {
      const accToken = await getAccessToken(params)
      const accessToken = accToken['Access-Token']
      commit('SET_USER', accToken)
      return accessToken
    },
    // 注册
    async Signup ({ commit }, params) {
      const idToken = await signup(params)['ID-Token']
      const user = JSON.parse(atob(idToken.split('.')[1]))
      commit('SET_USER', {
        idToken,
        ...user
      })
      // storeWithExpiration.set('user', userWithToken)
      return idToken
    },
    async PutUser ({ commit, state }, params) {
      const backMessage = await putUser(params)
      const user = Object.create(null)
      Object.assign(user, state, params)
      // storeWithExpiration.set('user', {
      //   ...user,
      //   'Access-Token': ''
      // }, 86400000)
      commit('SET_USER', user)
      return backMessage
      // return new Promise((resolve, reject) => {
      //   putUser(params).then(response => {
      //     storeWithExpiration.set('user', {
      //       ...response,
      //       'Access-Token': loadUser()['Access-Token']
      //     }, 86400000);
      //     commit('SET_USER', response);
      //     resolve(response);
      //   }).catch(error => {
      //     reject(error);
      //   });
      // });
    }
  }
}

export default user
