import fetch from 'utils/fetch'
import store from 'vuex-store'

export function getArticle (id) {
  return fetch({
    url: `/api/articles/${id}`,
    method: 'get'
  })
}

// 新增文章
export function postArticles (params) {
  const data = {
    ...params
  }
  return fetch({
    url: '/api/articles',
    method: 'post',
    data
  })
}

// 修改文章
export function putArticle (id, params) {
  const data = {
    ...params
  }
  return fetch({
    url: `/api/articles/${id}`,
    method: 'put',
    data
  })
}
export function deleteArticle (id) {
  return fetch({
    url: `/api/articles/${id}`,
    method: 'delete'
  })
}

// 点赞文章
export function approveArticle (id) {
  return fetch({
    url: `/api/articles/${id}/approval`,
    method: 'post',
    data: {}
  })
}
export function unapproveArticle (id) {
  return fetch({
    url: `/api/articles/${id}/approval`,
    method: 'delete',
    data: {}
  })
}

// 收藏文章
export function collectArticle (id) {
  return fetch({
    url: `/api/users/${store.state.user.uid}/collections`,
    method: 'put',
    data: {
      article_id: id
    }
  })
}
export function uncollectArticle (id) {
  return fetch({
    url: `/api/users/${store.state.user.uid}/collections`,
    method: 'delete',
    data: {
      article_id: id
    }
  })
}

// 锁定文章
export function lockArticle (id) {
  return fetch({
    url: `/api/articles/${id}/lock`,
    method: 'post'
  })
}
export function unlockArticle (id) {
  return fetch({
    url: `/api/articles/${id}/lock`,
    method: 'delete'
  })
}
