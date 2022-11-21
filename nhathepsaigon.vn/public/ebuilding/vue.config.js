// vue.config.js
module.exports = {
  //"assetsDir": "static"
  devServer: {
    proxy: 'http://localhost:3005/',
    disableHostCheck: true,
    public: 'localhost'
  }
};
