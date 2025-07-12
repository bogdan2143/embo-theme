const path = require('path');

module.exports = {
  entry: {
    main: './src/js/header-ui.js',
    loadMore: './src/js/load-more.js',
    utils: './src/js/screen-utils.js',
  },
  output: {
    path: path.resolve(__dirname, 'dist/js'),
    filename: '[name].js',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
        },
      },
    ],
  },
};
