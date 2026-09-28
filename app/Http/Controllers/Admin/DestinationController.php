<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::latest()->paginate(10);

        return view('admin.destinations.index', compact('destinations'));
    }

    public function create()
    {
        return view('admin.destinations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.vi' => 'required|max:255',
            'name.en' => 'nullable|max:255',
            'name.zh' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Kiểm tra trùng lặp tên điểm đến (tiếng Việt)
        $nameVi = $request->name['vi'];
        $existingDestination = Destination::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.vi')) = ?", [$nameVi])->first();

        if ($existingDestination) {
            return back()->withErrors(['name.vi' => 'Tên điểm đến này đã tồn tại.'])->withInput();
        }

        $data = [
            'name' => [
                'vi' => $request->name['vi'],
                'en' => $request->name['en'] ?? $request->name['vi'],
                'zh' => $request->name['zh'] ?? $request->name['vi'],
            ],
            'description' => [
                'vi' => $request->description['vi'] ?? '',
                'en' => $request->description['en'] ?? ($request->description['vi'] ?? ''),
                'zh' => $request->description['zh'] ?? ($request->description['vi'] ?? ''),
            ],
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('destinations', 'public');
            $data['image_url'] = '/storage/'.$path;
        }

        Destination::create($data);

        return redirect()->route('admin.destinations.index')->with('success', 'Thêm điểm đến thành công!');
    }

    public function edit(Destination $destination)
    {
        return view('admin.destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination)
    {
        $request->validate([
            'name.vi' => 'required|max:255',
            'name.en' => 'nullable|max:255',
            'name.zh' => 'nullable|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Kiểm tra trùng lặp tên điểm đến (tiếng Việt), trừ chính nó
        $nameVi = $request->name['vi'];
        $existingDestination = Destination::whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.vi')) = ?", [$nameVi])
            ->where('id', '!=', $destination->id)
            ->first();

        if ($existingDestination) {
            return back()->withErrors(['name.vi' => 'Tên điểm đến này đã tồn tại.'])->withInput();
        }

        $data = [
            'name' => [
                'vi' => $request->name['vi'],
                'en' => $request->name['en'] ?? $request->name['vi'],
                'zh' => $request->name['zh'] ?? $request->name['vi'],
            ],
            'description' => [
                'vi' => $request->description['vi'] ?? '',
                'en' => $request->description['en'] ?? ($request->description['vi'] ?? ''),
                'zh' => $request->description['zh'] ?? ($request->description['vi'] ?? ''),
            ],
        ];

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu có
            if ($destination->image_url) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $destination->image_url));
            }
            $path = $request->file('image')->store('destinations', 'public');
            $data['image_url'] = '/storage/'.$path;
        }

        $destination->update($data);

        return redirect()->route('admin.destinations.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Destination $destination)
    {
        // Kiểm tra xem điểm đến này có đang chứa tour nào không
        if ($destination->tours()->count() > 0) {
            return back()->with('error', 'Không thể xóa! Điểm đến này đang gắn với '.$destination->tours()->count().' tour. Cần đưa các tour đó vào thùng rác hoặc chuyển sang điểm đến khác trước.');
        }

        // Nếu an toàn, tiến hành xóa ảnh vật lý (Chuyển sang xóa mềm: bỏ qua để giữ lại khi restore)
        // if ($destination->image_url) {
        //     Storage::disk('public')->delete(str_replace('/storage/', '', $destination->image_url));
        // }

        // Xóa điểm đến (sẽ thành xóa mềm)
        $destination->delete();

        return back()->with('success', 'Đã xóa điểm đến an toàn!');
    }

    public function trash()
    {
        $destinations = Destination::onlyTrashed()->latest()->paginate(10);

        return view('admin.destinations.trash', compact('destinations'));
    }

    public function restore($id)
    {
        $destination = Destination::onlyTrashed()->findOrFail($id);
        $destination->restore();

        return redirect()->route('admin.destinations.trash')->with('success', 'Đã khôi phục điểm đến!');
    }

    public function forceDelete($id)
    {
        $destination = Destination::onlyTrashed()->findOrFail($id);

        if ($destination->image_url) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $destination->image_url));
        }

        $destination->forceDelete();

        return redirect()->route('admin.destinations.trash')->with('success', 'Đã xóa vĩnh viễn điểm đến!');
    }
}
