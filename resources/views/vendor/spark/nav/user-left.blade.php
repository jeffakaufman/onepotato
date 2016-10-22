<!-- Left Side Of Navbar -->
<ul class="nav navbar-nav navbar-left">
    <li @if (Request::is('delivery-schedule'))class="active"@endif><a href="/delivery-schedule">Delivery Schedule</a></li>
    <li @if (Request::is('faq'))class="active"@endif><a href="https://onepotato.zendesk.com/hc/en-us">FAQ</a></li>
    <li @if (Request::is('pricing'))class="active"@endif><a href="/pricing">Pricing</a></li>
    <li @if (Request::is('whats-cooking'))class="active"@endif><a href="/whats-cooking">What's Cooking?</a></li>
   <!-- <li @if (Request::is('marketplace'))class="active"@endif><a href="/marketplace">Marketplace</a></li>-->
</ul>