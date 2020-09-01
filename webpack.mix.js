let mix = require('laravel-mix');

mix.styles([
        'resources/assets/templates/css/font-awesome.min.css',
        'resources/assets/templates/css/simple-line-icons.min.css',
        'resources/assets/templates/css/style.css'
    ], 'public/css/templates.css')
    .scripts([
        'resources/assets/templates/js/jquery.min.js',
        'resources/assets/templates/js/popper.min.js',
        'resources/assets/templates/js/bootstrap.min.js',
        'resources/assets/templates/js/Chart.min.js',
        'resources/assets/templates/js/pace.min.js',
        'resources/assets/templates/js/template.js',
        'resources/assets/templates/js/sweetalert2.all.js'
    ], 'public/js/templates.js')
    .js(['resources/assets/js/app.js'], 'public/js/app.js').sourceMaps();

mix.browserSync({
    proxy: 'http://procedure.test/login',
    open: false
});

mix.disableNotifications();

mix.webpackConfig({
    node: {
        fs: 'empty'
    },
    externals: [{
        './cptable': 'var cptable'
    }]
});

mix.options({
    postCss: [
        require('autoprefixer'),
    ],
});