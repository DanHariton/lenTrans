let mix = require('laravel-mix');

mix
    .disableNotifications()
    .options({processCssUrls: false})
    .setPublicPath('public')
    .js('templates/_js/app.js', 'public')
    .stylus('templates/_css/app.styl', 'public')
    .version()
;