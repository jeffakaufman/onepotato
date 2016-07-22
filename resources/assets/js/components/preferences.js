Vue.component('preferences', {
    props: ['user'],

    ready() {
        //console.log('test');
    },
    data: function () {
	    return {
	    	plan_type: 'Omnivore Box',
	    	beef: true,
	    	poultry: true,
	    	fish: true,
	    	lamb: true,
	    	pork: true,
	    	shellfish: true,
	    	nuts: true
	    }
    },
    methods: {
	  	selectAllOmnivore: function () {
	  		this.beef = true,
	    	this.poultry = true,
	    	this.fish = true,
	    	this.lamb = true,
	    	this.pork = true,
	    	this.shellfish = true,
	    	this.nuts = true;
	  	},
	  	selectAllVegetarian: function () {
	  		this.beef = false,
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
