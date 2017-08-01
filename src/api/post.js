import fetch from 'utils/fetch';

export function getPosts(latest=true, offset=0, limit=20) {
  return fetch({
    url: `/posts?latest=${latest}&offset=${offset}&limit=${limit}`,
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