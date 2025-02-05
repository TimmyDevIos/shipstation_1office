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
        <div style="display: none;" id="add-form" class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Thêm Mới</h4>
                    <p class="card-description">
                        Thêm mới thông tin kết nối.
                    </p>
                    <form method="POST" class="form-submit" action="{{ asset('/settings/api/add') }}">
                        @csrf
                        <div class="form-group">
                            <label for="connection_name">Tên kết nối</label>
                            <input type="text" class="form-control form-control-lg" id="connection_name"
                                name="connection_name" placeholder="Tên kết nối">
                        </div>

                        <div class="form-group">
                            <label for="type_auth">Phương thức xác thực</label>
                            <select class="form-control form-control-lg" id="type_auth" name="type_auth">
                                <option value="token">Access Token</option>
                                <option value="oauth">OAuth 2.0</option>
                                <option value="basic">Basic OAuth</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="retailer">Link connect</label>
                            <input type="text" class="form-control form-control-lg" id="link_connect" name="link_connect"
                                placeholder="Link connect">
                        </div>
                        <div class="form-group">
                            <label for="client-id">Client ID</label>
                            <input type="text" class="form-control form-control-lg" id="client_id" name="client_id"
                                placeholder="Client ID">
                        </div>
                        <div class="form-group">
                            <label for="token">Mã bảo mật</label>
                            <input type="text" class="form-control form-control-lg" id="token" name="token"
                                placeholder="Mã bảo mật">
                        </div>
                        <div class="form-group">
                            <label for="token">Scopes</label>
                            <input type="scopes" class="form-control form-control-lg" id="scopes" name="scopes"
                                placeholder="Scopes">
                        </div>
                        <div class="form-group">
                            <label for="retailer">Retailer</label>
                            <input type="text" class="form-control form-control-lg" id="retailer" name="retailer"
                                placeholder="Retailer">
                        </div>
                        <div class="form-check form-check-flat form-check-primary">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" id="active" name="active">
                                Hoạt động
                                <i class="input-helper"></i></label>
                        </div>
                        <button type="submit" class="btn btn-primary me-2">Thêm mới</button>
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
        @foreach ($API_connection as $item)
            <div class="col-md-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ $item->connection_name }}</h4>
                        <p class="card-description">
                            Thông tin kết nối API với Sapo, hướng dẫn chi tiết <a href="http://google.com">tại đây</a>.
                        </p>
                        <form method="POST" class="form-submit" id={{ $item->id }}
                            action="{{ asset('/settings/api/' . $item->id . '/update') }}" data-form="1office_api">
                            @csrf
                            <div class="form-group">
                                <label for="connection_name">Tên kết nối</label>
                                <input type="text" class="form-control form-control-lg"
                                    value="{{ $item->connection_name }}" id="connection_name" name="connection_name"
                                    placeholder="Tên kết nối">
                            </div>

                            <div class="form-group">
                                <label for="type_auth">Phương thức xác thực</label>
                                <select class="form-control form-control-lg" id="type_auth" name="type_auth">
                                    <option value="token" {{ $item->type_auth == 'token' ? 'selected' : '' }}>Access Token
                                    </option>
                                    <option value="oauth" {{ $item->type_auth == 'oauth' ? 'selected' : '' }}>OAuth 2.0
                                    </option>
                                    <option value="basic" {{ $item->type_auth == 'basic' ? 'selected' : '' }}>Basic OAuth
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="retailer">Link connect</label>
                                <input type="text" {{ $item->type_auth == 'oauth' ? '' : 'disabled' }}
                                    class="form-control form-control-lg" id="link_connect" name="link_connect"
                                    placeholder="Link connect" value="{{ $item->link_connect }}">
                            </div>
                            <div class="form-group">
                                <label for="client-id">Client ID</label>
                                <input type="text" class="form-control form-control-lg" id="client_id"
                                    name="client_id" placeholder="Client ID" value="{{ $item->client_id }}">
                            </div>
                            <div class="form-group">
                                <label for="token">Mã bảo mật</label>
                                <input type="text" class="form-control form-control-lg" id="token" name="token"
                                    placeholder="Mã bảo mật" value="{{ $item->token }}">
                            </div>
                            <div class="form-group">
                                <label for="token">Scopes</label>
                                <input type="scopes" class="form-control form-control-lg" id="scopes" name="scopes"
                                    placeholder="Scopes" value="{{ $item->scopes }}">
                            </div>
                            <div class="form-group">
                                <label for="retailer">Retailer</label>
                                <input type="text" class="form-control form-control-lg" id="retailer"
                                    name="retailer" placeholder="Retailer" value="{{ $item->retailer }}">
                            </div>
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" id="active"
                                        name="active" {{ $item->active == true ? 'checked' : '' }}>
                                    Hoạt động
                                    <i class="input-helper"></i></label>
                            </div>
                            <button type="submit" class="btn btn-primary me-2">Cập nhật</button>
                            <button class="btn btn-light">Hủy</button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
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
            // Lắng nghe sự kiện click trên thẻ a có id "add"
            document.getElementById("add").addEventListener("click", function() {
                // Hiển thị phần tử chứa đoạn mã HTML
                document.getElementById("add-form").style.display = "block";
            });

            // Lắng nghe sự kiện click trên thẻ a có id "cancel"
            document.getElementById("cancel").addEventListener("click", function() {
                // Ẩn phần tử chứa đoạn mã HTML
                document.getElementById("add-form").style.display = "none";
            });
        </script>
        @if (session('success'))
            <script>
                showSuccessToast('{{ session('success') }}')
            </script>
        @endif
    @endpush
@endsection
