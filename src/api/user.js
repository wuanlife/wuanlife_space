import fetch from 'utils/fetch'
import store from 'vuex-store'
// get user details
// const baseUrl = 'http://dev-oidc.wuanla.tk/api'
// const spaceUrl = 'http://dev-space-api.wuanla.tk/api'
export function getUser () {
  return fetch({
    url: `${process.env.SSO_SITE}/api/users/${store.state.user.uid}`,
    method: 'get'
  })
}
export function getUserById (id) {
  return fetch({
    url: `${process.env.SSO_SITE}/api/users/${id}`,
    method: 'get'
  })
}

export function searchUsers (keyword, offset, limit) {
  const data = {
    keyword,
    offset,
    limit
  }
  return fetch({
    method: 'post',
    url: `${process.env.SSO_SITE}/api/users/search?keyword=${keyword}&offset=${offset}&limit=${limit}`,
    data
  })
}

// change user details
export function putUser (params) {
  return fetch({
    url: `${process.env.SSO_SITE}/api/users/${store.state.user.uid}`,
    method: 'put',
    data: params
  })
}
export function getActiveUsers () {
  // http://dev-space-api.wuanla.tk/api/users/active
  return fetch({
    url: `/users/active`,
    method: 'get'
  })
}
export function changePassword (params) {
  const data = {
    old_psd: params.old_psd,
    new_psd: params.new_psd
  }
  return fetch({
    url: `${process.env.SSO_SITE}/api/users/${store.state.user.uid}/password`,
    method: 'put',
    data
  })
}
