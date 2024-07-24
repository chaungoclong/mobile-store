<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} | @yield('title')</title>

    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/bower_components/Ionicons/css/ionicons.min.css') }}">
    <!-- Sweet Alert 2 -->
    <!-- Embed CSS -->
    @yield('embed-css')
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/AdminLTE.min.css') }}">
    <!-- AdminLTE Skins -->
    <link rel="stylesheet" href="{{ asset('AdminLTE/dist/css/skins/skin-red.min.css') }}">
    <script src="{{ asset('plugins/tailwindcss/tailwindcss.min.css') }}"></script>
    <link rel="stylesheet" href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}">
    <script src="{{ asset('plugins/alpinejs/alpine3.min.js') }}" defer></script>

    <!-- Custom CSS -->
    @yield('custom-css')

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
<!--    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>-->
<!--    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>-->
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <style>
        .swal2-popup {
            font-size: 14px !important;
        }
    </style>
</head>

<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">

    <!-- Main Header -->
    @include('admin.layouts.header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('admin.layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                @yield('title')
            </h1>
            @yield('breadcrumb')
        </section>

        <!-- Main content -->
        <section class="content">
            @yield('content')
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    @include('admin.layouts.footer')

</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->
<script src="{{ asset('AdminLTE/bower_components/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('AdminLTE/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('plugins/flatpickr/lang/vn.js') }}"></script>
<!-- Sweet Alert 2 -->
<script src="{{ asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Embed JS -->
@yield('embed-js')
<!-- AdminLTE App -->
<script src="{{ asset('AdminLTE/dist/js/adminlte.min.js') }}"></script>
<!-- Custom JS -->
<script>
    const BASE_URL = '{{ config('app.url') }}';
    $.ajaxSetup({
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function debounce(func, wait) {
        return function (...args) {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });

    function showToast(
        responseOrXhr,
        type = 'success',
        callback = undefined,
        fieldMessage = 'message'
    ) {
        let title = '';

        if (responseOrXhr.responseJSON !== undefined) {
            title = responseOrXhr.responseJSON[fieldMessage];
        } else {
            title = responseOrXhr[fieldMessage];
        }

        if (title === '') {
            title = (type === 'error') ? 'Đã có lỗi xảy ra' : 'Thành công';
        }

        // init toast
        let toast = Toast.fire({
            title: title,
            icon: type.toLowerCase().trim(),
            showCloseButton: true
        });

        // Execute callback if callback is function
        if (typeof callback === 'function') {
            toast.then(callback);
        }
    }

    $(document).ready(function () {
        $('#logout').click(function () {
            Swal.fire({
                title: 'Đăng Xuất',
                text: "Bạn có chắc muốn đăng xuất khỏi hệ thống!",
                type: 'question',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Đăng Xuất',
            }).then((result) => {
                if (result.value)
                    document.getElementById('logout-form').submit();
            })
        });
    });

    $(function () {
        $('#sidebar-search-form').on('submit', function (e) {
            e.preventDefault();
        });

        $('.sidebar-menu li.active').data('lte.pushmenu.active', true);

        $('#sidebar-search-form input').on('keyup', function () {
            var term = $(this).val().trim();

            if (term.length === 0) {
                $('.sidebar-menu li').each(function () {
                    $(this).show(0);
                    $(this).removeClass('active');
                    if ($(this).data('lte.pushmenu.active')) {
                        $(this).addClass('active');
                    }
                });
                return;
            }

            $('.sidebar-menu li').each(function () {
                if ($(this).text().toLowerCase().indexOf(term.toLowerCase()) === -1) {
                    $(this).hide(0);
                    $(this).removeClass('pushmenu-search-found', false);

                    if ($(this).is('.treeview')) {
                        $(this).removeClass('active');
                    }
                } else {
                    $(this).show(0);
                    $(this).addClass('pushmenu-search-found');

                    if ($(this).is('.treeview')) {
                        $(this).addClass('active');
                    }

                    var parent = $(this).parents('li').first();
                    if (parent.is('.treeview')) {
                        parent.show(0);
                    }
                }

                if ($(this).is('.header')) {
                    $(this).show();
                }
            });

            $('.sidebar-menu li.pushmenu-search-found.treeview').each(function () {
                $(this).find('.pushmenu-search-found').show(0);
            });
        });
    });
</script>

@foreach (['error', 'success', 'warning'] as $messageStatus)
    @if(session($messageStatus))
        <script>
            $(function () {
                Toast.fire(
                    {
                        'title': `{{ session($messageStatus )}}`,
                        'icon': `{{ $messageStatus }}`
                    }
                );
            });
        </script>
    @endif
@endforeach
@yield('custom-js')
</body>
</html>
