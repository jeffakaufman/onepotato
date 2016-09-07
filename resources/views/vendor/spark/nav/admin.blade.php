<section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
        <div class="pull-left image">
            @if(Auth::check())
            <img src="{{{ Auth::user()->photo_url }}}" class="img-circle" alt="User Image">
            @endif
        </div>
        <div class="pull-left info">
            <p>
                @if(Auth::check())
                    {{{ Auth::user()->name }}}
                @else
                    Unregistered
                @endif
            </p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
    </div>
    {{--
Alexander Pierce App\User Object
(
    [fillable:protected] => Array
        (
            [0] => name
            [1] => email
        )

    [hidden:protected] => Array
        (
            [0] => password
            [1] => remember_token
            [2] => authy_id
            [3] => country_code
            [4] => phone
            [5] => card_brand
            [6] => card_last_four
            [7] => card_country
            [8] => billing_address
            [9] => billing_address_line_2
            [10] => billing_city
            [11] => billing_zip
            [12] => billing_country
            [13] => extra_billing_information
        )

    [casts:protected] => Array
        (
            [trial_ends_at] => date
            [uses_two_factor_auth] => boolean
        )

    [connection:protected] =>
    [table:protected] =>
    [primaryKey:protected] => id
    [keyType:protected] => int
    [perPage:protected] => 15
    [incrementing] => 1
    [timestamps] => 1
    [attributes:protected] => Array
        (
            [id] => 1
            [name] => Aleksey Zagarov
            [email] => azagarov@gmail.com
            [password] => $2y$10$0xTMzevARIuCSiNM0dxVm.laR6WFW3n//n.YxhwworWODfulISQI6
            [remember_token] =>
            [photo_url] =>
            [uses_two_factor_auth] => 0
            [authy_id] =>
            [country_code] =>
            [phone] =>
            [two_factor_reset_code] =>
            [current_team_id] =>
            [stripe_id] =>
            [current_billing_plan] =>
            [card_brand] =>
            [card_last_four] =>
            [card_country] =>
            [billing_address] =>
            [billing_address_line_2] =>
            [billing_city] =>
            [billing_state] =>
            [billing_zip] =>
            [billing_country] =>
            [vat_id] =>
            [extra_billing_information] =>
            [trial_ends_at] => 2016-04-29 06:54:34
            [last_read_announcements_at] => 2016-04-19 06:54:34
            [created_at] => 2016-04-19 06:54:35
            [updated_at] => 2016-06-03 09:00:41
        )

    [original:protected] => Array
        (
            [id] => 1
            [name] => Aleksey Zagarov
            [email] => azagarov@gmail.com
            [password] => $2y$10$0xTMzevARIuCSiNM0dxVm.laR6WFW3n//n.YxhwworWODfulISQI6
            [remember_token] =>
            [photo_url] =>
            [uses_two_factor_auth] => 0
            [authy_id] =>
            [country_code] =>
            [phone] =>
            [two_factor_reset_code] =>
            [current_team_id] =>
            [stripe_id] =>
            [current_billing_plan] =>
            [card_brand] =>
            [card_last_four] =>
            [card_country] =>
            [billing_address] =>
            [billing_address_line_2] =>
            [billing_city] =>
            [billing_state] =>
            [billing_zip] =>
            [billing_country] =>
            [vat_id] =>
            [extra_billing_information] =>
            [trial_ends_at] => 2016-04-29 06:54:34
            [last_read_announcements_at] => 2016-04-19 06:54:34
            [created_at] => 2016-04-19 06:54:35
            [updated_at] => 2016-06-03 09:00:41
        )

    [relations:protected] => Array
        (
        )

    [visible:protected] => Array
        (
        )

    [appends:protected] => Array
        (
        )

    [guarded:protected] => Array
        (
            [0] => *
        )

    [dates:protected] => Array
        (
        )

    [dateFormat:protected] =>
    [touches:protected] => Array
        (
        )

    [observables:protected] => Array
        (
        )

    [with:protected] => Array
        (
        )

    [morphClass:protected] =>
    [exists] => 1
    [wasRecentlyCreated] =>
    [currentToken:protected] =>
)
1

    --}}
    <!-- search form -->
    <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
    </form>
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li class="menuitem-dashboard">
            <a href="/admin/dashboard">
                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            </a>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-list"></i>
                <span>Products</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                <li class="menuitem-subscription-products"><a href="/admin/subs_products"><i class="fa fa-circle-o"></i> Subscription Products</a></li>
                <li class="menuitem-one-time-products"><a href="/admin/one_time_products"><i class="fa fa-circle-o"></i> One Time Products</a></li>
            </ul>
        </li>
        <li class="menuitem-users">
            <a href="/admin/users">
                <i class="fa fa-user"></i> <span>Customers</span>
            </a>
        </li>

        <li class="menuitem-giftcards">
            <a href="/admin/gift_cards">
                <i class="fa fa-gift"></i> <span>Gift Cards</span>
            <span class="pull-right-container">
              <small class="label pull-right bg-green">new</small>
            </span>
            </a>
        </li>
        <li class="menuitem-subscriptions">
            <a href="/admin/subscriptions">
                <i class="fa fa-list"></i> <span>Subscriptions</span>
            </a>
        </li>
        <li class="menuitem-orders">
            <a href="/admin/product_orders">
                <i class="fa fa-money"></i> <span>Product Orders</span>
            </a>
        </li>
        <li class="menuitem-whatscooking">
            <a href="/admin/whatscooking">
                <i class="fa fa-cutlery"></i> <span>What's Cooking</span>
            </a>
        </li>
        <li class="menuitem-shipments">
            <a href="/admin/shipments">
                <i class="fa fa-truck"></i> <span>Shipments</span>
            </a>
        </li>
        <li class="menuitem-reports">
            <a href="/admin/reports">
                <i class="fa fa-shekel"></i> <span>Reports</span>
            </a>
        </li>
    </ul>
</section>

<script language="JavaScript" type="text/javascript">
    $(document).ready(function() {
        var _activeMenuItem = '{{ $menuitem }}';
        var _className = 'li.menuitem-'+_activeMenuItem;
        $(_className).addClass('active');

        if($(_className).parent().parent().hasClass('treeview')) {
            $(_className).parent().parent().addClass('active');
        }
    });
</script>