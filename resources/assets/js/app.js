
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Bootstrap
 |--------------------------------------------------------------------------
 |
 | First, we will load all of the "core" dependencies for Spark which are
 | libraries such as Vue and jQuery. This also loads the Spark helpers
 | for things such as HTTP calls, forms, and form validation errors.
 |
 | Next, we'll create the root Vue application for Spark. This will start
 | the entire application and attach it to the DOM. Of course, you may
 | customize this script as you desire and load your own components.
 |
 */

require('spark-bootstrap');

require('./components/bootstrap');

var app = new Vue({
    mixins: [require('spark')]
});

$(function () {
  $('[data-toggle="tooltip"]').tooltip({html:true});
  //$('[data-toggle="popover"]').popover();
  $('.slick').slick({
  	slidesToShow: 1,
  	slidesToScroll: 1,
  	prevArrow: '<div class="slick-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></div>',
  	nextArrow: '<div class="slick-next"><i class="fa fa-angle-right" aria-hidden="true"></i></div>',
  	infinite: false,
  });
  $('.menu_slider').slick({
  	slidesToShow: 1,
  	slidesToScroll: 1,
  	dots: true,
  	arrows: false,
  	fade: true,
  	autoplay: true,
  	autoplaySpeed: 2000,
  });
});