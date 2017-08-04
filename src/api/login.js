import fetch from 'utils/fetch';

export function login(email, password) {
  const data = {
    mail: email,
    password: password,
  };
  return fetch({
    url: '/users/signin',
    method: 'post',
    data: data,
  });
}

export function getInfo(email, name, resource) {
  const data = {
    email: email,
    name: name,
    resource: resource,
  }
  return fetch({
    url: '/user/info',
    method: 'post',
    data: data,
  });
}
