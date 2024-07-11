@php use App\Enums\PaymentStatus;use App\Helpers\Helpers; @endphp
@extends('admin.layouts.master')

@section('title', 'Đơn Hàng: #'.$order->order_code)

@section('embed-css')
@endsection

@section('custom-css')
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
    <section class="invoice mx-auto p-6 bg-white shadow-lg rounded-lg">
        <div id="print-invoice">
            <div class="flex justify-between items-center border-b pb-4 mb-6">
                <div class="flex items-center">
                    <div class="w-10 mr-3">
                        {{-- <img src="{{ asset('images/favicon.png') }}" alt="PhoneStore Logo" class="w-full h-auto object-cover"> --}}
                    </div>
                    <div class="text-2xl text-red-600 font-bold">{{ config('app.name') }}</div>
                </div>
                <div class="text-xl text-gray-500">
                    <p>Ngày: {{ date("d/m/Y") }}</p>
                    <p>Order #: {{ $order->order_code }}</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-semibold">Thông tin người nhận</h3>
                    <address class="text-gray-700">
                        <b>{{ $order->name }}</b><br>
                        SĐT: {{ $order->phone }}<br>
                        Email: {{ $order->email }}<br>
                        Địa chỉ: {{ $order->address }}
                    </address>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Thông tin người đặt</h3>
                    <address class="text-gray-700">
                        <b>{{ $order->user->name }}</b><br>
                        SĐT: {{ $order->user->phone }}<br>
                        Email: {{ $order->user->email }}<br>
                        Địa chỉ: {{ $order->user->address }}
                    </address>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Thông tin đơn hàng</h3>
                    <address class="text-gray-700">
                        <b>Đơn Hàng #{{ $order->order_code }}</b><br>
                        <b>Ngày Tạo:</b> {{ date_format($order->created_at, 'd/m/Y') }}<br>
                        <b>Thanh Toán:</b> {{ $order->payment_method->name }}
                    </address>
                </div>
            </div>

            <div class="table-responsive mb-6">
                <table class="table-auto w-full border-collapse border border-gray-200 text-gray-700">
                    <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-200 p-2 text-center">STT</th>
                        <th class="border border-gray-200 p-2">Mã Sản Phẩm</th>
                        <th class="border border-gray-200 p-2">Tên Sản Phẩm</th>
                        <th class="border border-gray-200 p-2">Màu Sắc</th>
                        <th class="border border-gray-200 p-2 text-center">Số Lượng</th>
                        <th class="border border-gray-200 p-2">Đơn Giá</th>
                        <th class="border border-gray-200 p-2">Tổng Tiền</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $price = 0; @endphp
                    @foreach($order->order_details as $key => $order_detail)
                        @php $price += $order_detail->price * $order_detail->quantity; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-200 p-2 text-center">{{ $key + 1 }}</td>
                            <td class="border border-gray-200 p-2">{{ '#'.$order_detail->product_detail->product->sku_code }}</td>
                            <td class="border border-gray-200 p-2 flex items-center space-x-2">
                                <a href="{{ route('admin.product.edit', $order_detail->product_detail->product) }}"
                                   class="text-blue-500 hover:underline">{{ $order_detail->product_detail->product->name }}</a>
                                <img class="w-10 h-10 rounded-md object-cover"
                                     src="{{ $order_detail->product_detail->product_image_urls[0] ?? '' }}"
                                     alt="Product image">
                            </td>
                            <td class="border border-gray-200 p-2">{{ $order_detail->product_detail->color }}</td>
                            <td class="border border-gray-200 p-2 text-center">{{ $order_detail->quantity }}</td>
                            <td class="border border-gray-200 p-2 text-green-500">{{ number_format($order_detail->price,0,',','.') }}
                                VNĐ
                            </td>
                            <td class="border border-gray-200 p-2 text-green-500">{{ number_format($order_detail->price * $order_detail->quantity,0,',','.') }}
                                VNĐ
                            </td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-100">
                        <td colspan="6" class="border border-gray-200 p-2 text-right font-semibold">Tổng</td>
                        <td class="border border-gray-200 p-2 text-green-500">{{ number_format($price,0,',','.') }}
                            VNĐ
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between">
                <div class="w-1/2">
                    <h3 class="text-lg font-semibold">Phương thức thanh toán</h3>
                    <p class="text-gray-700 bg-gray-100 p-4 rounded">
                        <b>{{ $order->payment_method->name }}:</b><br>
                        <span class="ml-4">{{ $order->payment_method->describe }}</span>
                    </p>
                </div>
                <div class="w-1/2">
                    <h3 class="text-lg font-semibold">Chi tiết thanh toán</h3>
                    <div class="table-responsive">
                        <table class="table-auto w-full text-gray-700">
                            <tr>
                                <th class="p-2 text-left">Tổng Tiền:</th>
                                <td class="p-2 text-green-500"> {{ Helpers::formatVietnameseCurrency($price) }}</td>
                            </tr>
{{--                            <tr>--}}
{{--                                <th class="p-2 text-left">Đã Thanh Toán:</th>--}}
{{--                                <td class="p-2 text-green-500">--}}
{{--                                    @if((int)($order?->payment_status ?? null) !== PaymentStatus::Paid->value)--}}
{{--                                        {{ Helpers::formatVietnameseCurrency(0) }}--}}
{{--                                    @else--}}
{{--                                        {{ Helpers::formatVietnameseCurrency($price) }}--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <th class="p-2 text-left">Tổng số tiền phải thanh toán:</th>--}}
{{--                                <td class="p-2 text-green-500">--}}
{{--                                    @if((int)($order?->payment_status ?? null) === PaymentStatus::Paid->value)--}}
{{--                                        {{ Helpers::formatVietnameseCurrency(0) }}--}}
{{--                                    @else--}}
{{--                                        {{ Helpers::formatVietnameseCurrency($price) }}--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                            </tr>--}}
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="no-print mt-6 text-right">
            <button
                class="btn btn-success btn-print bg-green-500 text-white py-2 px-4 rounded shadow hover:bg-green-600"><i
                    class="fa fa-print"></i> In Hóa Đơn
            </button>
        </div>
    </section>
@endsection

@section('embed-js')
    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
@endsection

@section('custom-js')
    <script>
        $(document).ready(function () {
            $('.btn-print').click(function () {
                printJS({
                    printable: 'print-invoice',
                    type: 'html',
                    css: [
                        'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css'
                    ],
                    style: 'img { filter: grayscale(100%); -webkit-filter: grayscale(100%); }',
                    ignoreElements: []
                });
            });
        });
    </script>
@endsection
