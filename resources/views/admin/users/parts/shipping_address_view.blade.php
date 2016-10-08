<address>
    {{ $shippingAddress->shipping_address }}<br />
    @if ($shippingAddress->shipping_address_2)
        {{ $shippingAddress->shipping_address_2 }}<br />
    @endif
    {{ $shippingAddress->shipping_city }}, {{ $shippingAddress->shipping_state }} {{ $shippingAddress->shipping_zip}}<br />
    <a href="mailto:{{ $user->email}}">{{ $user->email }}</a>
</address>
