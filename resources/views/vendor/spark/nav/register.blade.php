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
        
        <div class="collapse navbar-collapse" id="register-navbar">
            <!-- Left Side Of Navbar -->
            <ul class="nav navbar-nav navbar-left">
                <li id="register1"><span><i class="icon icon-apron"></i>1. Welcome</span></li>
                @if (Session::has('step2'))
                    <li id="register2"><span><a href="{{ route('register.select_plan') }}"><i class="icon icon-silverware"></i>2. Select Plan</a></span></li>
                @else
                    <li id="register2"><span><i class="icon icon-silverware"></i>2. Select Plan</span></li>
                @endif

                @if (Session::has('step3'))
                    <li id="register3"><span><a href="{{ route('register.preferences') }}"><i class="icon icon-sliders"></i>3. Preferences</a></span></li>
                @else
                    <li id="register3"><span><i class="icon icon-sliders"></i>3. Preferences</span></li>
                @endif
                 
                @if (Session::has('step4'))
                    <li id="register4"><span><a href="{{ route('register.delivery') }}"><i class="icon icon-truck"></i>4. Delivery</a></span></li>
                @else
                    <li id="register4"><span><i class="icon icon-truck"></i>4. Delivery</span></li>
                @endif

                @if (Session::has('step5'))
                    <li id="register5"><span><a href="{{ route('register.payment') }}"><i class="icon icon-creditcard"></i>5. Payment</a></span></li>
                @else
                    <li id="register5"><span><i class="icon icon-creditcard"></i>5. Payment</span></li>
                @endif
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/login" class="navbar-link">Login</a></li>
                <li><a href="/register" class="navbar-link">Register</a></li>
            </ul>
        </div>
        
    </div>
</nav>
