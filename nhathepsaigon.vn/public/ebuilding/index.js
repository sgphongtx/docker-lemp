'use strict'
const Setting = require('./config/setting')
const Logger = require('./route/util/logController').Logger

const express = require('express')
const app = express()
const path = require('path')
const bodyParser = require('body-parser')
const mongoose = require('mongoose')
// const session = require('express-session');
// const RedisStore = require('connect-redis')(session);
const whitelist = ['http://localhost', 'http://localhost:3005']
const corsOptions = {
  origin: function (origin, callback) {
    if (whitelist.indexOf(origin) !== -1) {
      callback(null, true)
    } else {
      callback(null, new Error('Not allowed by CORS'))
    }
  },
  credentials: true,
  allowedHeaders: 'Origin, X-Requested-With, Content-Type, Accept, x-xsrf-token'
}

const cors = require('cors')

app.use(cors(corsOptions))
app.use(bodyParser.json({limit: '10mb'}))
app.use(bodyParser.urlencoded({extended: false}))
app.enable('trust proxy')
app.disable('x-powered-by')

const home = require('./route/home')
app.use('/v1', home)
app.use('/', (req, res) => {
  return res.send('Router not found')
})
app.listen(Setting.IS_PRODUCTION ? Setting.PRODUCTION.PORT : Setting.LOCAL.PORT, function () {
  Logger.info(`EBuilding app listening on port ${Setting.IS_PRODUCTION ? Setting.PRODUCTION.PORT : Setting.LOCAL.PORT}`)
})

mongoose.Promise = global.Promise
mongoose.set('useCreateIndex', true);
mongoose.connect(Setting.IS_PRODUCTION ? Setting.PRODUCTION.MONGODB : Setting.LOCAL.MONGODB, {
  useNewUrlParser: true,
  poolSize: 20
}, function (error) {
  if (error === null) {
    Logger.info('Connected MongoDB for HTTP listening')
  } else Logger.info('Connection error user: ', error)
})

