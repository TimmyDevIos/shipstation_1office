@extends('admin.layouts.app')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="home-tab">
                <div class="d-sm-flex align-items-center justify-content-between border-bottom">
                    <ul class="nav nav-tabs" role="tablist">

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
        <div style="" id="add-form" class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Thêm Mới</h4>
                    <p class="card-description">
                        Thêm mới thông báo
                    </p>
                    <form action="{{ route('notification.store') }}" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="retailer">title</label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title"
                                placeholder="Retailer">
                        </div>
                        <div class="form-group">
                            <label for="retailer">content</label>
                            <input type="text" class="form-control form-control-lg" id="content" name="content"
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

    </div>
    @push('third_party_scripts')
        @if (session('success'))
            <script>
                showSuccessToast('{{ session('success') }}')
            </script>
        @endif
    @endpush
@endsection
