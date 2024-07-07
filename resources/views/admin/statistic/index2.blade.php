@extends('admin.layouts.app')

@section('vendor-styles')
    <link rel="stylesheet" href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}">
@endsection

@section('breadcrumbs')
    <li>
        <a href="#" class="text-gray-500 hover:text-gray-700">Dashboard</a>
    </li>
@endsection

@section('content')
    <div x-data="dashboard()" x-init="init()">
        <!-- Time Range Filter -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <div class="flex space-x-4 mb-4">
                <button @click="timeRange = 'today'; customRange = false"
                        :class="{ 'bg-blue-600 text-white': timeRange === 'today', 'bg-gray-200 text-gray-800': timeRange !== 'today' }"
                        class="p-2 rounded">Hôm nay
                </button>
                <button @click="timeRange = 'thisWeek'; customRange = false"
                        :class="{ 'bg-blue-600 text-white': timeRange === 'thisWeek', 'bg-gray-200 text-gray-800': timeRange !== 'thisWeek' }"
                        class="p-2 rounded">Tuần này
                </button>
                <button @click="timeRange = 'thisMonth'; customRange = false"
                        :class="{ 'bg-blue-600 text-white': timeRange === 'thisMonth', 'bg-gray-200 text-gray-800': timeRange !== 'thisMonth' }"
                        class="p-2 rounded">Tháng này
                </button>
                <button @click="timeRange = 'thisYear'; customRange = false"
                        :class="{ 'bg-blue-600 text-white': timeRange === 'thisYear', 'bg-gray-200 text-gray-800': timeRange !== 'thisYear' }"
                        class="p-2 rounded">Năm này
                </button>
                <button @click="timeRange = 'custom'; customRange = true"
                        :class="{ 'bg-blue-600 text-white': timeRange === 'custom', 'bg-gray-200 text-gray-800': timeRange !== 'custom' }"
                        class="p-2 rounded">Khoảng thời gian
                </button>
            </div>
            <div x-show="customRange" class="flex space-x-4">
                <input x-ref="datepicker" class="p-2 bg-gray-200 rounded" placeholder="Chọn khoảng thời gian">
            </div>
        </div>

        <!-- Sales Overview -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <h2 class="text-xl font-semibold mb-2">Tổng quan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="p-6 bg-green-100 rounded-lg shadow-md flex items-center">
                    <div class="flex-1">
                        <div class="text-lg font-medium text-green-800 flex items-center">
                            <i class="fas fa-dollar-sign mr-2"></i>
                            Tổng doanh thu
                        </div>
                        <div class="text-3xl font-bold text-green-900" x-text="totalSales.revenue"></div>
                    </div>
                    {{--                    <div class="text-green-600 flex items-center">--}}
                    {{--                        <i class="fas fa-arrow-up text-2xl"></i>--}}
                    {{--                        <span class="ml-1 text-xl font-semibold">15%</span>--}}
                    {{--                    </div>--}}
                </div>

                <div class="p-6 bg-blue-100 rounded-lg shadow-md flex items-center">
                    <div class="flex-1">
                        <div class="text-lg font-medium text-green-800 flex items-center">
                            <i class="fas fa-dollar-sign mr-2"></i>
                            Tổng chi phí
                        </div>
                        <div class="text-3xl font-bold text-green-900" x-text="totalSales.cost"></div>
                    </div>
                    {{--                    <div class="text-green-600 flex items-center">--}}
                    {{--                        <i class="fas fa-arrow-up text-2xl"></i>--}}
                    {{--                        <span class="ml-1 text-xl font-semibold">15%</span>--}}
                    {{--                    </div>--}}
                </div>

                <div class="p-6 bg-yellow-100 rounded-lg shadow-md flex items-center">
                    <div class="flex-1">
                        <div class="text-lg font-medium text-green-800 flex items-center">
                            <i class="fas fa-dollar-sign mr-2"></i>
                            Tổng lợi nhuận
                        </div>
                        <div class="text-3xl font-bold text-green-900" x-text="totalSales.profit"></div>
                    </div>
                    {{--                    <div class="text-green-600 flex items-center">--}}
                    {{--                        <i class="fas fa-arrow-up text-2xl"></i>--}}
                    {{--                        <span class="ml-1 text-xl font-semibold">15%</span>--}}
                    {{--                    </div>--}}
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <h2 class="text-xl font-semibold mb-2">Biểu Đồ Kinh Doanh</h2>
            <canvas id="salesChart" width="400" height="200"></canvas>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Sales by Category Chart -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <h2 class="text-xl font-semibold mb-2 text-center">Doanh Thu Theo Danh Mục Sản Phẩm</h2>
                <canvas id="categoryChart" width="400" height="200"></canvas>
            </div>

            <!-- Sales by Brand Chart -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <h2 class="text-xl font-semibold mb-2 text-center">Doanh Thu Theo Hãng Sản Xuất</h2>
                <canvas id="brandChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white p-4 rounded shadow mb-4">
            <h2 class="text-xl font-semibold mb-2">Đơn Hàng Gần Đây</h2>
            <table class="w-full text-left border-collapse">
                <thead>
                <tr>
                    <th class="p-2 border-b">Mã Đơn Hàng</th>
                    <th class="p-2 border-b">Khách Hàng</th>
                    <th class="p-2 border-b">Trạng Thái</th>
                    <th class="p-2 border-b">Ngày Đặt</th>
                    <th class="p-2 border-b">Tổng tiền</th>
                </tr>
                </thead>
                <tbody>
                <template x-for="order in recentOrders" :key="order.id">
                    <tr>
                        <td class="p-2 border-b">
                            <a :href="order?.url" x-text="order?.order_code" class="text-blue-600"></a>
                        </td>
                        <td class="p-2 border-b" x-text="order?.customer.name"></td>
                        <td class="p-2 border-b" x-text="order?.status"></td>
                        <td class="p-2 border-b" x-text="order?.formatted_created_at"></td>
                        <td class="p-2 border-b" x-text="order?.amount"></td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Top Selling Products -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <h2 class="text-xl font-semibold mb-2">Top Sản Phẩm Bán Chạy</h2>
                <ul>
                    <template x-for="product in topProducts" :key="product.id">
                        <li class="border-b py-2">
                            <span x-text="product.name"></span> - <span x-text="product.total_sales"></span>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Inventory -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <h2 class="text-xl font-semibold mb-2">Sản Phẩm Sắp Hết Hàng</h2>
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr>
                        <th class="p-2 border-b">Tên Sản Phẩm</th>
                        <th class="p-2 border-b">Màu Sắc</th>
                        <th class="p-2 border-b">Số Lượng Còn Lại</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="product in lowStockProducts" :key="product.id">
                        <tr>
                            <td class="p-2 border-b">
                                <a :href="product?.url" x-text="product?.product_name" class="text-blue-600"></a>
                            </td>
                            <td class="p-2 border-b" x-text="product?.color"></td>
                            <td class="p-2 border-b" x-text="product?.quantity"></td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded shadow mb-4">
                <h2 class="text-xl font-semibold mb-2">Trạng thái đơn hàng</h2>
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr>
                        <th class="p-2 border-b">Trạng Thái</th>
                        <th class="p-2 border-b">Số Đơn</th>
                        <th class="p-2 border-b">Tổng Doanh Thu</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="(groupStatus, index) in orderByStatus" :key="index">
                        <tr>
                            <td class="p-2 border-b" x-text="groupStatus?.status"></td>
                            <td class="p-2 border-b" x-text="groupStatus?.count"></td>
                            <td class="p-2 border-b" x-text="groupStatus?.revenue"></td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('vendor-scripts')
    <script src="{{ asset('plugins/chartjs/chart.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/lang/vn.js') }}"></script>
