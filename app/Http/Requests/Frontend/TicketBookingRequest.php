<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class TicketBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_option_id' => 'required|exists:ticket_options,id',
            'quantity' => 'required|integer|min:1|max:20',
            'visit_date' => 'required|date|after_or_equal:today',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|regex:/^[0-9]{10,11}$/|max:20',
            'customer_email' => 'required|email|max:255',
            'payment_method' => 'required|in:transfer,vnpay',
            'coupon_code' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_option_id.required' => 'Vui lòng chọn loại vé.',
            'ticket_option_id.exists' => 'Loại vé không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng vé.',
            'quantity.integer' => 'Số lượng vé phải là số nguyên.',
            'quantity.min' => 'Số lượng vé tối thiểu là 1.',
            'quantity.max' => 'Số lượng vé tối đa là 20.',
            'visit_date.required' => 'Vui lòng chọn ngày sử dụng vé.',
            'visit_date.date' => 'Ngày sử dụng vé không hợp lệ.',
            'visit_date.after_or_equal' => 'Ngày sử dụng vé phải từ hôm nay trở đi.',
            'customer_name.required' => 'Vui lòng nhập họ tên.',
            'customer_name.max' => 'Họ tên không được vượt quá 255 ký tự.',
            'customer_phone.required' => 'Vui lòng nhập số điện thoại.',
            'customer_phone.regex' => 'Số điện thoại phải có 10-11 chữ số.',
            'customer_email.required' => 'Vui lòng nhập email.',
            'customer_email.email' => 'Email không hợp lệ.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ticket_option_id' => 'Loại vé',
            'quantity' => 'Số lượng',
            'visit_date' => 'Ngày sử dụng',
            'customer_name' => 'Họ tên',
            'customer_phone' => 'Số điện thoại',
            'customer_email' => 'Email',
            'payment_method' => 'Phương thức thanh toán',
            'coupon_code' => 'Mã giảm giá',
        ];
    }
}
