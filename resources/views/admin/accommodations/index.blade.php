@extends('layouts.admin')

@section('title', 'Quản lý Lưu trú')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý Lưu trú</h1>
        <a href="{{ route('admin.accommodations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Cơ sở</th>
                            <th>Điểm đến</th>
                            <th>Hạng sao</th>
                            <th>Hạng phòng</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accommodations as $acc)
                        <tr>
                            <td>{{ $acc->id }}</td>
                            <td>
                                <strong>{{ $acc->name }}</strong><br>
                                <small class="text-muted">{{ Str::limit($acc->address, 50) }}</small>
                            </td>
                            <td>{{ $acc->destination->name ?? 'N/A' }}</td>
                            <td>
                                @for($i=0; $i<$acc->star_rating; $i++)
                                    <i class="bi bi-star-fill text-warning"></i>
                                @endfor
                            </td>
                            <td>{{ $acc->room_types->count() }} loại phòng</td>
                            <td>
                                @if($acc->is_active)
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Tạm ngưng</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.accommodations.edit', $acc->id) }}" class="btn btn-sm btn-info text-white">Sửa</a>
                                <form action="{{ route('admin.accommodations.destroy', $acc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
