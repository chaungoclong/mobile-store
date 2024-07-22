@php use App\Enums\OrderStatus;use App\Enums\PaymentStatus;use Carbon\Carbon; @endphp
@extends('admin.layouts.master')

@section('title', 'Quản Lý Đơn Hàng')

@section('embed-css')
    <link rel="stylesheet"
          href="{{ asset('AdminLTE/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@endsection

@section('custom-css')
    <style>
        #order-table td,
        #order-table th {
            vertical-align: middle !important;
        }

        #order-table span.status-label {
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
        <li class="active">Quản Lý Đơn Hàng</li>
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
                                <input type="text" class="form-control" placeholder="search...">
                            </div>
                        </div>
                        <div class="col-md-7 col-sm-6 col-xs-6">
                            <div class="btn-group pull-right">
                                <a href="{{ route('admin.order.index') }}" class="btn btn-flat btn-primary"
                                   title="Refresh">
                                    <i class="fa fa-refresh"></i><span class="hidden-xs"> Refresh</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body" x-data="orderTable">
                    <table id="order-table" class="table table-hover" style="width:100%; min-width: 1024px;">
                        <thead>
                        <tr>
                            <th data-width="10px">ID</th>
                            <th data-orderable="false" data-width="85px">Mã Đơn Hàng</th>
                            <th data-orderable="false" data-width="100px">Tài Khoản</th>
                            <th data-orderable="false" data-width="100px">Tên</th>
                            <th data-orderable="false">Email</th>
                            <th data-orderable="false" data-width="70px">Điện Thoại</th>
                            <th data-orderable="false">Phương Thức Thanh Toán</th>
                            <th data-orderable="false">Trạng Thái Thanh Toán</th>
                            <th data-width="60px" data-type="date-euro">Ngày Tạo</th>
                            <th data-width="66px">Trạng Thái</th>
                            <th data-orderable="false" data-width="130px">Tác Vụ</th>
                        </tr>
                        </thead>

                        <tbody>
                        <template x-for="order in orders" :key="order.id">
                            <tr>
                                <td class="text-center" x-text="order.id"></td>
                                <td x-text="order.order_code"></td>
                                <td x-text="order.customer.name">
                                </td>
                                <td x-text="order.customer.name"></td>
                                <td x-text="order.email"></td>
                                <td x-text="order.phone"></td>
                                <td x-text="order.payment_method.name"></td>
                                <td x-text="order.payment_status == 1 ? 'Paid' : 'Unpaid'"></td>
                                <td x-text="order.created_at"></td>
                                <td x-text="order.status">
                                </td>
                                <td>

                                </td>
                            </tr>
                        </template>
                        </tbody>
                    </table>

                    <nav aria-label="...">
                        <ul class="pagination">
                            <template x-for="link in links">
                                <li x-if="link.url == null" class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" x-text="link.label"></a>
                                </li>
                            </template>
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Previous</a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">1</a></li>
                            <li class="page-item active">
                                <a class="page-link" href="#">2 <span class="sr-only">(current)</span></a>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
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
    <script src="{{ asset('AdminLTE/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('AdminLTE/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('AdminLTE/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('AdminLTE/bower_components/fastclick/lib/fastclick.js') }}"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/date-euro.js"></script>
@endsection

@section('custom-js')
    <script>
        const url = BASE_URL + '/admin/orders/fetch';

        document.addEventListener('alpine:init', () => {
            Alpine.data('orderTable', () => ({
                orders: [],
                filters: {
                    status: null,
                    payment_status: null,
                    search: null
                },
                pagination: {
                    current_page: 1,
                    per_page: 15,
                },
                links: [],
                async init() {
                    await this.fetchPage(this.pagination.current_page);
                },
                async fetchPage(page = 1) {
                    const params = new URLSearchParams(this.filters).toString();
                    const response = await fetch(url, {
                        headers: {
                            'Content-Type': 'application/json'
                        }
                    });
                    const json = await response.json();
                    this.orders = json.data;
                    this.links = json.links;
                }
            }))
        })
    </script>
@endsection
