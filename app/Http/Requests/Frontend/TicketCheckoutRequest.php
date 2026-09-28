<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class TicketCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ticket_id' => 'required|exists:tickets,id',
            'ticket_option_id' => 'required|exists:ticket_options,id',
            'quantity' => 'required|integer|min:1|max:20',
            'visit_date' => 'required|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'ticket_id.required' => 'Vui lòng chọn vé tham quan.',
            'ticket_id.exists' => 'Vé tham quan không tồn tại.',
            'ticket_option_id.required' => 'Vui lòng chọn loại vé.',
            'ticket_option_id.exists' => 'Loại vé không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng vé.',
            'quantity.integer' => 'Số lượng vé phải là số nguyên.',
            'quantity.min' => 'Số lượng vé tối thiểu là 1.',
            'quantity.max' => 'Số lượng vé tối đa là 20.',
            'visit_date.required' => 'Vui lòng chọn ngày sử dụng vé.',
            'visit_date.date' => 'Ngày sử dụng vé không hợp lệ.',
            'visit_date.after_or_equal' => 'Ngày sử dụng vé phải từ hôm nay trở đi.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ticket_id' => 'Vé tham quan',
            'ticket_option_id' => 'Loại vé',
            'quantity' => 'Số lượng',
            'visit_date' => 'Ngày sử dụng',
        ];
    }
}
