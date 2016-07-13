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
                <li @if (Request::is('register*'))class="active"@endif><i class="icon icon-apron"></i>1. Welcome</li>
                <li><i class="icon icon-silverware"></i>2. Select Plan</li>
                <li><i class="icon icon-sliders"></i>3. Preferences</li>
                <li><i class="icon icon-truck"></i>4. Delivery</li>
                <li><i class="icon icon-creditcard"></i>5. Payment</li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/login" class="navbar-link">Login</a></li>
                <li><a href="/register" class="navbar-link">Register</a></li>
            </ul>
        </div>
    </div>
</nav>
