import fetch from 'utils/fetch';

export function register(email,nickname,password,inviteword) {
  const data = {
    user_email: email,
    user_name: nickname,
    password: password,
    i_code: inviteword,
  };
  return fetch({
    url: '/user/reg',
    method: 'post',
    data: data,
  });
}