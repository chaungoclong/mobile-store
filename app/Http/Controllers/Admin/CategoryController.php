<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\CategoriesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CategoryController extends Controller
{
    public function index(CategoriesDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->render('admin.categories.index');
    }

    public function show(Category $category): View
    {
        return view('admin.categories.show', ['category' => $category]);
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
            'slug' => ['nullable', 'string', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:1024'],
        ]);

        try {
            $createData = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
//                'slug' => Str::slug($request->input('slug') ?? $request->input('name')),
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageUrl = $file->store('images/categories', 'public');
                $createData['image'] = $imageUrl;
            }

            Category::query()->create($createData);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Tạo danh mục sản phẩm thành công');
        } catch (Throwable $throwable) {
            Log::error(__METHOD__ . ':' . __LINE__ . ': ' . $throwable->getMessage(), $request->all());
            return redirect()->back()->with('error', 'Tạo danh mục sản phẩm không thành công');
        }
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Category $category, Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string'],
//            'slug' => ['string', 'unique:categories,slug,' . $category->getKey()],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:1024'],
        ]);

        try {
            $updateData = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'slug' => Str::slug($request->input('slug') ?? $request->input('name')),
            ];

            if ($request->hasFile('image')) {
                if (Storage::disk('public')->exists($category->image ?? '')) {
                    Storage::disk('public')->delete($category->image ?? '');
                }

                $file = $request->file('image');
                $updateData['image'] = $file->store('images/categories', 'public');
            }

            $category->update($updateData);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Cập nhật danh mục sản phẩm thành công');
        } catch (Throwable $throwable) {
            Log::error(__METHOD__ . ':' . __LINE__ . ': ' . $throwable->getMessage(), $request->all());
            return redirect()->back()->with('error', 'Cập nhật danh mục sản phẩm không thành công');
        }
    }

    public function destroy(Category $category): JsonResponse
    {
        try {
            if ($category->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa Danh mục đã có sản phẩm'
                ], Response::HTTP_BAD_REQUEST);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa Danh mục thành công'
            ], Response::HTTP_OK);
        } catch (Throwable $throwable) {
            Log::error(__METHOD__ . ':' . __LINE__ . ':' . $throwable->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Xóa Danh mục không thành công'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
