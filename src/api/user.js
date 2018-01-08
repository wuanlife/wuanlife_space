import fetch from 'utils/fetch';
// get user details
export function getUser(id) {
  return fetch({
    url: `/users/${id}`,
    method: 'get'
  });
}

// change user details
export function putUser(id, params) {
  return fetch({
    url: `/users/${id}`,
    method: 'put',
    data: params
  });
}