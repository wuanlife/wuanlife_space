import fetch from 'utils/fetch';

export function getPosts(id) {
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
