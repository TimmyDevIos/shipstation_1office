
@extends('admin.layouts.app')

@section('content')
@php
    use Carbon\Carbon;
@endphp
<div class="row">
    <div class="col-sm-12">
      <div class="home-tab">
        <div class="d-sm-flex align-items-center justify-content-between border-bottom">
          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#all" role="tab" aria-controls="overview" aria-selected="true">Tất cả</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#asread" role="tab" aria-selected="false">Chưa xem</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#unread" role="tab" aria-selected="false">Đã xem</a>
            </li>
          </ul>
          <div>
            <div class="btn-wrapper">
              {{-- <a href="#" class="btn btn-otline-dark align-items-center"><i class="icon-share"></i> Làm mới</a>
              <a href="#" class="btn btn-otline-dark"><i class="icon-printer"></i> Print</a> --}}
              <a onclick="window.location.reload()" class="btn btn-primary text-white me-0"><i class=" icon-reload"></i> Làm mới</a>
            </div>
          </div>
        </div>
        <div class="tab-content tab-content-basic">
          <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all">
            {{-- <div class="row">
              <div class="col-sm-12">
                <div class="statistics-details d-flex align-items-center justify-content-between">
                  <div>
                    <p class="statistics-title">Bounce Rate</p>
                    <h3 class="rate-percentage">32.53%</h3>
                    <p class="text-danger d-flex"><i class="mdi mdi-menu-down"></i><span>-0.5%</span></p>
                  </div>
                  <div>
                    <p class="statistics-title">Page Views</p>
                    <h3 class="rate-percentage">7,682</h3>
                    <p class="text-success d-flex"><i class="mdi mdi-menu-up"></i><span>+0.1%</span></p>
                  </div>
                  <div>
                    <p class="statistics-title">New Sessions</p>
                    <h3 class="rate-percentage">68.8</h3>
                    <p class="text-danger d-flex"><i class="mdi mdi-menu-down"></i><span>68.8</span></p>
                  </div>
                  <div class="d-none d-md-block">
                    <p class="statistics-title">Avg. Time on Site</p>
                    <h3 class="rate-percentage">2m:35s</h3>
                    <p class="text-success d-flex"><i class="mdi mdi-menu-down"></i><span>+0.8%</span></p>
                  </div>
                  <div class="d-none d-md-block">
                    <p class="statistics-title">New Sessions</p>
                    <h3 class="rate-percentage">68.8</h3>
                    <p class="text-danger d-flex"><i class="mdi mdi-menu-down"></i><span>68.8</span></p>
                  </div>
                  <div class="d-none d-md-block">
                    <p class="statistics-title">Avg. Time on Site</p>
                    <h3 class="rate-percentage">2m:35s</h3>
                    <p class="text-success d-flex"><i class="mdi mdi-menu-down"></i><span>+0.8%</span></p>
                  </div>
                </div>
              </div>
            </div> --}}
            <div class="row">
              <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-start">
                          <div>
                            <h4 class="card-title card-title-dash">Thông báo</h4>
                           <p class="card-subtitle card-subtitle-dash">Tất cả thông báo đã xem và chưa xem</p>
                          </div>
                          <div>
                            <div class="dropdown">
                              <button class="btn btn-secondary dropdown-toggle toggle-dark btn-lg mb-0 me-0" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Tất cả </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                <h6 class="dropdown-header">Settings</h6>
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="#">Something else here</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Separated link</a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                              <tbody>
                                  @foreach($notifications as $notification)
                                <tr>
                                  <td style="white-space: normal !important"><span style="font-size:0.85rem; font-weight:bold" >
                                      @if($notification && $notification->read_at === null)
                                          <i style="font-size: 8px; color:#1F3BB3" class="fa fa-circle"></i>
                                      @endif {{ $notification->data['title'] }}</span></br></br>
                                      <i style="">{{ $notification->created_at->locale('vi')->diffForHumans(Carbon::now()) }}</i></td>
                                  <td>
                                      @if($notification && $notification->read_at === null)
                                          <a href="{{ route('notifications.markAsRead', $notification->id) }}"><label class="badge badge-danger pull-right markAsRead_lable">Đánh dấu đã đọc</label></a>
                                      @else
                                          <a href="{{ route('notifications.markUnRead', $notification->id) }}"><label class="badge badge-info pull-right markUnRead_lable">Đánh dấu chưa đọc</label></a>
                                      @endif
                                  </td>
                                </tr>
                                  @endforeach
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 d-flex flex-column">
                <div class="row">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-lg-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                              <h4 class="card-title card-title-dash">Thống kê</h4>
                            </div>
                            <canvas class="my-auto" id="doughnutChart" height="200"></canvas>
                            <div id="doughnut-chart-legend" class="mt-5 text-center"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade show" id="asread" role="tabpanel" aria-labelledby="asread">
            <div class="row">
              <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-start">
                          <div>
                            <h4 class="card-title card-title-dash">Thông báo</h4>
                           <p class="card-subtitle card-subtitle-dash">Tất cả thông báo chưa xem</p>
                          </div>
                          <div>
                            <div class="dropdown">
                              <button class="btn btn-secondary dropdown-toggle toggle-dark btn-lg mb-0 me-0" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Tất cả </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                <h6 class="dropdown-header">Settings</h6>
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="#">Something else here</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Separated link</a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                              <tbody>
                                  @foreach($notifications as $notification)
                                <tr>
                                  <td style="white-space: normal !important"><span style="font-size:0.85rem; font-weight:bold" >
                                      @if($notification && $notification->read_at === null)
                                          <i style="font-size: 8px; color:#1F3BB3" class="fa fa-circle"></i>
                                      @endif {{ $notification->data['title'] }}</span></br></br>
                                      <i style="">{{ $notification->created_at->locale('vi')->diffForHumans(Carbon::now()) }}</i></td>
                                  <td>
                                      @if($notification && $notification->read_at === null)
                                          <a href="{{ route('notifications.markAsRead', $notification->id) }}"><label class="badge badge-danger pull-right markAsRead_lable">Đánh dấu đã đọc</label></a>
                                      @else
                                          <a href="{{ route('notifications.markUnRead', $notification->id) }}"><label class="badge badge-info pull-right markUnRead_lable">Đánh dấu chưa đọc</label></a>
                                      @endif
                                  </td>
                                </tr>
                                  @endforeach
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 d-flex flex-column">
                <div class="row">

                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane fade show" id="unread" role="tabpanel" aria-labelledby="unread">
            <div class="row">
              <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-start">
                          <div>
                            <h4 class="card-title card-title-dash">Thông báo</h4>
                           <p class="card-subtitle card-subtitle-dash">Tất cả thông báo đã xem</p>
                          </div>
                          <div>
                            <div class="dropdown">
                              <button class="btn btn-secondary dropdown-toggle toggle-dark btn-lg mb-0 me-0" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Tất cả </button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                <h6 class="dropdown-header">Settings</h6>
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="#">Something else here</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Separated link</a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                              <tbody>
                                  @foreach($notifications as $notification)
                                <tr>
                                  <td style="white-space: normal !important"><span style="font-size:0.85rem; font-weight:bold" >
                                      @if($notification && $notification->read_at === null)
                                          <i style="font-size: 8px; color:#1F3BB3" class="fa fa-circle"></i>
                                      @endif {{ $notification->data['title'] }}</span></br></br>
                                      <i style="">{{ $notification->created_at->locale('vi')->diffForHumans(Carbon::now()) }}</i></td>
                                  <td>
                                      @if($notification && $notification->read_at === null)
                                          <a href="{{ route('notifications.markAsRead', $notification->id) }}"><label class="badge badge-danger pull-right markAsRead_lable">Đánh dấu đã đọc</label></a>
                                      @else
                                          <a href="{{ route('notifications.markUnRead', $notification->id) }}"><label class="badge badge-info pull-right markUnRead_lable">Đánh dấu chưa đọc</label></a>
                                      @endif
                                  </td>
                                </tr>
                                  @endforeach
                              </tbody>
                            </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-4 d-flex flex-column">
                <div class="row">

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
  @push('third_party_scripts')

  <script>
    var doughnutChart = new Chart(document.getElementById("doughnutChart"), {
    type: 'doughnut',
    data: {
        labels: ["Chưa xem", "Đã xem "],
        datasets: [{
            label: 'My First Dataset',
            data: [{{ Auth::user()->unreadNotifications->count() }}, {{ Auth::user()->readNotifications->count() }},],
            backgroundColor: [
                'rgb(23, 46, 136)',
                'rgb(249, 95, 83)'
            ]
        }]
    },
    options: {
        responsive: true
    }
});

  </script>

  @endpush
  @endsection

