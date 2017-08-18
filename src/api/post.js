import fetch from 'utils/fetch';
// TODO: add vuex in this file to simplify the params

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

export function getCollectPost(id, offset=0, limit=20) {
  return new fetch({
    url: `/users/${id}/collections`,
    method: 'get',
  });
}

// params {id: postid, floor: floor}
export function approvePost(params) {
  const data = {
    floor: params.floor || 1,
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
    floor: params.floor || 1,
    post_id: params.id,
  };
  return fetch({
    url: `/users/${params.userid}/collections`,
    method: 'put',
    data: data,
  });  
}
export function deletePost(id) {
  return fetch({
    url: `/posts/${id}`,
    method: 'delete',
  })
}
export function lockPost(id) {
  return fetch({
    url: `/posts/${id}/locks`,
    method: 'put',
  })
}
export function settopPost(id) {
  return fetch({
    url: `/posts/${id}/tops`,
    method: 'put',
  })
}
// params {id: postid, floor: floor, comment: comment}
export function replyPost(postid, params) {
  const data = {
    floor: params.floor || 1,
    comment: params.comment,
  };
  return fetch({
    url: `/posts/${postid}/comments`,
    method: 'post',
    data: data,
  });
}

// publish post
export function publishPost(groupid, params) {
  const data = {
    title: params.title,
    content: params.content,
  };
  return fetch({
    url: `/groups/${groupid}/posts`,
    method: 'post',
    data: data,
  });
}

export function searchPosts(name, offset=0, limit=20) {
  const data = {
    name: name,
    offset: offset,
    limit: limit,
  };
  return fetch({
    url: '/posts',
    method: 'get',
    params: data,
  });
}
