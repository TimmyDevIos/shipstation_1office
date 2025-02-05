
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
            <div class="row">
              <div class="col-lg-8 d-flex flex-column">
                <div class="row flex-grow">
                  <div class="col-12 grid-margin stretch-card">
                    <div class="card card-rounded">
                      <div class="card-body">
                        <div class="d-sm-flex justify-content-between align-items-start">
                          <div>
                            <h4 class="card-title card-title-dash">KHÁCH HÀNG</h4>
                           <p class="card-subtitle card-subtitle-dash">Danh sách tất cả khách hàng</p>
                          </div>
                          <div>
                            <div class="dropdown">
                              {{-- <button class="btn btn-secondary dropdown-toggle toggle-dark btn-lg mb-0 me-0" type="button" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Tất cả </button> --}}
                              {{-- <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                <h6 class="dropdown-header">Settings</h6>
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <a class="dropdown-item" href="#">Something else here</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('customers.create')}}">Đồng bộ dữ liệu</a>
                              </div> --}}
                            </div>
                          </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                  <tr>
                                    <th>SYNC</th>
                                    <th>Mã</th>
                                    <th>Tên Khách Hàng</th>
                                    <th>Đồng Bộ</th>
                                    <th>Ngày Tạo</th>
                                  </tr>
                                </thead>
                                <tbody>
                                @foreach($customers ?? '' as $item)
                                  <tr>
                                    <td>
                                        <a href="{{asset("customers/".$item->customer_code."/re_sync")}}" class="btn btn-primary text-white me-0"><i class="icon-cloud-upload"></i></a>
                                    </td>
                                    <td>{{$item->customer_code}}</td>
                                    <td>{{json_decode($item->data)->name}}</td>
                                    <td>
                                        @if($item->synced_data == 0)
                                            <label class="badge badge-warning">Chưa đồng bộ</label>
                                        @else
                                            <label class="badge badge-primary">Đã đồng bộ</label>
                                        @endif
                                    </td>

                                    {{-- <td class="text-danger"> 28.76% <i class="ti-arrow-down"></i></td> --}}
                                    <td>{{$item->created_at}}</td>
                                  </tr>
                                  @endforeach
                                </tbody>
                              </table>
                        </div>
                        <div style="margin-top:10px;" class="btn-group" role="group">
                            @if ($customers->currentPage() > 1)
                                <a href="{{ $customers->previousPageUrl() }}" class="btn btn-primary text-white"><</a>
                            @endif
                            @for ($i = max(1, $customers->currentPage() - 2); $i <= min($customers->lastPage(), $customers->currentPage() + 2); $i++)
                                @if ($i == $customers->currentPage())
                                    <a href="{{ $customers->url($i) }}" class="btn btn-primary text-white active">{{ $i }}</a>
                                @else
                                    <a href="{{ $customers->url($i) }}" class="btn btn-primary text-white">{{ $i }}</a>
                                @endif
                                @if ($i == $customers->currentPage() + 2 && $i < $customers->lastPage())
                                    <button class="btn btn-primary text-white disabled">...</button>
                                @endif
                                @if ($i == $customers->currentPage() - 2 && $i > 1)
                                    <button class="btn btn-primary text-white disabled">...</button>
                                @endif
                            @endfor
                            @if ($customers->currentPage() < $customers->lastPage())
                                <a href="{{ $customers->nextPageUrl() }}" class="btn btn-primary text-white">></a>
                            @endif
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
        </div>
      </div>
    </div>
</div>
    @push('third_party_scripts')
    @if (session('success'))
        <script>
            showSuccessToast('{{ session('success') }}')
        </script>
    @endif
    <script src="{{ asset('assets/js/dashboard.js')}}"></script>
    @endpush
  @endsection

