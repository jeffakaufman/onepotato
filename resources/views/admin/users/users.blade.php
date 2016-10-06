@extends('spark::layouts.app-admin', ['menuitem' => 'users'])

@section('page_header')

    <h1>
        Users
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">UI</a></li>
        <li class="active">Buttons</li>
    </ol>
@endsection
@section('content')
<script>
jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.document.location = $(this).data("href");
    });
});
$(document).ready( function () {
    $('#users').DataTable({
  "pageLength": 25
});
} );

$('#users').DataTable( {
  "pageLength": 5
} );

</script>
	<div class="container">
		<!-- Application Dashboard -->
		<table id="users" class="table table-striped table-hover table-order-column" width="100%" cellspacing="0">
			<thead> 
				<tr>
        	    	<th style="width:20%">User Name</th>
					<th style="width:10%">Email Address</th>
					<th class="text-center">First Delivery Date</th>
					<th class="text-center">Revenue</th>
					<th class="text-center">Status</th>
				</tr> 
			</thead>
			<tfoot> 
				<tr>
        	    	<th style="width:20%">User Name</th>
					<th style="width:10%">Email Address</th>
					<th class="text-center">First Delivery Date</th>
					<th class="text-center">Revenue</th>
					<th class="text-center">Status</th>
				</tr> 
			</tfoot>
			<tbody>
			@foreach ($users as $user)
				<tr class='clickable-row'  data-href='/admin/user_details/{{ $user->id }}' style="cursor:pointer">
					<td>{{ $user->name }}</td>
					<td>{{ $user->email }}</td>
					<td class="text-center">{{ date('m/d/y', strtotime($user->start_date)) }}</td>
					<td class="text-right">${{ $user->revenue }}</td>
					<td>
						@if ($user->status == 'active')
						<p class="bg-success text-center" style="color:darkgreen">Active</p>
						@elseif ($user->status == 'inactive-cancelled')
						<p class="bg-danger text-center" style="color:darkred">Cancelled</p>
						@endif
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
@endsection