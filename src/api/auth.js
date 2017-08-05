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

export function signup(regObj) {
  const data = {
    name: regObj.nickname,
    mail: regObj.email,
    password: regObj.password,
    code: regObj.inviteword,
  };
  return fetch({
    url: '/users',
    method: 'post',
    data: data,
  });
}

export function setinfo(infoObj) {
  const data = {
    name: infoObj.name,
    avatar_url: infoObj.imageUrl,
    sex: infoObj.sex,
    birthday: infoObj.birthday,
  };
  return fetch({
    url: '/users/:id',
    method: 'put',
    data: data,
  })
}
