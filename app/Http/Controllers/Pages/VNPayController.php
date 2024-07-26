<?php

namespace App\Http\Controllers\Pages;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ProductDetail;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class VNPayController extends Controller
{
    public function returnUrl(Request $request): Factory|View|Application
    {
        $vnp_HashSecret = config('payments.vnpay.hash_secret');;
        $vnp_SecureHash = $request->input('vnp_SecureHash');
        $inputData = array();
        $params = $request->all();

        foreach ($params as $key => $value) {
            if (str_starts_with($key, "vnp_")) {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            return view('pages.payment-success');
        }

        return view('pages.payment-failed');
    }

    public function ipnUrl(Request $request): JsonResponse
    {
        Log::debug('ipn', $request->all());

        $vnp_HashSecret = config('payments.vnpay.hash_secret');

        $inputData = [];
        $returnData = [];
        foreach ($_GET as $key => $value) {
            if (str_starts_with($key, "vnp_")) {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i === 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnp_Amount = $inputData['vnp_Amount'] / 100; // Số tiền thanh toán VNPAY phản hồi
        $orderId = $inputData['vnp_TxnRef'];

        try {
            if ($secureHash !== $vnp_SecureHash) {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
                Log::debug('ipn response to vnpay', $returnData);
                return response()->json($returnData);
            }

            $order = Order::query()->where('order_code', $orderId)->first();
            if (!($order instanceof Order)) {
                $returnData['RspCode'] = '01';
                $returnData['Message'] = 'Order not found';
                Log::debug('ipn response to vnpay', $returnData);
                return response()->json($returnData);
            }

            if ((int)$order->getAttribute('amount') !== (int)$vnp_Amount) {
                $returnData['RspCode'] = '04';
                $returnData['Message'] = 'invalid amount';
                Log::debug('ipn response to vnpay', $returnData);
                return response()->json($returnData);
            }

            $oldPaymentStatus = (int)$order->getAttribute('payment_status');

            if ($oldPaymentStatus !== PaymentStatus::Unpaid->value) {
                $returnData['RspCode'] = '02';
                $returnData['Message'] = 'Order already confirmed';
                Log::debug('ipn response to vnpay', $returnData);
                return response()->json($returnData);
            }

            if ($inputData['vnp_ResponseCode'] === '00' && $inputData['vnp_TransactionStatus'] === '00') {
                $paymentStatus = PaymentStatus::Paid->value;
            } else {
                $paymentStatus = PaymentStatus::Failed->value;
            }

            Log::debug('payment status to vnpay: ' . $paymentStatus);

            if ($paymentStatus === PaymentStatus::Failed->value) {
                $order->orderDetails()
                    ->get()
                    ->each(function (OrderDetail $orderDetail) {
                        ProductDetail::query()
                            ->where('id', $orderDetail->getAttribute('product_detail_id'))
                            ->increment('quantity', (int)$orderDetail->getAttribute('quantity'));
                    });
            }

            $order->update(['payment_status' => $paymentStatus, 'status' => OrderStatus::Confirmed->value]);

            $adminUsers = User::query()
                ->where('active', 1)
                ->where('admin', 1)
                ->get();
            if ($adminUsers->isNotEmpty()) {
                Notification::send($adminUsers, new OrderStatusNotification($order, true));
            }

            Notification::send(auth()->user(), new OrderStatusNotification($order, false));

            $returnData['RspCode'] = '00';
            $returnData['Message'] = 'Confirm Success';

            Log::debug('ipn response to vnpay', $returnData);
            return response()->json($returnData);
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
            Log::debug('ipn response to vnpay', $returnData);
            return response()->json($returnData);
        }
    }
}
