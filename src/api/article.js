import fetch from 'utils/fetch';
import store from 'vuex-store';

export function getArticle(id) {
  return fetch({
    url: `/articles/${id}`,
    method: 'get'
  })
}

// 新增文章
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

// 修改文章
export function putArticle(id, params) {
  const data = {
    ...params
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
    url: `/users/${store.state.user.id}/collections`,
    method: 'put',
    data: {
      article_id: id
    }
  });
}

// 锁定文章
export function lockArticle(id) {
  return fetch({
    url: `/articles/${id}/lock`,
    method: 'post'
  })
}

// 置顶文章
export function settopArticle(id) {
  return fetch({
    url: `/articles/${id}/tops`,
    method: 'post'
  })
}