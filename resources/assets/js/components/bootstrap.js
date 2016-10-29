
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Components
 |--------------------------------------------------------------------------
 |
 | Here we will load the Spark components which makes up the core client
 | application. This is also a convenient spot for you to load all of
 | your components that you write while building your applications.
 */

require('./../spark-components/bootstrap');

// require('./welcome');
//require('./home');
require('./user');
require('./whats_cooking');
require('./preferences');
require('./delivery');
require('./payment');
require('./account');
require('./delivery_schedule');

$(function() {
	$('<div class="dec numButton">-</div>').insertBefore('input.number');
	$('<div class="inc numButton">+</div>').insertAfter('input.number');

	$('.numButton').click(function() {
		
	  	var oldValue, newVal, $button = $(this);
	  	if ($button.hasClass('dec')) {
	  		oldValue = $button.next().val();
	  		if (oldValue > 0) {
	      		newVal = parseFloat(oldValue) - 1;
	    	} else {
	      		newVal = 0;
	    	}
	  		$button.next().val(newVal);
	  	} else {
	  		oldValue = $button.prev().val();
	  		newVal = parseFloat(oldValue) + 1;
	    	$button.prev().val(newVal);
	  	}
	});
});