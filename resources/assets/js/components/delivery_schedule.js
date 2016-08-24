
var ChangeMenuComponent = Vue.extend({
    template: '#change-template',
    ready: function() {
    	
    },
  	props: ['fulllist', 'menuFull'],

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
        	this.menuFull = true;
        	var numMeals = 3;
        	//console.log(this.menuFull);
        	$('#changeMenu .meal.avail').click(function() {
        		
        		var menuId = $(this).data('id');

        		if ($(this).hasClass('selected')) {
        			$('#changeMenu input[value='+menuId+']').val('');
        			$(this).removeClass('selected');
        			numMeals--;
        		} else if (numMeals < 3) {
        			$('#changeMenu input').filter(function() {
					    return !this.value;
					}).first().val(menuId);
        			$(this).addClass('selected');
        			numMeals++;
        		} else {
        			// $('#changeMenu .meal').removeClass('selected');
        			// $(this).addClass('selected');
        			// numMeals = 0;
     //    			$('#changeMenu input.menu_id').val('').filter(function() {
					//     return !this.value;
					// }).first().val(menuId);
					// numMeals++;
        		}
        		numMeals = $('#changeMenu .selected').length;
        		if (numMeals < 3) {
        			this.menuFull = false;
        			$('#changeMenu button[type=submit]').attr('disabled','disabled');
        		} else {
        			this.menuFull = true;
        			$('#changeMenu button[type=submit]').removeAttr('disabled');
        		}
        		//console.log(this.menuFull);

        	});
        }
    }
});
Vue.component('change-menu', ChangeMenuComponent);
if (document.getElementById('delivery_schedule')) {
	new Vue({
		el: '#delivery_schedule',
		
	    ready: function() {
	    	
	    },
	    data: function () {
		    return {
		    	fulllist: [],
		    	menuFull: ''
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