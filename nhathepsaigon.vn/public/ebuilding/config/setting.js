let SETTING = {
  IS_PRODUCTION: false,
  PRODUCTION: {
    PORT: 3005,
    MONGODB: 'mongodb://127.0.0.1:27017/ebuilding?authSource=ebuilding',
    PUBLIC_PATH: '/public/avatar/',
    STORE_PATH: '/public/avatar/',
    redis: {
      port: 6379,
      host: 'localhost',
      db: 2,
      pass: ''
    }
  },
  LOCAL: {
    PORT: 3005,
    MONGODB: 'mongodb://127.0.0.1:27017/ebuilding?authSource=ebuilding',
    PUBLIC_PATH: '/public/avatar/',
    STORE_PATH: 'D:\\Projects\\ebuilding\\public\\avatar',
    redis: {
      port: 6379,
      host: 'localhost',
      db: 2,
      pass: ''
    }
  },
  MAX_SIZE: 1000000000,
  PAGE_SIZE: 50,
  USER_SYSTEM: null
}
module.exports = SETTING