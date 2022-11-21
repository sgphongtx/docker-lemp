'use strict';
const Setting = require('./../../config/setting');
const mongoose = require('mongoose');
mongoose.Promise = global.Promise;
mongoose.connect(Setting.IS_PRODUCTION ? Setting.PRODUCTION.MONGODB : Setting.LOCAL.MONGODB, {
  poolSize: 20,
  useNewUrlParser: true
}, () => {
  createSystemUser().then(()=>{console.log('Done')}).catch(err => console.log(err))
});

function createSystemUser() {
  const {User, Company} = require('./../../model/user');
  return Company.create({name: 'AnPhuViet'}).then(company => {
    return User.create({
      email: 'anh.nguyen@anphuviet.com',
      password: '123456',
      company: company._id,
      role: 'Admin'
    })
  })
}


