import fetch from 'utils/fetch';

export function getGroup(id) {
  return fetch({
    url: `/groups/${id}`,
    method: 'get'
  });
}
export function setGroup(id, params) {
  return fetch({
    url: `/groups/${id}`,
    method: 'put',
    data: params
  });
}
export function getGroupMumbers(id, params) {
  return fetch({
    url: `/groups/${id}/members`,
    method: 'get',
    data: params
  });
}
export function deleteGroupMumbers(id, mumberId) {
  return fetch({
    url: `/groups/${id}/members/${mumberId}`,
    method: 'delete'
  });
}
export function getGroups(offset = 0, limit = 20) {
  return fetch({
    url: `/groups?offset=${offset}&limit=${limit}`,
    method: 'get'
  });
}
export function getGroupsByUserId(user_id, offset = 0, limit = 20) {
  return fetch({
    url: `/groups?user_id=${user_id}&fset=${offset}&limit=${limit}`,
    method: 'get'
  });
}

export function searchGroups(name, offset = 0, limit = 20) {
  const data = {
    name,
    offset,
    limit
  };
  console.log('searchGroups: ', data);
  return fetch({
    url: '/groups',
    method: 'get',
    params: data
  });
}

export function joinGroup(id) {
  return fetch({
    url: `/groups/${id}/members`,
    method: 'post'
  })
}
// user apply to join the private group
export function applyPrivateGroup(id, params) {
  return fetch({
    url: `/groups/${id}/private`,
    method: 'post',
    data: params
  })
}
// creator agree the user to join the private group or not
export function processGroupApply(id, mid, params) {
  return fetch({
    url: `/users/${id}/messages/${mid}`,
    method: 'post',
    data: params
  })
}

export function quitGroup(id) {
  return fetch({
    url: `/groups/${id}/members`,
    method: 'delete'
  })
}

export function createGroup(data) {
  const data1 = {
    name: data.name,
    image_url: data.image_url,
    introduction: data.introduction,
    private: data.private
  };
  return fetch({
    url: '/groups',
    method: 'post',
    data: data1
  })
}