import request from '@/utils/request'
import domain from '@/utils/domain.js'
export function searchUser(name) {
  return request({
    url: domain.baseUrl + '?c=openness/news-user',
    method: 'get',
    params: { name }
  })
}

export function transactionList(query) {
  return request({
    url: '/transaction/list',
    method: 'get',
    params: query
  })
}
