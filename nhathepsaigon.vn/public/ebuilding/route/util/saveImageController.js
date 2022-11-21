'use strict';
const fs = require('fs');
const path = require('path');
const Setting = require('./../../config/setting');
/**@class Image
 * @description */
const Image = function(){
  const self = {
    STORE_PATH: Setting.IS_PRODUCTION ? Setting.PRODUCTION.STORE_PATH : Setting.LOCAL.STORE_PATH,
    PUBLIC_PATH: Setting.IS_PRODUCTION ? Setting.PRODUCTION.PUBLIC_PATH : Setting.LOCAL.PUBLIC_PATH,
    save: function(folderName, fileName, imgBase64){
      return new Promise((resolve, reject) => {
        fs.stat(self.STORE_PATH + folderName, function(err){ //check folder is existed or not
          if(err){
            fs.mkdir(self.STORE_PATH + folderName, function(){
              let serverPath = self.STORE_PATH + folderName + path.sep + fileName;
              fs.writeFile(serverPath, imgBase64, 'base64', function(err) {
                if(err) return reject(err);
                return resolve({clientPath: `${self.PUBLIC_PATH}${folderName}/${fileName}`, serverPath: serverPath, size: imgBase64.length});
              });
            })
          } else {
            let serverPath = self.STORE_PATH + folderName + path.sep + fileName;
            fs.writeFile(serverPath, imgBase64, 'base64', function(err) {
              if(err) return reject(err);
              return resolve({clientPath: `${self.PUBLIC_PATH}${folderName}/${fileName}`, serverPath: serverPath, size: imgBase64.length});
            });
          }
        });
      })
    }
  };
  return {
    /**@memberOf Image
     * @return Promise with web path*/
    save: function({folderName = null, fileName = null, base64 = null}={}){
      //if(base64 === null) return Promise.reject(new Error(`Don't have image`));
      if(base64 === null) return Promise.resolve(null);
      base64 = base64.replace(/^data:image\/png;base64,/, '');
      base64 = base64.replace(/^data:image\/jpeg;base64,/, '');
      if(folderName === null){
        const d = new Date();
        folderName = d.getFullYear() + (('0' + (d.getMonth()+1)).slice(-2)) + `` + ('0' + d.getDate()).slice(-2);
        if(fileName === null)
          fileName = d.getTime() + `.png`;
      } else {
        if(fileName === null){
          const d = new Date();
          fileName = d.getTime() + `.png`;
        }
      }
      return self.save(folderName, fileName, base64);
    }
  }
};

module.exports = {Image: new Image()};