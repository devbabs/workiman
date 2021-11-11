@extends('admin.layouts.app')

@section('page_title')
    Contact Form responses
@endsection

@section('page_styles')
<style>

</style>
@endsection

@section('page_content')
<div class="row">

    <!-- Dashboard Box -->
    <div class="col-xl-12">
        <div class="dashboard-box margin-top-0">

            <!-- Headline -->
            <div class="headline">
                <h3><i class="icon-material-outline-assignment"></i> @yield('page_title')</h3>
                <button href="#add-category-popup"
                    class="mark-as-read ripple-effect-dark full-width popup-with-zoom-anim" title="Add new category"
                    data-tippy-placement="left">
                    <i class="icon-feather-plus"></i>
                </button>
            </div>

            <div class="content p-5" style="padding: 20px;">
                <table class="basic-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Message</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($contacts as $contact)
                            <tr>
                                <td data-label="Column 1">
                                    {{ $loop->iteration }}
                                </td>
                                <td data-label="Column 1">
                                    {{ $contact->name }}
                                </td>
                                <td data-label="Column 1">
                                    {{ $contact->email }}
                                </td>
                                <td data-label="Column 1">
                                    {{ $contact->phone }}
                                </td>
                                <td data-label="Column 1">
                                    {{ $contact->subject }}
                                </td>
                                <td data-label="Column 1">
                                    {{ $contact->message }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    Full Name
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>
                                        {{ $user->full_name }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table> --}}
            </div>
        </div>
    </div>

</div>
@endsection

@section('page_scripts')
<script>
    $(document).ready(() => {
        $('table').DataTable({
            order: [
                [0, 'desc']
            ]
        })
    })

</script>
@endsection
