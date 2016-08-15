var DeliveryComponent = Vue.extend({
    template: '#delivery-template',
    ready: function() {
    	this.fetchMenu(this.delivery);
    	//console.log(this.prefs);
    },
  	props: ['prefs', 'delivery'],

    data: function () {
	    return {
	    	list: [],
	    	form: new SparkForm()
	    }
    },
    methods: {
    	fetchMenu: function(ddate) {

			this.$http.get('/whatscooking/'+ ddate, function(menu){
	    		this.list = menu;
	    		//console.log(this.list);
	    	}.bind(this));
	  	}
	},
	computed: {
		filteredMenu: function(meal) {
	  		var userMenu = [];
			var meatDone = false;

	  		for (var i = 0; i < this.list.length; i++) {
	  			var noNuts = false;
	  			if(this.prefs.indexOf(7) > -1 && this.list[i].hasNuts) noNuts = true;
	  			
		        if (this.prefs.indexOf(1) > -1 && this.list[i].hasBeef && this.list[i].isNotAvailable == 0 && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.indexOf(3) > -1 && this.list[i].hasFish && this.list[i].isNotAvailable == 0 && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.indexOf(5) > -1 && this.list[i].hasPork && this.list[i].isNotAvailable == 0 && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.indexOf(2) > -1 && this.list[i].hasPoultry && this.list[i].isNotAvailable == 0 && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.indexOf(4) > -1 && this.list[i].hasLamb && this.list[i].isNotAvailable == 0 && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.indexOf(6) > -1 && this.list[i].hasShellfish && this.list[i].isNotAvailable == 0 && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.list[i].vegetarianBackup && this.list[i].isNotAvailable == 0) {
		          	userMenu.push(this.list[i]);
		        } 
		        meatDone = true;
		    }
	        if (meatDone) {
	        	for (var i = 0; i < this.list.length; i++) {
			        if (this.list[i].isVegetarian && !this.list[i].vegetarianBackup && this.list[i].isNotAvailable == 0) {
			          	userMenu.push(this.list[i]);
			        }
			    }
	        } 
		   	return userMenu.slice(0, 3);
        }
	}
});
var ChangeMenuComponent = Vue.extend({
    template: '#change-template',
    ready: function() {
    	
    },
  	props: ['fulllist', 'delivery'],

    data: function () {
	    return {
	    	
	    }
    },
    methods: {

	},
	computed: {
		fullMenu: function(meal) {
	  		var userMenu = [];
	  		for (var i = 0; i < this.fulllist.length; i++) {
		        userMenu.push(this.fulllist[i]);
		    }
		   	return userMenu;
        }
	},
	watch: {
        fullMenu:function(){
        	$('#changeMenu input').each(function() {
        		var menuId = $(this).val();
        		$('#changeMenu .meal[data-id='+menuId+']').removeClass('select').addClass('selected');
        	});
        	var numMeals = 0;
        	var menuChanged = false;
        	var menuFull = false;
        	$('#changeMenu .meal').click(function() {
        		
        		var menuId = $(this).data('id');
        		if (!menuChanged) { //typeof obj.foo != 'undefined'
        			$('#changeMenu .meal').removeClass('selected');
        			$('#changeMenu input.menu_id').val('');
        		}
        		
        		if ($(this).hasClass('selected')) {
        			$('#changeMenu input[value='+menuId+']').val('');
        			$(this).removeClass('selected');
        			numMeals--;
        		} else if (numMeals < 3 && !menuFull) {
        			$('#changeMenu .menu_id-'+numMeals+'').val(menuId);
        			$(this).addClass('selected');
        			numMeals++;
        		} else if (numMeals < 3 && menuFull) {
        			$('#changeMenu .menu_id').filter(function() { 
						return this.value === ''; 
					}).val(menuId);
					$(this).addClass('selected');
					numMeals++;
        		}
        		menuChanged = true;
        		if (numMeals == 3) menuFull = true;
        	});
        }
    }
});
Vue.component('delivery', DeliveryComponent);
Vue.component('change-menu', ChangeMenuComponent);
if (document.getElementById('delivery_schedule')) {
	new Vue({
		el: '#delivery_schedule',
		
	    ready: function() {
	    	
	    },
	    data: function () {
		    return {
		    	fulllist: []
			}
	    },
	    methods: {
		  	fetchWeekMenu: function(date) {

				this.$http.get('/whatscooking/'+ date, function(menu){
		    		this.fulllist = menu;
		    		//console.log(this.list);
		    	}.bind(this));
		    	//console.log(this.fulllist);
	    	
	  		}
		}
	});
}