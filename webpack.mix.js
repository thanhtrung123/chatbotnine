let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .js('resources/assets/js/admin.js', 'public/js')
    .sass('resources/assets/sass/app.scss', 'public/css').options({
    processCssUrls: false
});


mix
    .setPublicPath('./public')
    .js('resources/assets/js/user.js', 'js')
    .js('resources/assets/js/user_bot.js', 'js')
    .js('resources/assets/js/user_enquete.js', 'js')
    .js('resources/assets/js/ie9.js', 'js')
    .js('resources/assets/js/chart.js', 'js')
    .js('resources/assets/js/drawflow.js', 'js')
    .js('resources/assets/js/drawflow-custom.js', 'js')
    .js('resources/assets/js/voiceRecognition.js', 'js')
    .js('resources/assets/js/select2_replace.js', 'js')
    .js('resources/assets/js/select2_replace_modal_edit.js', 'js')
    .styles([
        'resources/assets/plain/css/select2_replace.css',
    ], 'public/css/select2_replace.css')
    .js('resources/assets/js/iexport-custom.js', 'js')
    .styles([
        'resources/assets/plain/css/speech-to-text.css',
    ], 'public/css/speech-to-text.css')
    .styles([
        'resources/assets/plain/css/synonym.css',
    ], 'public/css/synonym.css')
    .styles([
        'resources/assets/plain/css/dashboard.css',
    ], 'public/css/dashboard.css')
    .styles([
        'resources/assets/plain/css/drawflow.css',
        'resources/assets/plain/css/custom.css',
        'resources/assets/plain/css/loading.css',
    ], 'public/css/drawflow.css')
    .styles([
        'resources/assets/plain/css/upload-filestyle.css',
    ], 'public/css/upload-filestyle.css')
    .styles([
        'resources/assets/plain/css/normalize.css',
        'resources/assets/plain/css/reset-intervale.css',
        'resources/assets/plain/css/common.css',
        'resources/assets/plain/css/smartphone.css',
    ], 'public/css/main.css');

mix
    .copyDirectory('resources/assets/plain/img', 'public/img')
    .copyDirectory('resources/assets/plain/fonts', 'public/fonts');
//        .copy('node_modules/bootstrap-sass/assets/fonts/bootstrap/', 'public/fonts/bootstrap');

if (!mix.inProduction()) {
    mix.sourceMaps().version();
} else {
    mix.version();
}