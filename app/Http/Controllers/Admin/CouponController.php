<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(10);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $categories = Category::all();

        return view('admin.coupons.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validFrom = $request->input('valid_from');
        $maxValidUntil = $validFrom ? Carbon::parse($validFrom)->addYear()->format('Y-m-d') : null;

        $request->validate([
            'code' => 'required|string|min:3|max:30|regex:/^[A-Z0-9_]+$/|unique:coupons,code',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required', 'numeric', 'min:0.01',
                $request->discount_type === 'percent' ? 'max:100' : 'max:999999999',
            ],
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date|after_or_equal:today',
            'valid_until' => array_filter([
                'required', 'date', 'after:valid_from',
                $maxValidUntil ? 'before_or_equal:'.$maxValidUntil : null,
            ]),
            'category_id' => 'nullable|exists:categories,id',
        ], [
            'code.min' => 'Mã giảm giá phải có ít nhất 3 ký tự.',
            'code.max' => 'Mã giảm giá không được quá 30 ký tự.',
            'code.regex' => 'Mã giảm giá chỉ được chứa chữ in hoa (A-Z), số (0-9) và dấu gạch dưới (_).',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.min' => 'Giá trị giảm phải lớn hơn 0.',
            'discount_value.max' => $request->discount_type === 'percent'
                ? 'Giá trị giảm theo phần trăm không được vượt quá 100%.'
                : 'Giá trị giảm không hợp lệ.',
            'min_order_value.min' => 'Giá trị đơn tối thiểu không được là số âm.',
            'max_discount.min' => 'Giảm tối đa không được là số âm.',
            'usage_limit.min' => 'Số lượt sử dụng phải ít nhất là 1.',
            'valid_from.after_or_equal' => 'Ngày bắt đầu không được nhỏ hơn thời gian hiện tại.',
            'valid_until.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu.',
            'valid_until.before_or_equal' => 'Hạn dùng mã giảm giá không được quá 1 năm kể từ ngày bắt đầu.',
        ]);

        Coupon::create([
            'category_id' => $request->category_id,
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_value' => $request->min_order_value,
            'max_discount' => $request->discount_type === 'fixed' ? null : $request->max_discount,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'usage_limit' => $request->usage_limit,
            'used_count' => 0,
        ]);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Thêm mã giảm giá thành công!');
    }

    public function edit(Coupon $coupon)
    {
        $categories = Category::all();

        return view('admin.coupons.edit', compact('coupon', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validFrom = $request->input('valid_from');
        $maxValidUntil = $validFrom ? Carbon::parse($validFrom)->addYear()->format('Y-m-d') : null;

        $request->validate([
            'code' => 'required|string|min:3|max:30|regex:/^[A-Z0-9_]+$/|unique:coupons,code,'.$coupon->id,
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => [
                'required', 'numeric', 'min:0.01',
                $request->discount_type === 'percent' ? 'max:100' : 'max:999999999',
            ],
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => array_filter([
                'required', 'date', 'after:valid_from',
                $maxValidUntil ? 'before_or_equal:'.$maxValidUntil : null,
            ]),
            'category_id' => 'nullable|exists:categories,id',
        ], [
            'code.min' => 'Mã giảm giá phải có ít nhất 3 ký tự.',
            'code.max' => 'Mã giảm giá không được quá 30 ký tự.',
            'code.regex' => 'Mã giảm giá chỉ được chứa chữ in hoa (A-Z), số (0-9) và dấu gạch dưới (_).',
            'code.unique' => 'Mã giảm giá này đã tồn tại.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'discount_value.min' => 'Giá trị giảm phải lớn hơn 0.',
            'discount_value.max' => $request->discount_type === 'percent'
                ? 'Giá trị giảm theo phần trăm không được vượt quá 100%.'
                : 'Giá trị giảm không hợp lệ.',
            'min_order_value.min' => 'Giá trị đơn tối thiểu không được là số âm.',
            'max_discount.min' => 'Giảm tối đa không được là số âm.',
            'usage_limit.min' => 'Số lượt sử dụng phải ít nhất là 1.',
            'valid_until.after' => 'Ngày kết thúc phải lớn hơn ngày bắt đầu.',
            'valid_until.before_or_equal' => 'Hạn dùng mã giảm giá không được quá 1 năm kể từ ngày bắt đầu.',
        ]);

        $coupon->update([
            'category_id' => $request->category_id,
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_value' => $request->min_order_value,
            'max_discount' => $request->discount_type === 'fixed' ? null : $request->max_discount,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'usage_limit' => $request->usage_limit,
        ]);

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Đã chuyển mã giảm giá vào thùng rác.');
    }

    public function trash()
    {
        $coupons = Coupon::onlyTrashed()
            ->latest()
            ->paginate(10);

        return view('admin.coupons.trash', compact('coupons'));
    }

    public function restore($id)
    {
        Coupon::onlyTrashed()
            ->findOrFail($id)
            ->restore();

        return redirect()
            ->route('admin.coupons.trash')
            ->with('success', 'Khôi phục mã giảm giá thành công.');
    }

    public function forceDelete($id)
    {
        Coupon::onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();

        return redirect()
            ->route('admin.coupons.trash')
            ->with('success', 'Đã xóa vĩnh viễn mã giảm giá.');
    }
}
