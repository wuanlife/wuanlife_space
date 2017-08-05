import fetch from 'utils/fetch';

export function register(email,nickname,password,inviteword) {
  const data = {
    name: nickname,
    mail: email,
    password: password,
    code: inviteword,
  };
  return fetch({
    url: '/users',
    method: 'post',
    data: data,
  });
}