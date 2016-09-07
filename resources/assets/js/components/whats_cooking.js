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
    	fetchMenu: function(week) {
    		
    		var date, date2, today, tuesday, year, m, month, d, day, input;
	        
	        if (week  === undefined) {
	        	today = new Date();
	        	var daysUntilTuesday = 9 - today.getDay();
	        	tuesday = moment().add(daysUntilTuesday, 'days').calendar();
	        } else {
	        	if (this.currentDate != '') {
	        		input = this.currentDate;
	        	} else {
	        		input = $('.weekNav').data('week');
	        	}
	        	
	        	var parts = input.match(/(\d+)/g);
	        	today = new Date( parts[0], parts[1]-1, parts[2] );
	        	if (week == 'next') {
	        		tuesday = moment([today.getFullYear(), today.getMonth(), today.getDate()]).add(7, 'days');
	        	} else {
	        		tuesday = moment([today.getFullYear(), today.getMonth(), today.getDate()]).subtract(7, 'days');
	        	}
	        }
	        date = new Date(tuesday);

	        year = date.getFullYear();
	        if (year <= 1999) year = year + 100;
	        m = date.getMonth();
	        month = ('0' + (date.getMonth()+1)).slice(-2);
	        d = date.getDate();
	        day = ('0' + date.getDate()).slice(-2);
	        date = year + '-' + month + '-' + day;
	        this.currentDate = date;

	        var monthNames = ["January", "February", "March", "April", "May", "June",
			  "July", "August", "September", "October", "November", "December"
			];
	        date2 = 'Tuesday, ' + monthNames[m] + ' ' + d;

			this.$http.get('/whatscooking/'+ date, function(meals){
	    		this.list = meals;
	    		$('.weekNav').attr('data-week',date);
	    		$('.weekPager .date').text(date2);
	    		//console.log(this.list);
	    	}.bind(this));
	  	}
	},
	computed: {
		getMenu: function(meal) {
		   	return this.list;
        }
	},
	watch: {
		getMenu: function() {
			$('.meal').matchHeight();
		}
	}
});

if (document.getElementById('whats_cooking')) {
	new Vue({
		el: '#whats_cooking',
		ready: function() {
			
		},
	    data: function () {
		    return {
			    plan_type: '',
		    	prefs: {
		    		redmeat: '',
			    	poultry: '',
			    	fish: '',
			    	lamb: '',
			    	pork: '',
			    	shellfish: '',
			    	nuts: ''
			    }
			}
	    },
	    computed: {
	   		
		},
	    methods: {
	    	fetchNewMenu: function(week) {
	    		this.$refs.menu.fetchMenu(week);
	    	},
		  	selectAllOmnivore: function () {
		  		this.prefs.redmeat = true
		    	this.prefs.poultry = true,
		    	this.prefs.fish = true,
		    	this.prefs.lamb = true,
		    	this.prefs.pork = true,
		    	this.prefs.shellfish = true
		  	},
		  	selectAllVegetarian: function () {
		  		this.prefs.redmeat = false,
		    	this.prefs.poultry = false,
		    	this.prefs.fish = false,
		    	this.prefs.lamb = false,
		    	this.prefs.pork = false,
		    	this.prefs.shellfish = false
		  	},
		  	selectOmni: function () {
		  		
		  		var isOmni = false;
		  		$('input.pref').each(function() {
		  			if ($(this).is(':checked') ) isOmni = true;
		  		});
		  		if (isOmni) this.plan_type = 'Omnivore Box';
		  			else this.plan_type = 'Vegetarian Box';
		  	}
		},
		components: {
        	'menu': MenuComponent
        }
	});
}

