import fetch from 'utils/fetch';

export function getGroup(id) {
  return fetch({
    url: `/groups/${id}`,
    method: 'get',
  });
}
export function getGroups(offset=0, limit=20) {
  return fetch({
    url: `/groups?offset=${offset}&limit=${limit}`,
    method: 'get',
  });
}

export function joinGroup(id) {
  return fetch({
    url: `/groups/${id}/members`,
    method: 'post',
  })
}
export function quitGroup(id) {
  return fetch({
    url: `/groups/${id}/members`,
    method: 'delete',
  })
}