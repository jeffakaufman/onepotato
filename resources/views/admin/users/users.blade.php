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
</script>
	<div class="container">
		<!-- Application Dashboard -->
		<table id="example" class="table table-striped table-hover" width="100%" cellspacing="0">
			<thead> 
				<tr>
        	    	<th style="width:20%"><a href="/admin/users/updateListParams/orderBy/userName">User Name</a></th>
					<th style="width:10%"><a href="/admin/users/updateListParams/orderBy/email">Email Address</a></th>
					<th class="text-center"><a href="/admin/users/updateListParams/orderBy/startDate">First Delivery Date</a></th>
					<th class="text-center"><a href="/admin/users/updateListParams/orderBy/revenue">Revenue</a></th>
					<th class="text-center"><a href="/admin/users/updateListParams/orderBy/status">Status</a></th>
				</tr> 
			</thead>
			<tfoot> 
				<tr style="width:10%">
        	    	<th><a href="/admin/users/updateListParams/orderBy/userName">User Name</a></th>
					<th><a href="/admin/users/updateListParams/orderBy/email">Email Address</a></th>
					<th><a href="/admin/users/updateListParams/orderBy/startDate">First Delivery Date</a></th>
					<th><a href="/admin/users/updateListParams/orderBy/revenue">Revenue</a></th>
					<th><a href="/admin/users/updateListParams/orderBy/status">Status</a></th>
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
						@elseif ($user->status == 'cancelled')
						<p class="bg-danger text-center" style="color:darkred">Cancelled</p>
						@endif
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	</div>
@endsection