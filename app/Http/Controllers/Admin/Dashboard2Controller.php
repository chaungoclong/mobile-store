<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Dashboard2Controller extends Controller
{
    public function index(Request $request): View|Factory|JsonResponse|Application
    {
        if ($request->ajax() && $request->wantsJson()) {
            return $this->getDashboardData($request);
        }

        return view('admin.statistic.index2');
    }

    public function getDashboardData(Request $request): JsonResponse
    {
        // Validate the request inputs
        $validator = Validator::make($request->all(), [
            'date_range' => 'required|in:today,yesterday,this_week,last_week,this_month,last_month,this_year,last_year,custom',
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $dateRange = $request->input('date_range');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Determine the start and end dates based on the selected date range
        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'last_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'last_year':
                $startDate = Carbon::now()->subYear()->startOfYear();
                $endDate = Carbon::now()->subYear()->endOfYear();
                break;
            case 'custom':
                if (!$startDate || !$endDate) {
                    return response()->json([
                        'error' => 'Start date and end date are required for custom date range.',
                    ], 400);
                }
                break;
            default:
                return response()->json([
                    'error' => 'Invalid date range selection.',
                ], 400);
        }

        return response()->json([
            'totalProducts' => DB::table('products')->count(),
            'totalCustomers' => DB::table('users')->count(),
            'totalPendingOrders' => DB::table('orders')->where('status', 0)->count(),
            'salesData' => $this->getSalesData($startDate, $endDate),
            'salesByCategory' => $this->getSalesByCategory($startDate, $endDate),
            'salesByProducers' => $this->getSalesByProducers($startDate, $endDate),
            'topSellers' => $this->getTopSellers($startDate, $endDate),
            'topRatedProducts' => $this->getTopRatedProducts($startDate, $endDate),
            'recentOrders' => $this->getRecentOrders($startDate, $endDate),
            'lowStockProducts' => $this->getLowStockProducts(),
        ]);
    }

    private function getSalesData($startDate, $endDate): array
    {
        dd(DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(DB::raw('SUM(order_details.price * order_details.quantity) as total_sales'), 'orders.created_at')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(orders.created_at)'))->toSql());
        $salesData = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(DB::raw('SUM(order_details.price * order_details.quantity) as total_sales'), 'orders.created_at')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(orders.created_at)'))
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->created_at)->format('Y-m-d');
            });

        $period = Carbon::parse($startDate)->diffInDays($endDate) + 1 < 365
            ? Carbon::parse($startDate)->toPeriod($endDate)
            : Carbon::parse($startDate)->startOfMonth()->toPeriod($endDate, '1 month');

        $dates = [];
        $sales = [];

        foreach ($period as $date) {
            $format = Carbon::parse($startDate)->diffInDays($endDate) + 1 < 365
                ? $date->format('Y-m-d')
                : $date->locale('vi')->translatedFormat('F');
            $dates[] = $format;
            $sales[] = $salesData->get($date->format('Y-m-d'), (object)['total_sales' => 0])->total_sales;
        }

        return [
            'labels' => $dates,
            'data' => $sales
        ];
    }

    private function getSalesByCategory($startDate, $endDate): array
    {
        $salesByCategory = DB::table('order_details')
            ->join('products', 'order_details.product_detail_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(
                'categories.name as category',
                DB::raw('SUM(order_details.price * order_details.quantity) as total_sales')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('categories.name')
            ->get();

        return [
            'labels' => $salesByCategory->pluck('category')->toArray(),
            'data' => $salesByCategory->pluck('total_sales')->toArray()
        ];
    }

    private function getSalesByProducers($startDate, $endDate): array
    {
        $salesByProducers = DB::table('order_details')
            ->join('products', 'order_details.product_detail_id', '=', 'products.id')
            ->join('producers', 'products.producer_id', '=', 'producers.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(
                'producers.name as producer',
                DB::raw('SUM(order_details.price * order_details.quantity) as total_sales')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('producers.name')
            ->get();

        return [
            'labels' => $salesByProducers->pluck('producer')->toArray(),
            'data' => $salesByProducers->pluck('total_sales')->toArray()
        ];
    }

    private function getTopSellers($startDate, $endDate): Collection
    {
        return DB::table('order_details')
            ->join('product_details', 'order_details.product_detail_id', '=', 'product_details.id')
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->select(
                'products.name as product_name',
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('SUM(order_details.price * order_details.quantity) as total_sales')
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('products.name')
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();
    }

    private function getTopRatedProducts($startDate, $endDate): Collection
    {
        return DB::table('products')
            ->select('products.name', DB::raw('AVG(reviews.rating) as average_rating'))
            ->leftJoin('reviews', 'products.id', '=', 'reviews.product_id')
            ->whereBetween('products.created_at', [$startDate, $endDate])
            ->groupBy('products.id')
            ->orderBy('average_rating', 'desc')
            ->limit(5)
            ->get();
    }

    private function getRecentOrders($startDate, $endDate): Collection
    {
        return DB::table('orders')
            ->select('orders.*')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getLowStockProducts(): Collection
    {
        return DB::table('product_details')
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->select(
                'products.name',
                'products.image',
                'product_details.color',
                'product_details.quantity',
                'product_details.sale_price'
            )
            ->where('product_details.quantity', '<', 10)
            ->get();
    }
}
