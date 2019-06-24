import request from '@/utils/request'
import domain from '@/utils/domain.js'
export function getRoutes() {
  return request({
    url: domain.baseUrl + '?c=user/roles-routes',
    method: 'get'
  })
}

export function getRoles() {
  return request({
    url: domain.baseUrl + '?c=user/roles-roles',
    method: 'get'
  })
}

export function addRole(data) {
  return request({
    url: domain.baseUrl + '?c=user/roles-add',
    method: 'post',
    data
  })
}

export function updateRole(data) {
  return request({
    url: domain.baseUrl + '?c=user/roles-update',
    method: 'post',
    data
  })
}

export function deleteRole(data) {
  return request({
    url: domain.baseUrl + '?c=user/roles-delete',
    method: 'post',
    data
  })
}
