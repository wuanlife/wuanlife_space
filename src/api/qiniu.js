import fetch from 'utils/fetch'

export function getToken () {
  return fetch({
    url: '/qiniu/token',
    method: 'get'
  })
}
