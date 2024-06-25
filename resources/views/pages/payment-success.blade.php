@extends('layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('common/css/normalize.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/bootstrap-theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/fontawesome/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/sweetalert2.min.css') }}">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endsection
@section('content')
    <section class="bread-crumb">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đặt hàng</li>
            </ol>
        </nav>
    </section>

    <div class="row">
        <div class="col-lg-8 col-md-7 col-sm-6 col-xs-12">
            Thanh toán thành công
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('common/js/jquery-3.3.1.js') }}"></script>
    <script src="{{ asset('common/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('common/js/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>

    <!-- Custom Scripts -->
    <script src="{{ asset('js/checkout.js') }}"></script>
@endsection
