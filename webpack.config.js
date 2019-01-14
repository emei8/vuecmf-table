var path = require('path')
var webpack = require('webpack')
const VueLoaderPlugin = require('vue-loader/lib/plugin')

module.exports = {
  module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          'vue-style-loader',
          'css-loader'
        ],
      },{
        test: /\.vue$/,
        use: [
            {
                loader: 'vue-loader',
                options: {

                }
            },
            {
                loader: 'iview-loader',
                options: {
                    prefix: true
                }
            }
        ]
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/
      },
      {
        test: /\.(png|jpg|gif)$/,
        loader: 'file-loader',
        options: {
            name: 'images/[name].[ext]?[hash]'
        }
      },
      //增加字体样式解析
      {
          test: /\.(eot|svg|ttf|woff|woff2)$/,
          loader: 'file-loader',
          options: {
              name: 'fonts/[name].[ext]?[hash]'
          }
      }
    ]
  },
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue.esm.js'
    },
    extensions: ['*', '.js', '.vue', '.json']
  },
  devServer: {
    historyApiFallback: true,
    noInfo: true,
    overlay: true
  },
  performance: {
    hints: false
  },
  devtool: '#eval-source-map',
  plugins:[
      new VueLoaderPlugin()
  ]
}


if (process.env.NODE_ENV === 'production') {
    //发布插件使用
    module.exports.mode = 'production'
    module.exports.entry = ['babel-polyfill','./src/lib/vuecmf-table/index.js']  //打包操作的入口文件
    module.exports.output = {
        path: path.resolve(__dirname, './dist'),
        publicPath: './',  //打包后的文件存放目录
        filename: 'vuecmf.table.js', //打包生成的目标文件
        library: 'VuecmfTable', // library指定的就是你使用require时的模块名，这里便是require("VuecmfTable")
        libraryTarget: 'umd', //libraryTarget会生成不同umd的代码,可以只是commonjs标准的，也可以是指amd标准的，也可以只是通过script标签引入的。
        umdNamedDefine: true //会对 UMD 的构建过程中的 AMD 模块进行命名。否则就使用匿名的 define。
    }
    //添加 externals来告诉webpack我们这些第三方库不需要打包, key = 第三方库名， value = 项目中定义的别名
    module.exports.externals = {
        'vue': 'Vue',
        'iview': 'iView',
        'xlsx': 'XLSX'
    }


  module.exports.devtool = '#source-map'
  // http://vue-loader.vuejs.org/en/workflow/production.html
  module.exports.plugins = (module.exports.plugins || []).concat([
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: '"production"'
      }
    }),
    new webpack.optimize.UglifyJsPlugin({
      sourceMap: false,
      output: {
          comments: false,
          beautify: false,
      },
      compress: {
        warnings: false
      }
    }),
    new webpack.LoaderOptionsPlugin({
      minimize: true
    })
  ])

}else{
    //测试插件使用
    module.exports.mode = 'development'
    module.exports.entry = ['babel-polyfill','./src/main.js']
    module.exports.output = {
        path: path.resolve(__dirname, './dist'),
        publicPath: '/dist/',
        filename: 'build.js'
    }
}
