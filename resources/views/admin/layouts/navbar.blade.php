@php
    use Carbon\Carbon;
@endphp
<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        </div>
        <div>
            <a class="navbar-brand brand-logo" href="{{route('home')}}">
                <img src="{{ asset('assets/images/logo.svg') }}" alt="logo" />
            </a>
            <a class="navbar-brand brand-logo-mini" href="{{route('home')}}">
                <img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" />
            </a>
        </div>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-top">
        <ul class="navbar-nav">
            <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
                <h1 class="welcome-text">Hello, <span class="text-black fw-bold">{{ Auth::user()->name }}</span></h1>
                {{-- <h3 class="welcome-sub-text">Lịch sử hoạt động tuần này </h3> --}}
            </li>
        </ul>
        <ul class="navbar-nav ms-auto">




            <li class="nav-item">
                <form class="search-form" action="#">
                    <i class="icon-search"></i>
                    <input type="search" class="form-control" placeholder="Search Here" title="Search here">
                </form>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator" id="notificationDropdown_mail" href="#" data-bs-toggle="dropdown">
                    <i class="icon-mail"></i>
                    {{-- <span class="count"></span> --}}
                </a>
            </li>
{{--            <li class="nav-item dropdown">--}}
{{--                <a class="nav-link count-indicator"--}}
{{--                id="notificationDropdown"--}}
{{--                 href="#" data-bs-toggle="dropdown">--}}
{{--                    <i class="icon-bell"></i>--}}
{{--                    @if(Auth::user()->unreadNotifications()->count()>0)--}}
{{--                        <span class="count"></span>--}}
{{--                    @endif--}}
{{--                </a>--}}
{{--                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0 menu-notification"--}}
{{--                    aria-labelledby="notificationDropdown">--}}
{{--                    --}}{{-- <div style="padding: 10px;0;10px;0; font-weight: bold; background-color:#1F3BB3; color:#ffffff; border-radius: 0.25rem;" class="text-center header-notify">THÔNG BÁO</div>--}}
{{--                    <div class="content-notify" style="max-height: 400px; overflow-y: auto;">--}}
{{--                        @foreach (Auth::user()->notifications->take(15) as $notification)--}}
{{--                        <a class="dropdown-item preview-item py-3" href="{{ route('notifications.markAsRead', $notification->id) }}">--}}
{{--                            <div class="preview-thumbnail">--}}
{{--                                <i class="mdi mdi-alert m-auto text-primary"></i>--}}
{{--                            </div>--}}
{{--                            <div class="preview-item-content">--}}
{{--                                <h6 class="preview-subject fw-normal text-dark mb-1" style="white-space: normal !important">--}}
{{--                                    @if($notification && $notification->read_at === null)--}}
{{--                                        <i style="font-size: 8px; color:#1F3BB3" class="fa fa-circle"></i>--}}
{{--                                    @endif--}}
{{--                                    {{ $notification->data['title'] }}</h6>--}}
{{--                                <p class="fw-light small-text mb-0 timestamp" data-timestamp="{{$notification->data['created_at']}}">--}}
{{--                                    {{ $notification->created_at->locale('vi')->diffForHumans(Carbon::now()) }} </p>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                        @endforeach--}}
{{--                    </div> --}}
{{--                    --}}{{-- <a href="{{ route('notifications.all') }}"class="dropdown-item py-3 border-top view-more">--}}
{{--                        <p class="mb-0 font-weight-medium float-left">Bạn có <span class="count-notify">{{ Auth::user()->unreadNotifications->count() }}</span> thông báo mới chưa đọc </p>--}}
{{--                        <span class="badge badge-pill badge-primary float-right">Xem tất cả</span>--}}
{{--                    </a> --}}
{{--                </div>--}}
{{--            </li>--}}

            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <img class="img-xs rounded-circle" src="{{ asset('assets/images/faces/avatar.png') }}"
                        alt="Profile image"> </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <div class="dropdown-header text-center">
                        <img class="img-md rounded-circle" src="{{ asset('assets/images/faces/avatar.png') }}"
                            alt="Profile image">
                        <p class="mb-1 mt-3 font-weight-semibold">{{ Auth::user()->name }}</p>
                        <p class="fw-light text-muted mb-0">{{ Auth::user()->email }}</p>
                    </div>
                    <a class="dropdown-item"><i
                            class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i> My Profile <span
                            class="badge badge-pill badge-danger">1</span></a>
                    <a class="dropdown-item"><i
                            class="dropdown-item-icon mdi mdi-message-text-outline text-primary me-2"></i> Messages</a>
                    <a class="dropdown-item"><i
                            class="dropdown-item-icon mdi mdi-calendar-check-outline text-primary me-2"></i>
                        Activity</a>
                    <a class="dropdown-item"><i
                            class="dropdown-item-icon mdi mdi-help-circle-outline text-primary me-2"></i> FAQ</a>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>Sign Out
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
