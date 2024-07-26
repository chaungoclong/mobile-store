<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Advertise;
use App\Models\Category;
use App\Models\Post;
use App\Models\Producer;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class HomePage extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return View
     */
    public function __invoke(): View
    {
        $products = Product::query()
            ->select(
                'id',
                'name',
                'image',
                'monitor',
                'front_camera',
                'rear_camera',
                'CPU',
                'GPU',
                'RAM',
                'ROM',
                'OS',
                'pin',
                'rate',
                'slug',
            )
            ->whereHas('product_detail')
            ->with([
                'product_detail' => function ($query) {
                    $query->select(
                        'id',
                        'product_id',
                        'quantity',
                        'sale_price',
                        'promotion_price',
                        'promotion_start_date',
                        'promotion_end_date'
                    )->where('quantity', '>', 0)->orderBy('sale_price', 'ASC');
                }
            ])->latest()->limit(9)->get();

        $favorite_products = Product::query()
            ->select(
                'id',
                'name',
                'image',
                'monitor',
                'front_camera',
                'rear_camera',
                'CPU',
                'GPU',
                'RAM',
                'ROM',
                'OS',
                'pin',
                'rate',
                'slug',
            )
            ->whereHas('product_detail')
            ->where('rate', '>', 4)
            ->withCount('votes')
            ->having('votes_count', '>', 0)
            ->with([
                'product_detail' => function ($query) {
                    $query
                        ->select(
                            'id',
                            'product_id',
                            'quantity',
                            'sale_price',
                            'promotion_price',
                            'promotion_start_date',
                            'promotion_end_date'
                        )
                        ->where('quantity', '>', 0)
                        ->orderBy('sale_price', 'ASC');
                }
            ])
            ->latest()
            ->orderBy('rate', 'DESC')
            ->limit(10)
            ->get();

        $promotionProducts = Product::query()
            ->select(
                'id',
                'name',
                'image',
                'monitor',
                'front_camera',
                'rear_camera',
                'CPU',
                'GPU',
                'RAM',
                'ROM',
                'OS',
                'pin',
                'rate',
                'slug',
            )
            ->whereHas('product_detail', function (Builder $query) {
                $query->whereNotNull('promotion_price')
                    ->where('promotion_start_date', '<=', date('Y-m-d'))
                    ->where('promotion_end_date', '>=', date('Y-m-d'));
            })
            ->with([
                'product_detail' => function ($query) {
                    $query
                        ->select(
                            'id',
                            'product_id',
                            'quantity',
                            'sale_price',
                            'promotion_price',
                            'promotion_start_date',
                            'promotion_end_date'
                        )
                        ->where('quantity', '>', 0)
                        ->orderBy('sale_price', 'ASC');
                }
            ])
            ->latest()
            ->limit(10)
            ->get();

//        dd($promotionProducts);

        $latestProducts = Product::query()
            ->select(
                'id',
                'name',
                'image',
                'monitor',
                'front_camera',
                'rear_camera',
                'CPU',
                'GPU',
                'RAM',
                'ROM',
                'OS',
                'pin',
                'rate',
                'slug',
            )
            ->whereHas('product_detail')
            ->with([
                'product_detail' => function ($query) {
                    $query
                        ->select(
                            'id',
                            'product_id',
                            'quantity',
                            'sale_price',
                            'promotion_price',
                            'promotion_start_date',
                            'promotion_end_date'
                        )
                        ->where('quantity', '>', 0)
                        ->orderBy('sale_price', 'ASC');
                }
            ])
            ->latest()
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();

        $producers = Producer::query()->select('id', 'name')->get();
        $categories = Category::query()->select('id', 'name')->get();

        $advertises = Advertise::query()
            ->where([
                ['start_date', '<=', date('Y-m-d')],
                ['end_date', '>=', date('Y-m-d')],
                ['at_home_page', '=', true]
            ])
            ->latest()
            ->limit(5)
            ->get(['title', 'image', 'link']);

        $posts = Post::query()
            ->select('id', 'title', 'image', 'created_at')
            ->latest()
            ->limit(4)
            ->get();

        return view('pages.home')
            ->with(
                'data',
                [
                    'products' => $products,
                    'favorite_products' => $favorite_products,
                    'posts' => $posts,
                    'advertises' => $advertises,
                    'producers' => $producers,
                    'categories' => $categories,
                    'latest_products' => $latestProducts,
                    'promotion_products' => $promotionProducts,
                ]
            );
    }
}
