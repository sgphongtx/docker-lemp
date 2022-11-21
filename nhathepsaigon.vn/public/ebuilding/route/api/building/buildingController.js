'use strict'
const Logger = require('./../../util/logController').Logger
const Building = require('./../../../model/building').Building
const Image = require('./../../util/saveImageController').Image

/** @class BuildingController */
function BuildingController() {
  return {
    /** @memberOf BuildingController
     * @description List all building
     * @param req
     * @param res
     * @returns {Promise<any>}
     */
    list: (req, res) => {
      return Building.aggregate([
        {$match: {company: Building.objectId(req.user.company)}},
        {
          $lookup: {
            from: "b_companies",
            localField: "company",    // field in the companies collection
            foreignField: "_id",  // field in the b_buildings collection
            as: "cmp"
          }
        }
      ]).then(buildings => {
        return res.json({s: 200, buildings: buildings})
      })
    },
    /** @memberOf BuildingController
     * @description Create a building
     * @param req
     * @param res
     * @returns {Promise<any>}
     */
    create: (req, res) => {
      const building = req.body.building
      building.company = req.user.company
      building.createdBy = req.user._id
      if(building.logo){
        return Image.save({base64: building.avatar}).then(rs => {
          building.logo = rs.clientPath
          return Building.create(building).then(_building => {
            return res.json({s: 200, building: _building})
          })
        })
      }

      return Building.create(building).then(_building => {
        return res.json({s: 200, building: _building})
      })
    },
    /** @memberOf BuildingController
     * @description Update a building
     * @param req
     * @param res
     * @returns {Promise<any>}
     */
    update: (req, res) => {
      let building = {}
      building.name = req.body.name
      building.address = req.body.address
      building.investor = req.body.investor
      building.investorAddress = req.body.investorAddress
      building.kind = req.body.kind
      building.bank = req.body.bank
      building.invoiceNote = req.body.invoiceNote
      if(req.body.logo){
        return Image.save({base64: req.body.logo}).then(rs => {
          building.logo = rs.clientPath
          Building.update({_id: req.body._id, company: req.user.company}, {$set: building}).catch(error => Logger.info(`update building error ${error}`));
          return res.json({s: 200, data: {link: rs.clientPath}})
        })
      }
      Building.update({_id: req.body._id, company: req.user.company}, {$set: building}).catch(error => Logger.info(`update building error ${error}`));
      return res.json({s: 200, data: {link: null}})
    },
    /** @memberOf BuildingController
     * @description Delete a building
     * @param req
     * @param res
     * @returns {Promise<any>}
     */
    delete: (req, res) => {
      return Building.deleteOne({_id: Building.objectId(req.query._id), company: req.user.company}).then(()=>{
        return res.json({s: 200})
      }).catch(err => {return res.json({s: 400, msg: `${err}`})})
    }
  }
}

module.exports = new BuildingController()