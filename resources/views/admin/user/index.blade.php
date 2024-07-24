@extends('admin.layouts.master')

@section('title', 'Quản Lý Khách Hàng')

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
        <li class="active">Quản Lý Khách Hàng</li>
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
                            <input class="form-control" id="search" type="text"
                                   placeholder="Tìm kiếm theo tiêu đề..." name="search">

                            <select class="form-control" id="status" name="status">
                                <option value="">Trạng Thái</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive
                                </option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                        <div class="col-md-4 action-container">

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
    <!-- /.modal -->
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
            const $table = window.LaravelDataTables["customers-table"];
            $("#search").on(
                "input",
                debounce(function () {
                    $table.ajax.reload();
                }, 650)
            );

            $("#status, #perPage").on("change", () => {
                $table.ajax.reload();
            });

            $(document).on('click', '.deleteDialog', function () {
                const userId = $(this).data('id');
                const url = $(this).data('url');
                Swal.fire({
                    title: "Thông báo?",
                    text: "Bạn có muốn xóa khách hàng này!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Có!",
                    cancelButtonText: "Không!",
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            body: JSON.stringify({'user_id': userId}),
                        });

                        const json = await response.json();

                        if (response.ok) {
                            Toast.fire({
                                title: json['content'],
                                icon: 'success'
                            });

                            $table.ajax.reload(null, false);
                        } else {
                            Toast.fire({
                                title: json['content'],
                                icon: 'error'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
