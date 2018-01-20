import fetch from 'utils/fetch';

export function login(params) {
  const data = {
    mail: params.mail,
    password: params.password
  };
  return fetch({
    url: '/users/signin',
    method: 'post',
    data
  });
}

export function signup(regObj) {
  const data = {
    name: regObj.nickname,
    mail: regObj.email,
    password: regObj.password
  };
  return fetch({
    url: '/users',
    method: 'post',
    data
  });
}