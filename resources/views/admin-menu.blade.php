<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
           
            <div class="panel-body">
				<span class="nav_link"><a href="/users/" >All Users</a> <i class="fa fa-lemon-o"></i></span>
				<span class="nav_link"><a href="/user/{{ $user->id }}" >Account Info</a> <i class="fa fa-lemon-o"></i></span>
				
                <span class="nav_link"><a href="/user/subscriptions/{{ $user->id }}">Plan Details</a>  <i class="fa fa-lemon-o"></i></span>
				<span class="nav_link"><a href="/user/payment/{{ $user->id }}">Payment Info</a>  <i class="fa fa-lemon-o"></i></span>
				<span class="nav_link"><a href="#">Delivery History</a>  <i class="fa fa-lemon-o"></i></span>
				<span class="nav_link"><a href="/user/referrals/{{ $user->id }}">Referrals </a> </span>
            </div>
        </div>
    </div>
</div>