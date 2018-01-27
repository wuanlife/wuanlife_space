import fetch from 'utils/fetch';
import store from 'vuex-store';
// get user details
export function getUser() {
  return fetch({
    url: `/users/${store.state.user.id}`,
    method: 'get'
  });
}
export function getUserById(id) {
  return fetch({
    url: `/users/${id}`,
    method: 'get'
  });
}

export function searchUsers(keyword, offset, limit) {
  const data = {
    keyword,
    offset,
    limit
  };
  return fetch({
    method: 'post',
    url: `/users/search?keyword=${keyword}&offset=${offset}&limit=${limit}`,
    data
  });
}

// change user details
export function putUser(params) {
  return fetch({
    url: `/users/${store.state.user.id}`,
    method: 'put',
    data: params
  });
}
export function getActiveUsers() {
  return fetch({
    url: '/users/active',
    method: 'get'
  })
}
export function changepsw(params) {
  const data = {
    oldpsw: params.oldpsw,
    password: params.password
  };
  return fetch({
    url: '/users',
    method: 'put',
    data
  });
}