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

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Thông tin đặt hàng</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="form-group">
                                <label>Mã đơn hàng:</label>
                                <p class="form-control-static">{{ $_GET['vnp_TxnRef'] }}</p>
                            </div>
                            <div class="form-group">
                                <label>Số tiền:</label>
                                <p class="form-control-static">{{ $_GET['vnp_Amount'] }}</p>
                            </div>
                            <div class="form-group">
                                <label>Nội dung thanh toán:</label>
                                <p class="form-control-static">{{ $_GET['vnp_OrderInfo'] }}</p>
                            </div>
                            <div class="form-group">
                                <label>Mã GD Tại VNPAY:</label>
                                <p class="form-control-static">{{ $_GET['vnp_TransactionNo'] }}</p>
                            </div>
                            <div class="form-group">
                                <label>Mã Ngân hàng:</label>
                                <p class="form-control-static">{{ $_GET['vnp_BankCode'] }}</p>
                            </div>
                            <div class="form-group">
                                <label>Thời gian thanh toán:</label>
                                <p class="form-control-static">{{ $_GET['vnp_PayDate'] }}</p>
                            </div>
                            <div class="text-center mt-4">
                                <a href="{{ route('home_page') }}" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Quay lại mua sắm tiếp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
