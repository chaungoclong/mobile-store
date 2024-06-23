<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticate
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return RedirectResponse|mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if(!Auth::check()) {
            return redirect()->route('login')->with(['alert' => [
                'type' => 'warning',
                'title' => 'Từ chối truy cập!',
                'content' => 'Bạn không có quyền truy cập. Hãy đăng nhập tài khoản Admin để truy cập trang này.'
            ]]);
        }

        if(!Auth::user()->admin) {
            return redirect()->route('home_page')->with(['alert' => [
                'type' => 'warning',
                'title' => 'Từ chối truy cập!',
                'content' => 'Tài khoản của bạn không có quyền truy cập. Trang này chỉ dành cho tài khoản Admin.'
            ]]);
        }

        return $next($request);
    }
}
