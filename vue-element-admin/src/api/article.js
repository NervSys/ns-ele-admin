import request from '@/utils/request'
import domain from '@/utils/domain.js'
export function fetchList(query) {
  return request({
    url: domain.baseUrl + '?c=openness/news-list',
    method: 'get',
    params: query
  })
}

export function fetchArticle(id) {
  return request({
    url: domain.baseUrl + '?c=openness/news-detail',
    method: 'get',
    params: { id }
  })
}

export function fetchStatus(data) {
  return request({
    url: domain.baseUrl + '?c=openness/news-status',
    method: 'post',
    data
  })
}

export function createArticle(data) {
  return request({
    url: domain.baseUrl + '?c=openness/news-create',
    method: 'post',
    data
  })
}

export function updateArticle(data) {
  return request({
    url: domain.baseUrl + '?c=openness/news-update',
    method: 'post',
    data
  })
}
