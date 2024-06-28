<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Producer;
use App\Models\ProductDetail;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardStatisticController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        $carbon = new Carbon('first day of this month');
        $data = $this->getStatistics($carbon->year, $carbon->month);
        return view('admin.statistic.index', ['data' => $data]);
    }

    private function getStatistics($year, $month = null): array
    {
        $data = [
            'count_products' => 0,
            'total_revenue' => 0,
            'total_profit' => 0,
            'labels' => [],
            'revenues' => [],
            'producer' => [],
            'text' => $this->generateText($year, $month),
            'low_stock_products' => $this->getLowStockProducts(),
            'latest_orders' => $this->getLatestOrders(),
        ];

        if ($month) {
            $carbon = Carbon::createFromDate($year, $month, 1);
            $daysInMonth = $carbon->daysInMonth;

            for ($i = 0; $i < $daysInMonth; $i++) {
                $date = $carbon->copy()->addDay($i)->format('d/m/Y');
                $data['labels'][] = $date;
                $this->calculateDailyStatistics($data, $carbon, $i);
            }
        } else {
            for ($i = 0; $i < 12; $i++) {
                $data['labels'][] = 'Tháng ' . ($i + 1);
                $this->calculateMonthlyStatistics($data, $year, $i + 1);
            }
        }

        $data['count_orders'] = Order::where('status', '=', OrderStatus::Done)
            ->whereYear('created_at', $year)
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('created_at', $month);
            })->count();

        $order_details = $this->getOrderDetails($year, $month);

        $data['order_details'] = $order_details;
        $this->calculateProducerStatistics($data, $order_details);

        return $data;
    }

    private function generateText($year, $month): array
    {
        if ($month) {
            return [
                'title1' => 'Biểu Đồ Kinh Doanh Tháng ' . $month . ' Năm ' . $year,
                'title2' => 'Danh Sách Sản Phẩm Xuất Kho Tháng ' . $month . ' Năm ' . $year,
                'revenue' => 'DOANH THU THÁNG',
                'profit' => 'LỢI NHUẬN THÁNG'
            ];
        }

        return [
            'title1' => 'Biểu Đồ Kinh Doanh Năm ' . $year,
            'title2' => 'Danh Sách Sản Phẩm Xuất Kho Năm ' . $year,
            'revenue' => 'DOANH THU NĂM',
            'profit' => 'LỢI NHUẬN NĂM'
        ];
    }

    private function calculateDailyStatistics(&$data, $carbon, $day): void
    {
        $order_details = OrderDetail::select('product_detail_id', 'quantity', 'price')
            ->whereDate('created_at', $carbon->copy()->addDay($day)->format('Y-m-d'))
            ->whereHas('order', function (Builder $query) {
                $query->where('status', '=', OrderStatus::Done);
            })->with([
                'product_detail' => function ($query) {
                    $query->select('id', 'import_price');
                }
            ])->get();

        $revenue = 0;
        $profit = 0;

        foreach ($order_details as $order_detail) {
            $revenue += $order_detail->price * $order_detail->quantity;
            $profit += $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
            $data['count_products'] += $order_detail->quantity;
        }

        $data['total_revenue'] += $revenue;
        $data['total_profit'] += $profit;
        $data['revenues'][] = $revenue;
    }

    private function calculateMonthlyStatistics(&$data, $year, $month): void
    {
        $order_details = OrderDetail::select('product_detail_id', 'quantity', 'price')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->whereHas('order', function (Builder $query) {
                $query->where('status', '=', OrderStatus::Done);
            })->with([
                'product_detail' => function ($query) {
                    $query->select('id', 'import_price');
                }
            ])->get();

        $revenue = 0;
        $profit = 0;

        foreach ($order_details as $order_detail) {
            $revenue += $order_detail->price * $order_detail->quantity;
            $profit += $order_detail->quantity * ($order_detail->price - $order_detail->product_detail->import_price);
            $data['count_products'] += $order_detail->quantity;
        }

        $data['total_revenue'] += $revenue;
        $data['total_profit'] += $profit;
        $data['revenues'][] = $revenue;
    }

    private function getOrderDetails($year, $month)
    {
        return OrderDetail::select('id', 'order_id', 'product_detail_id', 'quantity', 'price', 'created_at')
            ->whereYear('created_at', $year)
            ->when($month, function ($query) use ($month) {
                return $query->whereMonth('created_at', $month);
            })->whereHas('order', function (Builder $query) {
                $query->where('status', '=', OrderStatus::Done);
            })->with([
                'order' => function ($query) {
                    $query->select('id', 'order_code');
                },
                'product_detail' => function ($query) {
                    $query->select('id', 'product_id', 'color', 'import_price')->with([
                        'product' => function ($query) {
                            $query->select('id', 'producer_id', 'name', 'sku_code', 'OS')->with([
                                'producer' => function ($query) {
                                    $query->select('id', 'name');
                                }
                            ]);
                        }
                    ]);
                }
            ])->latest()->get();
    }

    private function calculateProducerStatistics(&$data, $order_details): void
    {
        $producers = Producer::select('name')->has('products')->get();

        foreach ($producers as $producer) {
            $data['producer'][$producer->name] = [
                'quantity' => 0,
                'revenue' => 0,
                'profit' => 0
            ];
        }

        foreach ($order_details as $order_detail) {
            $producer_name = $order_detail->product_detail->product->producer->name;
            $quantity = $order_detail->quantity;
            $price = $order_detail->price;
            $import_price = $order_detail->product_detail->import_price;

            $data['producer'][$producer_name]['quantity'] += $quantity;
            $data['producer'][$producer_name]['revenue'] += $quantity * $price;
            $data['producer'][$producer_name]['profit'] += $quantity * ($price - $import_price);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? null;

        $data = $this->getStatistics($year, $month);
        return response()->json($data, 200);
    }

    private function getLowStockProducts(): Collection
    {
        return ProductDetail::query()
            ->select('id', 'product_id', 'color', 'quantity')
            ->where('quantity', '<', 10)
            ->with([
                'product' => function ($query) {
                    $query->select('id', 'producer_id', 'name', 'sku_code', 'OS')->with([
                        'producer' => function ($query) {
                            $query->select('id', 'name');
                        }
                    ]);
                }
            ])->get();
    }

    private function getLatestOrders(): Collection
    {
        return Order::query()
            ->with('order_details')
            ->latest()
            ->limit(10)
            ->get();
    }

//    private function getTopSellingProducts(): Collection
//    {
//
//    }
}
