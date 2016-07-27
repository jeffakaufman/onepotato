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