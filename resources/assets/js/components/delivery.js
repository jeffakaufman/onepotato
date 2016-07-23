Vue.component('delivery', {
    props: ['user'],

    ready() {
        //
    },
    data: function () {
	    return {
			months: [
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
			],
			states: [
				{ abbr: 'AL', state: 'Alabama' },
			    { abbr: 'AK', state: 'Alaska' },
			    { abbr: 'AZ', state: 'Arizona' },
			    { abbr: 'AR', state: 'Arkansas' },
			    { abbr: 'CA', state: 'California' },
			    { abbr: 'CO', state: 'Colorado' },
			    { abbr: 'CT', state: 'Connecticut' },
			    { abbr: 'DE', state: 'Delaware' },
			    { abbr: 'DC', state: 'District of Columbia' },
			    { abbr: 'FL', state: 'Florida' },
			    { abbr: 'GA', state: 'Georgia' },
			    { abbr: 'HI', state: 'Hawaii' },
			    { abbr: 'ID', state: 'Idaho' },
			    { abbr: 'IL', state: 'Illinois' },
			    { abbr: 'IN', state: 'Indiana' },
			    { abbr: 'IA', state: 'Iowa' },
			    { abbr: 'KS', state: 'Kansas' },
			    { abbr: 'KY', state: 'Kentucky' },
			    { abbr: 'LA', state: 'Louisiana' },
			    { abbr: 'ME', state: 'Maine' },
			    { abbr: 'MD', state: 'Maryland' },
			    { abbr: 'MA', state: 'Massachusetts' },
			    { abbr: 'MI', state: 'Michigan' },
			    { abbr: 'MN', state: 'Minnesota' },
			    { abbr: 'MS', state: 'Mississippi' },
			    { abbr: 'MO', state: 'Missouri' },
			    { abbr: 'MT', state: 'Montana' },
			    { abbr: 'NE', state: 'Nebraska' },
			    { abbr: 'NV', state: 'Nevada' },
			    { abbr: 'NH', state: 'New Hampshire' },
			    { abbr: 'NJ', state: 'New Jersey' },
			    { abbr: 'NM', state: 'New Mexico' },
			    { abbr: 'NY', state: 'New York' },
			    { abbr: 'NC', state: 'North Carolina' },
			    { abbr: 'ND', state: 'North Dakota' },
			    { abbr: 'OH', state: 'Ohio' },
			    { abbr: 'OK', state: 'Oklahoma' },
			    { abbr: 'OR', state: 'Oregon' },
			    { abbr: 'PA', state: 'Pennsylvania' },
			    { abbr: 'RI', state: 'Rhode Island' },
			    { abbr: 'SC', state: 'South Carolina' },
			    { abbr: 'SD', state: 'South Dakota' },
			    { abbr: 'TN', state: 'Tennessee' },
			    { abbr: 'TX', state: 'Texas' },
			    { abbr: 'UT', state: 'Utah' },
			    { abbr: 'VT', state: 'Vermont' },
			    { abbr: 'VA', state: 'Virginia' },
			    { abbr: 'WA', state: 'Washington' },
			    { abbr: 'WV', state: 'West Virginia' },
			    { abbr: 'WI', state: 'Wisconsin' },
			    { abbr: 'WY', state: 'Wyoming' },
			]
		}
	}
});