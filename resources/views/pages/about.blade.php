@extends('layouts.master')

@section('title', 'Giới Thiệu')

@section('content')

    <section class="bread-crumb">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home_page') }}">{{ __('Trang Chủ') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giới Thiệu</li>
            </ol>
        </nav>
    </section>

    <div class="site-about">
        <section class="section-advertise">
            <div class="content-advertise">
                <div id="slide-advertise" class="owl-carousel">
                    @foreach($advertises as $advertise)
                        <div class="slide-advertise-inner"
                             style="background-image: url('{{ Helper::get_image_advertise_url($advertise->image) }}');"
                             data-dot="<button>{{ $advertise->title }}</button>"></div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="section-about">
            <div class="section-header">
                <h2 class="section-title">Giới Thiệu</h2>
            </div>
            <div class="section-content">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Giới Thiệu Về Chúng Tôi</h2>
                        <p>Tại <strong>{{ config('app.name') }}</strong>, chúng tôi tự hào mang đến cho khách hàng những
                            sản
                            phẩm công nghệ hiện đại nhất với chất lượng cao và dịch vụ hoàn hảo. Với nhiều năm kinh
                            nghiệm
                            trong lĩnh vực kinh doanh điện thoại di động và phụ kiện, chúng tôi cam kết đem lại sự hài
                            lòng
                            tuyệt đối cho quý khách.</p>

                        <h2>Tầm Nhìn và Sứ Mệnh</h2>

                        <h3>Tầm Nhìn</h3>
                        <p>Chúng tôi hướng tới việc trở thành một trong những cửa hàng điện thoại trực tuyến hàng đầu
                            tại
                            Việt Nam, nơi khách hàng có thể tìm thấy mọi thứ họ cần về công nghệ, từ các dòng điện thoại
                            thông minh mới nhất đến những phụ kiện tiện ích.</p>

                        <h3>Sứ Mệnh</h3>
                        <p><strong>Chất Lượng Sản Phẩm:</strong> Cung cấp những sản phẩm chính hãng từ các thương hiệu
                            uy
                            tín trên thế giới.</p>
                        <p><strong>Giá Cả Hợp Lý:</strong> Đảm bảo mức giá cạnh tranh và những ưu đãi hấp dẫn.</p>
                        <p><strong>Dịch Vụ Khách Hàng:</strong> Luôn sẵn sàng hỗ trợ và tư vấn nhiệt tình, tận tâm, giúp
                            khách hàng có trải nghiệm mua sắm tốt nhất.</p>
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
