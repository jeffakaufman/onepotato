<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <ul class="nav nav-tabs">
				<li class="nav_link"5><a href="/admin/users/" >All Users</a></i></li>
				<li role="presentation" class="nav_link @if ($submenu == 'accountInfo') active @endif"><a href="/admin/user/{{ $user->id }}" >Account Info</a></i></li>
				
                <li class="nav_link @if ($submenu == 'subscription') active @endif"><a href="/admin/user/subscriptions/{{ $user->id }}">Plan Details</a> </i></li>
				<li class="nav_link @if ($submenu == 'payment') active @endif"><a href="/admin/user/payment/{{ $user->id }}">Payment Info</a> </i></li>
				<li class="nav_link @if ($submenu == 'delivery') active @endif"><a href="#">Delivery History</a> </i></li>
				<li class="nav_link @if ($submenu == 'referrals') active @endif"><a href="/admin/user/referrals/{{ $user->id }}">Referrals </a> </li>
            </ul>
        </div>
    </div>
</div>