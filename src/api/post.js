import fetch from 'utils/fetch'
import store from 'vuex-store'
// TODO: add vuex in this file to simplify the params

export function getMockTest () {
  return fetch({
    url: '/test',
    method: 'get'
  })
}

export function getArticles (offset = 0, limit = 20, order = 'desc') { // 获取 首页 文章数据
  return fetch({
    url: `/articles?offset=${offset}&limit=${limit}&order=${order}`,
    method: 'get'
  })
}

export function getMyArticles (params) { // 获取 我的空间 数据
  const offset = params.offset | 0
  const limit = params.limit | 20
  return fetch({
    url: `/users/${params.id}/articles?offset=${offset}&limit=${limit}`,
    method: 'get'
  })
}

export function getPosts (latest = true, offset = 0, limit = 20) {
  return fetch({
    url: `/posts?latest=${latest}&offset=${offset}&limit=${limit}`,
    //  url: `/articles`,
    method: 'get'
  })
}

export function getPostsByGroupId (groupid, offset = 0, limit = 20) {
  return fetch({
    url: `/groups/${groupid}/posts?offset=${offset}&limit=${limit}`,
    method: 'get'
  })
}

export function getPost (id) {
  return fetch({
    url: `/posts/${id}`,
    method: 'get'
  })
}

export function getCommentsByPostId (id, offset = 0, limit = 20) {
  return fetch({
    url: `/posts/${id}/comments?offset=${offset}&limit=${limit}`,
    method: 'get'
  })
}

export function getCollection (offset = 0, limit = 20) {
  return fetch({
    url: `/users/${store.state.user.uid}/collections?offset=${offset}&limit=${limit}`,
    method: 'get'
  })
}

export function searchArticles (keyword, offset, limit) {
  // const data = {
  //   keyword,
  //   offset,
  //   limit
  // }
  return fetch({
    url: `/articles/search?keyword=${keyword}&offset=${offset}&limit=${limit}`,
    method: 'post'
    // params: data
  })
}
