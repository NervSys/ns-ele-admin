import request from '@/utils/request'
import domain from '@/utils/domain.js'
export function fetchList(data) {
  return request({
    url: domain.baseUrl + '?c=openness/recipe-list',
    method: 'post',
    data
  })
}

export function fetchStatus(data) {
  return request({
    url: domain.baseUrl + '?c=openness/recipe-status',
    method: 'post',
    data
  })
}

export function createArticle(data) {
  return request({
    url: domain.baseUrl + '?c=openness/recipe-create',
    method: 'post',
    data
  })
}

export function updateArticle(data) {
  return request({
    url: domain.baseUrl + '?c=openness/recipe-update',
    method: 'post',
    data
  })
}
