<?php

return [
    'vnpay' => [
        'tmn_code' => 'CN1GOEHQ',
        'hash_secret' => 'Y58CIX9MIYQH5VSGURW91XX03S9EEIOO',
        'pay_url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
        'return_url' => env('VNPAY_RETURN_URL'),
        'ipn_url' => env('VNPAY_IPN_URL'),
    ],
];
