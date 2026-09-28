<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class TourFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'destination_id' => ['nullable', 'integer', 'exists:destinations,id'],
            'departure_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:today',
                'before:'.now()->addYears(2)->format('Y-m-d'),
            ],
            'category_id' => ['nullable'],
            'budget' => ['nullable', 'in:all,under_5m,5m_10m,10m_20m,over_20m'],
        ];
    }

    public function messages(): array
    {
        return [
            'destination_id.exists' => 'Điểm đến không hợp lệ.',
            'departure_date.date_format' => 'Ngày không hợp lệ, vui lòng chọn lại.',
            'departure_date.after_or_equal' => 'Vui lòng chọn ngày từ hôm nay trở đi.',
            'departure_date.before' => 'Ngày đi không được quá 2 năm trong tương lai.',

            'budget.in' => 'Mức ngân sách không hợp lệ.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Với bộ lọc user: KHÔNG redirect, KHÔNG throw exception
        // Chỉ lưu lỗi vào session để hiện inline
        session()->flash('filter_errors', $validator->errors()->toArray());
    }
}
