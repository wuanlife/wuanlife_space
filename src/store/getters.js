const getters = {
  token: state => state.user.token,
  user: state => state.user,
  userInfo: state => state.user.userInfo,
  setting: state => state.user.setting,
};
export default getters
