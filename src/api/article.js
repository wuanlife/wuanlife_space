import fetch from 'utils/fetch';

export function getArticle(id) {
  return fetch({
    url: `/articles/${id}`,
    method: 'get'
  })
}