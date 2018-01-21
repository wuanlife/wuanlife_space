import fetch from 'utils/fetch';
import store from 'vuex-store';
// TODO: add vuex in this file to simplify the params

export function getMockTest() {
  return fetch({
    url: '/test',
    method: 'get'
  })
}

export function getArticles(latest = true, offset = 0, limit = 20) { // 获取 首页 文章数据
  return fetch({
    url: `/articles?latest=${latest}&offset=${offset}&limit=${limit}`,
    method: 'get'
  });
}


export function getMyArticles(params) { // 获取 我的空间 数据
  const offset = params.offset | 0
  const limit = params.limit | 20
  return fetch({
    url: `/user/${params.id}/articles?offset=${offset}&limit=${limit}`,
    method: 'get'
  })
}

export function getPosts(latest = true, offset = 0, limit = 20) {
  return fetch({
    url: `/posts?latest=${latest}&offset=${offset}&limit=${limit}`,
//  url: `/articles`,
    method: 'get'
  });
}

export function getPostsByGroupId(groupid, offset = 0, limit = 20) {
  return fetch({
    url: `/groups/${groupid}/posts?offset=${offset}&limit=${limit}`,
    method: 'get'
  });
}

export function getPost(id) {
  return fetch({
    url: `/posts/${id}`,
    method: 'get'
  });
}

export function getCommentsByPostId(id, offset = 0, limit = 20) {
  return fetch({
    url: `/posts/${id}/comments?offset=${offset}&limit=${limit}`,
    method: 'get'
  });
}

export function getCollection(id, offset = 0, limit = 20) {
  return new fetch({
    url: `/users/${id}/collections?offset=${offset}&limit=${limit}`,
    method: 'get'
  });
}

// 点赞文章
export function approveArticle(id) {
  return fetch({
    url: `/articles/${id}/approval`,
    method: 'post',
    data: {}
  });
}

// 收藏文章
export function collectArticle(id) {
  return fetch({
    url: `/users/${store.user.id}/collections`,
    method: 'put',
    data: {
      article_id: id
    }
  });
}

// 修改文章
export function putArticle(params) {
  const data = {
    title: params.title,
    content: params.content
  };
  return fetch({
    url: `/articles/${id}`,
    method: 'put',
    data
  })
}
export function deleteArticle(id) {
  return fetch({
    url: `/articles/${id}`,
    method: 'delete'
  })
}
export function lockArticle(id) {
  return fetch({
    url: `/articles/${id}/lock`,
    method: 'post'
  })
}
export function settopArticle(id) {
  return fetch({
    url: `/articles/${id}/tops`,
    method: 'post'
  })
}



// publish post
export function postArticles(params) {
  const data = {
    ...params
  };
  return fetch({
    url: '/articles',
    method: 'post',
    data
  });
}

export function searchArticles(keyword, offset, limit) {
  const data = {
    keyword,
    offset,
    limit
  };
  return fetch({
    url: `/articles/search?keyword=${keyword}&offset=${offset}&limit=${limit}`,
    method: 'get',
    params: data
  });
}

export function searchUsers(keyword, offset, limit) {
  const data = {
    keyword,
    offset,
    limit
  };
  return fetch({
    method: 'get',
    url: `/users/search?keyword=${keyword}&offset=${offset}&limit=${limit}`,
    data
  });
}
