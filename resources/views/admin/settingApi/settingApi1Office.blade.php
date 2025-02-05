

@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="home-tab">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <ul class="nav nav-tabs" role="tablist">
                        {{-- <li class="nav-item">
              <a class="nav-link active ps-0" id="home-tab" data-bs-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Tổng quát</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#audiences" role="tab" aria-selected="false">Sapo API</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#demographics" role="tab" aria-selected="false">KiotViet API</a>
            </li>
            <li class="nav-item">
              <a class="nav-link border-0" id="more-tab" data-bs-toggle="tab" href="#more" role="tab" aria-selected="false">Misa API</a>
            </li> --}}
                    </ul>
                    <div>
                        <div class="btn-wrapper">
                            <a id="add" name="add" class="btn btn-primary text-white"><i class="icon-plus"></i> Thêm
                                Mới</a>
                            <a id="cancel" name="cancel" class="btn btn-otline-dark align-items-center"><i
                                    class="icon-trash"></i> Hủy</a>
                            <a href="#" class="btn btn-otline-dark me-0"><i class="icon-info"></i> Hướng dẫn</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div style="margin-top:25px;" class="row">
        <div style="display: block;" id="add-form" class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Thông tin cấu hình API 1Office</h4>
                    <p class="card-description">
                        Cấu hình thông tin kết nối và các trường dữ liệu tuỳ biến trong phần mềm 1Offce
                    </p>
                    <form method="POST" class="form-submit" action="/update-api-1office">
                        @csrf
                        @foreach($data as $key => $value)
                        <div class="form-group">
                            <label for="{{ $key }}" data-i18n="{{ $key }}">{{ $key }}</label>
                            @if($key === "1OFFICE_ACCESS_TOKEN")
                                <input type="password" class="form-control form-control-lg" id="{{ $key }}" name="{{ $key }}" value="{{ $value }}"
                                    placeholder="{{ $key }}" data-i18n="{{ $key }}">
                            @else
                                <input type="text" class="form-control form-control-lg" id="{{ $key }}" name="{{ $key }}" value="{{ $value }}"
                                    placeholder="{{ $key }}" data-i18n="{{ $key }}">
                            @endif
                        </div>
                        @endforeach

                        <button type="submit" class="btn btn-primary me-2">Cập nhật</button>
                        <button class="btn btn-light">Nhập lại</button>
                    </form>
                </div>
            </div>
        </div>
        @if (Session::has('success'))
            <script>
                showSuccessToast();
            </script>
        @endif
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const locale = 'vi'; // Định ngôn ngữ bạn muốn sử dụng
                const elements = document.querySelectorAll('[data-i18n]');
                fetch(`/translations/${locale}`)
                    .then(response => response.json())
                    .then(translations => {
                        elements.forEach(element => {
                            const key = element.getAttribute('data-i18n');
                            if (translations[key]) {

                                element.textContent = translations[key];
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching translations:', error);
                    });
            });
        </script>

    </div>
    @push('third_party_scripts')
        <script>
            // Lấy tất cả các phần tử select có thuộc tính name là "type_auth" và tất cả các phần tử input có thuộc tính id là "link_connect"
            const selectElements = document.querySelectorAll('select[name="type_auth"]');
            const inputElements = document.querySelectorAll('input[id="link_connect"]');

            // Lặp qua mỗi phần tử select và thêm sự kiện change
            selectElements.forEach((selectElement) => {
                selectElement.addEventListener("change", function() {
                    // Lấy giá trị đã chọn của select
                    const selectedValue = selectElement.value;
                    // Tìm form gần nhất của select đã chọn
                    const form = selectElement.closest("form");
                    // Tìm input có thuộc tính id là "link_connect" trong form tìm được
                    const inputElement = form.querySelector('input[id="link_connect"]');
                    // Nếu giá trị đã chọn là "oauth", bỏ chặn input "link_connect". Ngược lại, chặn input "link_connect".
                    if (selectedValue === "oauth") {
                        inputElement.disabled = false;
                    } else {
                        inputElement.disabled = true;
                    }
                });
            });
        </script>

        <script>
            // // Lắng nghe sự kiện click trên thẻ a có id "add"
            // document.getElementById("add").addEventListener("click", function() {
            //     // Hiển thị phần tử chứa đoạn mã HTML
            //     document.getElementById("add-form").style.display = "block";
            // });

            // // Lắng nghe sự kiện click trên thẻ a có id "cancel"
            // document.getElementById("cancel").addEventListener("click", function() {
            //     // Ẩn phần tử chứa đoạn mã HTML
            //     document.getElementById("add-form").style.display = "none";
            // });
        </script>
        @if (session('success'))
            <script>
                showSuccessToast('{{ session('success') }}')
            </script>
        @endif
    @endpush
@endsection

