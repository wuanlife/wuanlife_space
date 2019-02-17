import fetch from 'utils/fetch'

export function userSearch (name) {
  return fetch({
    url: '/api/search/user',
    method: 'get',
    params: { name }
  })
}
