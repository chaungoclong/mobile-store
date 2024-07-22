<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->select(
                'id',
                'user_id',
                'status',
                'payment_method_id',
                'status',
                'order_code',
                'name',
                'email',
                'phone',
                'payment_status',
                'created_at'
            )
            ->where('status', '<>', 0)->with([
                'user' => function ($query) {
                    $query->select('id', 'name');
                },
                'payment_method' => function ($query) {
                    $query->select('id', 'name');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.order.index2')->with('orders', $orders);
    }

    public function fetch(Request $request): JsonResponse
    {
        $orders = Order::query()
            ->with(['customer', 'payment_method'])
            ->when($request->has('status'), function (Builder $query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->when($request->has('payment_status'), function (Builder $query) use ($request) {
                $query->when('payment_status', $request->input('payment_status'));
            })
            ->paginate(10)->appends($request->except(['page', '_token']));

        return response()->json($orders, Response::HTTP_OK);
    }

    public function show($id)
    {
        $order = Order::select(
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
        )->where([['status', '<>', 0], ['id', $id]])->with([
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
        return view('admin.order.show')->with('order', $order);
    }

    public function actionTransaction($action, $id)
    {
        $orderAction = Order::find($id);
        if ($orderAction) {
            switch ($action) {
                case 'pending':
                    if ($orderAction->status == OrderStatus::Done->value) {
                        return redirect()->back()->with(
                            'error',
                            'Không thể Thay đổi trạng thái của đơn hàng đã hoàn thành'
                        );
                    }
                    $orderAction->status = OrderStatus::Pending->value;
                    break;
                case 'confirmed':
                    if ($orderAction->status == OrderStatus::Done->value) {
                        return redirect()->back()->with(
                            'error',
                            'Không thể Thay đổi trạng thái của đơn hàng đã hoàn thành'
                        );
                    }
                    $orderAction->status = OrderStatus::Confirmed->value;
                    break;
                case 'success':
                    if ($orderAction->status == OrderStatus::Done->value) {
                        return redirect()->back()->with(
                            'error',
                            'Không thể Thay đổi trạng thái của đơn hàng đã hoàn thành'
                        );
                    }
                    $orderAction->status = OrderStatus::Done->value;
                    $orderAction->payment_status = PaymentStatus::Paid->value;
                    break;
                case 'cancel':
                    if ($orderAction->status == OrderStatus::Done->value) {
                        return redirect()->back()->with('error', 'Không thể Hủy đơn hàng đã hoàn thành');
                    }
                    $orderAction->status = OrderStatus::Cancelled->value;
                    foreach ($orderAction->orderDetails as $orderDetail) {
                        if (($orderDetail instanceof OrderDetail)
                            && !empty($orderDetail->product_detail_id)
                            && !empty($orderDetail->quantity)) {
                            ProductDetail::query()
                                ->where('id', $orderDetail->product_detail_id)
                                ->increment('quantity', (int)$orderDetail->quantity);
                        }
                    }
                    break;
                case 'delivery':
                    if ($orderAction->status == OrderStatus::Done->value) {
                        return redirect()->back()->with(
                            'error',
                            'Không thể Thay đổi trạng thái của đơn hàng đã hoàn thành'
                        );
                    }
                    $orderAction->status = OrderStatus::Delivery->value;
                    break;
            }
            $orderAction->save();
        }
        return redirect()->back();
    }
}
