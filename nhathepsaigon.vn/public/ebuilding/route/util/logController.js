'use strict';
const winston = require('winston');
const format = winston.format;
const myFormat = format.printf(info => {
  const data = {timestamp: info.timestamp, message: info.message};
  return `${JSON.stringify(data)}`;
});
winston.loggers.add('default', {
  transports: [
    new winston.transports.File({
      maxsize: 10000000, /*10MB*/
      filename: __dirname + '/../../logs/log.log',
      format: format.combine(
        format.timestamp(),
        myFormat
      )
    }),
    new winston.transports.Console({
      format: format.combine(
        format.timestamp(),
        myFormat
      )
    })
  ]
});
winston.loggers.add('login', {
  transports: [
    new winston.transports.File({
      maxsize: 10000000, /*10MB*/
      filename: __dirname + '/../../logs/login.log',
      format: format.combine(
        format.timestamp(),
        myFormat
      )
    }),
    new winston.transports.Console({
      format: format.combine(
        format.timestamp(),
        myFormat
      )
    })
  ]
});

module.exports = {
  Logger: winston.loggers.get('default'),
  Login: winston.loggers.get('login')
};