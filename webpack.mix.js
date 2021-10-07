const mix = require('laravel-mix');

mix.disableNotifications();

mix
    .postCss('resources/css/front/tailwind.css', 'public/css/front/tailwind.css', [
        require("tailwindcss")
    ])
    .sass('resources/sass/front/main.scss', 'public/css/front/main.css')
    .js('resources/js/front/main.js', 'public/js/front/main.js')
    .version()
;
