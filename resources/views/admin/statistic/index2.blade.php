<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Product Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
</head>
<body class="bg-gray-100 flex">

<!-- Sidebar -->
<div class="w-64 bg-gray-800 text-white min-h-screen">
    <div class="p-4">
        <h1 class="text-2xl font-bold">Admin Dashboard</h1>
    </div>
    <nav class="mt-10">
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Dashboard</a>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Products</a>
        <a href="#"
           class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Users</a>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 hover:text-white">Settings</a>
    </nav>
</div>

<!-- Main Content -->
<div class="flex-1 flex flex-col">
    <!-- Navbar -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 leading-tight">Dashboard</h2>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center focus:outline-none">
                    <img src="https://via.placeholder.com/40" alt="Avatar" class="rounded-full mr-2">
                    <span>John Doe</span>
                    <svg class="ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M5.23 7.21a.75.75 0 011.04-.02l3.73 3.59 3.73-3.59a.75.75 0 111.02 1.1l-4.23 4.06a.75.75 0 01-1.04 0L5.25 8.3a.75.75 0 01-.02-1.08z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2">
                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                    <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumbs -->
    <nav class="bg-gray border-b border-gray-200">
        <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="#" class="text-gray-500 hover:text-gray-700">Home</a>
                </li>
                <li>
                    <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </li>
                <li>
                    <a href="#" class="text-gray-500 hover:text-gray-700">Products</a>
                </li>
            </ol>
        </div>
    </nav>

    <!-- Main Section -->
    <main class="flex-1 bg-gray-100" x-data="dashboard()" x-init="init()">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Time Range Filter -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <div class="flex space-x-4 mb-4">
                    <button @click="timeRange = 'today'; customRange = false"
                            :class="{ 'bg-blue-600 text-white': timeRange === 'today', 'bg-gray-200 text-gray-800': timeRange !== 'today' }"
                            class="p-2 rounded">Today
                    </button>
                    <button @click="timeRange = 'thisWeek'; customRange = false"
                            :class="{ 'bg-blue-600 text-white': timeRange === 'thisWeek', 'bg-gray-200 text-gray-800': timeRange !== 'thisWeek' }"
                            class="p-2 rounded">This Week
                    </button>
                    <button @click="timeRange = 'thisMonth'; customRange = false"
                            :class="{ 'bg-blue-600 text-white': timeRange === 'thisMonth', 'bg-gray-200 text-gray-800': timeRange !== 'thisMonth' }"
                            class="p-2 rounded">This Month
                    </button>
                    <button @click="timeRange = 'thisYear'; customRange = false"
                            :class="{ 'bg-blue-600 text-white': timeRange === 'thisYear', 'bg-gray-200 text-gray-800': timeRange !== 'thisYear' }"
                            class="p-2 rounded">This Year
                    </button>
                    <button @click="timeRange = 'custom'; customRange = true"
                            :class="{ 'bg-blue-600 text-white': timeRange === 'custom', 'bg-gray-200 text-gray-800': timeRange !== 'custom' }"
                            class="p-2 rounded">Custom Range
                    </button>
                </div>
                <div x-show="customRange" class="flex space-x-4">
                    <input x-ref="datepicker" class="p-2 bg-gray-200 rounded" placeholder="select date">
                </div>
            </div>

            <!-- Sales Overview -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <h2 class="text-xl font-semibold mb-2">Sales Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 bg-green-100 rounded">
                        <div class="text-sm">Total Sales Today</div>
                        <div class="text-2xl font-bold" x-text="totalSales.today"></div>
                    </div>
                    <div class="p-4 bg-blue-100 rounded">
                        <div class="text-sm">Total Sales This Week</div>
                        <div class="text-2xl font-bold" x-text="totalSales.week"></div>
                    </div>
                    <div class="p-4 bg-yellow-100 rounded">
                        <div class="text-sm">Total Sales This Month</div>
                        <div class="text-2xl font-bold" x-text="totalSales.month"></div>
                    </div>
                    <div class="p-4 bg-red-100 rounded">
                        <div class="text-sm">Total Sales This Year</div>
                        <div class="text-2xl font-bold" x-text="totalSales.year"></div>
                    </div>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <h2 class="text-xl font-semibold mb-2">Sales Chart</h2>
                <canvas id="salesChart" width="400" height="200"></canvas>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Sales by Category Chart -->
                <div class="bg-white p-4 rounded shadow mb-4">
                    <h2 class="text-xl font-semibold mb-2">Sales by Category</h2>
                    <canvas id="categoryChart" width="400" height="200"></canvas>
                </div>

                <!-- Sales by Brand Chart -->
                <div class="bg-white p-4 rounded shadow mb-4">
                    <h2 class="text-xl font-semibold mb-2">Sales by Brand</h2>
                    <canvas id="brandChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <h2 class="text-xl font-semibold mb-2">Recent Orders</h2>
                <ul>
                    <template x-for="order in recentOrders" :key="order.id">
                        <li class="border-b py-2">
                            <span x-text="order.name"></span> - <span x-text="order.status"></span>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Top Selling Products -->
            <div class="bg-white p-4 rounded shadow mb-4">
                <h2 class="text-xl font-semibold mb-2">Top Selling Products</h2>
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
                <h2 class="text-xl font-semibold mb-2">Inventory</h2>
                <ul>
                    <template x-for="(product, index) in inventory" :key="index">
                        <li class="border-b py-2">
                            <span x-text="product.name || 'Unknown Product'"></span> -
                            <span x-text="product.quantity || '0'"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white shadow mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-gray-600 text-center">&copy; 2024 Your Company. All rights reserved.</p>
        </div>
    </footer>
</div>

<script>
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
            inventory: [],
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
            timeRange: 'thisMonth',
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
            },
            fetchDashboardData() {
                const url = `{{ route('admin.dashboardData') }}?timeRange=${this.timeRange}&startDate=${this.startDate}&endDate=${this.endDate}`;
                fetch(url, {headers: {'Accept': 'application/json'}})
                    .then(response => response.json())
                    .then(data => {
                        this.totalSales.today = `$${data.totalSales.today}`;
                        this.totalSales.week = `$${data.totalSales.week}`;
                        this.totalSales.month = `$${data.totalSales.month}`;
                        this.totalSales.year = `$${data.totalSales.year}`;

                        this.recentOrders = data.recentOrders;
                        this.topProducts = data.topProducts;
                        this.inventory = data.inventory;

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
                                label: 'Revenue',
                                data: this.salesChartData.revenue,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Cost',
                                data: this.salesChartData.cost,
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Profit',
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
                        }
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
                                label: 'Sales by Category',
                                data: this.categoryChartData.data,
                                backgroundColor: this.categoryChartData.labels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.2)`),
                                borderColor: this.categoryChartData.labels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`),
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
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
                                label: 'Sales by Brand',
                                data: this.brandChartData.data,
                                backgroundColor: this.brandChartData.labels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.2)`),
                                borderColor: this.brandChartData.labels.map(() => `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 1)`),
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            },
            initFlatpickr() {
                flatpickr(this.$refs.datepicker, {
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

</body>
</html>
