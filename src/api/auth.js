import fetch from 'utils/fetch'

// login and get ID-Token
export function login (params) {
  const data = {
    mail: params.mail,
    password: params.password,
    client_id: '123'
  }
  return fetch({
    url: '/api/users/login',
    method: 'post',
    data
  })
}

// get Access-Token
export function getAccessToken (params) {
  const data = {
    'scope': params.scope
  }

  return fetch({
    url: '/api/auth',
    method: 'post',
    data,
    header: { 'ID-Token': params['ID-Token'] }
  })
}

// 验证Token完整性
export function loginOrNot (params) {
  return fetch({
    url: '/api/auth',
    method: 'get',
    headers: { 'Access-Token': params['Access-Token'],
      'ID-Token': params['ID-Token'] }
  })
}

export function signup (params) {
  const data = {
    name: params.name,
    mail: params.mail,
    password: params.password,
    client_id: '123'
  }
  return fetch({
    url: '/api/users/register',
    method: 'post',
    data
  })
}