@endsection

@section('custom-scripts')
    <script>
        function formatTooltip(tooltipItem) {
            let value = tooltipItem.raw;
            let formattedValue = new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(value);
            return `${tooltipItem.dataset.label}: ${formattedValue}`;
        }

        function dashboard() {
            return {
                customRange: false,
                startDate: '',
                endDate: '',
                selectedDate: '',
                totalSales: {
                    today: '',
                    week: '',
                    month: '',
                    year: '',
                },
                recentOrders: [],
                topProducts: [],
                lowStockProducts: [],
                salesChartData: {
                    labels: [],
                    revenue: [],
                    cost: [],
                    profit: []
                },
                categoryChartData: {
                    labels: [],
                    data: []
                },
                brandChartData: {
                    labels: [],
                    data: []
                },
                salesChart: null,
                categoryChart: null,
                brandChart: null,
                timeRange: 'today',
                timeRangePicker: null,
                orderByStatus: [],
                init() {
                    this.fetchDashboardData();
                    this.initFlatpickr();
                    this.$watch('timeRange', (value) => {
                        if (value !== 'custom') {
                            this.fetchDashboardData();
                        }
                    });
                    this.$watch('startDate', () => {
                        if (this.customRange && this.startDate && this.endDate) {
                            this.fetchDashboardData();
                        }
                    });
                    this.$watch('endDate', () => {
                        if (this.customRange && this.startDate && this.endDate) {
                            this.fetchDashboardData();
                        }
                    });
                    this.$watch('customRange', (value) => {
                        if (!value) {
                            this.startDate = '';
                            this.endDate = '';
                            this.timeRangePicker?.clear();
                        }
                    });
                },
                fetchDashboardData() {
                    const url = `{{ route('admin.dashboardData') }}?timeRange=${this.timeRange}&startDate=${this.startDate}&endDate=${this.endDate}`;
                    fetch(url, {headers: {'Accept': 'application/json'}})
                        .then(response => response.json())
                        .then(data => {
                            this.totalSales.revenue = `${data.totalSales.revenue}`;
                            this.totalSales.cost = `${data.totalSales.cost}`;
                            this.totalSales.profit = `${data.totalSales.profit}`;

                            this.recentOrders = data.recentOrders;
                            this.topProducts = data.topProducts;
                            this.lowStockProducts = data.lowStockProducts;
                            this.orderByStatus = data.orderByStatus;

                            this.salesChartData.labels = data.salesChartData.labels;
                            this.salesChartData.revenue = data.salesChartData.revenue;
                            this.salesChartData.cost = data.salesChartData.cost;
                            this.salesChartData.profit = data.salesChartData.profit;

                            this.categoryChartData.labels = data.salesByCategory.labels;
                            this.categoryChartData.data = data.salesByCategory.data;

                            this.brandChartData.labels = data.salesByBrand.labels;
                            this.brandChartData.data = data.salesByBrand.data;

                            this.updateSalesChart();
                            this.updateCategoryChart();
                            this.updateBrandChart();
                        });
                },
                updateSalesChart() {
                    const ctx = document.getElementById('salesChart').getContext('2d');

                    if (this.salesChart) {
                        this.salesChart.destroy();
                    }

                    this.salesChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.salesChartData.labels,
                            datasets: [
                                {
                                    label: 'Doanh thu',
                                    data: this.salesChartData.revenue,
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Chi phí',
                                    data: this.salesChartData.cost,
                                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Lợi nhuận',
                                    data: this.salesChartData.profit,
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1,
                                    type: 'line'
                                }
                            ]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: formatTooltip
                                    }
                                }
                            },
                        }
                    });
                },
                updateCategoryChart() {
                    const ctx = document.getElementById('categoryChart').getContext('2d');

                    if (this.categoryChart) {
                        this.categoryChart.destroy();
                    }

                    this.categoryChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: this.categoryChartData.labels,
                            datasets: [
                                {
                                    label: 'Doanh thu',
                                    data: this.categoryChartData.data,
                                    backgroundColor: this.categoryChartData.labels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.2)`),
                                    borderColor: this.categoryChartData.labels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`),
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: formatTooltip
                                    }
                                }
                            },
                        }
                    });
                },
                updateBrandChart() {
                    const ctx = document.getElementById('brandChart').getContext('2d');

                    if (this.brandChart) {
                        this.brandChart.destroy();
                    }

                    this.brandChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: this.brandChartData.labels,
                            datasets: [
                                {
                                    label: 'Doanh thu',
                                    data: this.brandChartData.data,
                                    backgroundColor: this.brandChartData.labels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.2)`),
                                    borderColor: this.brandChartData.labels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`),
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: formatTooltip
                                    }
                                }
                            },
                        }
                    });
                },
                initFlatpickr() {
                    this.timeRangePicker = flatpickr(this.$refs.datepicker, {
                        onChange: ([startDate, endDate]) => {
                            if (startDate && endDate) {
                                this.startDate = flatpickr.formatDate(startDate, 'Y-m-d');
                                this.endDate = flatpickr.formatDate(endDate, 'Y-m-d');
                            }
                        },
                        locale: "vn",
                        mode: "range",
                        altInput: true,
                        conjunction: " - ",
                        maxDate: "today",
                        altFormat: "d/m/Y",
                        dateFormat: "Y-m-d",
                    });
                },
            }
        }
    </script>
@endsection

