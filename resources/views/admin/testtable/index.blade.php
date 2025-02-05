@extends('admin.layouts.app')

@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="row">
        <div class="col-sm-12">
            <div class="home-tab">
                <div
                    style='font-family: "Manrope", sans-serif;font-style: normal;font-weight: bold;font-size: 15px; line-height: 22px;color: #8D8D8D;margin-bottom: 0;'>
                    Tìm kiếm:</div>
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <div class="filter-container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <!-- Global Filter -->
                                <input type="text" id="global_filter" class="global_filter" placeholder="Global Search">
                            </li>
                            <!-- Column Filters -->
                            <li class="nav-item">
                                <select id="col0_filter" class="column_filter">
                                    <option value="">All</option>
                                    <option value="John Doe">John Doe</option>
                                    <option value="Jane Smith">Jane Smith</option>
                                    <!-- Add other options as needed -->
                                </select>
                            </li>
                            <li class="nav-item">
                                <select id="col1_filter" class="column_filter">
                                    <option value="">All</option>
                                    <option value="john.doe@example.com">john.doe@example.com</option>
                                    <option value="jane.smith@example.com">jane.smith@example.com</option>
                                    <!-- Add other options as needed -->
                                </select>
                            </li>
                            <li class="nav-item">
                                <select id="col2_filter" class="column_filter">
                                    <option value="">All</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content tab-content-basic">
                    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all">
                        <div class="row">
                            <div class="col-lg-12 d-flex flex-column">
                                <div class="row flex-grow">
                                    <div class="col-12 grid-margin stretch-card">
                                        <div class="card card-rounded">
                                            <div class="card-body">
                                                <div class="d-sm-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h4 class="card-title card-title-dash">KHÁCH HÀNG</h4>
                                                        <p class="card-subtitle card-subtitle-dash">Danh sách tất cả khách hàng</p>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">

                                                    <table id="example" class="table">
                                                        <thead>
                                                            <tr>
                                                              <th>Order Code</th>
                                                              <th>Tên Khách hàng</th>
                                                              <th>Trạng thái</th>
                                                              <th>Địa chỉ</th>
                                                              <th>Ngày Tạo</th>
                                                            </tr>
                                                          </thead>
                                                        <tbody>
                                                            @foreach($orders ?? '' as $item)
                                                            <tr>
                                                                <td>
                                                                {{$item->code}}
                                                                    {{-- <a href="{{asset("orders/".$item->customer_code."/re_sync")}}" class="btn btn-primary text-white me-0"><i class="icon-cloud-upload"></i></a> --}}
                                                                </td>
                                                                <td>{{$item->customerUsername}}</td>
                                                                <td>{{$item->orderStatus}}</td>
                                                                <td>
                                                                @if($item->Address_Ship["addressVerified"] === "Address validated successfully")
                                                                    <i style="color: green; font-weight: bold" class="icon-check" data-bs-toggle="tooltip" data-placement="right" data-bs-original-title="Address validated successfully" ></i>
                                                                @else
                                                                    <i style="color: red; font-weight: bold" class="icon-close" data-bs-toggle="tooltip" data-placement="right" data-bs-original-title="Address not yet validated"></i>
                                                                @endif
                                                                    {{$item->Address_Ship["street1"]}}, {{$item->Address_Ship["city"]}}, {{$item->Address_Ship["state"]}}, {{$item->Address_Ship["country"]}}, {{$item->Address_Ship["postalCode"]}}</td>

                                                                {{-- <td class="text-danger"> 28.76% <i class="ti-arrow-down"></i></td> --}}
                                                                <td>{{ \Carbon\Carbon::parse($item->date_sign)->format('d/m/Y') }}</td>
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

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('third_party_scripts')
        <script>
            $(document).ready(function() {
                function filterGlobal(table) {
                    let filter = document.querySelector('#global_filter');
                    table.search(filter.value).draw();
                }

                function filterColumn(table, i) {
                    let filter = document.querySelector('#col' + i + '_filter');
                    let filterValue = filter.value;

                    // If the filter value is empty (i.e., "All" selected), clear the search for this column
                    if (filterValue === "") {
                        table.column(i).search("").draw();
                    } else {
                        table.column(i).search('^' + filterValue + '$', true, false).draw();
                    }
                }

                let table = $('#example').DataTable({
                    dom: 'lrtip', // Loại bỏ ô tìm kiếm mặc định
                    lengthChange: false, // Tắt tùy chọn số lượng hiển thị
                    pageLength: 50 // Đặt mặc định là 50
                });

                // Global filter
                document.querySelector('#global_filter').addEventListener('keyup', () => filterGlobal(table));

                // Column filters
                document.querySelectorAll('select.column_filter').forEach((el) => {
                    let columnIndex = el.closest('th') ? el.closest('th').getAttribute('data-column') : el
                        .getAttribute('id').replace('col', '').replace('_filter', '');
                    el.addEventListener('change', () => filterColumn(table, columnIndex));
                });
            });
        </script>
        @if (session('success'))
            <script>
                showSuccessToast('{{ session('success') }}')
            </script>
        @endif
        <script src="{{ asset('assets/js/dashboard.js') }}"></script>
        <script>
            $(function() {
                $('[data-bs-toggle="tooltip"]').tooltip()
            })
        </script>
    @endpush
