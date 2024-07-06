<?php

declare(strict_types=1);

return [
    [
        'title' => 'Dashboard',
        'route' => 'admin.dashboard',
        'icon' => 'fas fa-home',
        'submenu' => []
    ],
    [
        'title' => 'Sản phẩm',
        'route' => 'admin.product.index',
        'icon' => 'fas fa-box-open',
        'submenu' => []
    ],
    [
        'title' => 'Banner Quảng Cáo',
        'route' => 'admin.advertise.index',
        'icon' => 'fas fa-image',
        'submenu' => []
    ],
    [
        'title' => 'Khách hàng',
        'route' => 'admin.users',
        'icon' => 'fas fa-users',
        'submenu' => []
    ],
    [
        'title' => 'Danh Mục Sản Phẩm',
        'route' => 'admin.categories.index',
        'icon' => 'fas fa-list',
        'submenu' => []
    ],
    [
        'title' => 'Hãng Sản Xuất',
        'route' => 'admin.producers.index',
        'icon' => 'fas fa-industry',
        'submenu' => []
    ],
    [
        'title' => 'Đơn hàng',
        'route' => 'admin.order.index',
        'icon' => 'fas fa-shopping-cart',
        'submenu' => []
    ],
    [
        'title' => 'Kho',
        'route' => 'admin.warehouse',
        'icon' => 'fas fa-warehouse',
        'submenu' => []
    ],
//    [
//        'title' => 'Users',
//        'route' => '',
//        'icon' => 'users',
//        'submenu' => [
//            [
//                'title' => 'All Users',
//                'route' => 'admin.dashboard',
//            ],
//            [
//                'title' => 'Add User',
//                'route' => 'admin.dashboard',
//            ]
//        ]
//    ],
];
