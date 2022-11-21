'use strict';
const mongoose = require('mongoose');
const ROLE = ['Member', 'Admin', 'System'];

/** @class Company
 * @description
 */
const Company = mongoose.Schema({
  name: {type: String, index: true, unique: true},
  logo: {type: String, default: '/designer/images/logo_square.png'},
  createdBy: {type: mongoose.Schema.Types.ObjectId, ref: 'b_user'},
}, {versionKey: false, timestamps: true})
const _company = mongoose.model('b_company', Company);

/** @class User
 * @description
 */
const User = mongoose.Schema({
  email: {type: String, index: true},
  password: {type: String},
  avatar: {type: String, default: '/designer/images/logo_square.png'},
  company: {type: mongoose.Schema.Types.ObjectId, ref: 'b_company'},
  role: {type: String, enum: ROLE, 'default': ROLE[0]},
  fullName: {type: String},
  phone: {type: String}
}, {versionKey: false, timestamps: true});

/**@memberOf User*/
User.statics.objectId = function(id){
  return mongoose.Types.ObjectId(id)
}

/**@memberOf Company*/
Company.statics.objectId = function(id){
  return mongoose.Types.ObjectId(id)
}
const _user = mongoose.model('b_user', User);
module.exports = {
  User: _user,
  Company: _company
};
