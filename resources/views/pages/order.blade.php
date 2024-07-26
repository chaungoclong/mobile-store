@php use App\Enums\OrderStatus;use App\Helpers\Helpers; @endphp
@extends('layouts.master')

@section('title', $order?->order_code ?? '')

@section('content')
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
    <section class="bread-crumb">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('orders_page') }}">Đơn Hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $order?->order_code ?? '' }}</li>
            </ol>
        </nav>
    </section>

    <div class="site-order">
        <div class="section-order" style="padding: 10px;">
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
                                <span>{{ $order?->user?->name ?? '' }}</span>
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

            @if($status !== OrderStatus::Delivery && $status !== OrderStatus::Done && $status !== OrderStatus::Cancelled)
                <div class="panel panel-custom" style="margin-top: 20px;">
                    <div class="panel-heading panel-heading-custom">
                        <h3 class="panel-title">Tùy Chọn Đơn Hàng</h3>
                    </div>
                    <div class="panel-body panel-body-custom">
                        <div class="button-container">
                            <button
                                data-status="{{ OrderStatus::Cancelled->value }}"
                                class="{{ OrderStatus::Cancelled->buttonClass() }} btn-space btn-update-status"
                                data-action="cancel"
                                id="cancelOrderBtn">
                                <i class="fa fa-times" style="margin-right: 5px;"></i>Hủy Đơn
                            </button>
                        </div>
                        <form action="{{ route('cancel_order', $order) }}" method="post" id="formUpdateStatus">
                            @csrf
                            @method('delete')
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('css')
    <style>
        .slide-advertise-inner {
            background-repeat: no-repeat;
            background-size: cover;
            padding-top: 21.25%;
        }

        #slide-advertise.owl-carousel .owl-item.active {
            -webkit-animation-name: zoomIn;
            animation-name: zoomIn;
            -webkit-animation-duration: .6s;
            animation-duration: .6s;
        }
    </style>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $('#cancelOrderBtn').on('click', function (event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Bạn có muốn hủy đơn hàng này',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Không!',
                    confirmButtonText: 'Có!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        $('#formUpdateStatus').submit();
                    }
                });
            });
        });
    </script>
@endsection
