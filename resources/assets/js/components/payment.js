Vue.component('payment', {
    props: ['user'],

    ready() {
        //
    },
    data: function () {
	    return {
			cards: [
				'Visa', 'Mastercard', 'Discover', 'American Express'
			],
			months: [
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
			],
			years: [
				'2016', '2017', '2018', '2019', '2020'
			]
		}
	}
});
