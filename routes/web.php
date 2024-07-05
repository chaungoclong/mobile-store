<?php

use App\Enums\PaymentStatus;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DashboardStatisticController;
use App\Http\Controllers\Admin\ProducerController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('active/{token}', 'Auth\RegisterController@activation')->name('active_account');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::namespace('Admin')->prefix('admin')->name('admin.')->middleware('admin')
    ->group(function () {
//        Route::get('dashboard', [Dashboard2Controller::class, 'index'])->name('dashboard');
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboardData');

        Route::get('users', 'UserController@index')->name('users');
        Route::post('user/new', 'UserController@new')->name('user_new');
        Route::post('user/delete', 'UserController@delete')->name('user_delete');
        Route::get('user/{id}/show', 'UserController@show')->name('user_show');
        Route::get('user/{id}/send', 'UserController@send')->name('user_send');

        Route::get('posts', 'PostController@index')->name('post.index');
        Route::get('post/new', 'PostController@new')->name('post.new');
        Route::post('post/save', 'PostController@save')->name('post.save');
        Route::post('post/delete', 'PostController@delete')->name('post.delete');
        Route::get('post/{id}/edit', 'PostController@edit')->name('post.edit');
        Route::post('post/{id}/update', 'PostController@update')->name('post.update');

        Route::get('advertises', 'AdvertiseController@index')->name('advertise.index');
        Route::get('advertise/new', 'AdvertiseController@new')->name('advertise.new');
        Route::post('advertise/save', 'AdvertiseController@save')->name('advertise.save');
        Route::post('advertise/delete', 'AdvertiseController@delete')->name('advertise.delete');
        Route::get('advertise/{id}/edit', 'AdvertiseController@edit')->name('advertise.edit');
        Route::post('advertise/{id}/update', 'AdvertiseController@update')->name('advertise.update');

        Route::get('products', 'ProductController@index')->name('product.index');
        Route::get('product/new', 'ProductController@new')->name('product.new');
        Route::post('product/save', 'ProductController@save')->name('product.save');
        Route::post('product/delete', 'ProductController@delete')->name('product.delete');
        Route::get('product/{id}/edit', 'ProductController@edit')->name('product.edit');
        Route::post('product/{id}/update', 'ProductController@update')->name('product.update');
        Route::post('promotion/delete', 'ProductController@delete_promotion')->name('product.delete_promotion');
        Route::post('product_detail/delete', 'ProductController@delete_product_detail')
            ->name('product.delete_product_detail');
        Route::post('product/image/delete', 'ProductController@delete_image')->name('product.delete_image');

        Route::get('orders', 'OrderController@index')->name('order.index');
        Route::get('order/{id}/show', 'OrderController@show')->name('order.show');
        Route::get('active/{action}/{id}', 'OrderController@actionTransaction')->name('orderTransaction');

        Route::get('statistic', 'StatisticController@index')->name('statistic');
        Route::get('statistic/change', [DashboardStatisticController::class, 'index'])->name('statistic.edit');

        route::get('warehouse', 'WarehouseController@index')->name('warehouse');
        route::get('orderDetails', 'WarehouseController@orderDetails')->name('orderDetails');

        Route::get('producers', [ProducerController::class, 'index'])->name('producers.index');
        Route::get('producers/create', [ProducerController::class, 'create'])->name('producers.create');
        Route::post('producers/store', [ProducerController::class, 'store'])->name('producers.store');
        Route::delete('producers/destroy/{producer}', [ProducerController::class, 'destroy'])->name(
            'producers.destroy'
        );
        Route::get('producers/edit/{producer}', [ProducerController::class, 'edit'])->name('producers.edit');
        Route::put('producers/update/{producer}', [ProducerController::class, 'update'])->name('producers.update');

        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories/store', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('categories/edit/{category}', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/update/{category}', [CategoryController::class, 'update'])->name('categories.update');
    });

Route::namespace('Pages')->group(function () {
    Route::get('/', 'HomePage')->name('home_page');
    Route::get('about', 'AboutPage')->name('about_page');
    Route::get('contact', 'ContactPage')->name('contact_page');
    Route::get('search', 'SearchController')->name('search');
    Route::get('posts', 'PostController@index')->name('posts_page');
    Route::get('post/{id}', 'PostController@show')->name('post_page');
    Route::get('orders', 'OrderController@index')->name('orders_page');
    Route::get('order/{id}', 'OrderController@show')->name('order_page');

    Route::get('user/profile', 'UserController@show')->name('show_user');
    Route::get('user/edit', 'UserController@edit')->name('edit_user');
    Route::post('user/save', 'UserController@save')->name('save_user');
    //page products
    Route::get('products', 'ProductsController@index')->name('products_page');
    Route::get('producer/{id}', 'ProductsController@getProducer')->name('producer_page');
    Route::get('product/{id}', 'ProductsController@getProduct')->name('product_page');
    Route::post('vote', 'ProductsController@addVote')->name('add_vote');
    Route::post('cart/add', 'CartController@addCart')->name('add_cart');
    Route::post('cart/remove', 'CartController@removeCart')->name('remove_cart');
    Route::post('minicart/update', 'CartController@updateMiniCart')->name('update_minicart');
    Route::post('cart/update', 'CartController@updateCart')->name('update_cart');
    Route::get('cart', 'CartController@showCart')->name('show_cart');
    Route::post('checkout', 'CartController@showCheckout')->name('show_checkout');
    Route::post('payment', 'CartController@payment')->name('payment');
    Route::get('payment/response', 'CartController@responsePayment')->name('payment_response');
    Route::get('/payment/vnpay/return', function (Request $request) {
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

        echo "Payment failed";
    })->middleware('auth');

    Route::get('/payment/vnpay/ipn', function (Request $request) {
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

            $order->update(['payment_status' => $paymentStatus]);
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
            Log::debug('ipn response to vnpay', $returnData);
            return response()->json($returnData);
        }
    });
});

Route::get('/test', [DashboardController::class, 'getDashboardData']);
