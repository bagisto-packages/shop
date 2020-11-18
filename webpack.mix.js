const mix = require("laravel-mix");

require("laravel-mix-merge-manifest");

const publicPath = mix.inProduction() ? 'publishable/assets' : "../../../public/themes/default/assets";

mix.disableNotifications();
mix.setPublicPath(publicPath).mergeManifest();

mix
    .js([__dirname + "/src/Resources/assets/js/app.js"], "js/shop.js")
    .copy(__dirname + "/src/Resources/assets/images", publicPath + "/images")
    .sass(__dirname + "/src/Resources/assets/sass/app.scss", "css/shop.css")
    .sass(__dirname + "/src/Resources/assets/sass/default.scss", "css/default-booking.css")
    .options({
        processCssUrls: false
    });

if (!mix.inProduction()) {
    mix.sourceMaps();
}

if (mix.inProduction()) {
    mix.version();
}
