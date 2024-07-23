<?php

namespace App\Http\Controllers\Pages;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Advertise;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check() && Auth::user()->admin == 0) {
            $advertises = Advertise::where([
                ['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')],
                ['at_home_page', '=', false]
            ])->latest()->limit(5)->get(['link', 'title', 'image']);

            $orders = Order::query()
                ->where('user_id', Auth::id())
                ->with([
                    'payment_method' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'order_details' => function ($query) {
                        $query->select('id', 'order_id', 'quantity', 'price');
                    }
                ])
                ->when($request->has('status'), function (Builder $query) use ($request) {
                    $status = trim($request->input('status'));
                    if($status !== '') {
                        $query->where('status', $request->input('status'));
                    }
                })
                ->orderBy('created_at', 'desc')->paginate(10);
            if ($orders->isNotEmpty()) {
                return view('pages.orders')->with('data', ['orders' => $orders, 'advertises' => $advertises]);
            } else {
                return redirect()->route('home_page')->with([
                    'alert' => [
                        'type' => 'info',
                        'title' => 'Thông Báo',
                        'content' => 'Bạn không có đơn hàng nào. Hãy mua hàng để thực hiện chức năng này!'
                    ]
                ]);
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('admin.dashboard')->with([
                    'alert' => [
                        'type' => 'warning',
                        'title' => 'Cảnh Báo',
                        'content' => 'Bạn không có quyền truy cập vào trang này!'
                    ]
                ]);
            } else {
                return redirect()->route('login')->with([
                    'alert' => [
                        'type' => 'warning',
                        'title' => 'Cảnh Báo',
                        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
                    ]
                ]);
            }
        }
    }

    public function show($id)
    {
        if (Auth::check() && Auth::user()->admin == 0) {
            $advertises = Advertise::where([
                ['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')],
                ['at_home_page', '=', false]
            ])->latest()->limit(5)->get(['link', 'title', 'image']);

            $order = Order::where('id', $id)->with([
                'payment_method' => function ($query) {
                    $query->select('id', 'name');
                },
                'user' => function ($query) {
                    $query->select('id', 'name', 'email', 'phone', 'address');
                },
                'order_details' => function ($query) {
                    $query->select('id', 'order_id', 'product_detail_id', 'quantity', 'price')
                        ->with([
                            'product_detail' => function ($query) {
                                $query->select('id', 'product_id', 'color')
                                    ->with([
                                        'product' => function ($query) {
                                            $query->select('id', 'name', 'image', 'sku_code');
                                        }
                                    ]);
                            }
                        ]);
                }
            ])->first();

            if (!$order) {
                abort(404);
            }

            if (Auth::user()->id != $order->user_id) {
                return redirect()->route('home_page')->with([
                    'alert' => [
                        'type' => 'warning',
                        'title' => 'Cảnh Báo',
                        'content' => 'Bạn không có quyền truy cập vào trang này!'
                    ]
                ]);
            } else {
                return view('pages.order')->with('data', ['order' => $order, 'advertises' => $advertises]);
            }
        } else {
            if (Auth::check()) {
                return redirect()->route('admin.dashboard')->with([
                    'alert' => [
                        'type' => 'warning',
                        'title' => 'Cảnh Báo',
                        'content' => 'Bạn không có quyền truy cập vào trang này!'
                    ]
                ]);
            } else {
                return redirect()->route('login')->with([
                    'alert' => [
                        'type' => 'warning',
                        'title' => 'Cảnh Báo',
                        'content' => 'Bạn phải đăng nhập để sử dụng chức năng này!'
                    ]
                ]);
            }
        }
    }

    public function cancelOrder(Order $order): JsonResponse
    {
        try {
            $orderStatus = (int)($order->status);

            if (in_array($orderStatus, OrderStatus::uncancellableStatus(), true)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Không thể hủy đon hàng có trạng thái '
                        . OrderStatus::getOrderStatusTitle($orderStatus)
                ], Response::HTTP_BAD_REQUEST);
            }

            $order->update(['status' => OrderStatus::Cancelled->value]);
            return response()->json(['ok' => true, 'message' => 'Hủy đơn hàng thành công']);
        } catch (Throwable $throwable) {
            Log::error(__METHOD__ . ':' . __LINE__ . ': ' . $throwable->getMessage());
            return response()->json([
                'ok' => false,
                'message' => 'Hủy đơn hàng không thành công'
            ]);
        }
    }
}
