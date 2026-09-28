@extends('layouts.admin')

@section('page-title', 'Quản lý Ngày lễ')

@section('content')
<div class="admin-card">
    <div class="admin-card-header">
        <h5 class="admin-card-title">Danh sách Ngày lễ</h5>
        <a href="{{ route('admin.holidays.create') }}" class="btn-admin btn-admin-primary">
            <i class="bi bi-plus-lg me-1"></i> Thêm Ngày lễ
        </a>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên ngày lễ</th>
                        <th>Thời gian</th>
                        <th>Phụ thu (%)</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($holidays as $holiday)
                    <tr>
                        <td>{{ $holiday->id }}</td>
                        <td class="fw-bold">{{ $holiday->name }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($holiday->start_date)->format('d/m/Y') }} 
                            - 
                            {{ \Carbon\Carbon::parse($holiday->end_date)->format('d/m/Y') }}
                        </td>
                        <td><span class="badge badge-soft-danger">+{{ $holiday->price_increase_percentage }}%</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.holidays.edit', $holiday->id) }}" class="btn btn-sm btn-action text-primary" title="Sửa">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.holidays.destroy', $holiday->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-action text-danger" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa ngày lễ này?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Chưa có ngày lễ nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($holidays->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $holidays->links() }}
    </div>
    @endif
</div>
@endsection
