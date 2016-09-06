<!--
Order #431247690 Placed on Sep 01 2016

Items:
Item	Quantity	Price
One Potato Box for 2 Adults, 1 Child Vegetarian / Wednesday / None SKU: SQ7362909	1	$80.97



Total:
Subtotal : $80.97
Shipping : $0.00
Sales Tax : $0.00
Total : $80.97

Shipping address:
Christine Baldan
20770 Lost Ranch Road
San Jose, CA 95120
US
-->


<p>Order #{{$orderId}} Placed on {{$orderDate}}</p>

<p>Items</p>

<table>
<thead>
    <tr>
        <th>Item</th>
        <th>Quantity</th>
        <th>Price</th>
    </tr>
</thead>
<tbody>
@foreach($lines as $l)
    <tr>
        <td>{{ $l->name }}</td>
        <td>{{ $l->qty }}</td>
        <td>${{ $l->price }}</td>
    </tr>
@endforeach
</tbody>
</table>

<br />
<br />
<p>Total:</p>
<p>Subtotal : ${{$subtotal}}</p>
<p>Shipping : ${{$shipping}}</p>
<p>Sales tax : ${{$tax}}</p>
<p>Total : ${{$total}}</p>

<br />
<br />
<p>Shipping address:</p>
<p>{{$name}}</p>
<p>{{$address}}</p>
<p>{{$city}}, {{$state}} {{$zip}}</p>
<p>{{$country}}</p>