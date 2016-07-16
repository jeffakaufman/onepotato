<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <!-- Collapsed Hamburger -->
            <div class="hamburger">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#spark-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <!-- Branding Image -->
            @include('spark::nav.brand')
        </div>

        <div class="collapse navbar-collapse" id="spark-navbar-collapse">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav navbar-left">
                <li @if (Request::is('register'))class="active"@endif><span><i class="icon icon-apron"></i>1. Welcome</span></li>
                <li @if (Request::is('register/select_plan'))class="active"@endif><span><i class="icon icon-silverware"></i>2. Select Plan</span></li>
                <li @if (Request::is('register/preferences'))class="active"@endif><span><i class="icon icon-sliders"></i>3. Preferences</span></li>
                <li @if (Request::is('register/delivery'))class="active"@endif><span><i class="icon icon-truck"></i>4. Delivery</span></li>
                <li @if (Request::is('register/payment'))class="active"@endif><span><i class="icon icon-creditcard"></i>5. Payment</span></li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/login" class="navbar-link">Login</a></li>
                <li><a href="/register" class="navbar-link">Register</a></li>
            </ul>
        </div>
    </div>
</nav>
