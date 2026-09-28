<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Quản trị viên',
            self::STAFF => 'Nhân viên',
            self::CUSTOMER => 'Khách hàng',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ADMIN => 'Toàn quyền quản lý hệ thống',
            self::STAFF => 'Quản lý tour, đơn hàng, khách hàng',
            self::CUSTOMER => 'Đặt tour và mua vé',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($role) {
            return [$role->value => $role->label()];
        })->toArray();
    }
}
