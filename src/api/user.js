import fetch from 'utils/fetch';
import store from 'vuex-store';
// get user details
export function getUser() {
  return fetch({
    url: `/users/${store.user.id}`,
    method: 'get'
  });
}
export function getUserById(id) {
  return fetch({
    url: `/users/${id}`,
    method: 'get'
  });
}

// change user details
export function putUser(params) {
  return fetch({
    url: `/users/${store.user.id}`,
    method: 'put',
    data: params
  });
}