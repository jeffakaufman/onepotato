Vue.component('home', {
    props: ['user'],

    ready() {
        //
    },

    computed: {

    	usersName() {
    		return this.user.name;
    	}
    }
});
