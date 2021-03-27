@extends('layouts.app')

@section('page_title', 'Project Manager Offers')

@section('page_styles')
    <style type="text/css">
        @media (min-width: 1367px) {
            .container {
                max-width: 1210px;
            }
        }

    </style>
@endsection

@section('page_content')
    <div class="contests-banner">
        <div class="contests-banner-inner">
            <div class="container">
                <h1 class="text-white mb-0">
                    Search Active Offers
                </h1>
                <form action="{{ route('search') }}" method="get">
                    <div class="intro-banner-search-form">
                        <div class="intro-search-field with-autocomplete">
                            <label for="autocomplete-input" class="field-title ripple-effect d-none d-sm-flex">Enter Offer
                                Keywords</label>
                            <div class="input-with-icon">
                                <input name="keyword" type="text"
                                    placeholder="E.g Logo design, Letter head, Envelope design" required
                                    value="{{ $search_keyword }}">
                                <i class="icon-material-outline-location-on"></i>
                            </div>
                        </div>

                        <div class="intro-search-field">
                            <label for="intro-keywords" class="field-title ripple-effect d-none d-sm-flex">
                                Select a Category
                            </label>
                            {{-- <input id="intro-keywords" type="text" placeholder="Job Title or Keywords"> --}}
                            <input name="category" type="hidden" value="project-managers" />
                            <select name="offer_category">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ in_array($category->id, $filter_categories) ? 'selected' : '' }}>
                                        {{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="intro-search-button mt-3 mt-sm-0 mr-0 mr-sm-2 mb-0">
                            <button class="button ripple-effect" type="submit">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="container pb-5">
        <div class="row">
            <div class="col-xl-3 col-lg-4 d-none">
                @include("offers.project-manager.sidebar", ["categories" => $categories, 'filter_categories' =>
                $filter_categories, 'filter_keywords' => $filter_keywords])
            </div>
            <div class="col-xl-12 content-left-offset">

                <h3 class="page-title d-none">@yield('page_title')</h3>

                <div class="notify-box margin-top-15 d-none">
                    <div class="switch-container">
                        <label class="switch"><input type="checkbox"><span class="switch-button"></span><span
                                class="switch-text">Turn on email alerts for this search</span></label>
                    </div>

                    <div class="sort-by">
                        <span>Sort by:</span>
                        <select class="selectpicker hide-tick">
                            <option>Relevance</option>
                            <option>Newest</option>
                            <option>Oldest</option>
                            <option>Random</option>
                        </select>
                    </div>
                </div>

                <div class="listings-container compact-list-layout margin-top-10">
                    @forelse ($offers as $contest)
                        <a href="{{ route('offers.project-managers.show', ['offer_slug' => $contest->slug]) }}"
                            class="job-listing">
                            <div class="job-listing-details">
                                <div class="job-listing-company-logo listing-user-avatar">
                                    @if (is_null($contest->user->avatar))
                                        <img src="{{ asset('images/user-avatar-placeholder.png') }}" alt="">
                                    @else
                                        <img src="{{ asset("storage/avatars/{$contest->user->avatar}") }}" alt="">
                                    @endif
                                </div>
                                <div class="job-listing-description">
                                    <h3 class="job-listing-title">
                                        {{ $contest->title }}
                                    </h3>
                                    <div class="job-listing-footer">
                                        <ul class="text-small">
                                            <li class="d-none">
                                                <i class="icon-material-outline-business"></i>
                                                Hexagon
                                                <div class="verified-badge" title="Verified Employer"
                                                    data-tippy-placement="top"></div>
                                            </li>
                                            <li>
                                                <i class="icon-material-outline-bookmark-border"></i>
                                                {{ $contest->sub_category->title }}
                                            </li>
                                            <li>
                                                <i class="icon-material-outline-business-center"></i>
                                                @if ($contest->minimum_designer_level == 0)
                                                    Any designer can apply
                                                @else
                                                    Only designers with minimum of {{ $contest->minimum_designer_level }} can
                                                    apply
                                                @endif
                                            </li>
                                            <li>
                                                <i class="icon-material-outline-access-time"></i>
                                                {{ $contest->created_at->diffForHumans() }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <span class="bookmark-icon"></span>
                            </div>
                        </a>
                    @empty
                        <div class="alert alert-info">
                            <small>
                                There are no offers available at the moment.
                            </small>
                        </div>
                    @endforelse
                </div>

                <div class="mt-3">
                    {{ $offers->links() }}
                </div>

                <!-- Pagination -->
                <div class="row d-none">
                    <div class="col-md-12">
                        <!-- Pagination -->
                        <div class="pagination-container margin-top-60 margin-bottom-60">
                            <nav class="pagination">
                                <ul>
                                    <li class="pagination-arrow"><a href="#"><i
                                                class="icon-material-outline-keyboard-arrow-left"></i></a></li>
                                    <li><a href="#">1</a></li>
                                    <li><a href="#" class="current-page">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#">4</a></li>
                                    <li class="pagination-arrow"><a href="#"><i
                                                class="icon-material-outline-keyboard-arrow-right"></i></a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <!-- Pagination / End -->

            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
<script type="text/javascript">
    const contests_filter = $(".contests-filter")

    // alert("ds")

    contests_filter.on("change", function(e) {
        // let filter = $(e.target).data('filter')
        filterContests()
    })

    $('body').on('DOMSubtreeModified', '#contest-keywords-list', function() {
        filterContests()
    });

    function filterContests() {
        let filters, keyword = []

        let keyword_texts = $("#contest-keywords-list").find(".keyword-text")

        $.each($("#contest-keywords-list").find(".keyword-text"), (key, val) => {
            keyword.push($(val).text())
        })


        let payload = {
            _token,
            keyword: keyword
        }

        $.each(contests_filter, (key, filter) => {
            payload[$(filter).data('filter')] = $(filter).val()
        })

        console.log(payload)

        loading_container.show();

        fetch(`${webRoot}offers/project-managers/filter`, {
                method: 'post',
                headers: {
                    'Accept': 'application/json',
                    'Content-type': 'application/json'
                },
                body: JSON.stringify(payload)
            }).then(response => response.json())
            .then(async responseJson => {
                if (responseJson.success) {
                    console.log("Success here", responseJson);
                    setTimeout(() => {
                        window.location = responseJson.redirect_url;
                    }, 2000)
                } else {
                    Snackbar.show({
                        text: responseJson.message,
                        pos: 'top-center',
                        showAction: false,
                        actionText: "Dismiss",
                        duration: 5000,
                        textColor: '#fff',
                        backgroundColor: '#721c24'
                    });
                    $('html, body').animate({
                        scrollTop: $('#wrapper').offset().top
                    }, 500);
                    loading_container.hide();
                }
            })
            .catch(error => {
                console.log("Error occurred: ", error);
                Snackbar.show({
                    text: `Error occurred, please try again`,
                    pos: 'top-center',
                    showAction: false,
                    actionText: "Dismiss",
                    duration: 5000,
                    textColor: '#fff',
                    backgroundColor: '#721c24'
                });
                $('html, body').animate({
                    scrollTop: $('#wrapper').offset().top
                }, 500);
            })
    }

</script>
@endsection
