<!-- NavBar For Authenticated Users -->
<spark-navbar
    :user="user"
    :teams="teams"
    :current-team="currentTeam"
    :has-unread-notifications="hasUnreadNotifications"
    :has-unread-announcements="hasUnreadAnnouncements"
    inline-template>

    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container" v-if="user">
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
                @includeIf('spark::nav.user-left')

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    @includeIf('spark::nav.user-right')

                    <!-- Notifications -->
                    <?php /*<li>
                        <a @click="showNotifications" class="has-activity-indicator">
                            <div class="navbar-icon">
                                <i class="activity-indicator" v-if="hasUnreadNotifications || hasUnreadAnnouncements"></i>
                                <i class="icon fa fa-bell"></i>
                            </div>
                        </a>
                    </li>*/ ?>

                    <li class="dropdown">
                        <!-- User Photo / Name -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <img :src="user.photo_url" class="spark-nav-profile-photo m-r-xs"> 
                            <div>
                                <div class="username">Welcome back, @{{ usersName }}</div>
                                <span class="text">Your Account</span> <span class="fa fa-angle-down"></span>
                            </div>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <!-- Impersonation -->
                            @if (session('spark:impersonator'))
                                <li class="dropdown-header">Impersonation</li>

                                <!-- Stop Impersonating -->
                                <li>
                                    <a href="/spark/kiosk/users/stop-impersonating">
                                        <i class="fa fa-fw fa-btn fa-user-secret"></i>Back To My Account
                                    </a>
                                </li>

                                <li class="divider"></li>
                            @endif

                            <!-- Developer -->
                            @if (Spark::developer(Auth::user()->email))
                                @include('spark::nav.developer')
                            @endif

                            <!-- Subscription Reminders -->
                            <?php /* @include('spark::nav.subscriptions') */ ?>

                            <!-- Your Settings -->
                            <li>
                                <a href="/account">
                                    Account Settings
                                </a>
                            </li>

                            @if (Spark::usesTeams())
                                <!-- Team Settings -->
                                @include('spark::nav.teams')
                            @endif

                            <li class="divider"></li>

                            <li>
                                <a href="#">
                                    Delivery History
                                </a>
                            </li>

                            <li class="divider"></li>

                            <li>
                                <a href="#">
                                    Recipe Book
                                </a>
                            </li>

                            <li class="divider"></li>

                            <!-- Logout -->
                            <li>
                                <a href="/logout">
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>

            </div>
                
        </div>
    </nav>
</spark-navbar>
