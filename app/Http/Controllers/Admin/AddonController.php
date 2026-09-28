<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Tour;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function index()
    {
        $addons = Addon::orderBy('id', 'desc')->paginate(10);

        return view('admin.addons.index', compact('addons'));
    }

    public function create()
    {
        $tours = Tour::select('id', 'title')->get();

        return view('admin.addons.create', compact('tours'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:addons,name',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'tours' => 'nullable|array',
            'tours.*' => 'exists:tours,id',
        ], [
            'name.unique' => 'Tên dịch vụ Addon này đã tồn tại.',
        ]);

        $data = $request->except(['image', 'tours']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('addons', 'public');
            $data['image_url'] = '/storage/'.$path;
        }

        $addon = Addon::create($data);

        if ($request->has('tours')) {
            $addon->tours()->sync($request->tours);
        }

        return redirect()->route('admin.addons.index')->with('success', 'Đã thêm dịch vụ Addon thành công.');
    }

    public function edit(Addon $addon)
    {
        $tours = Tour::select('id', 'title')->get();
        $selectedTours = $addon->tours->pluck('id')->toArray();

        return view('admin.addons.edit', compact('addon', 'tours', 'selectedTours'));
    }

    public function update(Request $request, Addon $addon)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:addons,name,'.$addon->id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'tours' => 'nullable|array',
            'tours.*' => 'exists:tours,id',
        ], [
            'name.unique' => 'Tên dịch vụ Addon này đã tồn tại.',
        ]);

        $data = $request->except(['image', 'tours']);
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('addons', 'public');
            $data['image_url'] = '/storage/'.$path;
        }

        $addon->update($data);

        if ($request->has('tours')) {
            $addon->tours()->sync($request->tours);
        } else {
            $addon->tours()->sync([]);
        }

        return redirect()->route('admin.addons.index')->with('success', 'Đã cập nhật dịch vụ Addon thành công.');
    }

    public function destroy(Addon $addon)
    {
        $addon->delete();

        return redirect()->route('admin.addons.index')->with('success', 'Đã xóa dịch vụ Addon thành công.');
    }

    public function trash()
    {
        $addons = Addon::onlyTrashed()->orderBy('id', 'desc')->paginate(10);

        return view('admin.addons.trash', compact('addons'));
    }

    public function restore($id)
    {
        $addon = Addon::onlyTrashed()->findOrFail($id);
        $addon->restore();

        return redirect()->route('admin.addons.trash')->with('success', 'Đã khôi phục dịch vụ Addon thành công.');
    }

    public function forceDelete($id)
    {
        $addon = Addon::onlyTrashed()->findOrFail($id);

        $addon->tours()->detach();
        $addon->forceDelete();

        return redirect()->route('admin.addons.trash')->with('success', 'Đã xóa vĩnh viễn dịch vụ Addon thành công.');
    }
}
