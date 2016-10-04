var MenuComponent = Vue.extend({
	name: 'menu',
    template: '#menu-template',
    ready: function() {
		if(_defaultWhatsCookingWeek) {
			this.fetchMenu(new Date(_defaultWhatsCookingWeek));
		} else {
			this.fetchMenu();
		}
    },
    data: function () {
	    return {
	    	list: [],
	    	currentDate: ''
	    }
    },
    methods: {
    	fetchMenu: function(week) {
    		var date, date2, today, year, m, month, d, day, input;
    		var tuesday = '';
	        if (week  === undefined) {
	        	today = new Date();
	        	var daysUntilTuesday = 9 - today.getDay();
	        	tuesday = moment().add(daysUntilTuesday, 'days');
			} else if(week.getDay) {
				tuesday = week;
	        } else {
	        	today = new Date();
	        	var parts = this.currentDate.match(/(\d+)/g);
	        	var curDeliveryWeek = new Date( parts[0], parts[1]-1, parts[2] );
	        	var nextDeliveryWeek = moment([curDeliveryWeek.getFullYear(), curDeliveryWeek.getMonth(), curDeliveryWeek.getDate()]).add(7, 'days');
	        	var nextnextDeliveryWeek = moment([curDeliveryWeek.getFullYear(), curDeliveryWeek.getMonth(), curDeliveryWeek.getDate()]).add(14, 'days');
	        	var lastDeliveryWeek = moment([curDeliveryWeek.getFullYear(), curDeliveryWeek.getMonth(), curDeliveryWeek.getDate()]).subtract(7, 'days');
	        	var lastlastDeliveryWeek = moment([curDeliveryWeek.getFullYear(), curDeliveryWeek.getMonth(), curDeliveryWeek.getDate()]).subtract(14, 'days');
	        	// console.log(nextDeliveryWeek.format('YYYY-MM-DD'));
	        	// console.log(nextnextDeliveryWeek.format('YYYY-MM-DD'));

	        	if (week == 'next' && $('.weekNav.next').hasClass('active')) {
	        		tuesday = nextDeliveryWeek;
	        		$('.weekNav.prev').removeClass('disabled').addClass('active');
	        		var http = new XMLHttpRequest();
		        	var testjson = '/whatscooking/'+nextnextDeliveryWeek.format('YYYY-MM-DD');

				    http.open('HEAD', testjson, false);
				    http.send();
				    if(http.status>200) {
				    	$('.weekNav.next').addClass('disabled').removeClass('active');
		        	}
	        	} else if (week == 'prev' && $('.weekNav.prev').hasClass('active')) {
	        		tuesday = lastDeliveryWeek;
	        		$('.weekNav.next').removeClass('disabled').addClass('active');
	        		if (moment(lastlastDeliveryWeek).isBefore(today, 'day')) 
	        			$('.weekNav.prev').addClass('disabled').removeClass('active');
	        		else 
	        			$('.weekNav.prev').removeClass('disabled').addClass('active');
	        	}
	        }
	        // console.log(tuesday);
	        if (tuesday != '') {
	        	var filter;
	        	$('.btn-outline').each(function() {
	        		if ($(this).hasClass('active')) filter = $(this).attr('id');
	        	});
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
		        date2 = monthNames[m] + ' ' + d;

				this.$http.get('/whatscooking/'+ date, function(meals){
		    		this.list = meals;
		    		$('.weekNav').attr('data-week',date);
		    		$('.title .date').text(date2);
		    		if (filter) {
		    			this.filterMenu(filter);
		    		} else {
		    			setTimeout(function(){ $('.meal').addClass('loaded'); }, 1000);
		    		}
		    		//console.log(this.list);
		    	}.bind(this));
		    }

	  	},
	  	filterMenu: function(filter) {
			setTimeout(function(){ 
				$('.meal').hide();
				$('.meal.'+filter).show();
				$('.meal').addClass('loaded');
			}, 1000);
			setTimeout(function(){ 
				$('.meal:visible .inner').matchHeight();
			}, 3000);
	  	}
	},
	computed: {
		getMenu: function(meal) {
		   	return this.list;
        }
	},
	watch: {
		getMenu: function() {
			setTimeout(function(){ $('.meal .inner').matchHeight(); }, 2000);
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

