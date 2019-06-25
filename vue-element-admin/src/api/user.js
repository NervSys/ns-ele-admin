import request from '@/utils/request'
import domain from '@/utils/domain.js'

export function login(data) {
  return request({
    url: domain.baseUrl + '?c=user/login-login',
    method: 'post',
    data
  })
}

export function getInfo(token) {
  return request({
    url: domain.baseUrl + '?c=user/login-getUserInfo',
    method: 'get',
    params: { token }
  })
}

export function logout() {
  return request({
    url: domain.baseUrl + '?c=user/login-logout',
    method: 'post'
  })
}
