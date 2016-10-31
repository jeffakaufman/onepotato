var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass('app.scss')
    	.copy('node_modules/sweetalert/dist/sweetalert.css', 'resources/assets/css/sweetalert.css')
    	.styles([
	       	'fontello.css',
	       	'fontello-embedded.css',
	       	'sweetalert.css'
	    ], 'public/css/vendor.css')
	    .copy('node_modules/sweetalert/dist/sweetalert.min.js', 'resources/assets/js/sweetalert.min.js')
	    .scripts([
	    	'modernizr-custom.js', 
	    	'moment.min.js', 
	    	'moment-timezone-with-data-2010-2020.min.js',
	    ], 'public/js/vendor_header.js')
	    .scripts([
	    	'device.min.js',
	    	'slick.min.js',
	    	'jquery.matchHeight.js',
	    	'sweetalert.min.js'
	    ], 'public/js/vendor_footer.js')
       	.browserify('app.js', null, null, { paths: 'vendor/laravel/spark/resources/assets/js' });
});
