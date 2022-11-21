'use strict'
const Logger = require('./../../util/logController').Logger
const Company = require('./../../../model/user').Company
const Image = require('./../../util/saveImageController').Image

/** @class CompanyController */
function CompanyController() {
  return {
    /** @memberOf CompanyController
     * @description List all companies
     * @param req
     * @param res
     * @returns {Promise<any>}
     */
    list: (req, res) => {
      if(req.user.role !== 'System') return res.status(400).json({s: 400, msg: `You don't have permission`})
      return Company.find({}).then(companies => {
        return res.json({s: 200, companies: companies})
      })
    },
    /** @memberOf CompanyController
     * @description Create a company
     * @param req
     * @param res
     * @returns {Promise<any>}
     */
    create: (req, res) => {
      if(req.user.role !== 'System') return res.status(400).json({s: 400, msg: `You don't have permission`})
      const company = req.body.company
      company.createdBy = req.user._id
      if(company.logo){
        return Image.save({base64: company.logo}).then(rs => {
          company.logo = rs.clientPath
          return Company.create(company).then(_company=> {
            return res.json({s: 200, company: _company})
          })
        })
      }

      return Company.create(company).then(_company => {
        return res.json({s: 200, company: _company})
      })
    },
    /** @memberOf CompanyController
     * @description Update a company
     * @param req
     * @param res
     * @returns {Promise<any>}
     */
    update: (req, res) => {
      if(req.user.role !== 'System') return res.status(400).json({s: 400, msg: `You don't have permission`})
      let company = {}
      company.name = req.body.name
      if(req.body.logo){
        return Image.save({base64: company.logo}).then(rs => {
          company.logo = rs.clientPath
          Company.update({_id: req.body._id}, {$set: company}).catch(error => Logger.info(`update company error ${error}`));
          return res.json({s: 200, data: {link: rs.clientPath}})
        })
      }
      Company.update({_id: req.body._id}, {$set: company}).catch(error => Logger.info(`update company error ${error}`));
      return res.json({s: 200, data: {link: null}})
    },
    /** @memberOf CompanyController
     * @description Delete a company
     * @param req
     * @param res
     * @returns {Promise<any>}
     */
    delete: (req, res) => {
      if(req.user.role !== 'System') return res.status(400).json({s: 400, msg: `You don't have permission`})
      return Company.deleteOne({_id: Company.objectId(req.query._id)}).then(()=>{
        return res.json({s: 200})
      }).catch(err => {return res.json({s: 400, msg: `${err}`})})
    }
  }
}

module.exports = new CompanyController()