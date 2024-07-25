@php use App\Enums\OrderStatus;use App\Helpers\Helpers; @endphp
@extends('admin.layouts.master')

@section('title', 'Đơn Hàng: #'.$order->order_code)

@section('embed-css')
@endsection

@section('custom-css')
    <style>
        body {
            background-color: #f5f5f5; /* Light grey background for the whole page */
        }

        .container {
            margin-top: 20px;
        }

        .panel-custom {
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px; /* Space between panels */
        }

        .panel-heading-custom {
            background-color: #f5f5f5;
            border-bottom: 1px solid #ddd;
        }

        .panel-body-custom {
            padding: 15px;
        }

        .product-image {
            max-width: 50px;
            max-height: 50px;
        }

        .status-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .status-container h2 {
            margin: 0;
            margin-right: 20px;
        }

        .label-status {
            font-size: 16px;
            padding: 8px 12px;
            margin-left: 10px;
        }

        .delivery-info {
            margin-top: 15px;
            padding: 10px;
            background-color: #f9f9f9; /* Slightly darker background for input section */
            border: 1px solid #ddd; /* Border for input section */
            border-radius: 4px; /* Rounded corners for input section */
        }

        .button-container {
            display: flex;
            gap: 10px; /* Adjust spacing between buttons */
        }

        .btn-space {
            margin-right: 10px;
        }

        /* Flexbox styles to make panels equal height */
        .row-equal-height {
            display: flex;
            flex-wrap: wrap;
        }

        .panel-equal {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb flex space-x-2 text-gray-500">
        <li><a href="{{ route('admin.dashboard') }}" class="hover:text-gray-700"><i class="fa fa-dashboard"></i>
                Home</a></li>
        <li><a href="{{ route('admin.order.index') }}" class="hover:text-gray-700"><i class="fa fa-list-alt"
                                                                                      aria-hidden="true"></i> Quản Lý
                Đơn Hàng</a></li>
        <li class="active">{{ 'Đơn Hàng: #'.$order->order_code }}</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-body">
                    <div class="">
                        <div class="row row-equal-height">
                            <div class="col-md-4">
                                <div class="panel panel-custom panel-equal">
                                    <div class="panel-heading panel-heading-custom">
                                        <h3 class="panel-title">Thông Tin Đơn Hàng</h3>
                                    </div>
                                    <div class="panel-body panel-body-custom">
                                        <p><strong>Mã Đơn Hàng:</strong> #{{ $order->order_code ?? '' }}</p>
                                        <p><strong>Trạng Thái:</strong> {!! $status?->toHtml() !!}</p>
                                        <p><strong>Tình Trạng Thanh Toán:</strong>
                                            <span id="paymentStatusText">{!! $payment_status?->toHtml() !!}</span>
                                        </p>
                                        <p>
                                            <strong>Mã Vận Chuyển:</strong>
                                            <span id="deliveryCodeText">{{ $order->delivery_code ?? '' }}</span>
                                        </p>
                                        <p>
                                            <strong>Số Tiền:</strong>
                                            {{ Helpers::formatVietnameseCurrency($order?->amount ?? null) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-custom panel-equal">
                                    <div class="panel-heading panel-heading-custom">
                                        <h3 class="panel-title">Thông Tin Khách Hàng</h3>
                                    </div>
                                    <div class="panel-body panel-body-custom">
                                        <p>
                                            <strong>Tên:</strong>
                                            <a href="{{ route('admin.user_show', $order?->user?->id ?? '') }}"
                                               class="text-info">{{ $order?->user?->name ?? '' }}</a>
                                        </p>
                                        <p><strong>Email:</strong> {{ $order?->user?->email ?? '' }}</p>
                                        <p><strong>Điện Thoại:</strong> {{ $order?->user?->phone ?? '' }}</p>
                                        <p><strong>Địa Chỉ:</strong> {{ $order?->user?->address ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="panel panel-custom panel-equal">
                                    <div class="panel-heading panel-heading-custom">
                                        <h3 class="panel-title">Thông Tin Giao Hàng</h3>
                                    </div>
                                    <div class="panel-body panel-body-custom">
                                        <p><strong>Tên Người Nhận:</strong> {{ $order?->name ?? '' }}</p>
                                        <p><strong>Email Người Nhận:</strong> {{ $order?->email ?? '' }}</p>
                                        <p><strong>Điện Thoại Người Nhận:</strong> {{ $order?->phone ?? '' }}</p>
                                        <p><strong>Địa Chỉ Giao Hàng:</strong> {{ $order?->address ?? '' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-custom" style="margin-top: 20px;">
                            <div class="panel-heading panel-heading-custom">
                                <h3 class="panel-title">Sản Phẩm</h3>
                            </div>
                            <div class="panel-body panel-body-custom">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Hình Ảnh</th>
                                        <th>Tên</th>
                                        <th>Màu</th>
                                        <th>Số Lượng</th>
                                        <th>Giá</th>
                                        <th>Tổng</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($order?->order_details ?? [] as $orderDetail)
                                        <tr>
                                            <td>
                                                <img
                                                    src="{{ $orderDetail?->product_detail?->product_image_urls[0] ?? '' }}"
                                                    class="product-image" alt="Hình Ảnh Sản Phẩm">
                                            </td>
                                            <td>{{ $orderDetail?->product_detail?->product?->name ?? '' }}</td>
                                            <td>{{ $orderDetail?->product_detail?->color ?? '' }}</td>
                                            <td>{{ $orderDetail?->quantity ?? '' }}</td>
                                            <td>{{ Helpers::formatVietnameseCurrency($orderDetail?->price ?? null) }}</td>
                                            <td>{{ Helpers::formatVietnameseCurrency($orderDetail?->price * $orderDetail?->quantity) }}</td>
                                        </tr>
                                    @endforeach
                                    <!-- Repeat for other products -->
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Tổng Tiền:</th>
                                        <th>{{ Helpers::formatVietnameseCurrency($order?->amount ?? null) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="panel panel-custom" style="margin-top: 20px;">
                            <div class="panel-heading panel-heading-custom">
                                <h3 class="panel-title">Tùy Chọn Đơn Hàng</h3>
                            </div>
                            <div class="panel-body panel-body-custom">
                                <div class="button-container">
                                    @if($status === OrderStatus::Pending)
                                        <button
                                            data-status="{{ OrderStatus::Confirmed->value }}"
                                            class="{{ OrderStatus::Confirmed->buttonClass() }} btn-space btn-update-status"
                                            data-action="confirm">
                                            <i class="fa fa-check" style="margin-right: 5px;"></i>Xác Nhận
                                        </button>
                                    @endif
                                    @if($status === OrderStatus::Confirmed)
                                        <button
                                            data-status="{{ OrderStatus::Delivery->value }}"
                                            class="{{ OrderStatus::Delivery->buttonClass() }} btn-space btn-update-status"
                                            data-action="ship">
                                            <i class="fa fa-truck" style="margin-right: 5px;"></i>Vận chuyển
                                        </button>
                                    @endif
                                    @if($status === OrderStatus::Delivery)
                                        <button
                                            data-status="{{ OrderStatus::Done->value }}"
                                            class="{{ OrderStatus::Done->buttonClass() }} btn-space btn-update-status"
                                            data-action="complete">
                                            <i class="fa fa-check-circle" style="margin-right: 5px;"></i>Hoàn thành
                                        </button>
                                    @endif
                                    @if($status !== OrderStatus::Delivery && $status !== OrderStatus::Done && $status !== OrderStatus::Cancelled)
                                        <button
                                            data-status="{{ OrderStatus::Cancelled->value }}"
                                            class="{{ OrderStatus::Cancelled->buttonClass() }} btn-space btn-update-status"
                                            data-action="cancel">
                                            <i class="fa fa-times" style="margin-right: 5px;"></i>Hủy Đơn
                                        </button>
                                    @endif

                                    <button
                                        class="btn-primary btn btn-space"
                                        id="btnPrintOrder">
                                        <i class="fa fa-print" style="margin-right: 5px;"></i>In Hóa đơn
                                    </button>
                                </div>
                                <form action="{{ route('admin.order.update') }}" method="post" id="formUpdateStatus">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $order?->id ?? '' }}">
                                    <input type="hidden" name="status" id="status">
                                    @if($status === OrderStatus::Confirmed)
                                        <div class="delivery-info" id="deliveryInfo">
                                            <label for="deliveryCode">Mã Vận Chuyển:</label>
                                            <input type="text" id="deliveryCode" class="form-control"
                                                   name="delivery_code"
                                                   placeholder="Nhập mã vận chuyển">
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('embed-js')
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
@endsection

@section('custom-js')
    <script>
        $(function () {
            const $buttonsUpdateStatus = $('.btn-update-status');
            const $formUpdateStatus = $('#formUpdateStatus');
            const $statusInput = $formUpdateStatus.find('#status').first();
            $buttonsUpdateStatus.on('click', function (event) {
                event.preventDefault();

                const status = $(this).data('status');
                const action = $(this).data('action');

                let message = '';
                if (action === 'confirm') {
                    message = 'Bạn có chắc chắn muốn xác nhận đơn hàng này không?';
                } else if (action === 'ship') {
                    message = 'Bạn có chắc chắn muốn chuyển đơn hàng này vào trạng thái vận chuyển không?';
                } else if (action === 'complete') {
                    message = 'Bạn có chắc chắn muốn hoàn thành đơn hàng này không?';
                } else if (action === 'cancel') {
                    message = 'Bạn có chắc chắn muốn hủy đơn hàng này không?';
                }

                Swal.fire({
                    title: message,
                    text: "Bạn sẽ không thể hoàn tác hành động này",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Có",
                    cancelButtonText: "Không",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $statusInput.val(status);
                        $formUpdateStatus.submit();
                    }
                });
            });
        });
    </script>
@endsection
