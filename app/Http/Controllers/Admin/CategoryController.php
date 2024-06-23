<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $query = Category::query();

            $query->when($request->has('name'), function (Builder $query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('status') . '%');
            });

            $query->when($request->boolean('status'), function (Builder $query) use ($request) {
                $query->where('status', $request->boolean('status'));
            });

            return DataTables::eloquent($query)
                ->filter(function ($query) use ($request) {
                    if ($request->has('status')) {
                        $query->where('status', $request->get('status'));
                    }

                    if ($request->has('name')) {
                        $query->where('name', 'like', '%' . $request->get('status') . '%');
                    }
                })
                ->addColumn('action', function ($category) {
                    return '<a href="' . route('admin.categories.edit', $category->id) . '" class="btn btn-warning">Edit</a>
                        <form action="' . route('admin.categories.destroy', $category->id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('delete') . '
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>';
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('admin.categories.index');
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
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        try {
            $createData = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'slug' => Str::slug($request->input('slug', $request->input('name'))),
                'status' => $request->input('status'),
                'is_featured' => $request->has('is_featured'),
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
            'slug' => ['required', 'string', 'unique:categories,slug,' . $category->getKey()],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:1024'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        try {
            $updateData = [
                'description' => $request->input('description'),
                'slug' => Str::slug($request->input('slug', $request->input('name'))),
                'status' => $request->input('status'),
                'is_featured' => $request->has('is_featured'),
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

    public function destroy(Category $category): RedirectResponse
    {
        try {
            if ($category->products()->count() > 0) {
                return redirect()->back()->with('error', 'Không thể xóa danh mục đã có sản phẩm');
            }

            $category->delete();

            return redirect()->back()->with('success', 'Xóa danh mục thành công');
        } catch (Throwable $throwable) {
            Log::error(__METHOD__ . ':' . __LINE__ . ':' . $throwable->getMessage());

            return redirect()->back()->with('success', 'Xóa danh mục không thành công');
        }
    }
}
