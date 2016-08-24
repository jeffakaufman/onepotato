var MenuComponent = Vue.extend({
    template: '#menu-template',
    ready: function() {
    	this.fetchMenu();
    	//alert(this['pref']);
    },
  	props: ['prefs', 'loaded'],

    data: function () {
	    return {
	    	list: [],
	    	altlist: [{"id":1,"menu_description":"","menu_title":"","image":"/img/foodpot.jpg","isVegetarian":1,"isOmnivore":0,"hasBeef":0,"hasPoultry":0,"hasFish":0,"hasLamb":0,"hasPork":0,"hasShellfish":0,"hasNoGluten":0,"hasNuts":0,"vegetarianBackup":0,"noDairy":0,"noEgg":0,"noSoy":0,"oven":0,"stovetop":0,"slowcooker":0,"isNotAvailable":0,"dietaryPreferenceNumber":null,"pivot":{"whats_cookings_id":null,"menus_id":null}},
                {"id":2,"menu_description":"","menu_title":"","image":"/img/foodpot.jpg","isVegetarian":1,"isOmnivore":0,"hasBeef":0,"hasPoultry":0,"hasFish":0,"hasLamb":0,"hasPork":0,"hasShellfish":0,"hasNoGluten":0,"hasNuts":0,"vegetarianBackup":0,"noDairy":0,"noEgg":0,"noSoy":0,"oven":0,"stovetop":0,"slowcooker":0,"isNotAvailable":0,"dietaryPreferenceNumber":null,"pivot":{"whats_cookings_id":null,"menus_id":null}},
                {"id":3,"menu_description":"","menu_title":"","image":"/img/foodpot.jpg","isVegetarian":1,"isOmnivore":0,"hasBeef":0,"hasPoultry":0,"hasFish":0,"hasLamb":0,"hasPork":0,"hasShellfish":0,"hasNoGluten":0,"hasNuts":0,"vegetarianBackup":0,"noDairy":0,"noEgg":0,"noSoy":0,"oven":0,"stovetop":0,"slowcooker":0,"isNotAvailable":0,"dietaryPreferenceNumber":null,"pivot":{"whats_cookings_id":null,"menus_id":null}}],
            clickable: true
	    }
    },
    methods: {
    	fetchMenu: function() {
    		
    		var date, year, month, day;
    		this.loaded = true;
	        date = new Date( $('#startDate').val() );
	        year = date.getFullYear();
	        if (year <= 1999) year = year + 100;
	        month = ('0' + (date.getMonth()+1)).slice(-2);
	        day = ('0' + date.getDate()).slice(-2);

			this.$http.get('/whatscooking/'+ year + '-' + month + '-' + day, function(menu){
	    		this.list = menu;
	    		this.loaded = false;
	    		//console.log(this.list);
	    	}.bind(this)).error(function(error) {
                this.list = this.altlist;
                this.loaded = false;
                this.clickable = false;
            });
	  	}
	},
	computed: {
		filteredMenu: function(meal) {
	  		var userMenu = [];
			var meatDone = false;
	  		for (var i = 0; i < this.list.length; i++) {
	  			var noNuts = false;
	  			if(this.prefs.nutfree && this.list[i].hasNuts) noNuts = true;
	  			
		        if (this.prefs.redmeat && this.list[i].hasBeef && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.fish && this.list[i].hasFish && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.pork && this.list[i].hasPork && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.poultry && this.list[i].hasPoultry && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.lamb && this.list[i].hasLamb && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.prefs.shellfish && this.list[i].hasShellfish && !noNuts) {
		          	userMenu.push(this.list[i]);
		        }
		        if (this.list[i].vegetarianBackup) {
		          	userMenu.push(this.list[i]);
		        } 
		        meatDone = true;
		    }
	        if (meatDone) {
	        	for (var i = 0; i < this.list.length; i++) {
			        if (this.list[i].isVegetarian && !this.list[i].vegetarianBackup) {
			          	userMenu.push(this.list[i]);
			        }
			    }
	        } 
		   	return userMenu.slice(0, 3);
        }
	}
});
Vue.component('menu', MenuComponent);

if (document.getElementById('preferences')) {
	new Vue({
		el: '#preferences',
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
			    },
			    loaded: ''
			}
	    },
	    computed: {
	   		concatPrefs: function() {
	   			var userPrefs = [];
	   			Object.keys(this.prefs).forEach(function(name) {
	   				if (this.prefs[name] == true) {
	   					if (name == 'redmeat') name = 'red meat';
	   					if (name == 'glutenfree') name = 'gluten free';
	   					if (name == 'nutfree') name = 'nut free';
	   					userPrefs.push(name);
	   				}
	   			}.bind(this));
	   			return userPrefs.join(', ');
	   		}
		},
	    methods: {
	    	fetchNewMenu: function() {
	    		this.$children[0].fetchMenu();
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
		}
	});
}

