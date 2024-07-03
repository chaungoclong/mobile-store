<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\ProductDetail;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Dashboard2Controller extends Controller
{
    public function index(Request $request): View|Factory|JsonResponse|Application
    {
        if ($request->ajax() && $request->wantsJson()) {
            return $this->fetchData($request);
        }

        return view('admin.statistic.index2');
    }

    private function fetchData(Request $request): JsonResponse
    {
        // Get date range from request
        $request->validate([
            'date_range' => 'required|in:today,this_week,this_month,this_year,custom',
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $dateRange = $request->input('date_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Total Products
        $totalProducts = Product::count();

        // Total Customers
        $totalCustomers = Order::distinct('user_id')->count('user_id');

        // Total Pending Orders
        $totalPendingOrders = Order::where('status', OrderStatus::Pending->value)->count();

        // Sales Data for Line Chart
        $salesData = Order::selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(amount) as total_sales')
            ->where('status', OrderStatus::Done->value) // done orders
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Sales by Category for Pie Chart
        $salesByCategory = OrderDetail::join(
            'product_details',
            'order_details.product_detail_id',
            '=',
            'product_details.id'
        )
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->selectRaw('categories.name as category_name')
            ->selectRaw('SUM(order_details.quantity * order_details.price) as total_sales')
            ->whereHas('order', function ($query) {
                $query->where('status', OrderStatus::Done->value); // done orders
            })
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('categories.name')
            ->get();

        // Sales by Producer for Pie Chart
        $salesByProducers = OrderDetail::join(
            'product_details',
            'order_details.product_detail_id',
            '=',
            'product_details.id'
        )
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->join('producers', 'products.producer_id', '=', 'producers.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->selectRaw('producers.name as producer_name')
            ->selectRaw('SUM(order_details.quantity * order_details.price) as total_sales')
            ->whereHas('order', function ($query) {
                $query->where('status', OrderStatus::Done->value); // done orders
            })
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('producers.name')
            ->get();

        // Top Seller Products
        $topSellerProducts = OrderDetail::query()
            ->join(
                'product_details',
                'order_details.product_detail_id',
                '=',
                'product_details.id'
            )
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->selectRaw('products.id as product_id, products.name as product_name')
            ->selectRaw('SUM(order_details.quantity) as total_quantity')
            ->selectRaw('SUM(order_details.quantity * order_details.price) as total_money_sale')
            ->whereHas('order', function ($query) {
                $query->where('status', OrderStatus::Done->value); // done orders
            })
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_money_sale', 'desc')
            ->limit(5)
            ->get();

        // Top Rated Products
        $topRatedProducts = Product::orderBy('rate', 'desc')->limit(5)->get();

        // Recent Orders
        $recentOrders = Order::orderBy('created_at', 'desc')->limit(5)->get();

        // Low Stock Products
        $lowStockProducts = ProductDetail::query()
            ->with([
                'product' => function ($query) {
                    $query->select(['id', 'name']);
                },
                'product_images'
            ])
            ->where('quantity', '<', 10)
            ->get();

        return response()->json([
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'totalPendingOrders' => $totalPendingOrders,
            'salesData' => $salesData,
            'salesByCategory' => $salesByCategory,
            'salesByProducers' => $salesByProducers,
            'topSellerProducts' => $topSellerProducts,
            'topRatedProducts' => $topRatedProducts,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

    /**
     * @param string $dateRange
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array|Collection
     */
    private function getSalesData(string $dateRange, string|null $startDate = null, string|null $endDate = null): array|Collection
    {
        /**
         * @var Builder $query
         */
        $query = Order::selectRaw('DATE(created_at) as date')
            ->selectRaw('SUM(amount) as total_sales')
            ->where('status', OrderStatus::Done->value);

        switch ($dateRange) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'this_year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
                break;
        }

        return $query->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getSalesByCategory($orders)
    {
        // Implement the logic to calculate sales by category
    }

    private function getSalesByProducers($orders)
    {
        // Implement the logic to calculate sales by producers
    }

    private function getTopSellers($orders)
    {
        // Implement the logic to get top sellers
    }

    private function getTopRatedProducts()
    {
        // Implement the logic to get top-rated products
    }

    private function getRecentOrders()
    {
        // Implement the logic to get recent orders
    }

    private function getLowStockProducts()
    {
        // Implement the logic to get low stock products
    }

    private function applyDateFilter(Builder $query): void
    {
    }
}
