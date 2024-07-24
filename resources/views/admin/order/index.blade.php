@php use App\Enums\OrderStatus;use App\Enums\PaymentStatus; @endphp

@extends('admin.layouts.master')

@section('title', 'Quản Lý Đơn Hàng')

@section('embed-css')
    <link href="{{ asset('plugins/datatables/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('custom-css')
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

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Quản Lý Đơn Hàng</li>
    </ol>
@endsection

@section('content')

    <!-- Main row -->
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="row filter-container">
                        <div class="col-md-8">
                            <div class="form-group">
                                <input class="form-control" id="search" type="text"
                                       placeholder="Tìm kiếm theo tiêu đề...">

                                <select class="form-control" id="status">
                                    <option value="">Trạng Thái Thực Hiện</option>
                                    @foreach(OrderStatus::cases() as $orderStatus)
                                        <option value="{{ $orderStatus->value }}">{{ $orderStatus->label() }}</option>
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

                                <select class="form-control" id="paymentMethod">
                                    <option value="">Phương Thức Thanh Toán</option>
                                    @foreach($payment_methods as $paymentMethod)
                                        <option value="{{ $paymentMethod->id ?? '' }}">{{ $paymentMethod->name ?? '' }}</option>
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
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection

@section('embed-js')
    <!-- DataTables -->
    <script src="{{ asset('plugins/datatables/datatables.min.js') }}"></script>
@endsection

@section('custom-js')
    {{ $dataTable->scripts() }}

    <script>
        let debounceTimeout;
        $(function () {
            const $table = window.LaravelDataTables["orders-table"];
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
    </script>
@endsection
