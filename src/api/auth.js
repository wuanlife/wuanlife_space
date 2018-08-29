import fetch from 'utils/fetch'

// login and get ID-Token
export function login (params) {
  const data = {
    mail: params.mail,
    password: params.password,
    client_id: params.client_id
  }
  return fetch({
    url: '/api/users/login',
    method: 'post',
    data
  })
}

// get Access-Token
export function getAccessToken (params = {scope: 'public_profile'}) {
  const data = {
    scope: params.scope
  }
  return fetch({
    url: '/api/auth',
    method: 'post',
    data
  })
}

// 验证Token完整性
export function loginOrNot (params) {
  return fetch({
    url: '/api/auth',
    method: 'get'
  })
}

export function signup (params) {
  const data = {
    name: params.name,
    mail: params.mail,
    password: params.password,
    client_id: 'wuan'
  }
  return fetch({
    url: '/api/users/register',
    method: 'post',
    data
  })
}
