<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\ProducersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Producer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProducerController extends Controller
{
    /**
     * @param ProducersDataTable $dataTable
     * @return mixed
     */
    public function index(ProducersDataTable $dataTable)
    {
        return $dataTable->render('admin.producers.index');
    }

    public function create(): View
    {
        return view('admin.producers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
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
//                'slug' => Str::slug($request->input('slug')),
                'logo' => $logoUrl
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


    public function edit(Producer $producer): View
    {
        return view('admin.producers.edit', ['producer' => $producer]);
    }

    public function update(Producer $producer, Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'logo' => ['nullable', 'file', 'max:1024']
        ]);

        $updateData = [];

        try {
            $updateData = [
                'name' => $request->input('name'),
//                'slug' => Str::slug($request->input('slug', $request->input('name', ''))),
            ];

            if ($request->hasFile('logo')) {
                if (Storage::disk('public')->exists($producer->logo ?? '')) {
                    Storage::disk('public')->delete($producer->logo ?? '');
                }

                $file = $request->file('logo');
                $updateData['logo'] = $file->store('images/producers', 'public');
            }

            $producer->update($updateData);

            return redirect()
                ->route('admin.producers.index')
                ->with('success', 'Cập nhật Nhà sản xuất thành công');
        } catch (Throwable $throwable) {
            Log::error(
                __METHOD__ . ':' . __LINE__ . ': ' . $throwable->getMessage(),
                [
                    'createData' => $updateData,
                ]
            );

            return back()->with('error', 'Đã có lỗi xảy ra, vui lòng thử lại');
        }
    }

    public function destroy(Producer $producer): JsonResponse
    {
        if ($producer->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa hãng sản xuất đã có sản phẩm'
            ], Response::HTTP_BAD_REQUEST);
        }

        $producer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa hãng sản xuất thành công'
        ], Response::HTTP_OK);
    }
}
