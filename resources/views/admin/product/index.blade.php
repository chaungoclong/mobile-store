@php use App\Enums\OrderStatus;use App\Enums\PaymentStatus;use App\Enums\StockStatus; @endphp
@extends('admin.layouts.master')

@section('title', 'Quản Lý Sản Phẩm')

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
        <li class="active">Quản Lý Sản Phẩm</li>
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

                                <select class="form-control" id="stock_status">
                                    <option value="">Trạng Thái Tồn Kho</option>
                                    @foreach(StockStatus::cases() as $stockStatus)
                                        <option
                                            value="{{ $stockStatus->value }}">{{ $stockStatus->label() }}</option>
                                    @endforeach
                                    <!-- Add more options as needed -->
                                </select>

                                <select class="form-control" id="category_id">
                                    <option value="">Danh Mục</option>
                                    @foreach($categories as $label => $id)
                                        <option
                                            value="{{ $id }}">{{ $label }}</option>
                                    @endforeach
                                    <!-- Add more options as needed -->
                                </select>

                                <select class="form-control" id="producer_id">
                                    <option value="">Hãng Sản Xuất</option>
                                    @foreach($producers as $label => $id)
                                        <option
                                            value="{{ $id }}">{{ $label }}</option>
                                    @endforeach
                                    <!-- Add more options as needed -->
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 action-container">
                            <a class="btn btn-success" href="{{ route('admin.product.new') }}">Tạo mới</a>
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
            const $table = window.LaravelDataTables["products-table"];
            $("#search").on(
                "input",
                debounce(function () {
                    $table.ajax.reload();
                }, 650)
            );

            $("#stock_status, #category_id, #producer_id").on("change", () => {
                $table.ajax.reload();
            });
        });

        $(document).ready(function () {

            $(document).on('click', '.deleteDialog', function () {

                var product_id = $(this).attr('data-id');
                var url = $(this).attr('data-url');

                Swal.fire({
                    type: 'question',
                    title: 'Thông báo',
                    text: 'Bạn có chắc muốn xóa sản phẩm này?',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            body: JSON.stringify({'product_id': product_id}),
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText);
                                }
                                return response.json();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(error);

                                Swal.update({
                                    icon: 'error',
                                    title: 'Lỗi!',
                                    text: '',
                                    showConfirmButton: false,
                                    cancelButtonText: 'Ok',
                                });
                            })
                    },
                }).then((result) => {
                    if (result.value) {
                        Swal.fire({
                            icon: result.value.type,
                            title: result.value.title,
                            text: result.value.content,
                        }).then((result) => {
                            if (result.value)
                                location.reload(true);
                        });
                    }
                })
            });
        })
        ;
    </script>
@endsection
