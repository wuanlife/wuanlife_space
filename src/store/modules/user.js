import { login } from 'api/login';
import Cookies from 'js-cookie';
import { storeWithExpiration } from 'utils';

const user = {
  state: {
    token: Cookies.get('Admin-Token'),
    userInfo: {},
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
      state.user = '';
    }
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
        storeWithExpiration.set('user', response, 86400000);
        storeWithExpiration.set('token', ['Access-Token'], 86400000);
        commit('SET_USERINFO', response);  
        commit('SET_TOKEN', response['Access-Token']);  
        resolve();      
      });
    },
  }
};

export default user;