@endsection



{{-- @extends('admin.layouts.app')

@section('content')
    @php
        use Carbon\Carbon;
    @endphp
    <div class="row">
        <div class="col-sm-12">
            <div class="home-tab">
                <div
                    style='font-family: "Manrope", sans-serif;font-style: normal;font-weight: bold;font-size: 15px; line-height: 22px;color: #8D8D8D;margin-bottom: 0;'>
                    Tìm kiếm:</div>
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <div class="filter-container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <!-- Global Filter -->
                                <input type="text" id="global_filter" class="global_filter" placeholder="Global Search">
                            </li>
                            <!-- Column Filters -->
                            <li class="nav-item">
                                <select id="col0_filter" class="column_filter">
                                    <option value="">All</option>
                                    <option value="John Doe">John Doe</option>
                                    <option value="Jane Smith">Jane Smith</option>
                                    <!-- Add other options as needed -->
                                </select>
                            </li>
                            <li class="nav-item">
                                <select id="col1_filter" class="column_filter">
                                    <option value="">All</option>
                                    <option value="john.doe@example.com">john.doe@example.com</option>
                                    <option value="jane.smith@example.com">jane.smith@example.com</option>
                                    <!-- Add other options as needed -->
                                </select>
                            </li>
                            <li class="nav-item">
                                <select id="col2_filter" class="column_filter">
                                    <option value="">All</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content tab-content-basic">
                    <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all">
                        <div class="row">
                            <div class="col-lg-12 d-flex flex-column">
                                <div class="row flex-grow">
                                    <div class="col-12 grid-margin stretch-card">
                                        <div class="card card-rounded">
                                            <div class="card-body">
                                                <div class="d-sm-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h4 class="card-title card-title-dash">KHÁCH HÀNG</h4>
                                                        <p class="card-subtitle card-subtitle-dash">Danh sách tất cả khách
                                                            hàng</p>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">

                                                    <table id="example" class="table">
                                                        <thead>
                                                            <tr>
                                                                <th data-column="0">Name</th>
                                                                <th data-column="1">Email</th>
                                                                <th data-column="2">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>John Doe</td>
                                                                <td>john.doe@example.com</td>
                                                                <td>Active</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Jane Smith</td>
                                                                <td>jane.smith@example.com</td>
                                                                <td>Inactive</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Michael Johnson</td>
                                                                <td>michael.johnson@example.com</td>
                                                                <td>Active</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Emily Davis</td>
                                                                <td>emily.davis@example.com</td>
                                                                <td>Inactive</td>
                                                            </tr>
                                                            <tr>
                                                                <td>William Brown</td>
                                                                <td>william.brown@example.com</td>
                                                                <td>Active</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Jessica Williams</td>
                                                                <td>jessica.williams@example.com</td>
                                                                <td>Inactive</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Christopher Jones</td>
                                                                <td>christopher.jones@example.com</td>
                                                                <td>Active</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Amanda Garcia</td>
                                                                <td>amanda.garcia@example.com</td>
                                                                <td>Inactive</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Matthew Miller</td>
                                                                <td>matthew.miller@example.com</td>
                                                                <td>Active</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Sarah Wilson</td>
                                                                <td>sarah.wilson@example.com</td>
                                                                <td>Inactive</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
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
        <script>
            $(document).ready(function() {
                function filterGlobal(table) {
                    let filter = document.querySelector('#global_filter');
                    table.search(filter.value).draw();
                }

                function filterColumn(table, i) {
                    let filter = document.querySelector('#col' + i + '_filter');
                    let filterValue = filter.value;

                    // If the filter value is empty (i.e., "All" selected), clear the search for this column
                    if (filterValue === "") {
                        table.column(i).search("").draw();
                    } else {
                        table.column(i).search('^' + filterValue + '$', true, false).draw();
                    }
                }

                let table = $('#example').DataTable({
                    dom: 'lrtip', // Loại bỏ ô tìm kiếm mặc định
                    lengthChange: false, // Tắt tùy chọn số lượng hiển thị
                    pageLength: 50 // Đặt mặc định là 50
                });

                // Global filter
                document.querySelector('#global_filter').addEventListener('keyup', () => filterGlobal(table));

                // Column filters
                document.querySelectorAll('select.column_filter').forEach((el) => {
                    let columnIndex = el.closest('th') ? el.closest('th').getAttribute('data-column') : el
                        .getAttribute('id').replace('col', '').replace('_filter', '');
                    el.addEventListener('change', () => filterColumn(table, columnIndex));
                });
            });
        </script>
        @if (session('success'))
            <script>
                showSuccessToast('{{ session('success') }}')
            </script>
        @endif
        <script src="{{ asset('assets/js/dashboard.js') }}"></script>
        <script>
            $(function() {
                $('[data-bs-toggle="tooltip"]').tooltip()
            })
        </script>
    @endpush
@endsection --}}
