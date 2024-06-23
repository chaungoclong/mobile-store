@extends('admin.layouts.master')

@section('title', 'Thêm Danh Mục Mới')

@section('embed-css')
    <!-- include Bootstrap File Input -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.6/css/fileinput.min.css"
          rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.6/themes/explorer-fa/theme.css"
          rel="stylesheet">
    <!-- daterange picker -->
    <link rel="stylesheet"
          href="{{ asset('AdminLTE/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection

@section('custom-css')
    <style>
        span.error {
            display: block;
            margin-top: 5px;
            margin-bottom: 10px;
            color: red;
        }

        input.error,
        select.error {
            border-color: #9fda58;
            box-shadow: none;
        }
    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('admin.product.index') }}"><i class="fa fa-product-hunt" aria-hidden="true"></i> Quản Lý
                Sản Phẩm</a></li>
        <li class="active">Thêm Danh Mục Mới</li>
    </ol>
@endsection

@section('content')
    <div class="content">
        <div class="card bg-white">
            <!-- /.card-header -->
            <div class="card-body">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Tên</label>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                               required>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="slug">Slug</label>
                        <input type="text" id="slug" name="slug"
                               class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}">
                        @error('slug')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả</label>
                        <textarea id="description" name="description"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" id="logo"
                                   name="logo">
                            @error('logo')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select id="status" name="status" class="form-control @error('status') is-invalid @enderror"
                                required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                        @error('status')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="is_featured">Danh mục nổi bật</label>
                        <div class="custom-control custom-switch">
                            <input type="radio" class="custom-control-input" id="is_featured"
                                   name="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_featured"></label>
                        </div>
                        @error('is_featured')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Lưu</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
@endsection

@section('embed-js')
    <!-- include tinymce js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.0.15/tinymce.min.js"></script>
    <!-- include jquery.repeater -->
    <script src="{{ asset('AdminLTE/bower_components/jquery.repeatable.js') }}"></script>
    <!-- include Bootstrap File Input -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.6/js/fileinput.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.6/themes/explorer-fa/theme.js"></script>
    <!-- date-range-picker -->
    <script src="{{ asset('AdminLTE/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <!-- autoNumeric -->
    <script src="{{ asset('AdminLTE/bower_components/autoNumeric.js') }}"></script>
    <!-- Jquery Validate -->
    <script src="{{ asset('AdminLTE/bower_components/jquery-validate/jquery.validate.js') }}"></script>
@endsection

@section('custom-js')
    <script>

    </script>
@endsection
