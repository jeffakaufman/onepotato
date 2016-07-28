Vue.component('spark-navbar', {
    props: ['user'],

    ready() {
        //
    },

    computed: {

    	usersName() {
    		return this.user.first_name;
    	}
    }
});
