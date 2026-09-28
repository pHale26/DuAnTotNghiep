<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();

        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $coupons = Coupon::where('valid_until', '>=', now())
            ->orderBy('code')
            ->get();

        return view('admin.banners.create', compact('coupons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_url' => 'nullable|string',
            'target_url' => 'nullable|string',
            'coupon_id' => 'nullable|exists:coupons,id',
            'position' => 'nullable|string|in:hero,home_ads',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if (! $request->filled('sort_order')) {
            $data['sort_order'] = Banner::max('sort_order') + 1;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.str_replace(' ', '_', $image->getClientOriginalName());

            // Sử dụng copy thay vì move để tránh lỗi quyền Windows
            $uploadPath = public_path('uploads'.DIRECTORY_SEPARATOR.'banners');

            // Tạo thư mục nếu chưa tồn tại
            if (! is_dir($uploadPath)) {
                @mkdir($uploadPath, 0777, true);
            }

            $destinationPath = $uploadPath.DIRECTORY_SEPARATOR.$imageName;

            // Dùng copy thay vì move
            if (copy($image->getRealPath(), $destinationPath)) {
                @chmod($destinationPath, 0666);
                $data['image_url'] = 'uploads/banners/'.$imageName;
            } else {
                return back()->with('error', 'Không thể upload ảnh. Vui lòng thử dùng URL hoặc liên hệ admin.')->withInput();
            }
        } elseif (! $request->filled('image_url')) {
            return back()->with('error', 'Vui lòng tải ảnh lên hoặc nhập URL ảnh')->withInput();
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Thêm Banner thành công!');
    }

    public function edit(Banner $banner)
    {
        $coupons = Coupon::where('valid_until', '>=', now())
            ->orderBy('code')
            ->get();

        return view('admin.banners.edit', compact('banner', 'coupons'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image_url' => 'nullable|string',
            'target_url' => 'nullable|string',
            'coupon_id' => 'nullable|exists:coupons,id',
            'position' => 'nullable|string|in:hero,home_ads',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $data = $request->except(['image']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image')) {
            // Delete old image if it's a local file (không phải URL)
            if ($banner->image_url &&
                ! str_starts_with($banner->image_url, 'http') &&
                file_exists(public_path($banner->image_url))) {
                @unlink(public_path($banner->image_url));
            }

            $image = $request->file('image');
            $imageName = time().'_'.str_replace(' ', '_', $image->getClientOriginalName());

            // Sử dụng copy thay vì move
            $uploadPath = public_path('uploads'.DIRECTORY_SEPARATOR.'banners');

            if (! is_dir($uploadPath)) {
                @mkdir($uploadPath, 0777, true);
            }

            $destinationPath = $uploadPath.DIRECTORY_SEPARATOR.$imageName;

            if (copy($image->getRealPath(), $destinationPath)) {
                @chmod($destinationPath, 0666);
                $data['image_url'] = 'uploads/banners/'.$imageName;
            } else {
                return back()->with('error', 'Không thể upload ảnh. Vui lòng thử dùng URL.')->withInput();
            }
        } elseif ($request->filled('image_url')) {
            // Nếu người dùng nhập URL mới, cập nhật image_url
            // Xóa ảnh cũ nếu là file local
            if ($banner->image_url &&
                ! str_starts_with($banner->image_url, 'http') &&
                file_exists(public_path($banner->image_url))) {
                @unlink(public_path($banner->image_url));
            }
            $data['image_url'] = $request->image_url;
        }
        // Nếu không upload file mới và không nhập URL mới, giữ nguyên image_url cũ

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật Banner thành công!');
    }

    public function destroy(Banner $banner)
    {
        // Chuyển sang xóa mềm: bỏ qua xóa ảnh vật lý để giữ lại khi restore
        // if ($banner->image_url && file_exists(public_path($banner->image_url))) {
        //     unlink(public_path($banner->image_url));
        // }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Đã xóa Banner!');
    }

    public function trash()
    {
        $banners = Banner::onlyTrashed()->orderBy('sort_order')->get();

        return view('admin.banners.trash', compact('banners'));
    }

    public function restore($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        $banner->restore();

        return redirect()->route('admin.banners.trash')->with('success', 'Đã khôi phục Banner!');
    }

    public function forceDelete($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);

        if ($banner->image_url && file_exists(public_path($banner->image_url))) {
            unlink(public_path($banner->image_url));
        }

        $banner->forceDelete();

        return redirect()->route('admin.banners.trash')->with('success', 'Đã xóa vĩnh viễn Banner!');
    }
}
