<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ProductDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\OrderDetail;
use App\Models\Producer;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductImage;
use App\Models\Promotion;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index(ProductDataTable $dataTable)
    {
        $categories = Category::query()->pluck('id', 'name');
        $producers = Producer::query()->pluck('id', 'name');
        return $dataTable->render('admin.product.index', [
            'categories' => $categories,
            'producers' => $producers
        ]);
    }

    public function delete(Request $request)
    {
        /**
         * @var Product $product
         */
        $product = Product::query()->find($request->input('product_id'));

        if (!$product) {
            $data['type'] = 'error';
            $data['title'] = 'Thất Bại';
            $data['content'] = 'Bạn không thể xóa sản phẩm không tồn tại!';
        } else {
            $hasOrders = $product->productDetails()->whereHas('orderDetails')->exists();
            if ($hasOrders) {
                $data['type'] = 'error';
                $data['title'] = 'Thất Bại';
                $data['content'] = 'Bạn không thể xóa sản phẩm đã có đơn hàng!';
            } else {
                $productDetails = $product->productDetails()->get();
                foreach ($productDetails as $productDetail) {
                    foreach ($productDetail->product_images as $image) {
                        Storage::disk('public')->delete('images/products/' . $image->image_name);
                        $image->delete();
                    }
                    $productDetail->delete();
                }

                $product->votes()->delete();
                $product->delete();

                $data['type'] = 'success';
                $data['title'] = 'Thành Công';
                $data['content'] = 'Xóa sản phẩm thành công!';
            }
        }

        return response()->json($data, 200);
    }

    public function new(Request $request)
    {
        $producers = Producer::select('id', 'name')->orderBy('name', 'asc')->get();
        $categories = Category::select('id', 'name')->orderBy('name', 'asc')->get();
        return view('admin.product.new', [
            'producers' => $producers,
            'categories' => $categories
        ]);
    }

    public function save(Request $request): RedirectResponse
    {
        $product = new Product;
        $product->product_introduction = $request->input('product_introduction', '');
        $product->information_details = $request->input('information_details', '');
        $product->name = $request->name;
        $product->producer_id = $request->producer_id;
        $product->category_id = $request->category_id;
        $product->sku_code = $request->sku_code;
        $product->monitor = $request->monitor;
        $product->front_camera = $request->front_camera;
        $product->rear_camera = $request->rear_camera;
        $product->CPU = $request->CPU;
        $product->GPU = $request->GPU;
        $product->RAM = $request->RAM;
        $product->ROM = $request->ROM;
        $product->OS = $request->OS;
        $product->pin = $request->pin;
        $product->rate = 5.0;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '_' . Str::random(8) . '_' . $image->getClientOriginalName();
            $image->storeAs('images/products', $image_name, 'public');
            $product->image = $image_name;
        }

        $product->save();

        if ($request->has('product_details')) {
            foreach ($request->product_details as $key => $product_detail) {
                $new_product_detail = new ProductDetail;
                $new_product_detail->product_id = $product->id;
                $new_product_detail->color = $product_detail['color'];
                $new_product_detail->quantity = $product_detail['quantity'];
                $new_product_detail->import_price = str_replace('.', '', $product_detail['import_price']);
                $new_product_detail->sale_price = str_replace('.', '', $product_detail['sale_price']);
                if ($product_detail['promotion_price'] != null) {
                    $new_product_detail->promotion_price = str_replace('.', '', $product_detail['promotion_price']);
                }
                if ($product_detail['promotion_date'] != null) {
                    //Xử lý ngày bắt đầu, ngày kết thúc
                    list($start_date, $end_date) = explode(' - ', $product_detail['promotion_date']);

                    $start_date = str_replace('/', '-', $start_date);
                    $start_date = date('Y-m-d', strtotime($start_date));

                    $end_date = str_replace('/', '-', $end_date);
                    $end_date = date('Y-m-d', strtotime($end_date));

                    $new_product_detail->promotion_start_date = $start_date;
                    $new_product_detail->promotion_end_date = $end_date;
                }

                $new_product_detail->save();

                foreach ($request->file('product_details')[$key]['images'] as $image) {
                    $image_name = time() . '_' . Str::random(8) . '_' . $image->getClientOriginalName();
                    $image->storeAs('images/products', $image_name, 'public');

                    $new_image = new ProductImage;
                    $new_image->product_detail_id = $new_product_detail->id;
                    $new_image->image_name = $image_name;

                    $new_image->save();
                }
            }
        }

        return redirect()
            ->route('admin.product.index')
            ->with(['success' => 'Thêm sản phẩm thành công.']);
    }

    public function edit($id): Factory|View|Application
    {
        $producers = Producer::select('id', 'name')->orderBy('name', 'asc')->get();
        $categories = Category::select('id', 'name')->orderBy('name', 'asc')->get();
        $product = Product::query()
            ->select(
                'id',
                'producer_id',
                'category_id',
                'name',
                'image',
                'sku_code',
                'monitor',
                'front_camera',
                'rear_camera',
                'CPU',
                'GPU',
                'RAM',
                'ROM',
                'OS',
                'pin',
                'information_details',
                'product_introduction'
            )
            ->where('id', $id)
            ->with([
                'product_details' => function ($query) {
                    $query
                        ->select(
                            'id',
                            'product_id',
                            'color',
                            'import_price',
                            'quantity',
                            'sale_price',
                            'promotion_price',
                            'promotion_start_date',
                            'promotion_end_date'
                        )
                        ->with([
                            'product_images' => function ($query) {
                                $query->select('id', 'product_detail_id', 'image_name');
                            },
                            'order_details' => function ($query) {
                                $query->select('id', 'product_detail_id', 'quantity');
                            }
                        ]);
                }
            ])
            ->first();
        if (!$product) {
            abort(404);
        }
        return view('admin.product.edit')->with(
            [
                'product' => $product,
                'producers' => $producers,
                'categories' => $categories,
            ]
        );
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $product = Product::where('id', $id)->first();

        if (!$product) {
            abort(404);
        }

        $product->product_introduction = $request->input('product_introduction', '');
        $product->information_details = $request->input('information_details', '');
        $product->name = $request->name;
        $product->producer_id = $request->producer_id;
        $product->sku_code = $request->sku_code;
        $product->monitor = $request->monitor;
        $product->front_camera = $request->front_camera;
        $product->rear_camera = $request->rear_camera;
        $product->CPU = $request->CPU;
        $product->GPU = $request->GPU;
        $product->RAM = $request->RAM;
        $product->ROM = $request->ROM;
        $product->OS = $request->OS;
        $product->pin = $request->pin;
        $product->category_id = $request->category_id;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '_' . Str::random(8) . '_' . $image->getClientOriginalName();
            $image->storeAs('images/products', $image_name, 'public');
            Storage::disk('public')->delete('images/products/' . $product->image);
            $product->image = $image_name;
        }

        $product->save();

        if ($request->has('old_product_details')) {
            foreach ($request->old_product_details as $key => $product_detail) {
                $old_product_detail = ProductDetail::where('id', $key)->first();
                if (!$old_product_detail) {
                    abort(404);
                }

                $old_product_detail->quantity = $product_detail['quantity'];
                $old_product_detail->import_price = str_replace('.', '', $product_detail['import_price']);
                $old_product_detail->sale_price = str_replace('.', '', $product_detail['sale_price']);
                if ($product_detail['promotion_price'] != null) {
                    $old_product_detail->promotion_price = str_replace('.', '', $product_detail['promotion_price']);
                }
                if ($product_detail['promotion_date'] != null) {
                    //Xử lý ngày bắt đầu, ngày kết thúc
                    [$start_date, $end_date] = explode(' - ', $product_detail['promotion_date']);

                    $start_date = str_replace('/', '-', $start_date);
                    $start_date = date('Y-m-d', strtotime($start_date));

                    $end_date = str_replace('/', '-', $end_date);
                    $end_date = date('Y-m-d', strtotime($end_date));

                    $old_product_detail->promotion_start_date = $start_date;
                    $old_product_detail->promotion_end_date = $end_date;
                }

                $old_product_detail->save();
            }
        }

        if ($request->has('product_details')) {
            foreach ($request->product_details as $key => $product_detail) {
                $new_product_detail = new ProductDetail;
                $new_product_detail->product_id = $product->id;
                $new_product_detail->color = $product_detail['color'];
                $new_product_detail->quantity = $product_detail['quantity'];
                $new_product_detail->import_price = str_replace('.', '', $product_detail['import_price']);
                $new_product_detail->sale_price = str_replace('.', '', $product_detail['sale_price']);
                if ($product_detail['promotion_price'] != null) {
                    $new_product_detail->promotion_price = str_replace('.', '', $product_detail['promotion_price']);
                }
                if ($product_detail['promotion_date'] != null) {
                    //Xử lý ngày bắt đầu, ngày kết thúc
                    [$start_date, $end_date] = explode(' - ', $product_detail['promotion_date']);

                    $start_date = str_replace('/', '-', $start_date);
                    $start_date = date('Y-m-d', strtotime($start_date));

                    $end_date = str_replace('/', '-', $end_date);
                    $end_date = date('Y-m-d', strtotime($end_date));

                    $new_product_detail->promotion_start_date = $start_date;
                    $new_product_detail->promotion_end_date = $end_date;
                }

                $new_product_detail->save();

                foreach ($request->file('product_details')[$key]['images'] as $image) {
                    $image_name = time() . '_' . Str::random(8) . '_' . $image->getClientOriginalName();
                    $image->storeAs('images/products', $image_name, 'public');

                    $new_image = new ProductImage;
                    $new_image->product_detail_id = $new_product_detail->id;
                    $new_image->image_name = $image_name;

                    $new_image->save();
                }
            }
        }

        if ($request->file('old_product_details') != null) {
            foreach ($request->file('old_product_details') as $key => $images) {
                foreach ($images['images'] as $image) {
                    $image_name = time() . '_' . Str::random(8) . '_' . $image->getClientOriginalName();
                    $image->storeAs('images/products', $image_name, 'public');

                    $new_image = new ProductImage;
                    $new_image->product_detail_id = $key;
                    $new_image->image_name = $image_name;

                    $new_image->save();
                }
            }
        }

        return redirect()->route('admin.product.index')->with([
            'alert' => [
                'type' => 'success',
                'title' => 'Thành Công',
                'content' => 'Chỉnh sửa sản phẩm thành công.'
            ]
        ]);
    }

    public function delete_promotion(Request $request)
    {
        $promotion = Promotion::where('id', $request->promotion_id)->first();

        if (!$promotion) {
            $data['type'] = 'error';
            $data['title'] = 'Thất Bại';
            $data['content'] = 'Bạn không thể xóa khuyễn mãi không tồn tại!';
        } else {
            $promotion->delete();

            $data['type'] = 'success';
            $data['title'] = 'Thành Công';
            $data['content'] = 'Xóa khuyến mãi thành công!';
        }

        return response()->json($data, 200);
    }

    public function delete_product_detail(Request $request): JsonResponse
    {
        $product_detail = ProductDetail::query()->find($request->product_detail_id);

        if (!$product_detail) {
            $data['type'] = 'error';
            $data['title'] = 'Thất Bại';
            $data['content'] = 'Bạn không thể xóa biến thể sản phẩm không tồn tại!';
            return response()->json($data, Response::HTTP_BAD_REQUEST);
        }

        $product = $product_detail->product;
        if($product->productDetails()->count() === 1) {
            $data['type'] = 'error';
            $data['title'] = 'Thất Bại';
            $data['content'] = 'Một sản phẩm phải có ít nhất một biến thể!';
            return response()->json($data, Response::HTTP_BAD_REQUEST);
        }

        $hasOrder = OrderDetail::query()->where('product_detail_id', $product_detail->getKey())->exists();

        if ($hasOrder) {
            $data['type'] = 'error';
            $data['title'] = 'Thất Bại';
            $data['content'] = 'Bạn không thể xóa biến thể sản phẩm đã được đặt hàng!';

            return response()->json($data, Response::HTTP_BAD_REQUEST);
        }

        foreach ($product_detail->product_images as $image) {
            Storage::disk('public')->delete('images/products/' . $image->image_name);
            $image->delete();
        }
        $product_detail->delete();

        $data['type'] = 'success';
        $data['title'] = 'Thành Công';
        $data['content'] = 'Xóa chi tiết sản phẩm thành công!';
        return response()->json($data, Response::HTTP_OK);
    }

    public function delete_image(Request $request)
    {
        $image = ProductImage::find($request->key);
        Storage::disk('public')->delete('images/products/' . $image->image_name);
        $image->delete();
        return response()->json();
    }
}
