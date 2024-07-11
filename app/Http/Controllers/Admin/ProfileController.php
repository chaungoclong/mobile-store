<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProfileController extends Controller
{
    public function show(Request $request): Factory|View|Application
    {
        try {
            $user = auth()->user();

            return view('admin.profile1', ['user' => $user]);
        } catch (Throwable) {
            abort(500);
        }
    }

    public function update(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required', 'string'],
                    'avatar_image' => ['nullable', 'url']
                ]
            );

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            /**
             * @var User $user ;
             */
            $user = auth()->user();
            $user->fill($request->only(['name', 'avatar_image']));
            $user->save();

            return back()->with('success', 'Update Successfully');
        } catch (Throwable $throwable) {
            Log::error(__METHOD__ . ':' . __LINE__ . ':' . $throwable->getMessage(), $request->all());

            return back()->with('error', 'Update Failed');
        }
    }


    public function updateWithFile(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'name' => ['required', 'string'],
                    'avatar_image' => ['file']
                ]
            );

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            /**
             * @var User $user ;
             */
            $user = auth()->user();
            if($request->hasFile('avatar_image')) {
                $image = $request->file('avatar_image');
                $image_name = time().'_'.$image->getClientOriginalName();
                $image->storeAs('images/avatars',$image_name,'public');

                if($user->avatar_image !== null) {
                    Storage::disk('public')->delete('images/avatars/'.$user->avatar_image);
                }

                $user->avatar_image = $image_name;
            }
            $user->name = $request->input('name');
            $user->save();

            return back()->with('success', 'Cập nhật thông tin thành công');
        } catch (Throwable $throwable) {
            Log::error(__METHOD__ . ':' . __LINE__ . ':' . $throwable->getMessage(), $request->all());

            return back()->with('error', 'Cập nhật thông tin không thành công');
        }
    }

    public function changePassword(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'current_password' => ['required', 'string'],
                    'new_password' => ['required', 'string', 'min:8', 'confirmed']
                ]
            );

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            /**
             * @var User $user
             */
            $user = auth()->user();

            $isValidPassword = Hash::check(
                $request->input('current_password'),
                $user->getAttribute('password')
            );

            if (!$isValidPassword) {
                $validator->errors()->add('current_password', 'The current_password is incorrect');
                return back()->withErrors($validator)->withInput();
            }

            $user->setAttribute('password', Hash::make($request->input('new_password')));
            $user->save();

            return back()->with('success', 'Update Password Success');
        } catch (Throwable $throwable) {
            Log::error(__METHOD__ . ':' . __LINE__ . ':' . $throwable->getMessage(), $request->all());

            return back()->with('error', 'Update Failed');
        }
    }
}
