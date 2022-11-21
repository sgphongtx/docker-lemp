const Logger = require('./logController').Logger
const redis = require('redis')
const Setting = require('./../../config/setting')
const Config = Setting.IS_PRODUCTION ? Setting.PRODUCTION.redis : Setting.LOCAL.redis
const client = redis.createClient({host: Config.host, port: Config.port})
client.on('error', function (err) {
  Logger.info(`Error on connect Redis ${err}`)
})
module.exports = {
  storeTokenInRedis: function (key, value) {
    return new Promise((resolve, reject) => {
      key = 'p_' + key
      if (!value) {
        client.del(key)
        return resolve()
      } else {
        client.set(key, value, function (error) {
          if (error) return reject(error)
          return resolve()
        })
      }
    })
  },
  receiveTokenInRedis: function (key) {
    return new Promise((resolve, reject) => {
      client.get('p_' + key, (error, reply) => {
        if (error) return reject(error)
        return resolve(reply)
      })
    })
  },
  verifyTokenInRedis: function (playerId, token, callback) {
    /* client.get("p_" + playerId, function(err, reply){
     callback(token === reply)
     }); */
    client.eval("return redis.call('get', ARGV[1]) == ARGV[2]", 0, 'p_' + playerId, token, (error, response) => {
      if (error) Logger.info(error)
      else callback(response === 1)
    })
  }
}
