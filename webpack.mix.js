const mix = require('laravel-mix');
const webpack = require('webpack')
const tailwindcss = require("tailwindcss");
const path = require('path'); mix.alias();

mix.disableNotifications();

mix
    .sass('resources/sass/start/main.scss', 'public/css/start/main.css')
    .js('resources/js/start/app.js', 'public/js/start/app.js')
    .sourceMaps()
    .version()
;

mix
    .sass('resources/sass/codebase/main.scss', 'public/css/codebase/codebase.css')
    .sass('resources/sass/codebase/codebase/themes/corporate.scss', 'public/css/codebase/themes/')
    .sass('resources/sass/codebase/codebase/themes/earth.scss', 'public/css/codebase/themes/')
    .sass('resources/sass/codebase/codebase/themes/elegance.scss', 'public/css/codebase/themes/')
    .sass('resources/sass/codebase/codebase/themes/flat.scss', 'public/css/codebase/themes/')
    .sass('resources/sass/codebase/codebase/themes/pulse.scss', 'public/css/codebase/themes/')
    .js('resources/js/codebase/app.js', 'public/js/codebase/codebase.app.js')
    .copy('node_modules/simplebar/dist/simplebar.esm.js.map', 'public/js/codebase')
    .version()
;

mix
    .webpackConfig({
        plugins: [
            new webpack.DefinePlugin({
                __VUE_OPTIONS_API__: true,
                __VUE_PROD_DEVTOOLS__: false
            })
        ]
    })
    .alias({
        ziggy: path.resolve('vendor/tightenco/ziggy/dist/vue')
    })
    .js('resources/js/apps/profile.js','public/js/apps/profile.js')
    .js('resources/js/apps/role.js','public/js/apps/role.js')
    .js('resources/js/apps/user.js','public/js/apps/user.js')
    .js('resources/js/apps/inbox.js','public/js/apps/inbox.js')
    .vue()
    .version()
;
