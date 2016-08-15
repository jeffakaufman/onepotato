Vue.component('payment', {

    ready() {
        //
    },
    data: function () {
	    return {
			cards: [
				'Visa', 'Mastercard', 'Discover', 'American Express'
			],
			expiry_month: 'Expiration Month',
			expiry_year: 'Expiration Year',
			bad_expiry: false
		}
	},
    methods: {
        checkDate: function () {
            var today = new Date();
            var this_month = today.getMonth() + 1;
            var this_year = today.getFullYear();
            if (this.expiry_month != 'Expiration Month' && this.expiry_year != 'Expiration Year') {
            	
            	var month = parseInt(this.expiry_month);
            	var year = parseInt(20 + this.expiry_year);
                if (month < this_month && year == this_year ) {
                	this.bad_expiry = true;
                } else {
                	this.bad_expiry = false;
                }

            }
        }
    }
});
