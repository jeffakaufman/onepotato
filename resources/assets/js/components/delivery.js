Vue.component('delivery', {
    props: ['user'],

    ready() {
        //
    },
    data: function () {
	    return {
			months: [
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
			]
		}
	}
});