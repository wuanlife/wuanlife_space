import Vue from 'vue'
import { login, signup } from 'api/auth';
import { putUser } from 'api/user';
import { storeWithExpiration } from 'utils';

const loadUser = () => {
  const user = storeWithExpiration.get('user')
  if (user && user.id) {
    return user;
  }
}

const user = {
  state: {
    ...loadUser()
  },
  mutations: {
    SET_USER: (state, userInfo) => {
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
    async Login({ commit }, params) {
      const userWithToken = await login(params)
      commit('SET_USER', userWithToken)
      storeWithExpiration.set('user', userWithToken);
      return userWithToken
    },
    // for later one-use token, Logout should in actions
    Logout({ commit }) {
      commit('LOGOUT_USER');
      storeWithExpiration.set('user', {});
    },
    // 注册
    async Signup({ commit }, params) {
      const userWithToken = await signup(params)
      commit('SET_USER', userWithToken)
      storeWithExpiration.set('user', userWithToken);
      return userWithToken
    },
    PutUser({ commit }, params) {
      return new Promise((resolve, reject) => {
        putUser(params).then(response => {
          storeWithExpiration.set('user', {
            ...response,
            'Access-Token': loadUser()['Access-Token']
          }, 86400000);
          commit('SET_USER', response);
          resolve(response);
        }).catch(error => {
          reject(error);
        });
      });
    }
  }
};

export default user;
