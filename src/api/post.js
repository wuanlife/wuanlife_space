import fetch from 'utils/fetch';

export function getPosts() {
  return fetch({
    url: '/posts',
    method: 'get',
  });
}

export function getPost(id) {
  return fetch({
    url: `/posts/${id}`,
    method: 'get',
  });
}

export function getCommentsByPostId(id, offset=0, limit=20) {
  return fetch({
    url: `/posts/${id}/comments?offset=${offset}&limit=${limit}`,
    method: 'get',
  });  
}