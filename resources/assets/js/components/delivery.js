Vue.component('delivery', {
    props: ['user'],

    ready() {
        //
    },
    
});

new Vue({
	el: '#bday_select',
	data: {
		months: [
			'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
		]
	}
});
