import fetch from 'utils/fetch';

export function getGroup(id) {
  return fetch({
    url: `/groups/${id}`,
    method: 'get',
  });
}
