const Encore = require('@symfony/webpack-encore');
const HtmlWebpackPlugin = require('html-webpack-plugin');
// eslint-disable-next-line import/no-extraneous-dependencies
const webpack = require('webpack');

Encore
  .setOutputPath('public/')
  .setPublicPath('/')
  .cleanupOutputBeforeBuild()
  .addEntry('app', './src/app.js')
  .enablePreactPreset()
  .enableSassLoader()
  .enableEslintLoader((options) => {
    // eslint-disable-next-line no-param-reassign
    options.extends = 'airbnb';
    // options.emitWarning = false;
  })
  .enableSingleRuntimeChunk()
  .addPlugin(new HtmlWebpackPlugin({ template: 'src/index.ejs', alwaysWriteToDisk: true }))
  .addPlugin(new webpack.DefinePlugin({
    ENV_API_ENDPOINT: JSON.stringify(process.env.API_ENDPOINT),
  }));

module.exports = Encore.getWebpackConfig();
