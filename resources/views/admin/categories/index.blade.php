@extends('admin.layouts.master')

@section('title', 'Quản Lý Danh mục')

@section('embed-css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://cdn.datatables.net/v/bs-3.3.7/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/sp-2.3.1/sl-2.0.3/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
@endsection

@section('custom-css')
    <style>
        #advertise-table td,
        #advertise-table th {
            vertical-align: middle !important;
        }

        #advertise-table span.status-label {
            display: block;
            width: 85px;
            text-align: center;
            padding: 2px 0px;
        }

        #search-input span.input-group-addon {
            padding: 0;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 34px;
            border: none;
            background: none;
        }

        #search-input span.input-group-addon i {
            font-size: 18px;
            line-height: 34px;
            width: 34px;
            color: #9fda58;
        }

        #search-input input {
            position: static;
            width: 100%;
            font-size: 15px;
            line-height: 22px;
            padding: 5px 5px 5px 34px;
            float: none;
            height: unset;
            border-color: #fbfbfb;
            box-shadow: none;
            background-color: #e8f0fe;
            border-radius: 5px;
        }
    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{ route('admin.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Quản Lý Danh mục</li>
    </ol>
@endsection

@section('content')

    <!-- Main row -->
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="row">
                        <div class="col-md-5 col-sm-6 col-xs-6">
                            <div id="search-input" class="input-group">
                                <span class="input-group-addon"><i class="fa fa-search" aria-hidden="true"></i></span>
                                <input type="text" class="form-control" placeholder="Tìm kiếm theo tên, slug..."
                                       id="searchInput">
                            </div>
                        </div>
                        <div class="col-md-7 col-sm-6 col-xs-6">
                            <div class="btn-group pull-right">
                                <a href="{{ route('admin.categories.create') }}" class="btn btn-success btn-flat"
                                   title="Thêm Mới">
                                    <i class="fa fa-plus" aria-hidden="true"></i><span
                                        class="hidden-xs"> Thêm Mới</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    {{ $dataTable->table() }}
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script
        src="https://cdn.datatables.net/v/bs-3.3.7/jszip-3.10.1/dt-2.0.8/b-3.0.2/b-colvis-3.0.2/b-html5-3.0.2/b-print-3.0.2/sp-2.3.1/sl-2.0.3/sr-1.4.1/datatables.min.js"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('AdminLTE/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('AdminLTE/bower_components/fastclick/lib/fastclick.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-euro.js"></script>
@endsection

@section('custom-js')
    {{ $dataTable->scripts() }}

    <script>
        let debounceTimeout;

        $(function () {
            const $table = window.LaravelDataTables["categories-table"];
            $("#searchInput").on(
                "input",
                debounce(function () {
                    $table.ajax.reload();
                }, 650)
            );

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

                            $table.ajax.reload();
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



