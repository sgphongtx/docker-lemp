'use strict'
const mongoose = require('mongoose')
const BUILDING_KIND = ['Chung cư trung bình', 'Tòa nhà', 'Căn hộ']
const APARTMENT_KIND = ['Chính chủ', 'Cho thuê']

/** @class Building
 * @description Tòa nhà
 */
const Building = mongoose.Schema({
  name: {type: String},
  logo: {type: String, default: '/designer/images/logo_square.png'},
  address: {type: String},
  investor: {type: String}, /*Chủ đầu tư*/
  investorAddress: {type: String},/*địa chỉ cty đầu tư*/
  kind: {type: String, enum: BUILDING_KIND, default: BUILDING_KIND[0]},
  bank: {type: String},
  invoiceNote: {type: String},
  createdBy: {type: mongoose.Schema.Types.ObjectId, ref: 'b_user'},
  company: {type: mongoose.Schema.Types.ObjectId, ref: 'b_company'}
}, {versionKey: false, timestamps: true})

/**@memberOf Building*/
Building.statics.objectId = function(id){
  return mongoose.Types.ObjectId(id)
}

/** @class Block
 * @description Khối của mỗi building
 */
const Block = mongoose.Schema({
  name: {type: String},
  regularWater: {type: String},/*nước sinh hoạt*/
  businessWater: {type: String},/*nước kinh doanh*/
  building: {type: mongoose.Schema.Types.ObjectId, ref: 'b_building'},
  createdBy: {type: mongoose.Schema.Types.ObjectId, ref: 'b_user'},
  company: {type: mongoose.Schema.Types.ObjectId, ref: 'b_company'}
}, {versionKey: false, timestamps: true})

/** @class Floor
 * @description Tầng của mỗi block
 */
const Floor = mongoose.Schema({
  name: {type: String},
  block: {type: mongoose.Schema.Types.ObjectId, ref: 'b_block'},
  createdBy: {type: mongoose.Schema.Types.ObjectId, ref: 'b_user'},
  company: {type: mongoose.Schema.Types.ObjectId, ref: 'b_company'}
}, {versionKey: false, timestamps: true})

/** @class Apartment
 * @description Căn hộ
 */
const Apartment = mongoose.Schema({
  name: {type: String},
  square: {type: Number},
  kind: {type: String, enum: APARTMENT_KIND, default: APARTMENT_KIND[0]},
  notes: [String],
  floor: {type: mongoose.Schema.Types.ObjectId, ref: 'b_floor'},
  createdBy: {type: mongoose.Schema.Types.ObjectId, ref: 'b_user'},
  company: {type: mongoose.Schema.Types.ObjectId, ref: 'b_company'}
}, {versionKey: false, timestamps: true})

const _building = mongoose.model('b_building', Building)
const _block = mongoose.model('b_block', Block)
const _floor = mongoose.model('b_floor', Floor)
const _apartment = mongoose.model('b_apartment', Apartment)

module.exports = {
  Building: _building,
  Block: _block,
  Floor: _floor,
  Apartment: _apartment,
  BUILDING_KIND: BUILDING_KIND,
  APARTMENT_KIND: APARTMENT_KIND
}