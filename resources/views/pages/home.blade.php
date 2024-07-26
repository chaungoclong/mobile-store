@extends('layouts.master')

@section('title', 'Trang Chủ')

@section('content')
    <div class="site-home">
        <section class="section-advertise">
            <div class="row">
                <div class="col-md-8">
                    <div class="content-advertise">
                        <div id="slide-advertise" class="owl-carousel">
                            @foreach($data['advertises'] as $advertise)
                                <a href="{{ $advertise->link ?? '' }}">
                                    <div class="slide-advertise-inner"
                                         style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}');"
                                         data-dot="<button>{{ $advertise->title }}</button>"></div>
                                </a>
                            @endforeach
                        </div>
                        <div class="custom-dots-slide-advertises"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="new-posts">
                        <div class="posts-header">
                            <h3 class="posts-title">BÀI VIẾT</h3>
                        </div>
                        <div class="posts-content">
                            @foreach($data['posts'] as $post)
                                <div class="post-item">
                                    <a href="{{ route('post_page', ['id' => $post->id]) }}" title="{{ $post->title }}">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-3 col-xs-3 col-xs-responsive">
                                                <div class="post-item-image"
                                                     style="background-image: url('{{ Helper::get_image_post_url($post->image) }}'); padding-top: 50%;"></div>
                                            </div>
                                            <div class="col-md-8 col-sm-9 col-xs-9 col-xs-responsive">
                                                <div class="post-item-content">
                                                    <h4 class="post-content-title">{{ $post->title }}</h4>
                                                    <p class="post-content-date">{{ date_format($post->created_at, 'h:i A d/m/Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-favorite-products">
            <div class="section-header">
                <h2 class="section-title">SẢN PHẨM MỚI</h2>
            </div>
            <div class="section-content" style="padding: 20px;">
                <div id="slide-latest" class="owl-carousel">
                    @foreach($data['latest_products'] as $product)
                        <div class="item-product" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px; padding: 5px; margin: 5px;">
                            <a href="{{ route('product_detail', ['slug' => $product?->slug ?? '']) }}"
                               title="{{ $product->name }}">
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
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-favorite-products">
            <div class="section-header">
                <h2 class="section-title">SẢN PHẨM ĐƯỢC ĐÁNH GIÁ CAO</h2>
            </div>
            <div class="section-content" style="padding: 0 20px 5px 20px;">
                <div id="slide-favorite" class="owl-carousel">
                    @foreach($data['favorite_products'] as $product)
                        <div class="item-product" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px; padding: 5px; margin: 5px;">
                            <a href="{{ route('product_detail', ['slug' => $product?->slug ?? '']) }}"
                               title="{{ $product->name }}">
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
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-favorite-products">
            <div class="section-header">
                <h2 class="section-title">SẢN PHẨM ĐANG KHUYẾN MÃI</h2>
            </div>
            <div class="section-content" style="padding: 20px;">
                <div id="slide-promotions" class="owl-carousel">
                    @foreach($data['promotion_products'] as $product)
                        <div class="item-product" style="box-shadow: rgba(0, 0, 0, 0.1) 0px 4px 12px; padding: 5px; margin: 5px;">
                            <a href="{{ route('product_detail', ['slug' => $product?->slug ?? '']) }}"
                               title="{{ $product->name }}">
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
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
@endsection

@section('css')
    <style>
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
                items: 1,
                autoplay: true,
                autoplayHoverPause: true,
                loop: true,
                nav: true,
                dots: true,
                dotsData: true,
                responsive: {
                    0: {
                        nav: false,
                        dots: false
                    },
                    641: {
                        nav: true,
                        dots: true
                    }
                },
                navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>'],
                dotsContainer: '.custom-dots-slide-advertises'
            });

            $("#slide-favorite").owlCarousel({
                items: 5,
                autoplay: true,
                autoplayHoverPause: true,
                nav: true,
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                        nav: false
                    },
                    480: {
                        items: 2,
                        nav: false
                    },
                    769: {
                        items: 3,
                        nav: true
                    },
                    992: {
                        items: 4,
                        nav: true,
                    },
                    1200: {
                        items: 5,
                        nav: true
                    }
                },
                navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>']
            });

            $("#slide-latest").owlCarousel({
                items: 5,
                autoplay: true,
                autoplayHoverPause: true,
                nav: true,
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                        nav: false
                    },
                    480: {
                        items: 2,
                        nav: false
                    },
                    769: {
                        items: 3,
                        nav: true
                    },
                    992: {
                        items: 4,
                        nav: true,
                    },
                    1200: {
                        items: 5,
                        nav: true
                    }
                },
                navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>']
            });

            $("#slide-promotions").owlCarousel({
                items: 5,
                autoplay: true,
                autoplayHoverPause: true,
                nav: true,
                dots: false,
                responsive: {
                    0: {
                        items: 1,
                        nav: false
                    },
                    480: {
                        items: 2,
                        nav: false
                    },
                    769: {
                        items: 3,
                        nav: true
                    },
                    992: {
                        items: 4,
                        nav: true,
                    },
                    1200: {
                        items: 5,
                        nav: true
                    }
                },
                navText: ['<i class="fas fa-angle-left"></i>', '<i class="fas fa-angle-right"></i>']
            });

            @if(session('alert'))
            Swal.fire(
                '{{ session('alert')['title'] }}',
                '{{ session('alert')['content'] }}',
                '{{ session('alert')['type'] }}'
            )
            @endif
        });
    </script>
@endsection
