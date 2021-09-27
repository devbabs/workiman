<!doctype html>
<html lang="en">

<head>
    <title>{{ config('app.name') }} | @yield("page_title", "Welcome")</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" href="{{ asset('logo/logo-icon.png') }}" type="image/jpeg">
    <link rel="stylesheet" href="{{ asset('_home/css/style.css') }}?{{ time() }}">
    <link rel="stylesheet" href="{{ asset('_home/css/colors/wexir-gold.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.css') }}">

    <link rel="stylesheet" href="{{ asset('vendor/line-awesome/css/line-awesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?{{ time() }}">

    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('vendor/owl-carousel/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/owl-carousel/assets/owl.theme.default.min.css') }}">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style type="text/css">
        .dz-error-mark,
        .dz-success-mark,
        .dz-error-message,
        .dz-progress {
            display: none !important;
        }

        .show-password {
            position: absolute;
            right: 0;
        }

        .contest-row-card {
            overflow: hidden;
        }

        .contest-row-card.top-rated {
            border: 2px solid var(--primary-color);
            /* border-right: 2px solid var(--primary-color); */
        }

        .contest-box-card .context-image-container {
            height: 200px;
        }

        .contest-box-card .context-image-container img {
            height: 100%;
            width: 100%;
            object-fit: cover;
            max-height: inherit;
            object-position: center;
        }

        textarea::placeholder,
        input::placeholder {
            color: #d0d0d0;
            font-weight: normal;
        }

        .status-strip {
            transform: rotate(90deg);
            /* position: absolute; */
            right: 0;
            top: 0;
            font-size: x-small;
            height: 20px;
            width: 160px;
            /* text-align: center; */
            margin-right: -70px;
            margin-top: 70px;
            padding-left: 40px;
            padding-top: 2px;
            text-transform: uppercase;
        }

        @media(max-width: 1500px) {
            /* .context-image-container img {
                max-width: 100px;
                object-position: unset;
            }

            .contest-row-card-right {
                max-width: 150px;
                padding: 20px 10px;
            }

            .contest-row-card-description {
                margin-bottom: 5px;
            }

            .contest-row-card-right-each {
                margin-bottom: 5px;
            }

            .contest-row-card-right-each i {
                margin-right: 5px;
            }

            .contest-row-card-title {
                font-size: 17px;
                margin-bottom: 5px;
            }

            .context-row-card-tag-each {
                padding: 0 5px;
            } */
        }

    </style>

    @yield('page_styles')
</head>

<body>

    <div id="loading">
        <img src="{{ asset('logo/logo.png') }}" alt="" height="100">
    </div>
    <!-- Wrapper -->
    <div id="wrapper">
        @php
            $count = 0;
        @endphp
        @include('layouts.header')

        {{-- <div class="clearfix"></div> --}}

        <div class="page-content" style="min-height: 70vh;">
            @yield('page_content')
        </div>

        @include('layouts.footer')

    </div>
    <!-- Wrapper / End -->

    @include('layouts.account_login_popup')

    <!-- Scripts
================================================== -->
    <script src="{{ asset('_home/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('_home/js/jquery-migrate-3.0.0.min.js') }}"></script>
    <script src="{{ asset('vendor/popper/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('_home/js/mmenu.min.js') }}"></script>
    <script src="{{ asset('_home/js/tippy.all.min.js') }}"></script>
    <script src="{{ asset('_home/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('_home/js/bootstrap-slider.min.js') }}"></script>
    <script src="{{ asset('_home/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('_home/js/snackbar.js') }}"></script>
    <script src="{{ asset('_home/js/clipboard.min.js') }}"></script>
    <script src="{{ asset('_home/js/counterup.min.js') }}"></script>
    <script src="{{ asset('_home/js/magnific-popup.min.js') }}"></script>
    <script src="{{ asset('_home/js/slick.min.js') }}"></script>
    <script src="{{ asset('_home/js/custom.js') }}"></script>

    <script src="{{ asset('js/custom.js') }}?{{ time() }}"></script>

    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>

    <script src="{{ asset('vendor/owl-carousel/owl.carousel.min.js') }}"></script>

    <script type="text/javascript">
        const webRoot = "{{ route('index') }}/"
        const _token = "{{ csrf_token() }}"
    </script>

    @yield('page_scripts')

    @include('layouts.snackbar_alerts')

    <!-- Snackbar // documentation: https://www.polonel.com/snackbar/ -->
    <script>
        // Snackbar for user status switcher
        $('#snackbar-user-status label').click(function() {
            Snackbar.show({
                text: 'Your status has been changed!',
                pos: 'top-center',
                showAction: false,
                actionText: "Dismiss",
                duration: 5000,
                textColor: '#fff',
                backgroundColor: '#721c24'
            });
        });

        @if (Session::has('register'))
            $(document).ready(() => {
            $('a#account-login-popup-trigger').trigger('click')
            $(".popup-tab-content").hide();
            $("#register.popup-tab-content").show();
            $("#account-login-popup-tabs-register").addClass('active')
            $("#account-login-popup-tabs-login").removeClass('active')
            })
        @elseif(Session::has('login'))
            $(document).ready(() => {
            $('a#account-login-popup-trigger').trigger('click')
            $(".popup-tab-content").hide();
            $("#login.popup-tab-content").show();
            $("#account-login-popup-tabs-login").addClass('active')
            $("#account-login-popup-tabs-register").removeClass('active')
            })
        @endif
    </script>

    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/8.3.1/firebase-app.js"></script>

    <!-- TODO: Add SDKs for Firebase products that you want to use
        https://firebase.google.com/docs/web/setup#available-libraries -->

    <script>
        // Your web app's Firebase configuration
        var firebaseConfig = {
            apiKey: "AIzaSyAfsElRXO9jIquc55wPrEOrZ7LRooXORJw",
            authDomain: "wexir-25f98.firebaseapp.com",
            projectId: "wexir-25f98",
            storageBucket: "wexir-25f98.appspot.com",
            messagingSenderId: "953308554512",
            appId: "1:953308554512:web:23dcebbe5e1010e9beb195"
        };
        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
    </script>

    <script>
        const show_password_trigger = $(".show-password")
        const notification_span = $("#notification-count")
        let notification_count = `{{ $count }}`

        $("#notification-count").text(notification_count)

        show_password_trigger.on('click', function(e) {
            let this_password_input_name = $(e.target).data('input')

            let this_password_input

            switch (this_password_input_name) {
                case 'login-password':
                    this_password_input = $('.popup-tab-content#login').find('input[name=password]')
                    break;
                case 'register-password':
                    this_password_input = $('.popup-tab-content#register').find('input[name=password]')
                    break;
                case 'register-password-confirmation':
                    this_password_input = $('.popup-tab-content#register').find(
                        'input[name=password_confirmation]')
                    break;
            }

            this_password_input.attr({
                type: this_password_input.attr('type') == 'text' ? 'password' : 'text'
            })
        })
    </script>

</body>

</html>
