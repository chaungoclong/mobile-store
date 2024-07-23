@extends('layouts.master')

@php use App\Enums\OrderStatus;use Illuminate\Support\Facades\Request; @endphp

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
        <section class="section-advertise">
            <div class="content-advertise">
                <div id="slide-advertise" class="owl-carousel">
                    @foreach($data['advertises'] as $advertise)
                        <div class="slide-advertise-inner"
                             style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}');"
                             data-dot="<button>{{ $advertise->title }}</button>"></div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-orders">
            <div class="section-header">
                <h2 class="section-title">Đơn Hàng <span>({{ $data['orders']->count() }} đơn hàng)</span></h2>
            </div>
            <div class="section-content">
                <div class="row">
                    <div class="col-md-12">
                        <div style="margin-bottom: 10px;">
                            <form action="">
                                <select name="status" id="">
                                    <option value="">Tất cả</option>
                                    @foreach(OrderStatus::cases() as $status)
                                        <option value="{{ $status->value }}" @if($status->value == request('status')) selected @endif>{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                                <button>Tìm</button>
                            </form>
                        </div>
                        <div class="orders-table">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center">STT</th>
                                        <th class="text-center">Mã<br>Đơn Hàng</th>
                                        <th class="text-center">Phương Thức<br>Thanh Toán</th>
                                        <th class="text-center">Số Lượng</th>
                                        <th class="text-center">Tổng tiền</th>
                                        <th class="text-center">Trạng Thái</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($data['orders'] as $key => $order)
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                            <td class="text-center"><a
                                                    href="{{ route('order_page', ['id' => $order->id]) }}"
                                                    title="Chi tiết đơn hàng: {{ $order->order_code }}">{{ $order->order_code }}</a>
                                            </td>
                                            <td class="text-center">{{ $order->payment_method->name }}</td>
                                            <td class="text-center">{{ $order->order_details->sum('quantity') }}</td>
                                            <td class="text-center"
                                                style="color: #9fda58;">{{ number_format($order->amount,0,',','.') }}₫
                                            </td>
                                            <td>
                                                @if($order->status == OrderStatus::Pending->value)
                                                    <span class="label label-default" style="font-size:13px">Chờ xác nhận</span>
                                                @elseif ($order->status == OrderStatus::Confirmed->value)
                                                    <span class="label label-info"
                                                          style="font-size:13px">Đã xác nhận</span>
                                                @elseif ($order->status == OrderStatus::Delivery->value)
                                                    <span class="label label-info" style="font-size:13px">Đang Vận Chuyển</span>
                                                @elseif ($order->status == OrderStatus::Done->value)
                                                    <span class="label label-success" style="font-size:13px">Đã Hoàn Thành</span>
                                                @else
                                                    <span class="label label-danger" style="font-size:13px">Hủy</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{ $data['orders']->appends(Request::except('page'))->links('vendor.pagination.default') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
