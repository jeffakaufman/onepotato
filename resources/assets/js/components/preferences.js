Vue.component('preferences', {
    props: ['user'],

    ready() {
        //console.log('test');
    },
    data: function () {
	    return {
	    	beef: false,
	    	poultry: false,
	    	fish: false,
	    	lamb: false,
	    	pork: false,
	    	shellfish: false,
	    	nuts: false
	    }
    },
    methods: {
	  	selectAllOmnivore: function () {
		    this.beef = true,
	  		this.beef = true,
	    	this.poultry = true,
	    	this.fish = true,
	    	this.lamb = true,
	    	this.pork = true,
	    	this.shellfish = true,
	    	this.nuts = true
	  	},
	  	selectAllVegetarian: function () {
	  		this.beef = false,
	  		this.beef = false,
	    	this.poultry = false,
	    	this.fish = false,
	    	this.lamb = false,
	    	this.pork = false,
	    	this.shellfish = false,
	  		this.nuts = true
	  	}
	  }
});
