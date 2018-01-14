import qs from 'qs'
import fetch from 'utils/fetch';

export function getRepliesById(articleid, params) {
  return fetch({
    url: `/articles/${articleid}/comments?${qs.stringify(params)}`,
    method: 'get'
  })
}

export function deleteReply(postid, floor) {
  return fetch({
    url: `/posts/${postid}/comments/${floor}`,
    method: 'delete'
  })
}

// params {id: postid, floor: floor, comment: comment}
export function postReply(articleid, params) {
  return fetch({
    url: `/articles/${articleid}/comments`,
    method: 'post',
    data: params
  });
}