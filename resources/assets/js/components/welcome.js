var MenuComponent = Vue.extend({
	name: 'menu',
    template: '#menu-template',
    ready: function() {
		this.fetchMenu();
    },
    data: function () {
	    return {
	    	list: [],
	    	currentDate: ''
	    }
    },
    methods: {
    	fetchMenu: function() {

    		var date, date2, today, year, m, month, d, day, input;
    		var tuesday = '';
	        	today = new Date();
	        	var daysUntilTuesday = 9 - today.getDay();
	        	tuesday = moment().add(daysUntilTuesday, 'days');

		        date = new Date(tuesday);
		        
		        year = date.getFullYear();
		        if (year <= 1999) year = year + 100;
		        m = date.getMonth();
		        month = ('0' + (date.getMonth()+1)).slice(-2);
		        d = date.getDate();
		        day = ('0' + date.getDate()).slice(-2);
		        date = year + '-' + month + '-' + day;
		        this.currentDate = date;

				this.$http.get('/whatscooking/'+ date, function(meals){
		    		this.list = meals;
		    		// $('.slick2').slick('unslick');
				    // $('.slick2 .meal').remove(); 
				    $('.slick2').unslick().slick({
				    	slidesToShow: 1,
					  	slidesToScroll: 1,
					  	dots: true,
					  	prevArrow: '<div class="slick-prev"><i class="fa fa-angle-left" aria-hidden="true"></i></div>',
					  	nextArrow: '<div class="slick-next"><i class="fa fa-angle-right" aria-hidden="true"></i></div>',
					});

		    	}.bind(this));

	  	}
	},
	computed: {
		getMenu: function(meal) {
		   	return this.list;
        }
	}
});

if (document.getElementById('welcome')) {
	new Vue({
		el: '#welcome',
		ready: function() {
			
		},
	    data: function () {
		    return {
			    
			}
	    },
	    computed: {
	   		
		},
	    methods: {
	    	
		},
		components: {
        	'menu': MenuComponent
        }
	});
}

