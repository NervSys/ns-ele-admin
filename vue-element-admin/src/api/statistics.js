import request from '@/utils/request'
import domain from '@/utils/domain.js'

export function getData(data) {
  return request({
    url: domain.baseUrl + '?c=openness/statistics-getData',
    method: 'post',
    data
  })
}



