<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourGuide;
use App\Models\User;
use Illuminate\Http\Request;

class TourGuideController extends Controller
{
    public function index()
    {
        $tourGuides = TourGuide::with('user')->withCount('schedule_guides')->latest()->paginate(10);

        return view('admin.tour_guides.index', compact('tourGuides'));
    }

    public function create()
    {
        // Lấy tất cả user có vai trò HDV (role 'guide' hoặc Spatie role 'Guide') chưa có hồ sơ HDV
        $users = User::where(function ($q) {
            $q->where('role', 'guide')
                ->orWhereHas('roles', function ($r) {
                    $r->whereIn('name', ['Guide', 'guide']);
                });
        })->doesntHave('tour_guide')->get();

        return view('admin.tour_guides.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|max:255',
            'phone' => 'required|digits:10|unique:tour_guides,phone',
            'email' => 'nullable|email|max:255|unique:tour_guides,email',
            'guide_card_type' => 'nullable|string|max:100',
            'languages' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'is_blacklisted' => 'boolean',
            'bio' => 'nullable|string',
        ], [
            'phone.digits' => 'Số điện thoại phải đúng 10 chữ số.',
            'phone.unique' => 'Số điện thoại này đã tồn tại, vui lòng nhập số khác.',
            'email.unique' => 'Email này đã tồn tại, vui lòng nhập email khác.',
        ]);

        $validated['is_blacklisted'] = $request->has('is_blacklisted');

        $tourGuide = TourGuide::create($validated);

        // Tự động cấp quyền HDV cho User được liên kết
        if ($tourGuide->user_id) {
            $user = User::find($tourGuide->user_id);
            if ($user) {
                if (! $user->hasRole('Guide')) {
                    $user->assignRole('Guide');
                }
                $user->update(['role' => 'guide']);
            }
        }

        return redirect()->route('admin.tour_guides.index')->with('success', 'Đã thêm Hướng dẫn viên mới và đồng bộ quyền tài khoản!');
    }

    public function edit(TourGuide $tourGuide)
    {
        // Lấy tất cả user có vai trò HDV chưa có hồ sơ HDV, hoặc là user đang được liên kết với HDV này
        $users = User::where(function ($q) {
            $q->where('role', 'guide')
                ->orWhereHas('roles', function ($r) {
                    $r->whereIn('name', ['Guide', 'guide']);
                });
        })->where(function ($query) use ($tourGuide) {
            $query->doesntHave('tour_guide')
                ->orWhere('id', $tourGuide->user_id);
        })->get();

        return view('admin.tour_guides.edit', compact('tourGuide', 'users'));
    }

    public function update(Request $request, TourGuide $tourGuide)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'name' => 'required|max:255',
            'phone' => 'required|digits:10|unique:tour_guides,phone,'.$tourGuide->id,
            'email' => 'nullable|email|max:255|unique:tour_guides,email,'.$tourGuide->id,
            'guide_card_type' => 'nullable|string|max:100',
            'languages' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'is_blacklisted' => 'boolean',
            'bio' => 'nullable|string',
        ], [
            'phone.digits' => 'Số điện thoại phải đúng 10 chữ số.',
            'phone.unique' => 'Số điện thoại này đã tồn tại, vui lòng nhập số khác.',
            'email.unique' => 'Email này đã tồn tại, vui lòng nhập email khác.',
        ]);

        $oldUserId = $tourGuide->user_id;

        $validated['is_blacklisted'] = $request->has('is_blacklisted');
        $tourGuide->update($validated);

        // Nếu có thay đổi tài khoản liên kết, cấp quyền cho tài khoản mới
        if ($tourGuide->user_id && $tourGuide->user_id != $oldUserId) {
            $user = User::find($tourGuide->user_id);
            if ($user) {
                if (! $user->hasRole('Guide')) {
                    $user->assignRole('Guide');
                }
                $user->update(['role' => 'guide']);
            }
        }

        return redirect()->route('admin.tour_guides.index')->with('success', 'Cập nhật Hướng dẫn viên thành công!');
    }

    public function destroy(TourGuide $tourGuide)
    {
        $tourGuide->delete();

        return redirect()->route('admin.tour_guides.index')->with('success', 'Đã xóa Hướng dẫn viên!');
    }
}
