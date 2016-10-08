<?php $statesList = array(
        'AL'=>'Alabama',
        'AK'=>'Alaska',
        'AZ'=>'Arizona',
        'AR'=>'Arkansas',
        'CA'=>'California',
        'CO'=>'Colorado',
        'CT'=>'Connecticut',
        'DE'=>'Delaware',
        'DC'=>'District of Columbia',
        'FL'=>'Florida',
        'GA'=>'Georgia',
        'HI'=>'Hawaii',
        'ID'=>'Idaho',
        'IL'=>'Illinois',
        'IN'=>'Indiana',
        'IA'=>'Iowa',
        'KS'=>'Kansas',
        'KY'=>'Kentucky',
        'LA'=>'Louisiana',
        'ME'=>'Maine',
        'MD'=>'Maryland',
        'MA'=>'Massachusetts',
        'MI'=>'Michigan',
        'MN'=>'Minnesota',
        'MS'=>'Mississippi',
        'MO'=>'Missouri',
        'MT'=>'Montana',
        'NE'=>'Nebraska',
        'NV'=>'Nevada',
        'NH'=>'New Hampshire',
        'NJ'=>'New Jersey',
        'NM'=>'New Mexico',
        'NY'=>'New York',
        'NC'=>'North Carolina',
        'ND'=>'North Dakota',
        'OH'=>'Ohio',
        'OK'=>'Oklahoma',
        'OR'=>'Oregon',
        'PA'=>'Pennsylvania',
        'RI'=>'Rhode Island',
        'SC'=>'South Carolina',
        'SD'=>'South Dakota',
        'TN'=>'Tennessee',
        'TX'=>'Texas',
        'UT'=>'Utah',
        'VT'=>'Vermont',
        'VA'=>'Virginia',
        'WA'=>'Washington',
        'WV'=>'West Virginia',
        'WI'=>'Wisconsin',
        'WY'=>'Wyoming',
); ?>
<address class="shipping-address-form">
    {!! Form::text('address1', $shippingAddress->shipping_address, array('class' => 'form-control', 'placeholder' => "Address")) !!}
    <br />
    {!! Form::text('address2', $shippingAddress->shipping_address_2, array('class' => 'form-control', 'placeholder' => 'Address 2')) !!}
    <br />
    {!! Form::text('city', $shippingAddress->shipping_city, array('class' => 'form-control', 'placeholder' => 'City')) !!}
    <br />
    {!! Form::select('state', $statesList, $shippingAddress->shipping_state, array('class' => 'form-control',)) !!}
    <br />
    {!! Form::text('zip', $shippingAddress->shipping_zip, array('class' => 'form-control', 'placeholder' => 'Zip')) !!}
    <br />
    <button type="button" class="btn btn-primary save-button">Save changes</button>
</address>