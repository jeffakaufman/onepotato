


<div class="modal fade" id="previousAddressModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
	<div class="modal-dialog" style="width: 900px;" role="document">
		<div class="modal-content">
			<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
        			<span aria-hidden="true">&times;</span>
        		</button>
    			<h4 class="modal-title" id="favoritesModalLabel"><strong>Previous Shipping Addresses for {{$user->name}}</strong></h4>
			</div>
			<div class="modal-body">
			<?PHP		
				$subsrcribeCount = 0;
			?>
				<div class="row">
					<div class="col-sm-3 col-sm-offset-1">
						<strong>Address</strong>
					</div>
					<div class="col-sm-3">
						<strong>Address 2</strong>
					</div>
					<div class="col-sm-2">
						<strong>City</strong>
					</div>
					<div class="col-sm-1">
						<strong>State</strong>
					</div>
					<div class="col-sm-1">
						<strong>Zip</strong>
					</div>
				</div>

	@foreach ($shippingAddresses as $shippingAddress)
		@if ($shippingAddress->is_current === 0)
				<div class="row">
					<div class="col-sm-3 col-sm-offset-1">
						{{ $shippingAddress->shipping_address }}
					</div>
					<div class="col-sm-3">
						{{ $shippingAddress->shipping_address_2 }}
					</div>
					<div class="col-sm-2">
						{{ $shippingAddress->shipping_city }}
					</div>
					<div class="col-sm-1">
						{{ $shippingAddress->shipping_state }}
					</div>
					<div class="col-sm-1">
						{{ $shippingAddress->shipping_zip }}
					</div>
				</div>
		@endif
	@endforeach
			</div>
		</div>
	</div>
</div>

