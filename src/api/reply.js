import qs from 'qs'
import fetch from 'utils/fetch'

export function getRepliesByArticleId (articleId, params) {
  return fetch({
    url: `/articles/${articleId}/comments?${qs.stringify(params)}`,
    method: 'get'
  })
}

export function deleteReply (articleId, floor) {
  return fetch({
    url: `/articles/${articleId}/comments/${floor}`,
    method: 'delete'
  })
}

// params {id: postid, floor: floor, comment: comment}
export function postReply (articleId, params) {
  return fetch({
    url: `/articles/${articleId}/comments`,
    method: 'post',
    data: params
  })
}
