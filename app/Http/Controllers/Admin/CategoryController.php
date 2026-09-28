<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.vi' => 'required|max:255',
            'name.en' => 'nullable|max:255',
            'name.zh' => 'nullable|max:255',
        ]);

        Category::create([
            'name' => [
                'vi' => $request->name['vi'],
                'en' => $request->name['en'] ?? $request->name['vi'],
                'zh' => $request->name['zh'] ?? $request->name['vi'],
            ],
            'slug' => Str::slug($request->name['vi']),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name.vi' => 'required|max:255',
            'name.en' => 'nullable|max:255',
            'name.zh' => 'nullable|max:255',
        ]);

        $category->update([
            'name' => [
                'vi' => $request->name['vi'],
                'en' => $request->name['en'] ?? $request->name['vi'],
                'zh' => $request->name['zh'] ?? $request->name['vi'],
            ],
            'slug' => Str::slug($request->name['vi']),
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Category $category)
    {
        if ($category->tours()->exists()) {
            return back()->with('error', 'Không thể xóa danh mục này vì vẫn còn tour đang liên kết với nó!');
        }

        // Tiến hành xóa danh mục (sẽ thành xóa mềm)
        $category->delete();

        return back()->with('success', 'Đã xóa danh mục thành công!');
    }

    public function trash()
    {
        $categories = Category::onlyTrashed()->latest()->paginate(10);

        return view('admin.categories.trash', compact('categories'));
    }

    public function restore($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('admin.categories.trash')->with('success', 'Đã khôi phục danh mục!');
    }

    public function forceDelete($id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);

        $category->tours()->detach();
        $category->forceDelete();

        return redirect()->route('admin.categories.trash')->with('success', 'Đã xóa vĩnh viễn danh mục!');
    }
}
