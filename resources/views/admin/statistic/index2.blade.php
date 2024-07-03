@extends('admin.layouts.master')

@section('title', 'Thống Kê')

@section('content')
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="#" class="text-gray-700 hover:text-gray-900 inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"
                         xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M10 3.172l7.071 7.071a1 1 0 01-1.414 1.414L11 7.414V17a1 1 0 11-2 0V7.414L4.343 11.657a1 1 0 11-1.414-1.414L10 3.172z"></path>
                    </svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 18l-6-6 6-6v12zm6 0V6l6 6-6 6z"/>
                    </svg>
                    <a href="#" class="ml-1 text-gray-700 hover:text-gray-900 md:ml-2">Dashboard</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 18l-6-6 6-6v12zm6 0V6l6 6-6 6z"/>
                    </svg>
                    <span class="ml-1 text-gray-500 md:ml-2">Sales</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="container mx-auto py-10">
        <!-- Date Filter Form -->
        <form id="filterForm" class="mb-6">

        </form>

        <!-- Widgets -->
        <div id="widgets" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Widgets will be dynamically updated -->
        </div>

        <!-- Charts -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <div class="flex justify-between">
                <h2 class="text-3xl font-bold mb-4">Doanh thu</h2>
                <div class="flex items-center space-x-3 mb-5 justify-end">
                    <select id="dateRange"
                            class="block h-11 appearance-none bg-white border border-gray-300 text-gray-700 py-2 px-3 pr-8
                        rounded leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                        flex items-center justify-center"
                            name="date_range_type">
                        <option value="today">Hôm nay</option>
                        <option value="this_week">Tuần này</option>
                        <option value="this_month">Tháng này</option>
                        <option value="this_year">Năm này</option>
                        <option value="custom">Khoảng thời gian</option>
                    </select>

                    <div class="flex items-center space-x-2 hidden" id="customRange">
                        <input id="customRangeInput"
                               class="form-input border-gray-300 rounded-md h-11 flex items-center justify-center"
                               name="date_range">
                    </div>

                    <button type="button" onclick="fetchData()"
                            class="bg-blue-500 text-white px-4 py-2 rounded-md h-11 flex items-center justify-center">
                        Lọc
                    </button>
                </div>
            </div>
            <canvas id="salesChart" class="h-80"></canvas>
        </div>

        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h2 class="text-3xl font-bold mb-4">Doanh Thu theo Danh mục và Hãng sản xuất</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Theo Danh mục</h3>
                    <canvas id="salesByCategoryChart"></canvas>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Theo Hãng sản xuất</h3>
                    <canvas id="salesByProducersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tables -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h2 class="text-3xl font-bold mb-4">Sản phẩm bán chạy</h2>
            <table id="topSellerTable" class="min-w-full bg-white">
                <thead class="bg-gray-100">
                <tr>
                    <th class="py-2">ID</th>
                    <th class="py-2">Tên</th>
                    <th class="py-2">Số lượng đã bán</th>
                    <th class="py-2">Tổng doanh thu</th>
                </tr>
                </thead>
                <tbody>
                <!-- Data will be dynamically inserted -->
                </tbody>
            </table>
        </div>

        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h2 class="text-3xl font-bold mb-4">Sản phẩm được đánh giá cao</h2>
            <table id="topRatedTable" class="min-w-full bg-white">
                <thead class="bg-gray-100">
                <tr>
                    <th class="py-2">ID</th>
                    <th class="py-2">Tên</th>
                    <th class="py-2">Đánh giá</th>
                </tr>
                </thead>
                <tbody>
                <!-- Data will be dynamically inserted -->
                </tbody>
            </table>
        </div>

        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h2 class="text-3xl font-bold mb-4">Đơn hàng mới</h2>
            <table id="recentOrdersTable" class="min-w-full bg-white">
                <thead class="bg-gray-100">
                <tr>
                    <th class="py-2">ID</th>
                    <th class="py-2">Order Code</th>
                    <th class="py-2">Customer Name</th>
                    <th class="py-2">Amount</th>
                    <th class="py-2">Status</th>
                </tr>
                </thead>
                <tbody>
                <!-- Data will be dynamically inserted -->
                </tbody>
            </table>
        </div>

        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <h2 class="text-3xl font-bold mb-4">Sản phẩm sắp hết hàng</h2>
            <table id="lowStockTable" class="min-w-full bg-white">
                <thead class="bg-gray-100">
                <tr>
                    <th class="py-2">ID</th>
                    <th class="py-2">Ảnh</th>
                    <th class="py-2">Tên</th>
                    <th class="py-2">Màu</th>
                    <th class="py-2">Số lượng</th>
                </tr>
                </thead>
                <tbody>
                <!-- Data will be dynamically inserted -->
                </tbody>
            </table>
        </div>

    </div>
@endsection

