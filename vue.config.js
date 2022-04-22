const { defineConfig } = require('@vue/cli-service')

module.exports = defineConfig({
  transpileDependencies: true,
  devServer:{
       host:'localhost',
       port:8080,
       open:true,
       proxy:{
         '/api':{
           target:'http://localhost/',
           changeOrigin:true,
           pathRewrite:{
             '^/api':''
           }
         }
       }
  },
  chainWebpack: config => {
    config
        .plugin('html')
        .tap(args => {
            args[0].title = "四元素部落格";
            return args;
        })
  }
})