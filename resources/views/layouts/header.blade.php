<header id="header-container" class="fullwidth ">
    <div id="header">
        <div class="container">
            <div class="left-side">

                <div id="logo">
                    <a href="{{ route('index') }}"><img src="{{ asset('logo/logo.png') }}" alt=""></a>
                </div>

                <nav id="navigation">
                    <ul id="responsive" class="d-none">

                        <li><a href="#" class="current">Home</a>
                            <ul class="dropdown-nav">
                                <li><a href="index-2.html">Home 1</a></li>
                                <li><a href="index-3.html">Home 2</a></li>
                                <li><a href="index-4.html">Home 3</a></li>
                            </ul>
                        </li>

                        <li><a href="#">Find Work</a>
                            <ul class="dropdown-nav">
                                <li><a href="#">Browse Jobs</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="jobs-list-layout-full-page-map.html">Full Page List + Map</a></li>
                                        <li><a href="jobs-grid-layout-full-page-map.html">Full Page Grid + Map</a></li>
                                        <li><a href="jobs-grid-layout-full-page.html">Full Page Grid</a></li>
                                        <li><a href="jobs-list-layout-1.html">List Layout 1</a></li>
                                        <li><a href="jobs-list-layout-2.html">List Layout 2</a></li>
                                        <li><a href="jobs-grid-layout.html">Grid Layout</a></li>
                                    </ul>
                                </li>
                                <li><a href="#">Browse Tasks</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="tasks-list-layout-1.html">List Layout 1</a></li>
                                        <li><a href="tasks-list-layout-2.html">List Layout 2</a></li>
                                        <li><a href="tasks-grid-layout.html">Grid Layout</a></li>
                                        <li><a href="tasks-grid-layout-full-page.html">Full Page Grid</a></li>
                                    </ul>
                                </li>
                                <li><a href="browse-companies.html">Browse Companies</a></li>
                                <li><a href="single-job-page.html">Job Page</a></li>
                                <li><a href="single-task-page.html">Task Page</a></li>
                                <li><a href="single-company-profile.html">Company Profile</a></li>
                            </ul>
                        </li>

                        <li><a href="#">For Employers</a>
                            <ul class="dropdown-nav">
                                <li><a href="#">Find a Freelancer</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="freelancers-grid-layout-full-page.html">Full Page Grid</a></li>
                                        <li><a href="freelancers-grid-layout.html">Grid Layout</a></li>
                                        <li><a href="freelancers-list-layout-1.html">List Layout 1</a></li>
                                        <li><a href="freelancers-list-layout-2.html">List Layout 2</a></li>
                                    </ul>
                                </li>
                                <li><a href="single-freelancer-profile.html">Freelancer Profile</a></li>
                                <li><a href="dashboard-post-a-job.html">Post a Job</a></li>
                                <li><a href="dashboard-post-a-task.html">Post a Task</a></li>
                            </ul>
                        </li>

                        <li><a href="#">Dashboard</a>
                            <ul class="dropdown-nav">
                                <li><a href="dashboard.html">Dashboard</a></li>
                                <li><a href="dashboard-messages.html">Messages</a></li>
                                <li><a href="dashboard-bookmarks.html">Bookmarks</a></li>
                                <li><a href="dashboard-reviews.html">Reviews</a></li>
                                <li><a href="dashboard-manage-jobs.html">Jobs</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="dashboard-manage-jobs.html">Manage Jobs</a></li>
                                        <li><a href="dashboard-manage-candidates.html">Manage Candidates</a></li>
                                        <li><a href="dashboard-post-a-job.html">Post a Job</a></li>
                                    </ul>
                                </li>
                                <li><a href="dashboard-manage-tasks.html">Tasks</a>
                                    <ul class="dropdown-nav">
                                        <li><a href="dashboard-manage-tasks.html">Manage Tasks</a></li>
                                        <li><a href="dashboard-manage-bidders.html">Manage Bidders</a></li>
                                        <li><a href="dashboard-my-active-bids.html">My Active Bids</a></li>
                                        <li><a href="dashboard-post-a-task.html">Post a Task</a></li>
                                    </ul>
                                </li>
                                <li><a href="dashboard-settings.html">Settings</a></li>
                            </ul>
                        </li>

                        <li><a href="#">Pages</a>
                            <ul class="dropdown-nav">
                                <li><a href="pages-blog.html">Blog</a></li>
                                <li><a href="pages-pricing-plans.html">Pricing Plans</a></li>
                                <li><a href="pages-checkout-page.html">Checkout Page</a></li>
                                <li><a href="pages-invoice-template.html">Invoice Template</a></li>
                                <li><a href="pages-user-interface-elements.html">User Interface Elements</a></li>
                                <li><a href="pages-icons-cheatsheet.html">Icons Cheatsheet</a></li>
                                <li><a href="pages-contact.html">Contact</a></li>
                            </ul>
                        </li>

                    </ul>
                </nav>
                <div class="clearfix"></div>

            </div>


            <div class="right-side">
                <div class="header-widget hide-on-mobile">
                    <div class="header-notifications">
                        <div class="header-notifications-trigger">
                            <a href="#account-login-popup" id="account-login-popup-trigger" class="popup-with-zoom-anim"><i class="icon-feather-user"></i></a>
                        </div>
                    </div>
                </div>

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
                            <li><a href="dashboard.html"><i class="icon-material-outline-dashboard"></i> Dashboard</a></li>
                            <li><a href="dashboard-settings.html"><i class="icon-material-outline-settings"></i> Settings</a></li>
                            <li><a href="index-logged-out.html"><i class="icon-material-outline-power-settings-new"></i> Logout</a></li>
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
