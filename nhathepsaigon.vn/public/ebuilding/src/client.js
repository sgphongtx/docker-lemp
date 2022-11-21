import axios from 'axios'
import router from './router'
//import store from './store'

const axiosInstance = axios.create({
  baseURL: 'http://localhost:3005/v1',
  withCredentials: true
})
axiosInstance.interceptors.response.use(function (response) {
  return response
}, function (error) {
  switch (error.response.status) {
    case 401:
      let type = error.response.data.type
      router.replace({name: 'login', query: {type: type}})
      break
    case 403:
      router.replace({name: 'Dashboard', query: {type: error.response.data.type}})
      break
    default:
      console.log(`axios error: ${error}`)
  }
  return Promise.reject(error)
})
export default axiosInstance
