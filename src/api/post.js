import fetch from 'utils/fetch';

export function getPosts(latest=true, offset=0, limit=20) {
  return fetch({
    url: `/posts?latest=${latest}&offset=${offset}&limit=${limit}`,
    method: 'get',
  });
}
export function getPostsByGroupId(groupid, offset=0, limit=20) {
  return fetch({
    url: `/groups/${groupid}/posts?offset=${offset}&limit=${limit}`,
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

// params {id: postid, floor: floor}
export function approvePost(params) {
  const data = {
    floor: floor || 1,
  };
  return fetch({
    url: `/posts/${params.id}/approval`,
    method: 'post',
    data: data,
  });
}
// put? 后端在逗我吧 /users/:id/collections
export function collectPost(params) {
  const data = {
    floor: floor || 1,
    post_id: params.id,
  };
  return fetch({
    url: `/users/${params.userid}/collections`,
    method: 'put',
    data: data,
  });  
}
// params {id: postid, floor: floor, comment: comment}
export function replyPost(params) {
  const data = {
    floor: floor || 1,
    comment: params.comment,
  };
  return fetch({
    url: `/posts/${params.id}/comments`,
    method: 'post',
    data: data,
  });
}