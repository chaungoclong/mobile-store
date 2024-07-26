@extends('layouts.master')

@section('title', 'Sản Phẩm')

@section('content')

    <section class="bread-crumb">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Sản Phẩm</li>
            </ol>
        </nav>
    </section>

    <div class="site-products">
        <section class="section-advertise">
            <div class="content-advertise">
                <div id="slide-advertise" class="owl-carousel">
                    @foreach($data['advertises'] as $advertise)
                        <a href="{{ $advertise?->link ?? '' }}">
                            <div class="slide-advertise-inner"
                                 style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}');"
                                 data-dot="<button>{{ $advertise->title }}</button>"></div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-filter">
            <div class="section-header">
                <h2 class="section-title">BỘ LỌC</h2>
            </div>
            <div class="section-content">
                <form action="{{ route('products_page') }}" method="GET" accept-charset="utf-8">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <select name='os'>
                                        <option value='' {{ Request::input('os') == null ? 'selected' : '' }}>
                                            Hệ Điều Hành
                                        </option>
                                        <option value='ios' {{ Request::input('os') == 'ios' ? 'selected' : '' }}>IOS
                                        </option>
                                        <option
                                            value='android' {{ Request::input('os') == 'android' ? 'selected' : '' }}>
                                            Android
                                        </option>
                                        <option
                                            value='windows' {{ Request::input('os') == 'windows' ? 'selected' : '' }}>
                                            Windows Phone
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <select name='price'>
                                        <option value='' {{ Request::input('price') == null ? 'selected' : '' }}>
                                            Giá Sản Phẩm
                                        </option>
                                        <option value='asc' {{ Request::input('price') == 'asc' ? 'selected' : '' }}>
                                            Giá từ thấp tới cao
                                        </option>
                                        <option value='desc' {{ Request::input('price') == 'desc' ? 'selected' : '' }}>
                                            Giá từ cao tới thấp
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <select name='category_id'>
                                        <option value="">Danh mục Sản phẩm</option>
                                        @foreach($data['categories'] ?? [] as $category)
                                            <option value="{{ $category?->id ?? '' }}" @if($category?->id == $category_id) selected @endif>{{ $category?->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 col-sm-6 col-xs-6">
                                    <select name='producer_id'>
                                        <option value="">Hãng sản xuất</option>
                                        @foreach($data['producers'] ?? [] as $producer)
                                            <option value="{{ $producer?->id ?? '' }}" @if($producer?->id == $producer_id) selected @endif>{{ $producer?->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-default">Lọc Sản Phẩm</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="section-products">
            <div class="section-content">
                @if($data['products']->isEmpty())
                    <div class="empty-content">
                        <div class="icon"><i class="fab fa-searchengin"></i></div>
                        <div class="title">Oooops!</div>
                        <div class="content">Không có sản phẩm</div>
                    </div>
                @else
                    <div class="row" style="padding: 0 5px 0 5px;">
                        @foreach($data['products'] as $key => $product)
                            <div class="col-md-2 col-md-20" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px; padding: 5px; margin: 5px;">
                                <div class="item-product" style="">
                                    <a href="{{ route('product_detail', ['slug' => $product?->slug ?? '']) }}"
                                       title="{{ $product->name }}">
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <div class="image-product"
                                                     style="background-image: url('{{ Helper::get_image_product_url($product->image) }}');padding-top: 100%;">
                                                    {!! Helper::get_promotion_percent($product?->product_detail_promotion?->sale_price, $product?->product_detail_promotion?->promotion_price, $product?->product_detail_promotion?->promotion_start_date, $product?->product_detail_promotion?->promotion_end_date) !!}
                                                </div>
                                                <div class="content-product">
                                                    <h3 class="title">{{ $product->name }}</h3>
                                                    <div class="start-vote">
                                                        {!! Helper::get_start_vote($product->rate) !!}
                                                    </div>
                                                    <div class="price">
                                                        {!! Helper::get_real_price($product?->product_detail_promotion?->sale_price, $product?->product_detail_promotion?->promotion_price, $product?->product_detail_promotion?->promotion_start_date, $product?->product_detail_promotion?->promotion_end_date) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-sm-12 col-xs-12 animate">
                                                <div class="product-details">
                                                    <p><strong><i class="fas fa-tv"></i> Màn Hình:
                                                        </strong>{{ $product->monitor }}</p>
                                                    <p><strong><i class="fas fa-camera-retro"></i> Camera Trước:
                                                        </strong>{{ $product->front_camera }}</p>
                                                    <p><strong><i class="fas fa-camera-retro"></i> Camera sau:
                                                        </strong>{{ $product->rear_camera }}</p>
                                                    <p><strong><i class="fas fa-microchip"></i> CPU:
                                                        </strong>{{ $product->CPU }}</p>
                                                    <p><strong><i class="fas fa-microchip"></i>GPU:
                                                        </strong>{{ $product->GPU }}</p>
                                                    <p><strong><i class="fas fa-hdd"></i> RAM:
                                                        </strong>{{ $product->RAM }}GB</p>
                                                    <p><strong><i class="fas fa-hdd"></i> Bộ Nhớ Trong:
                                                        </strong>{{ $product->ROM }}GB</p>
                                                    @if(Str::is('*Android*', $product->OS_version))
                                                        <p><strong><i class="fab fa-android"></i> HĐH:
                                                            </strong>{{ $product->OS_version }}</p>
                                                    @elseif(Str::is('*IOS*', $product->OS_version))
                                                        <p><strong><i class="fab fa-apple"></i> HĐH:
                                                            </strong>{{ $product->OS_version }}</p>
                                                    @elseif(Str::is('*Windows*', $product->OS_version))
                                                        <p><strong><i class="fab fa-windows"></i> HĐH:
                                                            </strong>{{ $product->OS_version }}</p>
                                                    @endif
                                                    <p><strong><i class="fas fa-battery-full"></i> Dung Lượng PIN:
                                                        </strong>{{ $product->pin }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="section-footer text-center">
                {{ $data['products']->appends(Request::query())->links('vendor.pagination.default') }}
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

        .item-product:hover {
            transform: translateY(-20px);
            transition: all 0.5s ease-in-out;
            -moz-transition: all 0.5s ease-in-out;
            -webkit-transition: all 0.5s ease-in-out;
            color: #9fda58;
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
