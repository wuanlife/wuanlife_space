import fetch from 'utils/fetch'

export function login (params) {
  const data = {
    mail: params.mail,
    password: params.password
  }
  return fetch({
    url: '/users/signin',
    method: 'post',
    data
  })
}

export function signup (params) {
  const data = {
    name: params.name,
    mail: params.mail,
    password: params.password
  }
  return fetch({
    url: '/users',
    method: 'post',
    data
  })
}
