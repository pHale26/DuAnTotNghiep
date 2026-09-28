<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('start_date', 'desc')->paginate(10);

        return view('admin.holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('admin.holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:holidays,name',
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) use ($request) {
                    $endDate = $request->end_date;
                    if ($endDate && strtotime($value) <= strtotime($endDate)) {
                        $exists = Holiday::where('start_date', '<=', $endDate)
                            ->where('end_date', '>=', $value)
                            ->exists();
                        if ($exists) {
                            $fail('Thời gian diễn ra ngày lễ bị trùng với một ngày lễ khác.');
                        }
                    }
                },
            ],
            'end_date' => 'required|date|after:start_date',
            'price_increase_percentage' => 'required|numeric|min:0|max:100',
        ], [
            'name.unique' => 'Tên ngày lễ này đã tồn tại.',
            'start_date.after_or_equal' => 'Ngày bắt đầu ngày lễ phải là ngày trong tương lai hoặc hôm nay.',
            'end_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu ít nhất 1 ngày.',
        ]);

        Holiday::create($request->all());

        return redirect()->route('admin.holidays.index')->with('success', 'Đã thêm ngày lễ thành công.');
    }

    public function edit(Holiday $holiday)
    {
        return view('admin.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:holidays,name,'.$holiday->id,
            'start_date' => [
                'required',
                'date',
                // Bỏ rule after_or_equal:today khi cập nhật để có thể sửa ngày lễ đang diễn ra hoặc đã qua
                function ($attribute, $value, $fail) use ($request, $holiday) {
                    $endDate = $request->end_date;
                    if ($endDate && strtotime($value) <= strtotime($endDate)) {
                        $exists = Holiday::where('id', '!=', $holiday->id)
                            ->where('start_date', '<=', $endDate)
                            ->where('end_date', '>=', $value)
                            ->exists();
                        if ($exists) {
                            $fail('Thời gian diễn ra ngày lễ bị trùng với một ngày lễ khác.');
                        }
                    }
                },
            ],
            'end_date' => 'required|date|after:start_date',
            'price_increase_percentage' => 'required|numeric|min:0|max:100',
        ], [
            'name.unique' => 'Tên ngày lễ này đã tồn tại.',
            'end_date.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu ít nhất 1 ngày.',
        ]);

        $holiday->update($request->all());

        return redirect()->route('admin.holidays.index')->with('success', 'Đã cập nhật ngày lễ thành công.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')->with('success', 'Đã xóa ngày lễ thành công.');
    }
}
