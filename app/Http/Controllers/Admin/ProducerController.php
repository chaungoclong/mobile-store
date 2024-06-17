<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProducerController extends Controller
{
    public function index(): View
    {
        $producers = Producer::query()->latest()->get();

        return view('admin.producers.index', ['producers' => $producers]);
    }

    public function create(): View
    {
        return view('admin.producers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'slug' => ['string', 'unique:producers,slug'],
            'image' => ['required', 'file']
        ]);

        $createData = [];

        try {
            $image = $request->file('image');
            $fileName = md5($image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
            $logoUrl = Storage::disk('public')->putFileAs('images/producers', $request->file('image'), $fileName);

            if ($logoUrl === false) {
                return redirect()->back()->with('error', 'Upload logo không thành công');
            }

            $createData = [
                'name' => $request->input('name'),
                'slug' => Str::slug($request->input('slug')),
                'logo_url' => $logoUrl
            ];

            $producer = Producer::query()->create($createData);

            if (!$producer instanceof Producer) {
                return back()->with('error', 'Đã có lỗi xảy ra, vui lòng thử lại');
            }

            return redirect()
                ->route('admin.producers.index')
                ->with('success', 'Thêm Nhà sản xuất thành công');
        } catch (Throwable $throwable) {
            Log::error(
                __METHOD__ . ':' . __LINE__ . ': ' . $throwable->getMessage(),
                [
                    'createData' => $createData
                ]
            );

            return back()->with('error', 'Đã có lỗi xảy ra, vui lòng thử lại');
        }
    }


    public function edit(): View
    {
        return view('admin.producers.create');
    }

    public function update(): View
    {
        return view('admin.producers.create');
    }

    public function destroy(): View
    {
        return view('admin.producers.create');
    }
}
