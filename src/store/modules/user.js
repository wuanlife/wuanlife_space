import { login } from 'api/login';
import { register } from 'api/register';
import Cookies from 'js-cookie';
import { storeWithExpiration } from 'utils';

const user = {
  state: {
    token: storeWithExpiration.get('user.token') == '' ? storeWithExpiration.get('user.token') : '',
    userInfo: storeWithExpiration.get('user.userInfo') || {},
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
      return new Promise((resolve, reject) => {
        login(login_params.email, login_params.password).then(response => {
          console.dir(response)
          const data = response;
          storeWithExpiration.set('user.userInfo', response, 86400000);
          storeWithExpiration.set('user.token', response['Access-Token'], 86400000);
          commit('SET_USERINFO', response);  
          commit('SET_TOKEN', response['Access-Token']);  
          resolve();      
        }).catch(error => {
          reject(error);
        });
   
      });
    },
    // for later one-use token, Logout should in actions
    Logout({ commit }) {
      commit('LOGOUT_USER'); 
      storeWithExpiration.set('user.userInfo', {});
      storeWithExpiration.set('user.token', '');
    },
    //注册
    Rigister({ commit },register_params){
      return new Promise((resolve, reject) => {
        register(register_params.email, register_params.nickname,register_params.password,register_params.inviteword).then(response => {
          console.dir(response)
          const data = response;
          storeWithExpiration.set('user.userInfo', response, 86400000);
          storeWithExpiration.set('user.token', response['Access-Token'], 86400000);
          commit('SET_USERINFO', response);  
          commit('SET_TOKEN', response['Access-Token']);  
          resolve();      
        }).catch(error => {
          reject(error);
        });
   
      });
    }
  }
};

export default user;
