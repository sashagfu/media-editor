const { mix } = require('laravel-mix');
const path = require('path');

function resolve(dir) {
  return path.join(__dirname, dir);
}

mix
  // Admin
  .stylus('resources/assets/sass/admin.styl', 'public/css/admin.css')

  .stylus('resources/assets/sass/maintenance.styl', 'public/css/maintenance.css')

  // Admin
  .js([
    'node_modules/toastr/toastr.js',
    'resources/assets/js/admin.js',
  ], 'public/js/admin.js')

  // Front
  .stylus('resources/assets/sass/front.styl', 'public/css/front.css')

  // Front
  .js([
    'resources/assets/js/index.js',
  ], 'public/js/front.js')

  // Maintenance
  .js([
    'resources/assets/js/maintenance.js',
  ], 'public/js/maintenance.js')

  .sourceMaps()
  .webpackConfig({
    resolve: {
      alias: {
        // Common, Pages, Components, Charts, Lib, Router, Store, StoreModules
        Pages: resolve('resources/assets/js/vue/components'),
        Editor: resolve('resources/assets/js/media-editor'),
        Models: resolve('resources/assets/js/models'),
        Helpers: resolve('resources/assets/js/helpers'),
        Scss: resolve('resources/assets/sass'),
        Gql: resolve('resources/assets/graphQL'),
      },
    },
    module: {
      rules: [
        {
          test: /\.(graphql|gql)$/,
          exclude: /node_modules/,
          use: ['graphql-tag/loader'],
        },
      ],
    },
  });
// Versioning files
if (mix.config.inProduction) {
  mix.version();
}

