<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\TimeInterval;
use App\Enums\TimeRange;
use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductDetail;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.index2');
    }

    public function getDashboardData(Request $request): JsonResponse
    {
        $this->validateRequest($request);

        $period = $request->input('timeRange', TimeRange::ThisMonth->value);
        $startDateInput = $request->input('startDate');
        $endDateInput = $request->input('endDate');
        $periodRange = $this->getPeriodRange($period, $startDateInput, $endDateInput);
        $startDate = $periodRange['start'];
        $endDate = $periodRange['end'];

        $recentOrders = $this->getRecentOrders($startDate, $endDate);
        $topProducts = $this->getTopProducts($startDate, $endDate);
        $lowStockProducts = $this->getLowStockProduct();
        $salesData = $this->getSalesData($periodRange);
        $salesByCategory = $this->getSalesByCategory($startDate, $endDate);
        $salesByBrand = $this->getSalesByBrand($startDate, $endDate);
        $orderByStatus = $this->getOrderByStatus($startDate, $endDate);

        return response()->json([
            'totalSales' => [
                'revenue' => Helpers::formatVietnameseCurrency($salesData['total_revenue']),
                'cost' => Helpers::formatVietnameseCurrency($salesData['total_cost']),
                'profit' => Helpers::formatVietnameseCurrency($salesData['total_profit']),
            ],
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'salesChartData' => $salesData['chart'],
            'salesByCategory' => $salesByCategory,
            'salesByBrand' => $salesByBrand,
            'orderByStatus' => $orderByStatus
        ]);
    }

    private function validateRequest(Request $request): void
    {
        $request->validate([
            'timeRange' => ['required', Rule::in(TimeRange::values())],
            'startDate' => ['required_if:timeRange,' . TimeRange::Custom->value, 'nullable', 'date_format:Y-m-d'],
            'endDate' => ['required_if:timeRange,' . TimeRange::Custom->value, 'nullable', 'date_format:Y-m-d'],
        ]);
    }

    private function getPeriodRange(string $period, string $startDate = null, string $endDate = null): array
    {
        $now = Carbon::now();

        switch ($period) {
            case TimeRange::ToDay->value:
                $start = $now->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case TimeRange::ThisWeek->value:
                $start = $now->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case TimeRange::ThisMonth->value:
                $start = $now->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case TimeRange::ThisYear->value:
                $start = $now->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case TimeRange::Custom->value:
                $start = Carbon::createFromFormat('Y-m-d', $startDate);
                $end = Carbon::createFromFormat('Y-m-d', $endDate);
                break;
            default:
                throw new InvalidArgumentException('Invalid period specified.');
        }

        $interval = $this->determineInterval($start, $end);

        if (in_array($period, TimeRange::PresentTimeRanges(), true) && $end->greaterThan(now())) {
            $end = now();
        }

        return [
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
            'interval' => $interval,
            'period' => $period,
        ];
    }

    private function determineInterval(Carbon $start, Carbon $end): string
    {
        $diffInDays = $end->diffInDays($start);

        if ($diffInDays <= 1) {
            return TimeInterval::Hour->value;
        }

        if ($diffInDays <= 31) {
            return TimeInterval::Day->value;
        }

        if ($diffInDays <= 366) {
            return TimeInterval::Month->value;
        }

        return TimeInterval::Year->value;
    }

    private function getSalesAmount(Carbon $startDate, ?Carbon $endDate = null): int
    {
        $query = Order::query()
            ->where('status', OrderStatus::Done->value)
            ->whereDate('created_at', '>=', $startDate);

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->sum('amount');
    }

    private function getRecentOrders(string $startDate, string $endDate): Collection
    {
        return Order::query()
            ->with([
                'customer' => function ($query) {
                    $query->select(['name', 'id']);
                }
            ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function (Order $order) {
                return $order
                    ->setAttribute('url', route('admin.order.show', ['id' => $order->getKey()]))
                    ->setAttribute('status', OrderStatus::getOrderStatusTitle((int)$order->getAttribute('status')))
                    ->setAttribute('amount', Helpers::formatVietnameseCurrency((int)$order->getAttribute('amount')))
                    ->setAttribute(
                        'formatted_created_at',
                        Carbon::createFromFormat('Y-m-d H:i:s', $order->getAttribute('created_at'))
                            ->format('d/m/Y H:i:s')
                    );
            });
    }

    private function getTopProducts(string $startDate, string $endDate): Collection
    {
        return DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_details', 'order_details.product_detail_id', '=', 'product_details.id')
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->where('orders.status', OrderStatus::Done->value)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select('products.id', 'products.name', DB::raw('SUM(order_details.quantity) as total_sales'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sales', 'desc')
            ->take(5)
            ->get();
    }

    private function getLowStockProduct(): Collection
    {
        return ProductDetail::query()
            ->select(['product_id', 'quantity', 'color', 'id'])
            ->where('quantity', '<', 10)
            ->get()
            ->map(function (ProductDetail $product) {
                return $product
                    ->setAttribute(
                        'url',
                        route('admin.product.edit', ['id' => $product->getAttribute('product_id')])
                    );
            });
    }

    public function getSalesData(array $periodRange): array
    {
        [
            'start' => $startDate,
            'end' => $endDate,
            'interval' => $interval,
        ] = $periodRange;

        $data = $this->getAggregatedData($startDate, $endDate, $interval);
        $fullTimeRange = $this->generateFullTimeRange($startDate, $endDate, $interval);
        $data = $this->mergeWithFullTimeRange($data, $fullTimeRange, $interval);

        return [
            'chart' => [
                'labels' => $data->pluck('label')->toArray(),
                'revenue' => $data->pluck('revenue')->toArray(),
                'cost' => $data->pluck('cost')->toArray(),
                'profit' => $data->pluck('profit')->toArray(),
            ],
            'total_revenue' => $data->sum('revenue'),
            'total_cost' => $data->sum('cost'),
            'total_profit' => $data->sum('profit'),
        ];
    }

    private function getAggregatedData(string $start, string $end, string $interval): Collection
    {
        $selectPeriod = match ($interval) {
            TimeInterval::Hour->value => 'DATE_FORMAT(orders.created_at, "%Y-%m-%d %H") as period',
            TimeInterval::Day->value => 'DATE(orders.created_at) as period',
            TimeInterval::Month->value => 'DATE_FORMAT(orders.created_at, "%Y-%m") as period',
            TimeInterval::Year->value => 'DATE_FORMAT(orders.created_at, "%Y") as period',
            default => throw new InvalidArgumentException('Invalid interval specified for date format ' . $interval)
        };

        return DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->where('orders.status', OrderStatus::Done->value)
            ->whereBetween('orders.created_at', [$start, $end])
            ->select(
                DB::raw($selectPeriod),
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

    private function generateFullTimeRange(string $start, string $end, string $interval): array
    {
        return match ($interval) {
            TimeInterval::Hour->value => $this->generateHourRange($start, $end),
            TimeInterval::Day->value => $this->generateDayRange($start, $end),
            TimeInterval::Month->value => $this->generateMonthRange($start, $end),
            TimeInterval::Year->value => $this->generateYearRange($start, $end)
        };
    }

    private function generateMonthRange(string $start, string $end): array
    {
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $end);
        $months = [];

        for ($date = $startDate; $date->lte($endDate); $date->addMonth()) {
            $months[] = $date->copy()->format('Y-m');
        }

        return $months;
    }

    private function generateHourRange(string $start, string $end): array
    {
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $end);
        $dates = [];

        for ($date = $startDate; $date->lte($endDate); $date->addHour()) {
            $dates[] = $date->copy()->format('Y-m-d H');
        }
        return $dates;
    }

    private function generateDayRange(string $start, string $end): array
    {
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $end);
        $dates = [];

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->copy()->format('Y-m-d');
        }
        return $dates;
    }

    private function generateYearRange(string $start, string $end): array
    {
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $start);
        $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $end);
        $dates = [];

        for ($date = $startDate; $date->lte($endDate); $date->addYear()) {
            $dates[] = $date->copy()->format('Y');
        }
        return $dates;
    }

    private function mergeWithFullTimeRange(Collection $data, array $fullRange, string $interval): Collection
    {
        $dataMap = $data->keyBy('period');

        return collect($fullRange)
            ->map(function ($date) use ($interval, $dataMap) {
                $defaultValues = [
                    'revenue' => 0,
                    'cost' => 0,
                    'profit' => 0,
                ];
                $item = $dataMap->get($date, (object)$defaultValues);
                $item->label = $this->formatLabel($date, $interval);

                return $item;
            });
    }

    private function formatLabel(string $date, string $interval): string
    {
        return match ($interval) {
            TimeInterval::Hour->value => Carbon::createFromFormat('Y-m-d H', $date)->format('H'),
            TimeInterval::Day->value => Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y'),
            TimeInterval::Month->value => Carbon::createFromFormat('Y-m', $date)->locale('vi')->isoFormat('MMMM'),
            TimeInterval::Year->value => Carbon::createFromFormat('Y', $date)->format('Y'),
        };
    }

    private function getSalesByCategory(string $startDate, string $endDate): array
    {
        $salesByCategory = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_details', 'order_details.product_detail_id', '=', 'product_details.id')
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category',
                DB::raw('SUM(order_details.price * order_details.quantity) as total_sales')
            )
            ->whereBetween('order_details.created_at', [$startDate, $endDate])
            ->where('orders.status', OrderStatus::Done->value)
            ->groupBy('categories.name')
            ->get();

        return [
            'labels' => $salesByCategory->pluck('category'),
            'data' => $salesByCategory->pluck('total_sales'),
        ];
    }

    private function getSalesByBrand(string $startDate, string $endDate): array
    {
        $salesByBrand = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('product_details', 'order_details.product_detail_id', '=', 'product_details.id')
            ->join('products', 'product_details.product_id', '=', 'products.id')
            ->join('producers', 'products.producer_id', '=', 'producers.id')
            ->select(
                'producers.name as brand',
                DB::raw('SUM(order_details.price * order_details.quantity) as total_sales')
            )
            ->whereBetween('order_details.created_at', [$startDate, $endDate])
            ->where('orders.status', OrderStatus::Done->value)
            ->groupBy('producers.name')
            ->get();

        return [
            'labels' => $salesByBrand->pluck('brand'),
            'data' => $salesByBrand->pluck('total_sales'),
        ];
    }

    private function getOrderByStatus(string $startDate, string $endDate): Collection
    {
        return DB::table('orders')
            ->select(
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as revenue'),
                'status'
            )
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                $item->status = OrderStatus::getOrderStatusTitle($item->status);
                $item->revenue = Helpers::formatVietnameseCurrency($item->revenue);
                return $item;
            });
    }
}
