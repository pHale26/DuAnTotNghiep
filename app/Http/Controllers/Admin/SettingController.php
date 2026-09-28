<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display settings list.
     */
    public function index()
    {
        $threshold = Setting::get('ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS', 24);

        return view('admin.settings.index', compact('threshold'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS' => 'required|integer|min:1|max:168',
        ], [
            'ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS.required' => 'Vui lòng nhập ngưỡng thời gian khẩn cấp.',
            'ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS.integer' => 'Ngưỡng thời gian khẩn cấp phải là số nguyên.',
            'ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS.min' => 'Ngưỡng thời gian khẩn cấp phải từ 1 giờ.',
            'ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS.max' => 'Ngưỡng thời gian khẩn cấp không được vượt quá 1 tuần (168 giờ).',
        ]);

        Setting::set(
            'ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS',
            $request->input('ABSENCE_REQUEST_URGENT_THRESHOLD_HOURS'),
            'Ngưỡng thời gian (giờ) để phân loại mức độ khẩn cấp của yêu cầu báo bận tour. Mặc định là 24 giờ.'
        );

        return redirect()->back()->with('success', 'Cập nhật cấu hình hệ thống thành công.');
    }
}
