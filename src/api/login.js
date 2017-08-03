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