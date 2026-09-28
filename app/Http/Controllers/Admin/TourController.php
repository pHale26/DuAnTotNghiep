<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Addon;
use App\Models\Category;
use App\Models\Destination;
use App\Models\Province;
use App\Models\Ticket;
use App\Models\Tour;
use App\Models\TourImage;
use App\Models\TourSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index()
    {
        // Lấy tour kèm theo destination và ảnh chính để hiển thị bảng
        $tours = Tour::with(['destination', 'tour_images' => function ($query) {
            $query->where('is_primary', 1);
        }])->latest()->paginate(10);

        return view('admin.tours.index', compact('tours'));
    }

    public function create()
    {
        $provinces = Province::all();
        $categories = Category::all();
        $destinations = Destination::all();
        $tickets = Ticket::all();
        $accommodations = Accommodation::with('room_types')->where('is_active', true)->get();
        $addons = Addon::all();

        return view('admin.tours.create', compact('provinces', 'categories', 'destinations', 'tickets', 'accommodations', 'addons'));
    }

    public function store(Request $request)
    {
        // 1. Validate dữ liệu
        $request->validate([
            'title.vi' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) {
                    $exists = Tour::where('title->vi', $value)->exists();
                    if ($exists) {
                        $fail('Tên tour (Tiếng Việt) đã tồn tại. Vui lòng chọn tên khác.');
                    }
                },
            ],
            'title.en' => 'nullable|max:255',
            'title.zh' => 'nullable|max:255',
            'base_price' => 'nullable|numeric',
            'child_price' => 'nullable|numeric',
            'meeting_point' => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'accommodation_id' => [
                'nullable',
                'exists:accommodations,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->filled('destination_id')) {
                        $accommodation = Accommodation::find($value);
                        if ($accommodation && $accommodation->destination_id != $request->destination_id) {
                            $fail('Địa chỉ của điểm lưu trú phải trùng với điểm đến của tour.');
                        }
                    }
                },
            ],
            'duration_days' => 'required|integer',
            'duration_nights' => 'required|integer',
            'departure_hour' => 'nullable|integer|between:0,23',
            'departure_minute' => 'nullable|integer|between:0,59',
            'cost_transport' => 'required|numeric|min:0',
            'cost_meal' => 'required|numeric|min:0',
            'cost_insurance' => 'required|numeric|min:0',
            'cost_service_fee' => 'required|numeric|min:0',
        ]);

        // 2. Tạo Tour và tự động sinh slug
        $tour = new Tour;
        $tour->title = [
            'vi' => $request->title['vi'],
            'en' => $request->title['en'] ?? $request->title['vi'],
            'zh' => $request->title['zh'] ?? $request->title['vi'],
        ];
        $tour->slug = Str::slug($request->title['vi']).'-'.time(); // Đảm bảo slug là duy nhất
        $tour->description = [
            'vi' => $request->description['vi'] ?? '',
            'en' => $request->description['en'] ?? ($request->description['vi'] ?? ''),
            'zh' => $request->description['zh'] ?? ($request->description['vi'] ?? ''),
        ];
        $tour->base_price = $request->base_price ?? 0;
        $tour->child_price = $request->child_price ?? 0;
        $tour->cost_transport = $request->cost_transport ?? 0;
        $tour->cost_meal = $request->cost_meal ?? 0;
        $tour->cost_insurance = $request->cost_insurance ?? 0;
        $tour->cost_service_fee = $request->cost_service_fee ?? 0;
        $tour->meeting_point = $request->meeting_point;
        $tour->departure_province_id = null;
        $tour->departure_ward_id = null;
        $tour->destination_id = $request->destination_id;
        $tour->destination_province_id = null;
        $tour->destination_ward_id = null;
        if ($request->filled('departure_hour') && $request->filled('departure_minute')) {
            $tour->departure_time = sprintf('%02d:%02d:00', $request->departure_hour, $request->departure_minute);
        } else {
            $tour->departure_time = null;
        }
        $tour->duration_days = $request->duration_days;
        $tour->duration_nights = $request->duration_nights;
        $tour->save();

        if ($request->hasFile('primary_image')) {
            $path = $request->file('primary_image')->store('tours', 'public');

            $tour->images()->create([
                'image_url' => '/storage/'.$path,
                'is_primary' => 1, // Đánh dấu đây là ảnh chính[cite: 8]
            ]);
        }
        // 3. Lưu danh mục (nhiều-nhiều) vào bảng tour_categories
        if ($request->has('categories')) {
            $tour->categories()->sync($request->categories);
        }
        if ($request->has('tickets')) {
            $tour->tickets()->sync($request->tickets);
        }
        if ($request->filled('accommodation_id')) {
            $accommodation = Accommodation::with('room_types')->find($request->accommodation_id);
            if ($accommodation) {
                foreach ($accommodation->room_types as $rt) {
                    $tour->accommodation_tiers()->create([
                        'room_type_id' => $rt->id,
                        'tier_label' => ($accommodation->star_rating ?? 3).' Sao',
                        'price_adjustment' => 0,
                    ]);
                }
            }
        }
        if ($request->has('addons')) {
            $tour->addons()->sync($request->addons);
        }

        // Recalculate base price based on cost components + included tickets
        $ticketAdultCost = $tour->tickets()->sum('adult_price');
        $ticketChildCost = $tour->tickets()->sum('child_price');
        $tour->base_price = ($tour->cost_transport ?? 0) + ($tour->cost_meal ?? 0) + ($tour->cost_insurance ?? 0) + ($tour->cost_service_fee ?? 0) + $ticketAdultCost;
        $tour->child_price = (($tour->cost_transport ?? 0) * config('booking.child_price_rate')) + (($tour->cost_meal ?? 0) * config('booking.child_price_rate')) + (($tour->cost_insurance ?? 0) * config('booking.child_price_rate')) + (($tour->cost_service_fee ?? 0) * config('booking.child_price_rate')) + $ticketChildCost;
        $tour->save();

        // Kiểm tra nếu là AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã tạo tour thành công!',
                'tour' => [
                    'id' => $tour->id,
                    'title' => $tour->title,
                    'slug' => $tour->slug,
                ],
                'redirect_url' => route('admin.tours.schedules', $tour->id),
            ]);
        }

        // Trong TourController@store, sửa dòng return thành:
        return redirect()->route('admin.tours.schedules', $tour->id)->with('success', 'Đã tạo tour, vui lòng thêm lịch trình!');
    }

    // Hiển thị danh sách lịch trình của một tour
    public function schedules($id)
    {
        $tour = Tour::with('tour_schedules')->findOrFail($id);

        return view('admin.tours.schedules', compact('tour'));
    }

    // Lưu lịch trình mới
    public function storeSchedule(Request $request, $id)
    {
        $request->validate([
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'capacity' => 'required|integer|min:1',
        ]);

        TourSchedule::create([
            'tour_id' => $id,
            'departure_date' => $request->departure_date,
            'return_date' => $request->return_date,
            'capacity' => $request->capacity,
            'available_seats' => $request->capacity, // Mặc định ban đầu chỗ trống bằng tổng chỗ
            'status' => 'available',
        ]);

        return back()->with('success', 'Đã thêm lịch trình mới thành công!');
    }

    public function images($id)
    {
        // Lưu ý: tour_images là tên hàm quan hệ anh đã xác nhận ở Model Tour.php
        $tour = Tour::with('tour_images')->findOrFail($id);

        return view('admin.tours.images', compact('tour'));
    }

    public function storeImages(Request $request, $id)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Chỉ chấp nhận file ảnh
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                // Lưu file vào thư mục storage/app/public/tours
                $path = $file->store('tours', 'public');

                TourImage::create([
                    'tour_id' => $id,
                    'image_url' => '/storage/'.$path, // Lưu đường dẫn để hiển thị ra web[cite: 8]
                    'is_primary' => 0,
                ]);
            }
        }

        return back()->with('success', 'Đã tải ảnh lên thành công!');
    }

    // 1. Hiển thị form Sửa
    public function edit($id)
    {
        $tour = Tour::with('categories')->findOrFail($id);
        $provinces = Province::all();
        $categories = Category::all();
        $destinations = Destination::all();
        $tickets = Ticket::all();
        $accommodations = Accommodation::with('room_types')->where('is_active', true)->get();
        $addons = Addon::all();

        // Lấy danh sách ID danh mục mà tour đang có để check vào checkbox
        $tourCategoryIds = $tour->categories->pluck('id')->toArray();
        $tourTicketIds = $tour->tickets->pluck('id')->toArray();
        $tourRoomTypeIds = $tour->accommodation_tiers()->pluck('room_type_id')->toArray();
        $tourAddonIds = $tour->addons->pluck('id')->toArray();
        $selectedAccommodationId = $tour->accommodation_tiers()->first()?->room_type?->accommodation_id;

        return view('admin.tours.edit', compact(
            'tour', 'provinces', 'categories', 'tourCategoryIds', 'destinations',
            'tickets', 'accommodations', 'addons', 'tourTicketIds', 'tourRoomTypeIds', 'tourAddonIds', 'selectedAccommodationId'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title.vi' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    $exists = Tour::where('title->vi', $value)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('Tên tour (Tiếng Việt) đã tồn tại. Vui lòng chọn tên khác.');
                    }
                },
            ],
            'title.en' => 'nullable|max:255',
            'title.zh' => 'nullable|max:255',
            'base_price' => 'nullable|numeric',
            'child_price' => 'nullable|numeric',
            'meeting_point' => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'accommodation_id' => [
                'nullable',
                'exists:accommodations,id',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->filled('destination_id')) {
                        $accommodation = Accommodation::find($value);
                        if ($accommodation && $accommodation->destination_id != $request->destination_id) {
                            $fail('Địa chỉ của điểm lưu trú phải trùng với điểm đến của tour.');
                        }
                    }
                },
            ],
            'duration_days' => 'required|integer',
            'duration_nights' => 'required|integer',
            'departure_hour' => 'nullable|integer|between:0,23',
            'departure_minute' => 'nullable|integer|between:0,59',
            'cost_transport' => 'required|numeric|min:0',
            'cost_meal' => 'required|numeric|min:0',
            'cost_insurance' => 'required|numeric|min:0',
            'cost_service_fee' => 'required|numeric|min:0',
        ]);

        $tour = Tour::findOrFail($id);
        $tour->title = [
            'vi' => $request->title['vi'],
            'en' => $request->title['en'] ?? $request->title['vi'],
            'zh' => $request->title['zh'] ?? $request->title['vi'],
        ];
        $tour->description = [
            'vi' => $request->description['vi'] ?? '',
            'en' => $request->description['en'] ?? ($request->description['vi'] ?? ''),
            'zh' => $request->description['zh'] ?? ($request->description['vi'] ?? ''),
        ];
        $tour->base_price = $request->base_price ?? 0;
        $tour->child_price = $request->child_price ?? 0;
        $tour->cost_transport = $request->cost_transport ?? 0;
        $tour->cost_meal = $request->cost_meal ?? 0;
        $tour->cost_insurance = $request->cost_insurance ?? 0;
        $tour->cost_service_fee = $request->cost_service_fee ?? 0;
        $tour->meeting_point = $request->meeting_point;
        $tour->departure_province_id = null;
        $tour->departure_ward_id = null;
        $tour->destination_id = $request->destination_id;
        $tour->destination_province_id = null;
        $tour->destination_ward_id = null;
        if ($request->filled('departure_hour') && $request->filled('departure_minute')) {
            $tour->departure_time = sprintf('%02d:%02d:00', $request->departure_hour, $request->departure_minute);
        } else {
            $tour->departure_time = null;
        }
        $tour->duration_days = $request->duration_days;
        $tour->duration_nights = $request->duration_nights;

        $imageUpdated = false;
        // Nếu có cập nhật ảnh đại diện
        if ($request->hasFile('primary_image')) {
            $path = $request->file('primary_image')->store('tours', 'public');
            // Xóa ảnh chính cũ, tạo ảnh chính mới
            $tour->tour_images()->where('is_primary', 1)->delete();
            $tour->tour_images()->create([
                'image_url' => '/storage/'.$path,
                'is_primary' => 1,
            ]);
            $imageUpdated = true;
        }

        $tour->save();

        if ($request->has('categories')) {
            $tour->categories()->sync($request->categories);
        } else {
            $tour->categories()->detach();
        }
        if ($request->has('tickets')) {
            $tour->tickets()->sync($request->tickets);
        } else {
            $tour->tickets()->detach();
        }
        $tour->accommodation_tiers()->delete();
        if ($request->filled('accommodation_id')) {
            $accommodation = Accommodation::with('room_types')->find($request->accommodation_id);
            if ($accommodation) {
                foreach ($accommodation->room_types as $rt) {
                    $tour->accommodation_tiers()->create([
                        'room_type_id' => $rt->id,
                        'tier_label' => ($accommodation->star_rating ?? 3).' Sao',
                        'price_adjustment' => 0,
                    ]);
                }
            }
        }
        if ($request->has('addons')) {
            $tour->addons()->sync($request->addons);
        } else {
            $tour->addons()->detach();
        }

        // Recalculate base price based on cost components + included tickets
        $ticketAdultCost = $tour->tickets()->sum('adult_price');
        $ticketChildCost = $tour->tickets()->sum('child_price');
        $tour->base_price = ($tour->cost_transport ?? 0) + ($tour->cost_meal ?? 0) + ($tour->cost_insurance ?? 0) + ($tour->cost_service_fee ?? 0) + $ticketAdultCost;
        $tour->child_price = (($tour->cost_transport ?? 0) * config('booking.child_price_rate')) + (($tour->cost_meal ?? 0) * config('booking.child_price_rate')) + (($tour->cost_insurance ?? 0) * config('booking.child_price_rate')) + (($tour->cost_service_fee ?? 0) * config('booking.child_price_rate')) + $ticketChildCost;
        $tour->save();

        // Kiểm tra nếu là AJAX request
        if ($request->expectsJson() || $request->ajax()) {
            $primaryImage = $tour->tour_images()->where('is_primary', 1)->first();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật tour thành công!',
                'tour' => [
                    'id' => $tour->id,
                    'title' => $tour->title,
                    'slug' => $tour->slug,
                    'primary_image' => $primaryImage ? $primaryImage->image_url : null,
                ],
                'image_updated' => $imageUpdated,
            ]);
        }

        return redirect()->route('admin.tours.index')->with('success', 'Cập nhật tour thành công!');
    }

    // 3. Xóa mềm (Đưa vào thùng rác)
    public function destroy($id)
    {
        Tour::findOrFail($id)->delete();

        return back()->with('success', 'Đã chuyển tour vào thùng rác!');
    }

    // 4. Xem thùng rác
    public function trash()
    {
        // Lấy các tour đã bị xóa
        $tours = Tour::onlyTrashed()->with('destination')->latest()->paginate(10);

        return view('admin.tours.trash', compact('tours'));
    }

    // 5. Khôi phục
    public function restore($id)
    {
        Tour::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Đã khôi phục tour thành công!');
    }

    // 6. Xóa vĩnh viễn (Tùy chọn)
    public function forceDelete($id)
    {
        Tour::withTrashed()->findOrFail($id)->forceDelete();

        return back()->with('success', 'Đã xóa vĩnh viễn tour!');
    }

    public function setPrimaryImage($tourId, $imageId)
    {
        // 1. Đưa tất cả các ảnh của tour này về trạng thái không phải ảnh chính (0)
        TourImage::where('tour_id', $tourId)->update(['is_primary' => 0]);

        // 2. Tìm bức ảnh được chọn và đặt nó làm ảnh chính (1)
        $image = TourImage::where('tour_id', $tourId)->findOrFail($imageId);
        $image->is_primary = 1;
        $image->save();

        return back()->with('success', 'Đã thay đổi ảnh chính thành công!');
    }
    // Nhớ kiểm tra ở đầu file đã có dòng này chưa nhé: use Illuminate\Support\Facades\Storage;

    public function destroyImage($tourId, $imageId)
    {
        // Tìm ảnh dựa trên ID ảnh và ID tour để đảm bảo bảo mật
        $image = TourImage::where('tour_id', $tourId)->findOrFail($imageId);

        // 1. Xóa file vật lý trong thư mục storage
        // Do image_url lưu trong DB có dạng "/storage/tours/ten-file.jpg"
        // Cần cắt bỏ chữ "/storage/" để hàm Storage::delete hiểu được đường dẫn đúng
        $path = str_replace('/storage/', '', $image->image_url);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // 2. Xóa dữ liệu trong Database
        $image->delete();

        return back()->with('success', 'Đã xóa ảnh thành công!');
    }

    public function show($slug)
    {
        $tour = Tour::with([
            'destination',
            'departure_location',
            'tour_images',
            'tour_schedules' => function ($query) {
                $query->where('departure_date', '>=', now())->where('status', 'available')->orderBy('departure_date', 'asc');
            },
            'tour_itineraries.activities',
        ])->where('slug', $slug)->firstOrFail();

        $allActivities = $tour->tour_itineraries->flatMap->activities;
        $groupedActivities = $allActivities->groupBy('activity_type');

        return view('frontend.tours.show', compact('tour', 'groupedActivities'));
    }
}
