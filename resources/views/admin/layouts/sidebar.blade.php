<aside class="main-sidebar" style="background: #0a0a0a;">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
{{--            <li class="header">MAIN NAVIGATION</li>--}}
            <!-- Optionally, you can add icons to the links -->
            <li class="{{ Helper::check_active(['admin.dashboard']) }}"><a href="{{ route('admin.dashboard') }}"><i
                        class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
            <li class="{{ Helper::check_active(['admin.advertise.index', 'admin.advertise.new', 'admin.advertise.edit']) }}">
                <a href="{{ route('admin.advertise.index') }}"><i class="fa fa-sliders" aria-hidden="true"></i> <span>Quản Lý Quảng Cáo</span></a>
            </li>
            <li class="{{ Helper::check_active(['admin.users', 'admin.user_show']) }}"><a
                    href="{{ route('admin.users') }}"><i class="fa fa-users"></i> <span>Quản Lý khách hàng</span></a>
            </li>
            <li class="{{ Helper::check_active(['admin.post.index', 'admin.post.new', 'admin.post.edit']) }}"><a
                    href="{{ route('admin.post.index') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i> <span>Quản Lý Bài Viết</span></a>
            </li>
            <li class="{{ Helper::check_active(['admin.product.index', 'admin.product.new', 'admin.product.edit']) }}">
                <a href="{{ route('admin.product.index') }}"><i class="fa fa-product-hunt" aria-hidden="true"></i>
                    <span>Quản Lý Sản Phẩm</span></a></li>
            <li class="{{ Helper::check_active(['admin.categories.index', 'admin.categories.create', 'admin.categories.edit']) }}">
                <a href="{{ route('admin.categories.index') }}"><i class="fa fa-product-hunt" aria-hidden="true"></i>
                    <span>Quản Lý Danh mục</span></a></li>
            <li class="{{ Helper::check_active(['admin.producers.index', 'admin.producers.create', 'admin.producers.edit']) }}">
                <a href="{{ route('admin.producers.index') }}"><i class="fa fa-product-hunt" aria-hidden="true"></i>
                    <span>Quản Lý Hãng sản xuất</span></a></li>
            <li class="{{ Helper::check_active(['admin.order.index', 'admin.order.show']) }}"><a
                    href="{{ route('admin.order.index') }}"><i class="fa fa-list-alt" aria-hidden="true"></i> <span>Quản Lý Đơn Hàng</span></a>
            </li>
{{--            <li class="{{ Helper::check_active(['admin.warehouse']) }}"><a href="{{route('admin.warehouse')}}"><i--}}
{{--                        class="fa fa-archive" aria-hidden="true"></i><span>Kho Hàng</span></a></li>--}}
{{--            <li class="{{ Helper::check_active(['admin.orderDetails']) }}"><a--}}
{{--                    href="{{route('admin.orderDetails')}}"><i class="fa fa-archive" aria-hidden="true"></i><span>Thống Kê Đơn Hàng</span></a>--}}
{{--            </li>--}}
{{--            <li class="{{ Helper::check_active(['admin.statistic']) }}"><a href="{{ route('admin.statistic') }}"><i--}}
{{--                        class="fa fa-line-chart" aria-hidden="true"></i> <span>Thống Kê Doanh Thu</span></a></li>--}}
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>
