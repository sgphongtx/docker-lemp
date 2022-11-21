'use strict'
const express = require('express')
const router = express.Router({})
const LoginController = require('./api/login/loginController')
const CompanyController= require('./api/company/companyController')
const BuildingController= require('./api/building/buildingController')

router.post('/login', LoginController.doLogin)
router.use(LoginController.verifyLogin)
router.get('/logout', LoginController.doLogout)
router.post('/choose-building', LoginController.chooseBuilding)
router.route('/building').get(BuildingController.list).post(BuildingController.create).put(BuildingController.update).delete(BuildingController.delete)
router.use(LoginController.verifyChosenBuilding)
router.route('/company').get(CompanyController.list).post(CompanyController.create).put(CompanyController.update).delete(CompanyController.delete)
module.exports = router