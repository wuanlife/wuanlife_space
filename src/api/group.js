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

export function searchGroups(name, offset=0, limit=20) {
  const data = {
    name: name,
    offset: offset,
    limit: limit,
  };
  return fetch({
    url: '/groups',
    method: 'get',
  });
}
