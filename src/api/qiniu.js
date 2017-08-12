import fetch from 'utils/fetch';

// use a imageServer or just back-end server, uploadToken should not be generated in front-end 


// OPTIMIZE: make it configurable
export function getToken() {
  return fetch({
    url: '/qiniu/upload/token', // 假地址 自行替换
    method: 'get'
  });
}
