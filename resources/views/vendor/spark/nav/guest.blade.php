<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">

            <!-- Branding Image -->
            @include('spark::nav.brand')

            <!-- Collapsed Hamburger -->
            <div class="hamburger">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#spark-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

        </div>

        <div class="collapse navbar-collapse" id="spark-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav navbar-left">
                <li @if (Request::is('whats-cooking'))class="active"@endif><a href="/whats-cooking">What's Cooking</a></li>
                <li @if (Request::is('faq'))class="active"@endif><a href="https://onepotato.zendesk.com/hc/en-us">FAQ</a></li>
               <!-- <li @if (Request::is('marketplace'))class="active"@endif><a href="/marketplace">Marketplace</a></li>-->
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <li @if (Request::is('login'))class="active"@endif><a href="/login" class="navbar-link">Login</a></li>
                <li @if (Request::is('register'))class="active"@endif><a href="/register" class="navbar-link">Register</a></li>
            </ul>
        </div>
    </div>
</nav>
