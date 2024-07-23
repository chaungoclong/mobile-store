<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\CustomerDataTable;
use App\Http\Controllers\Controller;
use App\Mail\Admin\ActiveAccountMail;
use App\Models\Order;
use App\Models\ProductVote;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function index(CustomerDataTable $dataTable)
    {
        return $dataTable->render('admin.user.index');
    }

    public function new(Request $request)
    {
        $rules = array(
            'email' => array('required', 'regex:/^[a-z](\.?[a-z0-9]){5,}@gmail\.com$/', 'unique:users')
        );
        $messsages = array(
            'email.required' => 'Email không được để trống!',
            'email.regex' => 'Email không đúng định dạng!',
            'email.unique' => 'Email đã tồn tại!'
        );
        $validator = Validator::make($request->all(), $rules, $messsages);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            $password = str::random(8);

            $user = new User;
            $user->name = 'New Account';
            $user->email = $request->email;
            $user->password = Hash::make($password);
            $user->active_token = str::random(40);

            $user->save();

            $data['token'] = $user->active_token;
            $data['password'] = $password;

            Mail::to($user)->send(new ActiveAccountMail($data));

            $data['type'] = 'success';
            $data['title'] = 'Thành Công';
            $data['content'] = 'Thêm tài khoản thành công!';

            return response()->json($data, 200);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = User::query()->where('id', $request->user_id)->first();
        if (!$user) {
            $data['type'] = 'error';
            $data['title'] = 'Thất Bại';
            $data['content'] = 'Bạn không thể xóa tài khoản không tồn tại!';
            return response()->json($data, Response::HTTP_BAD_REQUEST);
        }

        if ($user->orders()->count() > 0) {
            $data['type'] = 'error';
            $data['title'] = 'Thất Bại';
            $data['content'] = 'Bạn không thể xóa khách hàng đã có đơn hàng!';
            return response()->json($data, Response::HTTP_BAD_REQUEST);
        }

        $user->delete();

        $data['type'] = 'success';
        $data['title'] = 'Thành Công';
        $data['content'] = 'Xóa Khách hàng thành công!';

        return response()->json($data, Response::HTTP_OK);
    }

    public function show($id)
    {
        $user = User::query()->select(
            'id',
            'name',
            'email',
            'phone',
            'address',
            'avatar_image',
            'active',
            'created_at'
        )->where(
            [['id', $id], ['admin', false]]
        )->first();

        if (!$user) {
            abort(404);
        }
        $product_votes = ProductVote::where('user_id', $user->id)->with([
            'product' => function ($query) {
                $query->select('id', 'name', 'image');
            }
        ])->latest()->get();

        $orders = Order::where('user_id', $user->id)->with([
            'payment_method' => function ($query) {
                $query->select('id', 'name');
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
        ])->latest()->get();

        return view('admin.user.show')->with([
            'user' => $user,
            'product_votes' => $product_votes,
            'orders' => $orders
        ]);
    }

    public function send($id)
    {
        $user = User::where([['id', $id], ['active', false], ['admin', false]])->first();
        if (!$user) {
            abort(404);
        }

        $data['token'] = $user->active_token;
        $data['password'] = null;

        Mail::to($user)->send(new ActiveAccountMail($data));

        return back()->with([
            'alert' => [
                'type' => 'success',
                'title' => 'Thành Công',
                'content' => 'Gửi email kích hoạt tài khoản thành công.'
            ]
        ]);
    }
}
