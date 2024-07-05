<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.statistic.index2');
    }

    public function getDashboardData(Request $request): JsonResponse
    {
        $period = $request->input('timeRange', 'month');
        $currentPeriod = $this->getPeriodRange($period);
        $startDate = $currentPeriod['start'];
        $endDate = $currentPeriod['end'];

        // Tổng doanh thu
        $today = Order::query()
            ->whereDate('created_at', Carbon::today())
            ->where('status', OrderStatus::Done->value)
            ->sum('amount');

        $week = Order::query()
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('status', OrderStatus::Done->value)
            ->sum('amount');

        $month = Order::query()
            ->whereMonth('created_at', Carbon::now()->month)
            ->where('status', OrderStatus::Done->value)
            ->sum('amount');

        $year = Order::query()
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', OrderStatus::Done->value)
            ->sum('amount');

        // Đơn hàng gần đây
        $recentOrders = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Sản phẩm bán chạy nhất
        $topProducts = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_details', 'order_details.product_detail_id', '=', 'product_details.id')
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->where('orders.status', OrderStatus::Done->value)
            ->select('products.id', 'products.name', DB::raw('SUM(order_details.quantity) as total_sales'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sales', 'desc')
            ->take(10)
            ->get();

        // Kho hàng
        $inventory = DB::table('product_details')
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->select('products.name', 'product_details.quantity', 'product_details.color')
            ->get();

        // Dữ liệu doanh số
        $salesData = $this->getSalesData($period);

        // Doanh số theo danh mục
        $salesByCategory = DB::table('order_details')
            ->join('product_details', 'order_details.product_detail_id', '=', 'product_details.id')
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category',
                DB::raw('SUM(order_details.price * order_details.quantity) as total_sales')
            )
            ->whereBetween('order_details.created_at', [$startDate, $endDate])
            ->groupBy('categories.name')
            ->get();

        $categoryLabels = $salesByCategory->pluck('category');
        $categorySalesData = $salesByCategory->pluck('total_sales');

        // Doanh số theo thương hiệu
        $salesByBrand = DB::table('order_details')
            ->join('product_details', 'order_details.product_detail_id', '=', 'product_details.id')
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->join('producers', 'products.producer_id', '=', 'producers.id')
            ->select(
                'producers.name as brand',
                DB::raw('SUM(order_details.price * order_details.quantity) as total_sales')
            )
            ->whereBetween('order_details.created_at', [$startDate, $endDate])
            ->groupBy('producers.name')
            ->get();

        $brandLabels = $salesByBrand->pluck('brand');
        $brandSalesData = $salesByBrand->pluck('total_sales');

        return response()->json([
            'totalSales' => [
                'today' => $today,
                'week' => $week,
                'month' => $month,
                'year' => $year,
            ],
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'inventory' => $inventory,
            'salesChartData' => $salesData['chart'],
            'salesByCategory' => [
                'labels' => $categoryLabels,
                'data' => $categorySalesData,
            ],
            'salesByBrand' => [
                'labels' => $brandLabels,
                'data' => $brandSalesData,
            ],
            'totalRevenue' => $salesData['total_revenue'],
            'totalCost' => $salesData['total_cost'],
            'totalProfit' => $salesData['total_profit'],
        ]);
    }

    public function getSalesData($period): array
    {
        $currentPeriod = $this->getPeriodRange($period);
        $currentData = $this->getAggregatedData(
            $currentPeriod['start'],
            $currentPeriod['end'],
            $currentPeriod['interval']
        );
        $labels = $this->generateLabels($currentPeriod['start'], $currentPeriod['end'], $period);
        $currentData = $this->mergeWithFullRange($currentData, $labels, $period);

        return [
            'chart' => [
                'labels' => $labels,
                'revenue' => $currentData->pluck('revenue')->toArray(),
                'cost' => $currentData->pluck('cost')->toArray(),
                'profit' => $currentData->pluck('profit')->toArray(),
            ],
            'total_revenue' => $currentData->sum('revenue'),
            'total_cost' => $currentData->sum('cost'),
            'total_profit' => $currentData->sum('profit'),
        ];
    }

    private function getPeriodRange($period): array
    {
        $now = Carbon::now();

        $result = match ($period) {
            'week' => [
                'start' => $now->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
                'interval' => 'day'
            ],
            'month' => [
                'start' => $now->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'interval' => 'day'
            ],
            'year' => [
                'start' => $now->startOfYear(),
                'end' => $now->copy()->endOfYear(),
                'interval' => 'month'
            ],
            default => throw new InvalidArgumentException('Invalid period specified.'),
        };

        if ($result['end']->greaterThan(now())) {
            $result['end'] = now();
        }

        return $result;
    }

    private function getPreviousPeriodRange($period): array
    {
        $currentPeriod = $this->getPeriodRange($period);

        return match ($period) {
            'week' => [
                'start' => $currentPeriod['start']->copy()->subWeek(),
                'end' => $currentPeriod['end']->copy()->subWeek(),
                'interval' => 'day'
            ],
            'month' => [
                'start' => $currentPeriod['start']->copy()->subMonth(),
                'end' => $currentPeriod['end']->copy()->subMonth(),
                'interval' => 'day'
            ],
            'year' => [
                'start' => $currentPeriod['start']->copy()->subYear(),
                'end' => $currentPeriod['end']->copy()->subYear(),
                'interval' => 'month'
            ],
            default => throw new InvalidArgumentException('Invalid period specified.'),
        };
    }

    public function getAggregatedData($start, $end, $interval)
    {
        $selectRaw = $interval === 'day'
            ? 'DATE(orders.created_at) as period'
            : 'DATE_FORMAT(orders.created_at, "%Y-%m") as period';

        return DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.status', OrderStatus::Done->value)
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                DB::raw($selectRaw),
                DB::raw('SUM(order_details.price * order_details.quantity) as revenue'),
                DB::raw('SUM(order_details.import_price * order_details.quantity) as cost')
            )
            ->groupBy('period')
            ->orderBy('period', 'asc')
            ->get()
            ->map(function ($item) {
                $item->profit = $item->revenue - $item->cost;
                return $item;
            });
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param string $period
     * @return array
     */
    private function generateLabels(Carbon $start, Carbon $end, string $period): array
    {
        return $period === 'year' ? $this->generateMonthRange($start, $end) : $this->generateDayRange($start, $end);
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    public function generateMonthRange(Carbon $start, Carbon $end): array
    {
        $months = [];
        for ($date = $start; $date->lte($end); $date->addMonth()) {
            $months[] = $date->copy()->format('Y-m');
        }

        return $months;
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    public function generateDayRange(Carbon $start, Carbon $end): array
    {
        $dates = [];
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dates[] = $date->copy()->format('Y-m-d');
        }
        return $dates;
    }

    public function mergeWithFullRange($data, $fullRange, $period): Collection
    {
        $dataMap = $data->keyBy('period');

        return collect($fullRange)
            ->map(function ($date) use ($period, $dataMap) {
                $defaultValues = [
                    'revenue' => 0,
                    'cost' => 0,
                    'profit' => 0,
                ];
                $item = $dataMap->get($date, (object)$defaultValues);
                $item->period = $date;
                if ($period === 'year') {
                    $item->period = Carbon::createFromFormat('Y-m', $date)
                        ->locale('vi')
                        ->isoFormat('MMMM');
                }

                return $item;
            });
    }
}
