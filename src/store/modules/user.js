import { login } from 'api/login';
import Cookies from 'js-cookie';
import { storeWithExpiration } from 'utils';

const user = {
  state: {
    token: storeWithExpiration.get('user.token') == '' ? storeWithExpiration.get('user.token') : '',
    userInfo: storeWithExpiration.get('user.userInfo') || {},
    setting: '',
    name: '',
    email: '',
    resource: ''
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
    SET_NAME: (state, name) => {
      state.name = name;
    },
    SET_EMAIL: (state, email) => {
      state.email = email;
    },
    SET_RESOURCE: (state, resource) => {
      state.resource = resource;
    }
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
    //保存个人资料
    SetInfo({ commit }, setinfo_params) {
      return new Promise((resolve, reject) => {
        getInfo(setinfo_params.email, setinfo_params.name, setinfo_params.resource).then(response => {
          console.dir(response)
          const data = response;
          commit('SET_NAME', data.name);  
          commit('SET_EMAIL', data.email);  
          commit('SET_RESOURCE', data.resource);
          resolve();      
        }).catch(error => {
          reject(error);
        });
      });
    }
  }
}

export default user;
