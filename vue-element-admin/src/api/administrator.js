import request from '@/utils/request'
import domain from '@/utils/domain.js'
export function getRoles() {
  return request({
    url: domain.baseUrl + '?c=user/administrators-roles',
    method: 'get'
  })
}

export function getAdministrators() {
  return request({
    url: domain.baseUrl + '?c=user/administrators-administrators',
    method: 'get'
  })
}

export function addAdministrator(data) {
  return request({
    url: domain.baseUrl + '?c=user/administrators-add',
    method: 'post',
    data
  })
}

export function updateAdministrator(data) {
  return request({
    url: domain.baseUrl + '?c=user/administrators-update',
    method: 'post',
    data
  })
}

export function deleteAdministrator(data) {
  return request({
    url: domain.baseUrl + '?c=user/administrators-delete',
    method: 'post',
    data
  })
}
