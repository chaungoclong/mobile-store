@extends('admin.layouts.master')

@section('title', 'Quản Lý Bài Viết')

@section('embed-css')
    <link
        href="https://cdn.datatables.net/v/bs-3.3.7/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/sp-2.3.1/sl-2.0.3/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
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
        <li class="active">Quản Lý Bài Viết</li>
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
                        </div>
                        <div class="col-md-4 action-container">
                            <a class="btn btn-success" href="{{ route('admin.categories.create') }}">Tạo mới</a>
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
    <script
        src="https://cdn.datatables.net/v/bs-3.3.7/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/sp-2.3.1/sl-2.0.3/sr-1.4.1/datatables.min.js"></script>
@endsection

@section('custom-js')
    {{ $dataTable->scripts() }}

    <script>
        let debounceTimeout;

        $(function () {
            const $table = window.LaravelDataTables["categories-table"];
            $("#search").on(
                "input",
                debounce(function () {
                    $table.ajax.reload();
                }, 650)
            );

            $("#perPage").on("change", () => {
                $table.ajax.reload();
            });

            $(document).on('click', ".deleteDialog", function () {
                const url = $(this).attr('data-url');

                Swal.fire({
                    title: 'Bạn có muốn xóa Danh mục này',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Không!',
                    confirmButtonText: 'Có!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const response = await fetch(url, {
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                'Accept': 'application/json'
                            },
                            method: 'delete',
                        });

                        const jsonResponse = await response.json();

                        if (response.ok) {
                            Toast.fire({
                                title: jsonResponse?.message,
                                icon: 'success'
                            });

                            $table.ajax.reload(null, false);
                        } else {
                            Toast.fire({
                                title: jsonResponse?.message,
                                icon: 'error'
                            });
                        }
                    }
                });
            })
        })
    </script>
@endsection
