import { login } from 'api/login';
import Cookies from 'js-cookie';
import { storeWithExpiration } from 'utils';

const user = {
  state: {
    token: storeWithExpiration.get('token') || '',
    userInfo: storeWithExpiration.get('userInfo') || {},
    setting: '',
  },

  mutations: {
    SET_USERINFO: (state, userInfo) => {
      console.log('set')
      state.userInfo = userInfo
    },
    SET_TOKEN: (state, token) => {
      state.token = token
    },
    LOGOUT_USER: state => {
      state.token = '';
      state.userInfo = {};
    },
  },

  actions: {
    // 邮箱登录
    // TODO: login_params descripe

    Login({ commit }, login_params) {
      let response = {
        "id": "192",
        "name": "dfgsdsd",
        "mail": "1112",
        "Access-Token": "dsjkvbeivleavmkmvksdnboifejvmsks",
      }
      return new Promise((resolve, reject) => {
        /*login(login_params.email, login_params.password).then(response => {
          console.dir(response)
          const data = response;
          storeWithExpiration.set('user', response, 86400000);
          commit('SET_TOKEN', data.token);
          resolve();
        }).catch(error => {
          reject(error);
        });*/

        // for demo
        console.dir(response);
        storeWithExpiration.set('userInfo', response, 86400000);
        storeWithExpiration.set('token', response['Access-Token'], 86400000);
        commit('SET_USERINFO', response);  
        commit('SET_TOKEN', response['Access-Token']);  
        resolve();      
      });
    },
    // for later one-use token, Logout should in actions
    Logout({ commit }) {
      commit('LOGOUT_USER'); 
      storeWithExpiration.set('userInfo', {});
      storeWithExpiration.set('token', '');
    }
  }
};

export default user;
