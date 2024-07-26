@extends('layouts.master')

@php use App\Enums\OrderStatus;use App\Enums\PaymentStatus;use Illuminate\Support\Facades\Request; @endphp

@section('title', 'Đơn Hàng')

@section('content')

    <section class="bread-crumb">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Đơn Hàng</li>
            </ol>
        </nav>
    </section>

    <div class="site-orders">
        <section class="section-orders">
            <div class="section-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row filter-container">
                            <div class="col-md-12" style="display: flex; align-items: center;">
                                <div class="form-group">
                                    <input class="form-control" id="search" type="text"
                                           placeholder="Tìm kiếm theo tiêu đề...">

                                    <select class="form-control" id="status">
                                        <option value="">Trạng Thái Thực Hiện</option>
                                        @foreach(OrderStatus::cases() as $orderStatus)
                                            <option
                                                value="{{ $orderStatus->value }}">{{ $orderStatus->label() }}</option>
                                        @endforeach
                                        <!-- Add more options as needed -->
                                    </select>

                                    <select class="form-control" id="paymentMethod">
                                        <option value="">Phương Thức Thanh Toán</option>
                                        @foreach($payment_methods as $paymentMethod)
                                            <option
                                                value="{{ $paymentMethod->id ?? '' }}">{{ $paymentMethod->name ?? '' }}</option>
                                        @endforeach
                                        <!-- Add more options as needed -->
                                    </select>

                                    <select class="form-control" id="paymentStatus">
                                        <option value="">Trạng Thái Thanh Toán</option>
                                        @foreach(PaymentStatus::cases() as $paymentStatus)
                                            <option
                                                value="{{ $paymentStatus->value }}">{{ $paymentStatus->labelText() }}</option>
                                        @endforeach
                                        <!-- Add more options as needed -->
                                    </select>


                                </div>
                            </div>
                        </div>
                        <hr style="background: gray;">
                        <div class="row">
                            <div class="col-md-12 table-container">
                                {{ $dataTable->table() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@section('css')
    <link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
    <style>
        .table-container {
            margin-top: 20px;
        }

        .pagination-container {
            text-align: left;
        }

        .filter-container {
            margin-bottom: 10px;
        }

        .filter-container .form-control,
        .filter-container .btn {
            display: inline-block;
            width: auto;
            vertical-align: middle;
        }

        .filter-container .form-control {
            margin-right: 10px;
        }

        .filter-container .items-per-page {
            width: 100px;
        }

        .action-container {
            text-align: right;
        }

        .badge {
            padding: 5px 10px;
        }
    </style>
@endsection

@section('js')
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
    {{ $dataTable->scripts() }}
    <script>
        let debounceTimeout;
        $(function () {
            const $table = window.LaravelDataTables["orders-customer-table"];
            $("#search").on(
                "input",
                debounce(function () {
                    $table.ajax.reload();
                }, 650)
            );

            $("#status, #paymentStatus, #paymentMethod").on("change", () => {
                $table.ajax.reload();
            });
        });
        $(document).ready(function () {

            $("#slide-advertise").owlCarousel({
                items: 2,
                autoplay: true,
                loop: true,
                margin: 10,
                autoplayHoverPause: true,
                nav: true,
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                    },
                    992: {
                        items: 2,
                        animateOut: 'zoomInRight',
                        animateIn: 'zoomOutLeft',
                    }
                },
                navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>']
            });
        });
    </script>
@endsection
