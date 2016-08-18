Vue.component('account', {
    props: ['user'],

    ready() {
        //
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
		    	shellfish: ''
		    }
	    }
    },
    computed: {
   		concatPrefs: function() {
   			var userPrefs = [];
   			Object.keys(this.prefs).forEach(function(name) {
   				if (this.prefs[name] == true) {
   					if (name == 'redmeat') name = 'red meat';
   					userPrefs.push(name);
   				}
   			}.bind(this));
   			return userPrefs.join(', ');
   		}
	},
    methods: {
	  	selectAllOmnivore: function () {
	  		this.prefs.redmeat = true,
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

var url = document.location.toString();
if (url.match('#')) {
    $('.nav-sidebar a[href="#' + url.split('#')[1] + '"]').tab('show');
    removeHash();
} 

function removeHash() {
    var scrollV, scrollH, loc = window.location;
    if ('replaceState' in history) {
        history.replaceState('', document.title, loc.pathname + loc.search);
    } else {
        // Prevent scrolling by storing the page's current scroll offset
        scrollV = document.body.scrollTop;
        scrollH = document.body.scrollLeft;

        loc.hash = '';

        // Restore the scroll offset, should be flicker free
        document.body.scrollTop = scrollV;
        document.body.scrollLeft = scrollH;
    }
}