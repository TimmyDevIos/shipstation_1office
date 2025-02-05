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
                                <select id="event_filter" class="column_filter">
                                    <option value="">Tác vụ</option>
                                    <option value="created">Create</option>
                                    <option value="updated">Updated</option>
                                    <option value="deleted">Deleted</option>
                                    <option value="Cập nhật trạng thái xác thực địa chỉ">Cập nhật trạng thái xác thực địa chỉ</option>
                                    <option value="Cập nhật trạng thái đơn hàng lên 1Office">Cập nhật trạng thái đơn hàng lên 1Office</option>
                                </select>
                            </li>

                            

                            
                            
                            <li class="nav-item">
                                <input type="text" id="content_filter" class="column_filter" placeholder="Nội dung">
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
                                                        <h4 class="card-title card-title-dash">ĐƠN HÀNG</h4>
                                                        <p class="card-subtitle card-subtitle-dash">Danh sách tất cả đơn hàng</p>
                                                    </div>
                                                </div>
                                                <div class="table-responsive">

                                                    <table id="example" class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Tác vụ</th>
                                                                <th>Đối tượng</th>
                                                                <th>Hành Động</th>
                                                                <th>Nội dung</th>
                                                                <th>Id đối tượng</th>
                                                                <th>Ngày Tạo</th>
                                                              </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($history ?? '' as $item)
                                                          <tr>
                                                            <td>{{ $item->event }}</td>
                                                            <td>{{ $item->auditable_type }}</td>
                                                            <td style="text-align: center">
                                                              @switch($item->event)
                                                              @case('created')
                                                              <i style="color:#F05A30; font-weight: bold; font-size: 25px" class="icon-arrow-up-circle" data-bs-toggle="tooltip" data-placement="right" data-bs-original-title="Yêu cầu tạo mới từ 1Office"></i>
                                                              @break
                                                  
                                                              @case('updated')
                                                              @if (isset($item->new_values['orderKeyShipSation']) && empty($item->old_values['orderKeyShipSation']) && !empty($item->new_values['orderKeyShipSation']))
                                                              <i style="color: #97ce64; font-weight: bold; font-size: 25px" class="icon-arrow-down-circle" data-bs-toggle="tooltip" data-placement="right" data-bs-original-title="Dữ liệu cập nhật lấy từ ShipStation"></i>
                                                              @else
                                                              <i style="color:#F05A30; font-weight: bold; font-size: 25px" class="icon-arrow-up-circle" data-bs-toggle="tooltip" data-placement="right" data-bs-original-title="Yêu cầu cập nhật từ 1Office"></i>
                                                              @endif
                                                              @break
                                                  
                                                              @case('Cập nhật trạng thái xác thực địa chỉ')
                                                              <i style="color: #97ce64; font-weight: bold; font-size: 25px" class="icon-arrow-down-circle" data-bs-toggle="tooltip" data-placement="right" data-bs-original-title="Dữ liệu cập nhật lấy từ ShipStation"></i>
                                                              @break
                                                  
                                                              @case('Cập nhật trạng thái đơn hàng lên 1Office')
                                                              <i style="color: #97ce64; font-weight: bold; font-size: 25px" class="icon-arrow-down-circle" data-bs-toggle="tooltip" data-placement="right" data-bs-original-title="Dữ liệu cập nhật lấy từ ShipStation"></i>
                                                              @break
                                                  
                                                              @case('deleted')
                                                              <i style="color:#F05A30; font-weight: bold; font-size: 25px" class="icon-arrow-up-circle" data-bs-toggle="tooltip" data-placement="right" data-bs-original-title="Yêu cầu xoá từ 1Office"></i>
                                                              @break
                                                  
                                                              @default
                                                              @endswitch
                                                            </td>
                                                            <td>
                                                              @switch($item->event)
                                                              @case('created')
                                                              <strong>Tạo mới</strong>
                                                              <p><i>ID: {{ $item->auditable_id }} - {{ $item->new_values['code'] }}</i></p>
                                                              @break
                                                  
                                                              @case('updated')
                                                              <p>Nội dung chỉnh sửa:<br />
                                                                @foreach (array_keys($item->new_values) as $key)
                                                                @php
                                                                $value = $item->new_values[$key];
                                                                $cleanJsonString = stripcslashes($value);
                                                                $decodedArray = json_decode($cleanJsonString, true);
                                                                $isJson = json_last_error() === JSON_ERROR_NONE && is_array($decodedArray);
                                                                @endphp
                                                                @if ($isJson)
                                                                <li>{{ $key }}</li>
                                                                @else
                                                                @php
                                                                $oldValue = $item->old_values[$key] ?? '';
                                                                $newValue = $item->new_values[$key];
                                                  
                                                                if (strlen($oldValue) > 30) {
                                                                $oldValue = substr($oldValue, 0, 30) . '[...]';
                                                                $oldValue = htmlspecialchars($oldValue);
                                                                }
                                                  
                                                                if (strlen($newValue) > 30) {
                                                                $newValue = substr($newValue, 0, 30) . '[...]';
                                                                $newValue = htmlspecialchars($newValue);
                                                                }
                                                                @endphp
                                                  
                                                                <li>{{ $key }} từ <strong>{{ empty($oldValue) ? 'null' : $oldValue }}</strong> sang <strong>{{ $newValue }}</strong></li>
                                                                @endif
                                                                @endforeach
                                                              </p>
                                                              @break
                                                  
                                                              @case('Cập nhật trạng thái xác thực địa chỉ')
                                                              <strong>Cập nhật trạng thái xác thực địa chỉ</strong>
                                                              <p><i>Trạng thái: {{ $item->new_values['addressVerified'] }}</i></p>
                                                              @break
                                                  
                                                              @case('Cập nhật trạng thái đơn hàng lên 1Office')
                                                              <strong>Cập nhật trạng thái đơn hàng lên 1Office</strong>
                                                              <p><i>Trạng thái: {{ $item->new_values['orderStatus'] }} - Order Nummer: {{ $item->auditable_id }}</i></p>
                                                              @break
                                                  
                                                              @case('deleted')
                                                              <strong>Xoá</strong>
                                                              <p><i>ID: {{ $item->auditable_id }} - {{ $item->old_values['code'] }}</i></p>
                                                              @break
                                                  
                                                              @default
                                                              @endswitch
                                                            </td>
                                                            <td>{{ $item->auditable_id }}</td>
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
    // Initialize DataTable
    let table = $('#example').DataTable({
        dom: 'lrtip',
        paging: true, // Bật phân trang
        lengthMenu: [10, 20, 25, 50, 100], // Số lượng bản ghi trên mỗi trang
        lengthChange: true,
        pageLength: 20,
        ordering: true, // Bật tính năng sắp xếp
        order: [], // Bỏ sắp xếp mặc định
        columns: [
            { "searchable": true }, // Tác vụ
            null, // Order Code
            null, // Hành động
            null, // Khách hàng
            null, // Nội dung
            { "type": "date", "format": "DD/MM/YYYY" } // Ngày tạo
        ]
    });

    // Global filter
    $('#global_filter').on('keyup', function() {
                    table.search(this.value).draw();
                });

                // Custom filters
                $('#content_filter').on('keyup', function() {
                    let value = this.value.trim(); // Lấy giá trị và xóa khoảng trắng đầu cuối

                    if (value === '') {
                        table.column(3).search('').draw(); // Nếu input trống, xóa bộ lọc cho cột Order Code
                    } else {
                        table.column(3).search(value)
                    .draw(); // Nếu có giá trị, áp dụng bộ lọc cho cột Order Code
                    }
                });

                $('#event_filter').on('change', function() {
                    let val = $.fn.dataTable.util.escapeRegex(
                        $(this).val()
                    );
                    table.column(0).search(val ? '^' + val + '$' : '', true, false).draw();
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
                $('#content_filter, #event_filter').on('keyup change', function() {
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







