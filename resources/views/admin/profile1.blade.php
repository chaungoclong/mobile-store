@extends('admin.layouts.master')

@php $user = auth()?->user(); @endphp

@section('title', 'Profile')

@section('embed-css')

@endsection

@section('custom-css')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('admin.post.index') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i> Quản Lý Bài
                Viết</a></li>
        <li class="active">Profile</li>
    </ol>
@endsection

@section('content')

    @if ($errors->any())
        <div class="callout callout-danger">
            <h4>Warning!</h4>
            <ul style="margin-bottom: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- General Information Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Thông tin chung</h3>
                </div>
                <form id="generalInfoForm" action="{{ route('admin.profile.update2') }}" method="post"
                      enctype="multipart/form-data">
                    @method('put')
                    @csrf
                    <div class="box-body">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" value="{{ $user?->email ?? '' }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="name">Tên</label>
                            <input type="text" class="form-control" id="name" name="name"
                                   value="{{ old('name', $user->name ?? '') }}">
                            @error('name')
                            <div class="text-red">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="avatar">Avatar</label>
                            <div class="file-upload-wrapper">
                                <input type="file" class="form-control" id="avatar" name="avatar_image" onchange="previewImage(event)">
                                @error('avatar_image')
                                <div class="text-red">{{ $message }}</div>
                                @enderror
                                <div class="file-upload-preview">
                                    <img class=" w-64 mt-1 rounded-md object-cover" id="avatar-preview"
                                         src="{{ Helper::get_image_avatar_url($user->avatar_image ?? '') }}" alt="Rounded avatar">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>

            <!-- Change Password Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Đổi mật khẩu</h3>
                </div>
                <form id="changePasswordForm" action="{{ route('admin.profile.changePassword') }}" method="post">
                    @csrf
                    @method('put')
                    <div class="box-body">
                        <div class="form-group">
                            <label for="currentPassword">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="currentPassword"
                                   placeholder="Nhập mật khẩu hiện tại"
                                   required name="current_password">
                            @error('current_password')
                            <div class="text-red">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="newPassword">Mật khẩu mơi </label>
                            <input type="password" class="form-control" id="newPassword"
                                   placeholder="Enter new password" required name="new_password">
                            @error('new_password')
                            <div class="text-red">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="confirmPassword"
                                   placeholder="Confirm new password" required name="new_password_confirmation">
                            @error('new_password_confirmation')
                            <div class="text-red">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </section>
@endsection

@section('embed-js')

    <!-- include tinymce js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.0.15/tinymce.min.js"></script>
@endsection

@section('custom-js')
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                const output = document.getElementById('avatar-preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        $(document).ready(function () {
            $("#upload").change(function () {
                $('.upload-image .image-preview').css('background-image', 'url("' + getImageURL(this) + '")');
            });
        });

        function getImageURL(input) {
            return URL.createObjectURL(input.files[0]);
        };
    </script>
@endsection
