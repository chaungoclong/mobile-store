<?php

namespace App\Http\Controllers\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Advertise;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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
                    if ($status !== '') {
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
        $order = Order::query()
            ->select(
                'id',
                'user_id',
                'payment_method_id',
                'order_code',
                'name',
                'email',
                'phone',
                'address',
                'created_at',
                'payment_status',
                'status',
                'delivery_code',
                'amount',
            )
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'email', 'phone', 'address');
                },
                'payment_method' => function ($query) {
                    $query->select('id', 'name', 'describe');
                },
                'order_details' => function ($query) {
                    $query->select('id', 'order_id', 'product_detail_id', 'quantity', 'price')
                        ->with([
                            'product_detail' => function ($query) {
                                $query->select('id', 'product_id', 'color')
                                    ->with([
                                        'product_images:id,image_name,product_detail_id',
                                        'product:id,name'
                                    ]);
                            }
                        ]);
                }
            ])->first();
        if (!($order instanceof Order)) {
            abort(404);
        }

        $status = OrderStatus::tryFrom((int)$order->getAttribute('status'));
        $paymentStatus = PaymentStatus::tryFrom((int)$order->getAttribute('payment_status'));

        return view('pages.order', [
            'order' => $order,
            'status' => $status,
            'payment_status' => $paymentStatus
        ]);
    }

    public function cancelOrder(Order $order): RedirectResponse
    {
        try {
            if ((int)$order->getAttribute('user_id') !== (int)auth()->id()) {
                return back()->with('error', 'Không thể hủy đon hàng không phải của mình');
            }

            $orderStatus = (int)($order->getAttribute('status'));

            if (in_array($orderStatus, OrderStatus::uncancellableStatus(), true)) {
                return back()->with(
                    'error',
                    'Không thể hủy đon hàng có trạng thái '
                    . OrderStatus::getOrderStatusTitle($orderStatus)
                );
            }

            $order->update(['status' => OrderStatus::Cancelled->value]);
            $order->revertProductQuantityOnOrderCancel();

            $adminUsers = User::query()
                ->where('active', 1)
                ->where('admin', 1)
                ->get();
            if ($adminUsers->isNotEmpty()) {
                Notification::send($adminUsers, new OrderStatusNotification($order, true));
            }

            Notification::send(auth()->user(), new OrderStatusNotification($order, false));

            return back()->with('success', 'Hủy đơn hàng thành công');
        } catch (Throwable $throwable) {
            Log::error(__METHOD__ . ':' . __LINE__ . ': ' . $throwable->getMessage());
            return back()->with(
                'error',
                'Hủy đơn hàng không thành công'
            );
        }
    }
}
