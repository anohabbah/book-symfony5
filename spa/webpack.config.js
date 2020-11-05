const Encore = require('@symfony/webpack-encore');
const HtmlWebpackPlugin = require('html-webpack-plugin');

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
  .addPlugin(new HtmlWebpackPlugin({ template: 'src/index.ejs', alwaysWriteToDisk: true }));

module.exports = Encore.getWebpackConfig();
