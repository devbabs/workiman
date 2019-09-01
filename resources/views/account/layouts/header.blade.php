<header id="header-container" class="fullwidth dashboard-header not-sticky">
    <div id="header">
        <div class="container">
            <div class="left-side">
                <div id="logo">
                    <a href="{{ route('index') }}">
                        <img src="{{ asset('logo/logo.png') }}" alt="">
                    </a>
                </div>
                <nav id="navigation">
                </nav>
                <div class="clearfix"></div>
            </div>

            <div class="right-side">
                @if (!auth()->check())
                    <div class="header-widget hide-on-mobiles">
                        <div class="header-notifications">
                            <div class="header-notifications-trigger">
                                <a href="#account-login-popup" id="account-login-popup-trigger" class="popup-with-zoom-anim"><i class="icon-feather-user"></i></a>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="header-widget">
                    <div class="header-notifications user-menu">
                        <div class="header-notifications-trigger">
                            <a href="#"><div class="user-avatar status-online"><img src="{{ asset('_home/images/user-avatar-small-01.jpg') }}" alt=""></div></a>
                        </div>

                        <div class="header-notifications-dropdown">
                            <div class="user-status">
                                <div class="user-details">
                                    <div class="user-avatar status-online"><img src="{{ asset('_home/images/user-avatar-small-01.jpg') }}" alt=""></div>
                                    <div class="user-name">
                                        Tom Smith <span>Freelancer</span>
                                    </div>
                                </div>
                                <div class="status-switch" id="snackbar-user-status">
                                    <label class="user-online current-status">Online</label>
                                    <label class="user-invisible">Invisible</label>
                                    <span class="status-indicator" aria-hidden="true"></span>
                                </div>
                            </div>
                            <ul class="user-menu-small-nav">
                                <li><a href="{{ route('account') }}"><i class="icon-material-outline-dashboard"></i> Dashboard</a></li>
                                <li><a href="dashboard-settings.html"><i class="icon-material-outline-settings"></i> Settings</a></li>
                                <li><a href="{{ route('logout') }}"><i class="icon-material-outline-power-settings-new"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <span class="mmenu-trigger">
                    <button class="hamburger hamburger--collapse" type="button">
                        <span class="hamburger-box">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                </span>
            </div>
        </div>
    </div>

</header>
<div class="clearfix"></div>
