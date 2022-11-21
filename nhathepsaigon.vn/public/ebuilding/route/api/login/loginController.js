'use strict'
const jwt = require('jsonwebtoken')
const Logger = require('./../../util/logController').Login
const redisController = require('../../util/redisController')
const User = require('./../../../model/user').User
const Building = require('./../../../model/building').Building


/** @class LoginController */
function LoginController() {
  return {
    /** @memberOf LoginController */
    doLogin: (req, res) => {
      let user = req.body.user
      if(!user || !user.email || !user.password) return res.json({s: 400, msg: 'Please enter email and password'})

      return User.findOne({email: user.email}).then(_user => {
        if(!_user) return res.json({s: 400, msg: `User does not exist`})
        if(_user.password !== user.password) return res.json({s: 400, msg: 'Incorrect information'})
        let data = {
          _id: _user._id,
          email: _user.email,
          company: _user.company,
          role: _user.role
        }
        const token = jwt.sign({user: data}, `Break|Pause|Run`)
        const now = Date.now()
        res.cookie('token', token, {
          httpOnly: true,
          sameSite: false,
          maxAge: 600000 /* 1000 * 60 * 10: 10 minutes */
        })
        redisController.storeTokenInRedis(user.email, `${token}|${now}`).catch(error => Logger.info(`doLogin store token error ${error.message}`))
        data.avatar = _user.avatar;
        data.fullName = _user.fullName;
        return res.json({
          s: 200,
          data: {
            token: token,
            user: data
          }
        })
      })
    },
    /** @memberOf LoginController */
    refreshToken: (req, res) => {
      return res.json({
        s: 200,
        msg: 'Success'
      })
    },
    /** @memberOf LoginController
     * @description Chọn tòa nhà để làm việc, lưu tòa nhà vào trong cookie để các request sau có thể truy xuất được */
    chooseBuilding: (req, res) => {
      let buildingId = User.objectId(req.body.buildingId);
      if(!buildingId) return res.json({s: 400, msg: 'Please choose a building'})
      return Building.findOne({_id: buildingId, company: req.user.company}).then(building => {
        if(!building) return res.json({s: 400, msg: 'Please choose correct building'})
        const user = req.user
        user.building = buildingId
        const token = jwt.sign({user: user}, `Break|Pause|Run`)
        const now = Date.now()
        res.cookie('token', token, {
          httpOnly: true,
          sameSite: false,
          vary: 'User-Agent',
          maxAge: 600000 /* 1000 * 60 * 10: 10 minutes */
        })
        redisController.storeTokenInRedis(user.email, `${token}|${now}`).catch(error => Logger.info(`chooseBuilding store token error ${error.message}`))
        return res.json({s: 200, building: building})
      })
    },
    /** @memberOf LoginController
     * @description Middleware */
    verifyLogin: (req, res, next) => {
      res.setHeader('Access-Control-Allow-Credentials', true)
      res.setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept')
      if (req.url === `/login` || req.url === 'logout' || req.url === '/refresh-token') return next()

      if (req.headers.cookie) { // find cookie
        for (let i = 0, cookies = req.headers.cookie.split(`;`), ii = cookies.length; i < ii; ++i) {
          let cookie = cookies[i].trim().split(`=`)
          if (cookie[0] === `token`) {
            try {
              const data = jwt.verify(cookie[1], `Break|Pause|Run`)
              // this is good token, but need to check multi login
              return redisController.receiveTokenInRedis(data.user.email).then(token => {
                if (token) {
                  const value = token.split('|')
                  if (value[0] === cookie[1]) {
                    let now = Date.now()
                    if (now - parseInt(value[1]) <= 600000) {
                      redisController.storeTokenInRedis(data.user.email, `${value[0]}|${now}`).catch(error => Logger.info(`verifyLogin store token error ${error.track}`))
                      res.cookie('token', cookie[1], {maxAge: 600000, sameSite: false, vary: 'User-Agent'})
                      req.user = data.user // assign user data for all request
                      return next()
                    }
                  }
                }
                res.clearCookie('token')
                return res.status(401).json({s: 400, msg: `Another person is logging-in into your account`, type: 1})
              })
            } catch (e) {
              Logger.info(`${req.ip} token incorrect ${cookie[1]}`)
              res.clearCookie('token')
              break
            }
          }
        }
      }
      return res.status(401).json({s: 400, msg: `Please login to use app`, type: 2})
    },
    /** @memberOf LoginController
     */
    verifyChosenBuilding: (req, res, next) => {
      if(req.user && req.user.building) return next()
      return res.status(403).json({s: 400, msg: `Please choose a building to working on`})
    },
    /** @memberOf LoginController
     */
    doLogout: (req, res) => {
      res.clearCookie('token')
      redisController.storeTokenInRedis(req.user.email).catch(error => Logger.info(`doLogout clear token error ${error.track}`))
      res.send('Bye bye')
    }
  }
}

module.exports = new LoginController()
