Vue.component('account', {
    props: ['user'],

    ready() {
        //
    },
    data: function () {
	    return {
	    	redmeat: '',
	    	poultry: '',
	    	fish: '',
	    	lamb: '',
	    	pork: '',
	    	shellfish: '',
	    	nuts: ''
	    }
    },
    methods: {
	  	selectAllOmnivore: function () {
	  		this.redmeat = true,
	    	this.poultry = true,
	    	this.fish = true,
	    	this.lamb = true,
	    	this.pork = true,
	    	this.shellfish = true,
	    	this.nuts = true;
	  	},
	  	selectAllVegetarian: function () {
	  		this.redmeat = false,
	    	this.poultry = false,
	    	this.fish = false,
	    	this.lamb = false,
	    	this.pork = false,
	    	this.shellfish = false,
	  		this.nuts = true;
	  	},
	  	selectOmni: function () {
	  		this.plan_type = 'Omnivore Box'
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