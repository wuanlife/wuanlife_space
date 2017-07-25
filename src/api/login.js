import fetch from 'utils/fetch';

export function login(email, password) {
  const data = {
    email: email,
    password: password,
  };
  return fetch({
    url: '/users/signin',
    method: 'post',
    data
  });
}