import { login, signup, setinfo } from 'api/auth';
import { storeWithExpiration } from 'utils';

const user = {
  state: {
    token: storeWithExpiration.get('user.token') != '' ? storeWithExpiration.get('user.token') : '',
    userInfo: storeWithExpiration.get('user.userInfo') || {},
    setting: '',
    searchText: '',
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
    SET_SEARCHTEXT: (state, inputValue) => {
      state.searchText = inputValue;
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
    //注册
    Signup({ commit },register_params){
      return new Promise((resolve, reject) => {
        signup(register_params).then(response => {
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
    setinfo({ commit },info_params){
      return new Promise((resolve, reject) => {
        register(info_params).then(response => {
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
    setinfo({ commit },info_params){
      return new Promise((resolve, reject) => {
        register(info_params).then(response => {
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