@section('embed-js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('custom-js')
    <script>
        let saleChart;
        let saleByCategoryChart;
        let saleByProducerChart;

        const startOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1);
        const today = new Date();
        let startDate = flatpickr.formatDate(startOfMonth, 'Y-m-d');
        let endDate = flatpickr.formatDate(today, 'Y-m-d');
        const dateRange = document.getElementById('dateRange').value;

        async function fetchData() {
            if (saleChart) {
                saleChart.destroy();
            }

            if (saleByCategoryChart) {
                saleByCategoryChart.destroy();
            }

            if (saleByProducerChart) {
                saleByProducerChart.destroy();
            }

            const dateRangeType = $('#dateRangeType').val();
            let queryParams = {date_range: dateRange};
            if (dateRangeType === 'custom') {
                queryParams = {...queryParams, start_date: startDate, end_date: endDate};
            }
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const urlParams = new URLSearchParams(queryParams).toString();
            const url = `{{ route('admin.dashboard') }}?${urlParams}`;

            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                method: 'GET',
            });

            if (response.ok) {
                {
                    const data = await response.json();

                    // Update Widgets
                    document.getElementById('widgets').innerHTML = `
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Sản phẩm</h3>
                            <p class="text-2xl">${data.totalProducts}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Khách hàng</h3>
                            <p class="text-2xl">${data.totalCustomers}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h3 class="text-lg font-semibold mb-2">Đơn hàng chờ xử lý</h3>
                            <p class="text-2xl">${data.totalPendingOrders}</p>
                        </div>
                    `;

                    // Update Sales Chart
                    const salesCtx = document.getElementById('salesChart').getContext('2d');
                    saleChart = new Chart(salesCtx, {
                        type: 'line',
                        data: {
                            labels: data.salesData.map(item => item.date),
                            datasets: [{
                                label: 'Total Sales',
                                data: data.salesData.map(item => item.total_sales),
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                            }
                        }
                    });

                    // Update Sales by Category Chart
                    const salesByCategoryCtx = document.getElementById('salesByCategoryChart').getContext('2d');
                    saleByCategoryChart = new Chart(salesByCategoryCtx, {
                        type: 'pie',
                        data: {
                            labels: data.salesByCategory.map(item => item.category_name),
                            datasets: [{
                                label: 'Sales by Category',
                                data: data.salesByCategory.map(item => item.total_sales),
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            let label = context.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed !== null) {
                                                label += context.parsed + ' units';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Update Sales by Producers Chart
                    const salesByProducersCtx = document.getElementById('salesByProducersChart').getContext('2d');
                    saleByProducerChart = new Chart(salesByProducersCtx, {
                        type: 'pie',
                        data: {
                            labels: data.salesByProducers.map(item => item.producer_name),
                            datasets: [{
                                label: 'Sales by Producer',
                                data: data.salesByProducers.map(item => item.total_sales),
                                backgroundColor: [
                                    'rgba(255, 159, 64, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(75, 192, 192, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255, 159, 64, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(75, 192, 192, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            let label = context.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed !== null) {
                                                label += context.parsed + ' ₫';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });

                    // Update Top Seller Products Table
                    const topSellerTable = document.getElementById('topSellerTable').getElementsByTagName('tbody')[0];
                    topSellerTable.innerHTML = data?.topSellerProducts.map(product => `
                        <tr>
                            <td class="py-2">${product.product_id}</td>
                            <td class="py-2">${product?.product_name}</td>
                            <td class="py-2">${product?.total_quantity}</td>
                            <td class="py-2">${product?.total_money_sale.toLocaleString('vi', {
                        style: 'currency',
                        currency: 'VND'
                    })}</td>
                        </tr>
                    `).join('');

                    // Update Top Rated Products Table
                    const topRatedTable = document.getElementById('topRatedTable').getElementsByTagName('tbody')[0];
                    topRatedTable.innerHTML = data.topRatedProducts.map(product => `
                        <tr>
                            <td class="py-2">${product.id}</td>
                            <td class="py-2">${product.name}</td>
                            <td class="py-2">${product.rate}</td>
                        </tr>
                    `).join('');

                    // Update Recent Orders Table
                    const recentOrdersTable = document.getElementById('recentOrdersTable').getElementsByTagName('tbody')[0];
                    recentOrdersTable.innerHTML = data.recentOrders.map(order => `
                        <tr>
                            <td class="py-2">${order.id}</td>
                            <td class="py-2">${order.order_code}</td>
                            <td class="py-2">${order.name}</td>
                            <td class="py-2">${order.amount.toLocaleString('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    })}</td>
                            <td class="py-2">${order.status == 0 ? 'Pending' : 'Completed'}</td>
                        </tr>
                    `).join('');

                    // Update Low Stock Products Table
                    const lowStockTable = document.getElementById('lowStockTable').getElementsByTagName('tbody')[0];
                    lowStockTable.innerHTML = data.lowStockProducts.map(product => `
                        <tr>
                            <td class="py-2">${product.id}</td>
                            <td class="py-2 px-4 border-b">
                            <img src="${product?.product_image_urls[0]}" alt="${product.name}" class="w-12 h-12 object-cover">
                            </td>
                            <td class="py-2">${product.product_name}</td>
                            <td class="py-2">${product.color}</td>
                            <td class="py-2">${product.quantity}</td>
                        </tr>
                    `).join('');
                }
            }
        }

        // Fetch initial data on page load
        fetchData();

        $(function () {
            const customRangeInput = flatpickr("#customRangeInput", {
                locale: "vn",
                mode: "range",
                altInput: true,
                conjunction: " - ",
                maxDate: "today",
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                defaultDate: [startOfMonth, 'today'],
                onChange: function ([start, end]) {
                    if (start && end) {
                        startDate = flatpickr.formatDate(start, 'Y-m-d');
                        endDate = flatpickr.formatDate(end, 'Y-m-d');
                    }
                }
            });

            $('#dateRange').on('change', function () {
                const dateRangeType = $(this).val();
                if (dateRangeType === 'custom') {
                    $('#customRangeInput').closest('#customRange').removeClass('hidden');
                } else {
                    $('#customRangeInput').closest('#customRange').addClass('hidden');
                }
            })
        })
    </script>
@endsection
