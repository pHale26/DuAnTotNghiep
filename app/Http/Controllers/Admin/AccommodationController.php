<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Destination;
use App\Models\RoomType;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccommodationController extends Controller
{
    public function index()
    {
        $accommodations = Accommodation::with('destination', 'room_types')->latest()->get();

        return view('admin.accommodations.index', compact('accommodations'));
    }

    public function create()
    {
        $destinations = Destination::select('id', 'name')->get();
        $tours = Tour::select('id', 'title', 'destination_id')
            ->where('duration_days', '>', 1)
            ->get();

        return view('admin.accommodations.create', compact('destinations', 'tours'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'star_rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'tour_ids' => 'nullable|array',
            'tour_ids.*' => 'exists:tours,id',
            'rooms' => 'required|array|min:1',
            'rooms.*.name' => 'required|string|max:255',
            'rooms.*.base_capacity' => 'required|integer|min:1',
            'rooms.*.max_capacity' => 'required|integer|min:1',
            'rooms.*.total_rooms' => 'required|integer|min:1',
            'rooms.*.base_price' => 'required|numeric|min:0',
            'rooms.*.extra_bed_price' => 'nullable|numeric|min:0',
            'rooms.*.child_surcharge_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'destination_id', 'address', 'description', 'star_rating']);
            $data['is_active'] = $request->has('is_active');

            $accommodation = Accommodation::create($data);

            foreach ($request->rooms as $roomData) {
                $accommodation->room_types()->create([
                    'name' => $roomData['name'],
                    'base_capacity' => $roomData['base_capacity'],
                    'max_capacity' => $roomData['max_capacity'],
                    'total_rooms' => $roomData['total_rooms'] ?? 1,
                    'base_price' => $roomData['base_price'],
                    'extra_bed_price' => $roomData['extra_bed_price'] ?? 0,
                    'child_surcharge_price' => $roomData['child_surcharge_price'] ?? 0,
                ]);
            }

            // Sync tours after room types are created
            $accommodation->syncTours($request->input('tour_ids', []));

            DB::commit();

            return redirect()->route('admin.accommodations.index')->with('success', 'Thêm lưu trú thành công.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Lỗi: '.$e->getMessage());
        }
    }

    public function edit(Accommodation $accommodation)
    {
        $accommodation->load('room_types');
        $destinations = Destination::select('id', 'name')->get();
        $tours = Tour::select('id', 'title', 'destination_id')
            ->where('duration_days', '>', 1)
            ->get();

        return view('admin.accommodations.edit', compact('accommodation', 'destinations', 'tours'));
    }

    public function update(Request $request, Accommodation $accommodation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'destination_id' => 'required|exists:destinations,id',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'star_rating' => 'required|integer|min:1|max:5',
            'is_active' => 'boolean',
            'tour_ids' => 'nullable|array',
            'tour_ids.*' => 'exists:tours,id',
            'rooms' => 'required|array|min:1',
            'rooms.*.id' => 'nullable|exists:room_types,id',
            'rooms.*.name' => 'required|string|max:255',
            'rooms.*.base_capacity' => 'required|integer|min:1',
            'rooms.*.max_capacity' => 'required|integer|min:1',
            'rooms.*.total_rooms' => 'required|integer|min:1',
            'rooms.*.base_price' => 'required|numeric|min:0',
            'rooms.*.extra_bed_price' => 'nullable|numeric|min:0',
            'rooms.*.child_surcharge_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->only(['name', 'destination_id', 'address', 'description', 'star_rating']);
            $data['is_active'] = $request->has('is_active');

            $accommodation->update($data);

            $currentRoomIds = [];
            foreach ($request->rooms as $roomData) {
                if (! empty($roomData['id'])) {
                    $room = RoomType::findOrFail($roomData['id']);
                    $room->update([
                        'name' => $roomData['name'],
                        'base_capacity' => $roomData['base_capacity'],
                        'max_capacity' => $roomData['max_capacity'],
                        'total_rooms' => $roomData['total_rooms'] ?? 1,
                        'base_price' => $roomData['base_price'],
                        'extra_bed_price' => $roomData['extra_bed_price'] ?? 0,
                        'child_surcharge_price' => $roomData['child_surcharge_price'] ?? 0,
                    ]);
                    $currentRoomIds[] = $room->id;
                } else {
                    $newRoom = $accommodation->room_types()->create([
                        'name' => $roomData['name'],
                        'base_capacity' => $roomData['base_capacity'],
                        'max_capacity' => $roomData['max_capacity'],
                        'total_rooms' => $roomData['total_rooms'] ?? 1,
                        'base_price' => $roomData['base_price'],
                        'extra_bed_price' => $roomData['extra_bed_price'] ?? 0,
                        'child_surcharge_price' => $roomData['child_surcharge_price'] ?? 0,
                    ]);
                    $currentRoomIds[] = $newRoom->id;
                }
            }

            // Xóa các room type không có trong danh sách update
            $accommodation->room_types()->whereNotIn('id', $currentRoomIds)->delete();

            // Sync tours after room types are updated
            $accommodation->syncTours($request->input('tour_ids', []));

            DB::commit();

            return redirect()->route('admin.accommodations.index')->with('success', 'Cập nhật lưu trú thành công.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Lỗi: '.$e->getMessage());
        }
    }

    public function destroy(Accommodation $accommodation)
    {
        $accommodation->room_types()->delete();
        $accommodation->delete();

        return redirect()->route('admin.accommodations.index')->with('success', 'Xóa lưu trú thành công.');
    }
}
