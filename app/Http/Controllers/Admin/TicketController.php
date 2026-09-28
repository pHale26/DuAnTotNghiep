<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Ticket;
use App\Models\TicketImage;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['destination', 'ticket_options', 'ticket_images'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $destinations = Destination::select('id', 'name')->orderBy('name')->get();
        $tours = Tour::select('id', 'title', 'destination_id')->get();

        return view('admin.tickets.create', compact('destinations', 'tours'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'title' => 'required|string|max:255|unique:tickets,title',
            'description' => 'nullable|string',
            'adult_price' => 'required|numeric|min:0',
            'child_price' => 'required|numeric|min:0',
            'provider_name' => 'nullable|string|max:255',
            'cancellation_policy' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'tours' => 'nullable|array',
            'tours.*' => 'exists:tours,id',
        ], [
            'title.unique' => 'Tên vé tham quan này đã tồn tại.',
        ]);

        DB::beginTransaction();
        try {
            $ticket = Ticket::create([
                'destination_id' => $request->destination_id,
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'adult_price' => $request->adult_price,
                'child_price' => $request->child_price,
                'provider_name' => $request->provider_name,
                'cancellation_policy' => $request->cancellation_policy,
            ]);

            // Lưu hình ảnh
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('tickets', 'public');
                    TicketImage::create([
                        'ticket_id' => $ticket->id,
                        'image_url' => '/storage/'.$path,
                        'is_primary' => $index === 0,
                    ]);
                }
            }

            // Liên kết với tours
            if ($request->has('tours')) {
                $ticket->tours()->sync($request->tours);
            }

            DB::commit();

            return redirect()->route('admin.tickets.index')->with('success', 'Đã thêm vé tham quan thành công.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage())->withInput();
        }
    }

    public function edit(Ticket $ticket)
    {
        $ticket->load(['destination', 'ticket_images', 'ticket_options', 'tours']);
        $destinations = Destination::select('id', 'name')->orderBy('name')->get();
        $tours = Tour::select('id', 'title', 'destination_id')->get();
        $selectedTours = $ticket->tours->pluck('id')->toArray();

        return view('admin.tickets.edit', compact('ticket', 'destinations', 'tours', 'selectedTours'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'title' => 'required|string|max:255|unique:tickets,title,'.$ticket->id,
            'description' => 'nullable|string',
            'adult_price' => 'required|numeric|min:0',
            'child_price' => 'required|numeric|min:0',
            'provider_name' => 'nullable|string|max:255',
            'cancellation_policy' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'tours' => 'nullable|array',
            'tours.*' => 'exists:tours,id',
        ], [
            'title.unique' => 'Tên vé tham quan này đã tồn tại.',
        ]);

        DB::beginTransaction();
        try {
            $ticket->update([
                'destination_id' => $request->destination_id,
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'adult_price' => $request->adult_price,
                'child_price' => $request->child_price,
                'provider_name' => $request->provider_name,
                'cancellation_policy' => $request->cancellation_policy,
            ]);

            // Thêm hình ảnh mới
            if ($request->hasFile('images')) {
                $hasExisting = $ticket->ticket_images()->exists();
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('tickets', 'public');
                    TicketImage::create([
                        'ticket_id' => $ticket->id,
                        'image_url' => '/storage/'.$path,
                        'is_primary' => ! $hasExisting && $index === 0,
                    ]);
                    $hasExisting = true;
                }
            }

            // Cập nhật liên kết với tours
            if ($request->has('tours')) {
                $ticket->tours()->sync($request->tours);
            } else {
                $ticket->tours()->sync([]);
            }

            DB::commit();

            return redirect()->route('admin.tickets.index')->with('success', 'Đã cập nhật vé tham quan thành công.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(Ticket $ticket)
    {
        try {
            DB::beginTransaction();

            // Xóa các hình ảnh (Chuyển sang xóa mềm: bỏ qua để giữ lại khi restore)
            // $ticket->ticket_images()->delete();

            // Xóa các option (Chuyển sang xóa mềm: bỏ qua để giữ lại khi restore)
            // $ticket->ticket_options()->delete();

            // Xóa liên kết với tours (Chuyển sang xóa mềm: bỏ qua để giữ lại khi restore)
            // $ticket->tours()->detach();

            // Xóa vé (sẽ thành xóa mềm)
            $ticket->delete();

            DB::commit();

            return redirect()->route('admin.tickets.index')->with('success', 'Đã xóa vé tham quan thành công.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }

    public function destroyImage($ticketId, $imageId)
    {
        $image = TicketImage::where('ticket_id', $ticketId)->findOrFail($imageId);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa hình ảnh.']);
    }

    public function setPrimaryImage($ticketId, $imageId)
    {
        DB::beginTransaction();
        try {
            // Bỏ primary của tất cả ảnh
            TicketImage::where('ticket_id', $ticketId)->update(['is_primary' => false]);

            // Set primary cho ảnh được chọn
            $image = TicketImage::where('ticket_id', $ticketId)->findOrFail($imageId);
            $image->update(['is_primary' => true]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Đã đặt làm ảnh chính.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra.'], 500);
        }
    }

    public function trash()
    {
        $tickets = Ticket::onlyTrashed()->with(['destination', 'ticket_options', 'ticket_images'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.tickets.trash', compact('tickets'));
    }

    public function restore($id)
    {
        $ticket = Ticket::onlyTrashed()->findOrFail($id);
        $ticket->restore();

        return redirect()->route('admin.tickets.trash')->with('success', 'Đã khôi phục vé tham quan thành công.');
    }

    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();

            $ticket = Ticket::onlyTrashed()->findOrFail($id);

            // Xóa các hình ảnh
            $ticket->ticket_images()->delete();

            // Xóa các option
            $ticket->ticket_options()->delete();

            // Xóa liên kết với tours
            $ticket->tours()->detach();

            // Xóa vé vĩnh viễn
            $ticket->forceDelete();

            DB::commit();

            return redirect()->route('admin.tickets.trash')->with('success', 'Đã xóa vĩnh viễn vé tham quan thành công.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Có lỗi xảy ra: '.$e->getMessage());
        }
    }
}
