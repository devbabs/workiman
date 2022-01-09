<div class="dashboard-sidebar">
    <div class="dashboard-sidebar-inner" data-simplebar>
        <div class="dashboard-nav-container">

            <!-- Responsive Navigation Trigger -->
            <a href="#" class="dashboard-responsive-nav-trigger">
                <span class="hamburger hamburger--collapse">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </span>
                <span class="trigger-title">Dashboard Navigation</span>
            </a>

            <!-- Navigation -->
            <div class="dashboard-nav">
                <div class="dashboard-nav-inner">

                    {{-- <ul data-submenu-title="Start"> --}}
                    <ul>
                        <li class="{{ Route::currentRouteName() == 'account' ? 'active' : '' }}">
                            <a href="{{ route('account') }}">
                                <i class="icon-material-outline-dashboard"></i>
                                Dashboard
                            </a>
                        </li>
                        @if (auth()->user()->freelancer)
                            <li class="">
                                <a href="{{ route('account.wallet') }}">
                                    <i class="icon-line-awesome-cc-mastercard"></i>
                                    Wallet
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('account.profile') }}">
                                <i class="icon-feather-user"></i>
                                Profile
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('account.conversations') }}">
                                <i class=" icon-feather-message-square"></i>
                                Messages
                                @if(auth()->user()->unread_messages > 0)
                                    <span class="nav-tag">{{ auth()->user()->unread_messages }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'account.settings' ? 'active' : '' }}">
                            <a href="{{ route('account.settings') }}">
                                <i class="icon-material-outline-settings"></i>
                                Profile Settings
                            </a>
                        </li>

                        <li class="d-none">
                            <a href="#"><i class="icon-material-outline-business-center"></i> Browse</a>
                            <ul>
                                <li><a href="{{ route('contests.index') }}">Contests</a></li>
                                <li><a href="{{ route('offers.project-managers.index') }}">Project Manager Offers</a>
                                </li>
                                <li><a href="{{ route('offers.freelancers.index') }}">Freelancer Offers</a></li>
                            </ul>
                        </li>
                    </ul>


                    <ul data-submenu-title="Contests">
                        @if (auth()->user()->freelancer)
                            <li>
                                <a href="{{ route('contest.entries') }}">
                                    <i class="icon-material-outline-business-center"></i>
                                    My Contest Entries
                                    {{-- <span class="nav-tag">{{ auth()->user()->contests->count() }}</span> --}}
                                </a>
                            </li>
                        @else
                            {{-- <li>
                                <a href="{{ route('contests.user', ['username' => auth()->user()->username]) }}">
                                    <i class="icon-material-outline-business-center"></i>
                                    My Contests
                                </a>
                            </li> --}}
                            <li><a href="#"><i class="icon-material-outline-business-center"></i> Contests</a>
								<ul>
									<li><a href="{{ route('contests.user', ['username' => auth()->user()->username]) }}">All Contests</a></li>
									<li><a href="{{ route('contests.user', ['username' => auth()->user()->username, 'status' => 'pending']) }}">Pending Contests</a></li>
									<li><a href="{{ route('contests.user', ['username' => auth()->user()->username, 'status' => 'active']) }}">Active Contests</a></li>
									<li><a href="{{ route('contests.user', ['username' => auth()->user()->username, 'status' => 'inactive']) }}">Inactive Contests</a></li>
									<li><a href="{{ route('contests.user', ['username' => auth()->user()->username, 'status' => 'completed']) }}">Completed Contests</a></li>
								</ul>
							</li>
                        @endif
                        {{-- <li>
                            <a href="{{ route('contests.index') }}">
                                <i class="icon-material-outline-business-center"></i>
                                Browse Active Contests
                            </a>
                        </li> --}}
                        {{-- <li>
                            <a href="javascript: void(0)">
                                <i class="icon-material-outline-business-center"></i>
                                My Entries
                            </a>
                        </li> --}}
                    </ul>

                    <ul data-submenu-title="Offers">
                        @if (auth()->user()->freelancer)
                            <li>
                                <a href="{{ route('offers.assigned', ['username' => $user->username]) }}">
                                    <i class="icon-material-outline-extension"></i>
                                    Offers assigned to me
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('offers.pending-interests', ['user' => $user->id]) }}">
                                    <i class="icon-material-outline-extension"></i>
                                    Pending Offer Interests
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('offers.paid-interests', ['user' => $user->id]) }}">
                                    <i class="icon-material-outline-extension"></i>
                                    Paid Offer Interests
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('offers.user', ['username' => $user->username]) }}">
                                <i class="icon-material-outline-extension"></i>
                                My Offers
                            </a>
                        </li>
                        @if (!auth()->user()->freelancer)
                            <li>
                                <a href="{{ route('offers.project-managers.paid-interests', ['user' => $user->id]) }}">
                                    <i class="icon-material-outline-extension"></i>
                                    My Paid Freelancer Offers
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('offers.project-managers.offer-interests', ['user' => $user->id]) }}">
                                    <i class="icon-material-outline-extension"></i>
                                    Pending Offer Interests
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('offers.new') }}">
                                <i class="icon-material-outline-extension"></i>
                                Create New Offer
                            </a>
                        </li>
                    </ul>

                    {{-- <ul data-submenu-title="Account"> --}}
                    <ul>
                        {{-- <li>
                            <a href="{{ route('account.profile') }}">
                                <i class="icon-feather-user"></i>
                                Profile
                            </a>
                        </li>
                        @if (auth()->user()->freelancer)
                            <li>
                                <a href="{{ route('account.wallet') }}">
                                    <i class="icon-line-awesome-cc-mastercard"></i>
                                    Wallet
                                </a>
                            </li>
                        @endif
                        <li class="{{ Route::currentRouteName() == 'account.settings' ? 'active' : '' }}">
                            <a href="{{ route('account.settings') }}">
                                <i class="icon-material-outline-settings"></i>
                                Settings
                            </a>
                        </li> --}}
                        <li>
                            <a href="{{ route('logout') }}">
                                <i class="icon-material-outline-power-settings-new"></i>
                                Logout
                            </a>
                        </li>
                    </ul>

                </div>
            </div>

        </div>
    </div>
</div>
