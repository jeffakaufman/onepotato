if (document.getElementById('payment')) {
    new Vue({
        el: '#payment',
        created: function () {
            //window.addEventListener('keyup', this.validatePromo)
        },
        data: function () {
    	    return {
    			cards: [
    				'Visa', 'Mastercard', 'Discover', 'American Express'
    			],
    			promotype: 'coupon',
                promocode: '',
    			product_cost: '',
    			coupon: [],
    			expiry_month: 'Expiration Month',
    			expiry_year: 'Expiration Year',
    			bad_expiry: false
    		}
    	},
        methods: {
        	validatePromo: function() { // /coupon/getamount/85.90/SaveThreeBucks9109
        		this.promocode = $('input[name=promocode]').val();

                this.$http.get('/coupon/getamount/'+ this.product_cost + '/' + this.promocode, function(data){
                    
                    this.coupon = data;

                    if (this.promotype== 'coupon' && this.coupon.status == 'valid') {
                        var discount = parseFloat(this.coupon.discount).toFixed(2);
                        var newprice = parseFloat(this.coupon.newprice).toFixed(2);
                        
                        $('#discount').html('-$'+discount);
                        $('#totalcost').html('$'+newprice);
                    } else {
                        $('#discount').html('-$XX.XX');
                        $('#totalcost').html(this.product_cost);
                    }
                    //console.log(this.coupon);
                    
    	    	}.bind(this));
    		    
        	},
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
}