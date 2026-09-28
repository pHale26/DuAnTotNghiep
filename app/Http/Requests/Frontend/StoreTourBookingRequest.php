<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTourBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'schedule_id' => 'required|exists:tour_schedules,id',
            'adults' => 'required|integer|min:1',
            'children' => 'required|integer|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => ['required', 'email', 'max:255', 'confirmed', new \App\Rules\ValidEmailDomain],
            'passengers' => 'required|array',
            'passengers.adult.*.full_name' => 'required|string|max:255',
            'passengers.adult.0.date_of_birth' => 'required|date|before_or_equal:'.now()->subYears(18)->format('Y-m-d'),
            'passengers.adult.*.date_of_birth' => 'required|date',
            'passengers.adult.*.identity_number' => 'required|string|max:50',
            'passengers.adult.*.gender' => 'required|in:male,female,other',
            'passengers.child.*.full_name' => 'nullable|string|max:255',
            'passengers.child.*.date_of_birth' => 'nullable|date',
            'passengers.child.*.gender' => 'nullable|in:male,female,other',
            'total_price' => 'required|numeric',
            'transport_type' => 'required|in:flight,bus,self',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'issue_place' => 'nullable|string|max:255',
            'front_image' => 'nullable|image|max:5120',
            'back_image' => 'nullable|image|max:5120',
            'payment_type' => 'required|in:full,deposit',
            'payment_method' => 'required|in:transfer,vnpay',
            'transport_price' => 'nullable|numeric',
            'transport_data' => 'nullable|string',
            'tickets' => 'nullable|array',
            'addons' => 'nullable|array',
            'coupon_code' => 'nullable|string',
            'accommodation_id' => 'nullable|exists:room_types,id',
            'single_rooms_count' => 'nullable|integer|min:0',
            'extra_beds_count' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'passengers.adult.0.date_of_birth.before_or_equal' => 'Người đặt tour (hành khách đầu tiên) phải từ đủ 18 tuổi trở lên.',
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->accommodation_id) {
                $room = \App\Models\RoomType::find($this->accommodation_id);
                if ($room) {
                    $roomsCount = $this->single_rooms_count ?? 1;
                    $extraBeds = $this->extra_beds_count ?? 0;
                    
                    $adults = $this->adults ?? 0;
                    $children = $this->children ?? 0;
                    $totalPersons = $adults + $children;

                    $maxAllowedPersons = $roomsCount * $room->max_capacity;
                    if ($totalPersons > $maxAllowedPersons) {
                        $validator->errors()->add('single_rooms_count', "Số lượng phòng không đủ sức chứa cho tổng số hành khách (tối đa $maxAllowedPersons người).");
                    }
                    
                    $maxExtraBeds = $roomsCount * ($room->max_capacity - $room->base_capacity);
                    if ($extraBeds > $maxExtraBeds) {
                        $validator->errors()->add('extra_beds_count', "Số giường phụ vượt quá mức cho phép (tối đa $maxExtraBeds giường).");
                    }
                    
                    $adultCapacity = ($roomsCount * $room->base_capacity) + $extraBeds;
                    if ($adults > $adultCapacity) {
                        $validator->errors()->add('single_rooms_count', "Cần thêm phòng hoặc giường phụ. Sức chứa hiện tại chỉ đủ cho $adultCapacity người lớn.");
                    }
                }
            }
        });
    }
}
