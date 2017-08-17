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
  });
}

//重置密码接口
export function resetpsw(infoObj) {
  const data = {
    mail: infoObj.email,
  };
  return fetch({
    url: '/users/resetpsw',
    method: 'post',
    data: data,
  });
}

export function getPostInform(id, type, offset=0, limit=20) {
  return new fetch({
    url: `/users/${id}/messages?offset=${offset}&limit=${limit}&type=${type}`,
    method: 'get',
  });
}

export function dealApplyPlanetPost(id, mid, is_apply) {
  const data = {
    is_apply: is_apply,
  };
  return new fetch({
    url: `/users/${id}/messages/${mid}`,
    method: 'post',
    data: data,
  });
}

export function getUserInfo(id) {
  return new fetch({
    url: `/users/${id}`,
    method: 'get',
  });
}


//重置密码接口,发送新密码数据
export function resetpassword(infoObj) {
  const data = {
    token: infoObj.token,
    password: infoObj.password
  };
  return fetch({
    url: '/users/resetpsw',
    method: 'put',
    data: data,
  });
} 
