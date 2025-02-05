





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
                                <input type="text" id="global_filter" class="global_filter" placeholder="Search">
                            </li>
                            <!-- Column Filters -->
                            <li class="nav-item">
                                <input type="text" id="order_code_filter" class="column_filter" placeholder="Order Code">
                            </li>
                            <li class="nav-item">
                                <select id="status_filter" class="column_filter">
                                    <option value="">Trạng thái</option>
                                    <option value="Label Created">Label Created</option>
                                    <option value="Success Payment">Success Payment</option>
                                    <option value="In Transit">In Transit</option>
                                    <option value="Delivered">Delivered</option>
                                </select>
                            </li>
                            {{-- <li class="nav-item">
                                <input readonly type="text" id="start_date_filter" class="date_filter" placeholder="Start Date">
                                <input readonly type="text" id="end_date_filter" class="date_filter" placeholder="End Date">
                            </li> --}}
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
                                                        <h4 class="card-title card-title-dash">ĐƠN HÀNG</h4>
                                                        <p class="card-subtitle card-subtitle-dash">Danh sách tất cả đơn hàng</p>
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
                                                            @foreach ($orders ?? '' as $item)
                                                                <tr>
                                                                    <td>
                                                                        {{ $item->code }}
                                                                        {{-- <a href="{{asset("orders/".$item->customer_code."/re_sync")}}" class="btn btn-primary text-white me-0"><i class="icon-cloud-upload"></i></a> --}}
                                                                    </td>
                                                                    <td>{{ $item->customerUsername }}</td>
                                                                    <td>{{ $item->orderStatus }}</td>
                                                                    <td>
                                                                        @if ($item->Address_Ship['addressVerified'] === 'Address validated successfully' || $item->Address_Ship['addressVerified'] === 'Address Validated')
                                                                            <i style="color: green; font-weight: bold"
                                                                                class="icon-check" data-bs-toggle="tooltip"
                                                                                data-placement="right"
                                                                                data-bs-original-title="Address validated successfully"></i>
                                                                        @else
                                                                            <i style="color: red; font-weight: bold"
                                                                                class="icon-close" data-bs-toggle="tooltip"
                                                                                data-placement="right"
                                                                                data-bs-original-title="Address not yet validated"></i>
                                                                        @endif
                                                                        {{ $item->Address_Ship['street1'] }},
                                                                        {{ $item->Address_Ship['city'] }},
                                                                        {{ $item->Address_Ship['state'] }},
                                                                        {{ $item->Address_Ship['country'] }},
                                                                        {{ $item->Address_Ship['postalCode'] }}
                                                                    </td>

                                                                    {{-- <td class="text-danger"> 28.76% <i class="ti-arrow-down"></i></td> --}}
                                                                    <td>{{ \Carbon\Carbon::parse($item->date_sign)->format('d/m/Y') }}
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

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('third_party_scripts')
        <script>
            $(document).ready(function() {
                $('#start_date_filter, #end_date_filter').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    language: 'vi', // Chọn ngôn ngữ cho datepicker (nếu có)
                    keyboardNavigation: false, // Ngăn ngừa nhập từ bàn phím
                    clearBtn: true, // Hiển thị nút xóa để xóa giá trị
                    todayHighlight: true, // Làm nổi bật ngày hiện tại
                    startDate: null, // Không có ngày bắt đầu
                    endDate: null // Không có ngày kết thúc
                });
                // Initialize DataTable
                $.fn.dataTable.moment('DD/MM/YYYY');
                // Khởi tạo DataTable với cấu hình đã chỉnh sửa
                let table = $('#example').DataTable({
                    dom: 'lrtip',
                    paging: true, // Bật phân trang
                    lengthMenu: [10, 20, 25, 50, 100], // Số lượng bản ghi trên mỗi trang
                    lengthChange: true,
                    pageLength: 20,
                    ordering: true, // Bật tính năng sắp xếp
                    columns: [
                        null, // Order Code (default search type: text)
                        null, // Tên Khách hàng (default search type: text)
                        {
                            "searchable": true
                        }, // Trạng thái (search type: select)
                        null, // Địa chỉ (default search type: text)
                        {
                            "type": "datetime-moment",
                            "format": "DD/MM/YYYY"
                        } // Ngày Tạo (custom search type: date)
                    ]
                });

                // Global filter
                $('#global_filter').on('keyup', function() {
                    table.search(this.value).draw();
                });

                // Custom filters
                $('#order_code_filter').on('keyup', function() {
                    let value = this.value.trim(); // Lấy giá trị và xóa khoảng trắng đầu cuối

                    if (value === '') {
                        table.column(0).search('').draw(); // Nếu input trống, xóa bộ lọc cho cột Order Code
                    } else {
                        table.column(0).search(value)
                    .draw(); // Nếu có giá trị, áp dụng bộ lọc cho cột Order Code
                    }
                });

                $('#status_filter').on('change', function() {
                    let val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
                    table.column(2).search(val ? '^' + val + '$' : '', true, false).draw();
                });

                // Date range filter
                function filterByDateRange(start, end) {
                    console.log('Start Date:', start, 'End Date:', end);

                    table.column(4).search(function(data, dataIndex) {
                        let rowDate = moment(data, 'DD/MM/YYYY', true);
                        console.log('Row Date:', rowDate.format('DD/MM/YYYY'));

                        let startDate = start ? moment(start, 'DD/MM/YYYY', true) : null;
                        console.log('Start Date Object:', startDate);

                        let endDate = end ? moment(end, 'DD/MM/YYYY', true) : null;
                        console.log('End Date Object:', endDate);

                        let isStartNull = startDate === null;
                        let isEndNull = endDate === null;

                        if (
                            (isStartNull && isEndNull) ||
                            (isStartNull && rowDate.valueOf() <= endDate.valueOf()) ||
                            (startDate.valueOf() <= rowDate.valueOf() && isEndNull) ||
                            (startDate.valueOf() <= rowDate.valueOf() && rowDate.valueOf() <= endDate.valueOf())
                        ) {
                            console.log(`Matched: ${data}`);
                            return true;
                        } else {
                            console.log(`Not Matched: ${data}`);
                            return false;
                        }
                    }).draw();
                }



                $('#start_date_filter, #end_date_filter').on('change', function() {
                    let start_date = $('#start_date_filter').val();
                    let end_date = $('#end_date_filter').val();
                    console.log('Start Date Filter Change:', start_date, 'End Date Filter Change:', end_date);

                    if (start_date !== '' || end_date !== '') {
                        filterByDateRange(start_date, end_date);
                    } else {
                        table.column(4).search('').draw();
                    }
                });

                // Reset filters when inputs are cleared
                $('#order_code_filter, #status_filter').on('keyup change', function() {
                    if ($(this).val() === '') {
                        table.search('').draw(); // Reset global search if filters are cleared
                    }
                });

                $('#start_date_filter, #end_date_filter').on('keyup change', function() {
                    let start_date = $('#start_date_filter').val();
                    let end_date = $('#end_date_filter').val();

                    if (start_date === '' && end_date === '') {
                        table.column(4).search('').draw(); // Reset date range filter if both inputs are cleared
                    }
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
