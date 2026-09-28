<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class TicketSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'keyword' => 'nullable|string|max:255',
            'destination_id' => 'nullable|exists:destinations,id',
            'use_date' => 'nullable|date|after_or_equal:today',
            'sort' => 'nullable|in:latest,price_asc,price_desc',
        ];
    }

    public function messages(): array
    {
        return [
            'keyword.string' => 'Từ khóa tìm kiếm không hợp lệ.',
            'keyword.max' => 'Từ khóa tìm kiếm không được vượt quá 255 ký tự.',
            'destination_id.exists' => 'Điểm đến không tồn tại.',
            'use_date.date' => 'Ngày sử dụng không hợp lệ.',
            'use_date.after_or_equal' => 'Ngày sử dụng phải từ hôm nay trở đi.',
            'sort.in' => 'Phương thức sắp xếp không hợp lệ.',
        ];
    }

    public function attributes(): array
    {
        return [
            'keyword' => 'Từ khóa',
            'destination_id' => 'Điểm đến',
            'use_date' => 'Ngày sử dụng',
            'sort' => 'Sắp xếp',
        ];
    }

    /**
     * Get validated and sanitized data
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Sanitize keyword
        if (isset($validated['keyword'])) {
            $validated['keyword'] = strip_tags(trim($validated['keyword']));
        }

        return $validated;
    }
}
