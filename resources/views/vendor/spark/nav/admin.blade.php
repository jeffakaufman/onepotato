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
                <i class="fa fa-user"></i> <span>Users</span>
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
                <i class="fa fa-list"></i> <span>Product Orders</span>
            </a>
        </li>
        <li class="menuitem-whatscooking">
            <a href="/admin/whatscooking">
                <i class="fa fa-cutlery"></i> <span>What's Cooking</span>
            </a>
        </li>
        <li class="menuitem-customers">
            <a href="/admin/customers">
                <i class="fa fa-list"></i> <span>Customers</span>
            </a>
        </li>
        <li class="menuitem-shipments">
            <a href="/admin/shipments">
                <i class="fa fa-list"></i> <span>Shipments</span>
            </a>
        </li>
        <li class="menuitem-coupons">
            <a href="/admin/coupons">
                <i class="fa fa-list"></i> <span>Coupons</span>
            </a>
        </li>
        <li class="menuitem-menu-information">
            <a href="/admin/menu_information">
                <i class="fa fa-list"></i> <span>Menu Information</span>
            </a>
        </li>
        <li class="menuitem-recipes">
            <a href="/admin/recipes">
                <i class="fa fa-list"></i> <span>Recipes</span>
            </a>
        </li>

<!--
        <li class="treeview">
            <a href="#">
                <i class="fa fa-pie-chart"></i>
                <span>Charts</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="../charts/chartjs.html"><i class="fa fa-circle-o"></i> ChartJS</a></li>
                <li><a href="../charts/morris.html"><i class="fa fa-circle-o"></i> Morris</a></li>
                <li><a href="../charts/flot.html"><i class="fa fa-circle-o"></i> Flot</a></li>
                <li><a href="../charts/inline.html"><i class="fa fa-circle-o"></i> Inline charts</a></li>
            </ul>
        </li>
        <li class="treeview active">
            <a href="#">
                <i class="fa fa-laptop"></i>
                <span>UI Elements</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="general.html"><i class="fa fa-circle-o"></i> General</a></li>
                <li><a href="icons.html"><i class="fa fa-circle-o"></i> Icons</a></li>
                <li class="active"><a href="buttons.html"><i class="fa fa-circle-o"></i> Buttons</a></li>
                <li><a href="sliders.html"><i class="fa fa-circle-o"></i> Sliders</a></li>
                <li><a href="timeline.html"><i class="fa fa-circle-o"></i> Timeline</a></li>
                <li><a href="modals.html"><i class="fa fa-circle-o"></i> Modals</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-edit"></i> <span>Forms</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="../forms/general.html"><i class="fa fa-circle-o"></i> General Elements</a></li>
                <li><a href="../forms/advanced.html"><i class="fa fa-circle-o"></i> Advanced Elements</a></li>
                <li><a href="../forms/editors.html"><i class="fa fa-circle-o"></i> Editors</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-table"></i> <span>Tables</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="../tables/simple.html"><i class="fa fa-circle-o"></i> Simple tables</a></li>
                <li><a href="../tables/data.html"><i class="fa fa-circle-o"></i> Data tables</a></li>
            </ul>
        </li>
        <li>
            <a href="../calendar.html">
                <i class="fa fa-calendar"></i> <span>Calendar</span>
            <span class="pull-right-container">
              <small class="label pull-right bg-red">3</small>
              <small class="label pull-right bg-blue">17</small>
            </span>
            </a>
        </li>
        <li>
            <a href="../mailbox/mailbox.html">
                <i class="fa fa-envelope"></i> <span>Mailbox</span>
            <span class="pull-right-container">
              <small class="label pull-right bg-yellow">12</small>
              <small class="label pull-right bg-green">16</small>
              <small class="label pull-right bg-red">5</small>
            </span>
            </a>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-folder"></i> <span>Examples</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="../examples/invoice.html"><i class="fa fa-circle-o"></i> Invoice</a></li>
                <li><a href="../examples/profile.html"><i class="fa fa-circle-o"></i> Profile</a></li>
                <li><a href="../examples/login.html"><i class="fa fa-circle-o"></i> Login</a></li>
                <li><a href="../examples/register.html"><i class="fa fa-circle-o"></i> Register</a></li>
                <li><a href="../examples/lockscreen.html"><i class="fa fa-circle-o"></i> Lockscreen</a></li>
                <li><a href="../examples/404.html"><i class="fa fa-circle-o"></i> 404 Error</a></li>
                <li><a href="../examples/500.html"><i class="fa fa-circle-o"></i> 500 Error</a></li>
                <li><a href="../examples/blank.html"><i class="fa fa-circle-o"></i> Blank Page</a></li>
                <li><a href="../examples/pace.html"><i class="fa fa-circle-o"></i> Pace Page</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#">
                <i class="fa fa-share"></i> <span>Multilevel</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
            </a>
            <ul class="treeview-menu">
                <li><a href="#"><i class="fa fa-circle-o"></i> Level One</a></li>
                <li>
                    <a href="#"><i class="fa fa-circle-o"></i> Level One
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="#"><i class="fa fa-circle-o"></i> Level Two</a></li>
                        <li>
                            <a href="#"><i class="fa fa-circle-o"></i> Level Two
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="#"><i class="fa fa-circle-o"></i> Level Three</a></li>
                                <li><a href="#"><i class="fa fa-circle-o"></i> Level Three</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><a href="#"><i class="fa fa-circle-o"></i> Level One</a></li>
            </ul>
        </li>
        <li><a href="../../documentation/index.html"><i class="fa fa-book"></i> <span>Documentation</span></a></li>
        <li class="header">LABELS</li>
        <li><a href="#"><i class="fa fa-circle-o text-red"></i> <span>Important</span></a></li>
        <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Warning</span></a></li>
        <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Information</span></a></li>
-->
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