Vue.component('delivery', {
    props: ['user'],

    ready() {
        //
    },
    
});
if( document.getElementById("#bday_select") ) {
	new Vue({
		el: '#bday_select',
		data: {
			months: [
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
			]
		}
	});
}