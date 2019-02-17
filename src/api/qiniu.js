import fetch from 'utils/fetch'

export function getToken () {
  return fetch({
    url: `${process.env.SSO_SITE}/api/qiniu/token`,
    method: 'get'
  })
}
