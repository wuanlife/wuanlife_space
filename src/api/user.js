import fetch from 'utils/fetch';
export function getUser(id) {
  return fetch({
    url: `/users/${id}`,
    method: 'get',
  });
}